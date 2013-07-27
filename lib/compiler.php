<?php
// Copyright 2011 JMB Software, Inc.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//    http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.


require_once 'utility.php';


define('COMPILER_PHP_START', '<?php ');
define('COMPILER_PHP_START_ECHO', '<?php echo ');
define('COMPILER_PHP_END', ' ?>');
define('COMPILER_DELIMITER_START', '{');
define('COMPILER_DELIMITER_END', '}');
define('COMPILER_COMMENT_TAG', '*');
define('COMPILER_VARIABLE_TAG', '$');
define('COMPILER_STRING_TAG', '"');


$compiler = new Compiler();

class Compiler
{

    var $tag_stack;

    var $literal_stack;

    var $capture_stack;

    var $errors;

    var $defines;

    var $literal_close_tags = array('/php', '/phpcode', '/literal');

    function Compiler()
    {
    }

    function ErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if( $errno == E_USER_NOTICE )
        {
            $this->errors[] = $errstr;
        }
        else
        {
            error_handler($errno, $errstr, $errfile, $errline);
        }
    }

    function Compile($template)
    {
        // Initialization
        $this->tag_stack = array();
        $this->literal_stack = array();
        $this->capture_stack = array();
        $this->errors = array();
        $this->defines = array();

        // Convert to Unix newline characters
        $template = string_format_lf($template);

        // Strip comment sections
        $template = preg_replace('~{\*.*?\*}~msi', '', $template);

        // Create regular expression
        $regex = '~('.COMPILER_DELIMITER_START.'([/*$a-z"]+.*?)'.COMPILER_DELIMITER_END.')~msi';

        // Compile the code
        set_error_handler(array(&$this, 'ErrorHandler'));
        $compiled = preg_replace_callback($regex, array(&$this, 'CompileTag'), $template);
        restore_error_handler();

        // Check for unclosed tag(s)
        foreach( $this->tag_stack as $unclosed_tag )
        {
            $this->errors[] = 'Unclosed {' . $unclosed_tag . '} tag at end of template';
        }

        // Check for unclosed literal sections
        foreach( $this->literal_stack as $unclosed_tag )
        {
            $this->errors[] = 'Unclosed {' . $unclosed_tag . '} tag at end of template';
        }

        // Code cleanup
        //$compiled = preg_replace("~\n+~", "\n", trim($compiled));

        return count($this->errors) ? false : $compiled;
    }

    function CompileFile($filename, $directory = DIR_TEMPLATES)
    {
        return $this->Compile(file_get_contents($directory . '/' . $filename));
    }

    function CompileTag($matches)
    {
        $original = $matches[1];
        $tag = $matches[2];

        // Remove template comments
        if( $tag[0] === COMPILER_COMMENT_TAG )
        {
            return STRING_BLANK;
        }

        $tag = $this->ParseTag($tag);

        // Inside a literal section, don't parse code
        if( !in_array($tag['tag'], $this->literal_close_tags) && count($this->literal_stack) > 0 )
        {
            return $tag;
        }

        // Tag name is a variable
        if( $tag['tag'][0] == COMPILER_VARIABLE_TAG )
        {
            return COMPILER_PHP_START_ECHO . $this->ParseVars($tag['tag'], $tag['modifiers'], true) . ';' . COMPILER_PHP_END;
        }

        switch( $tag['tag'] )
        {
            case 'string':
                return $tag['term'];

            case 'define':
                $this->CompileDefineTag($tag['attributes']);
                return;

            case 'if':
                $this->PushTag($this->tag_stack, 'if');
                return COMPILER_PHP_START . 'if( ' . $this->ParseVars($tag['attributes']) . ' ):' . COMPILER_PHP_END;


            case 'elsif':
            case 'elseif':
                $this->CheckForUnexpected($this->tag_stack, 'if', 'elseif');
                return COMPILER_PHP_START . 'elseif( ' . $this->ParseVars($tag['attributes']) . ' ):' . COMPILER_PHP_END;


            case 'else':
                $this->CheckForUnexpected($this->tag_stack, 'if', 'else');
                return COMPILER_PHP_START . 'else:' . COMPILER_PHP_END;


            case '/if':
                $this->PopTag($this->tag_stack, 'if');
                return COMPILER_PHP_START . 'endif;' . COMPILER_PHP_END;


            case 'capture':
                $this->VerifyHasAttributes($tag['attributes'], $tag['tag']);
                $this->VerifyRequiredAttributes(array('var'), $tag['attributes'], $tag['tag']);
                $this->PushTag($this->tag_stack, 'capture');
                $this->capture_stack[] = $tag['attributes']['var'];
                return COMPILER_PHP_START . 'ob_start();' . COMPILER_PHP_END;


            case '/capture':
                $this->PopTag($this->tag_stack, 'capture');
                return COMPILER_PHP_START . $this->ParseVars(array_pop($this->capture_stack)) . ' = ob_get_clean();' . COMPILER_PHP_END;


            case 'nocache':
                $this->has_nocache = true;
                return '{nocache}';


            case '/nocache':
                return '{/nocache}';


            case 'literal':
                $this->PushTag($this->literal_stack, 'literal');
                return STRING_BLANK;


            case '/literal':
                $this->PopTag($this->literal_stack, 'literal');
                return STRING_BLANK;


            case 'insert':
                $this->PushTag($this->tag_stack, 'insert');
                return $this->CompileInsertTag($tag['attributes']);


            case '/insert':
                $this->PopTag($this->tag_stack, 'insert');
                return COMPILER_PHP_START . 'endif;' . COMPILER_PHP_END;


            case 'range':
                $this->PushTag($this->tag_stack, 'range');
                return $this->CompileRangeStart($tag['attributes']);


            case '/range':
                $this->PopTag($this->tag_stack, 'range');
                return COMPILER_PHP_START . 'endforeach;' . COMPILER_PHP_END;


            case 'foreach':
                $this->PushTag($this->tag_stack, 'foreach');
                return $this->CompileForeachStart($tag['attributes']);


            case 'foreachdone':
                return COMPILER_PHP_START . 'break;' . COMPILER_PHP_END;


            case '/foreach':
                $this->PopTag($this->tag_stack, 'foreach');
                return COMPILER_PHP_START . STRING_LF_UNIX .
                       '    endforeach;' . STRING_LF_UNIX .
                       'endif;' . STRING_LF_UNIX .
                       COMPILER_PHP_END;


            case 'php':
                $this->PushTag($this->literal_stack, 'php');
                return COMPILER_PHP_START;


            case '/php':
                $this->PopTag($this->literal_stack, 'php');
                return COMPILER_PHP_END;


            case 'phpcode':
                $this->PushTag($this->literal_stack, 'phpcode');
                return COMPILER_PHP_START . "echo '" . COMPILER_PHP_START . " ';" . COMPILER_PHP_END;


            case '/phpcode':
                $this->PopTag($this->literal_stack, 'phpcode');
                return COMPILER_PHP_START. "echo ' " . COMPILER_PHP_END . "';" . COMPILER_PHP_END;


            case 'setlocale':
                $this->VerifyHasAttributes($tag['attributes'], $tag['tag']);
                $this->VerifyRequiredAttributes(array('value'), $tag['attributes'], $tag['tag']);
                return COMPILER_PHP_START . 'setlocale(LC_TIME, \'' . $tag['attributes']['value'] . '\');' . COMPILER_PHP_END;


            case 'datelocale':
                $this->VerifyRequiredAttributes(array('value', 'format'), $tag['attributes'], $tag['tag']);

                switch( strtolower($tag['attributes']['value']) )
                {
                    case 0:
                    case '0':
                    case '-0':
                    case '-0 day':
                    case '-0 days':
                    case '+0':
                    case '+0 day':
                    case '+0 days':
                    case 'today':
                    case 'now':
                        $tag['attributes']['value'] = null;
                        break;

                    default:
                        $tag['attributes']['value'] = ' ' . $tag['attributes']['value'];
                        break;
                }

                return COMPILER_PHP_START_ECHO .
                       "ucwords(strftime('" . $tag['attributes']['format'] . "', strtotime(date('Y-m-d H:i:s')" .
                       (empty($tag['attributes']['value']) ? '' : " . '" . $tag['attributes']['value'] . "'") . ")));" .
                       COMPILER_PHP_END;


            case 'date':
                $this->VerifyRequiredAttributes(array('value', 'format'), $tag['attributes'], $tag['tag']);

                switch( strtolower($tag['attributes']['value']) )
                {
                    case 0:
                    case '0':
                    case '-0':
                    case '-0 day':
                    case '-0 days':
                    case '+0':
                    case '+0 day':
                    case '+0 days':
                    case 'today':
                    case 'now':
                        $tag['attributes']['value'] = null;
                        break;

                    default:
                        $tag['attributes']['value'] = ' ' . $tag['attributes']['value'];
                        break;
                }

                return COMPILER_PHP_START_ECHO .
                       "date('" . $tag['attributes']['format'] . "', strtotime(date('Y-m-d H:i:s')" .
                       (empty($tag['attributes']['value']) ? '' : " . '" . $tag['attributes']['value'] . "'") . "));" .
                       COMPILER_PHP_END;


            case 'assign':
                return $this->CompileAssignTag($tag['attributes']);


            case 'include':
                $this->VerifyRequiredAttributes(array('file'), $tag['attributes'], $tag['tag']);
                return COMPILER_PHP_START . "readfile('" . $tag['attributes']['file'] . "');" . COMPILER_PHP_END;

            case 'options':
                return $this->CompileOptionsTag($tag['attributes']);

            case 'template':
                return $this->CompileTemplateTag($tag['attributes']);

            case 'reasons':
                $this->has_db = true;
                return $this->CompileReasonsTag($tag['attributes']);

            default:
                // Call tag handler function in descendant class
                if( isset($this->handlers[$tag['tag']]) )
                {
                    return call_user_func($this->handlers[$tag['tag']], $tag['attributes']);
                }
        }

        return $original;
    }

    function CompileOptionsTag($attributes)
    {
        $this->VerifyHasAttributes($attributes, 'options');
        $this->VerifyRequiredAttributes(array('from'), $attributes, 'options');
        $this->VerifyVariableAttributes(array('from'), $attributes, 'options');

        $attributes['from'] = $this->ParseVars($attributes['from']);
        $attributes['selected'] = isset($attributes['selected']) ? $this->ParseVars($attributes['selected']) : 'null';

        return COMPILER_PHP_START .
               "foreach( " . $attributes['from'] . " as \$x_key => \$x_value )" . STRING_LF_UNIX .
               "{" . STRING_LF_UNIX .
               'echo "<option value=\"" . htmlspecialchars($x_value) . "\"" . ' . STRING_LF_UNIX .
               '($x_value == ' . $attributes['selected'] . ' ? " selected=\"selected\"" : "") . ' . STRING_LF_UNIX .
               '">" . htmlspecialchars($x_value) . "</option>";' . STRING_LF_UNIX .
               "}" . STRING_LF_UNIX .
               COMPILER_PHP_END;
    }

    function CompileAssignTag($attributes)
    {
        $this->VerifyHasAttributes($attributes, 'assign');
        $this->VerifyRequiredAttributes(array('var'), $attributes, 'assign');
        $this->VerifyVariableAttributes(array('var'), $attributes, 'assign');

        $attributes['var'] = $this->ParseVars($attributes['var']);

        if( isset($attributes['value']) )
        {
            $attributes['value'] = $this->ParseVarsInString(substr($attributes['value'], 1));
        }
        else
        {
            $attributes['value'] = $this->ParseVars($attributes['code']);
        }

        return COMPILER_PHP_START . $attributes['var'] . ' = ' . $attributes['value'] .  ';' . COMPILER_PHP_END;
    }

    function CompileTemplateTag($attributes)
    {
        $this->VerifyHasAttributes($attributes, 'template');
        $this->VerifyRequiredAttributes(array('file'), $attributes, 'template');

        if( !is_file(DIR_TEMPLATES . '/' . $attributes['file']) )
        {
            trigger_error("The template file '" . $attributes['file'] . "' does not exist", E_USER_NOTICE);
        }

        $variables = '';
        foreach( $attributes as $name => $value )
        {
            if( $name != 'file' )
            {
                $variables .= "\$this->vars['$name'] = " .
                              $this->ParseVarsInString($value) .
                              ";" . STRING_LF_UNIX;
            }
        }

        return COMPILER_PHP_START . STRING_LF_UNIX .
               $variables .
               "include(DIR_COMPILED . '/" . $attributes['file'] . "');" . STRING_LF_UNIX .
               COMPILER_PHP_END;
    }

    function CompileDefineTag($attributes)
    {
        $this->VerifyHasAttributes($attributes, 'define');
        $this->VerifyRequiredAttributes(array('name', 'value'), $attributes, 'define');

        $this->defines[$attributes['name']] = $this->ToBoolean($attributes['value']);
    }

    function CompileInsertTag($attributes)
    {
        $this->VerifyHasAttributes($attributes, 'insert');
        $this->VerifyRequiredAttributes(array('location', 'counter'), $attributes, 'insert');
        $this->VerifyVariableAttributes(array('counter'), $attributes, 'insert');

        $attributes['location'] = $this->ParseVars($attributes['location']);
        $attributes['counter'] = $this->ParseVars($attributes['counter']);

        // Format: +5
        if( preg_match('~\+(\d+)~', $attributes['location'], $matches) )
        {
            return COMPILER_PHP_START . " if( " . $attributes['counter'] . " % " . $matches[1] . " == 0 " .
                   (isset($attributes['max']) && is_numeric($attributes['max']) ? "&& " . $attributes['counter'] . " <= " . $attributes['max'] . " " : '') .
                   "): " . COMPILER_PHP_END;
        }

        // Format: $var
        if( preg_match('~^\$~', $attributes['location']) )
        {
            return COMPILER_PHP_START . " if( " . $attributes['counter'] . " % " . $attributes['location'] . " == 0 " .
                   (isset($attributes['max']) && is_numeric($attributes['max']) ? "&& " . $attributes['counter'] . " <= " . $attributes['max'] . " " : '') .
                   "): " . COMPILER_PHP_END;
        }

        // Format: 5
        else if( is_numeric($attributes['location']) )
        {
            return COMPILER_PHP_START . " if( " . $attributes['counter'] . " == " . $attributes['location'] . " ): " . COMPILER_PHP_END;
        }

        // Format: 5,10,15
        else if( preg_match_all('~(\d+)\s*,?~', $attributes['location'], $matches) )
        {
            return COMPILER_PHP_START .
                   " if( strstr('," . join(',', $matches[1]) . ",', ','." . $attributes['counter'] . ".',') ): " . COMPILER_PHP_END;
        }
    }

    function CompileRangeStart($attributes)
    {
        $this->VerifyHasAttributes($attributes, 'range');
        $this->VerifyRequiredAttributes(array('start', 'end', 'counter'), $attributes, 'range');
        $this->VerifyVariableAttributes(array('counter'), $attributes, 'range');

        $attributes['start'] = $this->ParseVars($attributes['start']);
        $attributes['end'] = $this->ParseVars($attributes['end']);
        $attributes['counter'] = $this->ParseVars($attributes['counter']);

        return COMPILER_PHP_START .
               "foreach( range(" . $attributes['start'] . "," . $attributes['end'] . ") as " . $attributes['counter'] . " ):" .
               COMPILER_PHP_END;
    }

    function CompileForeachStart($attributes)
    {
        $this->VerifyHasAttributes($attributes, 'foreach');

        // Set defaults
        $attributes = array_merge(array('counter' => null), $attributes);

        $this->VerifyRequiredAttributes(array('from', 'var'), $attributes, 'foreach');
        $this->VerifyVariableAttributes(array('from', 'var', 'counter'), $attributes, 'foreach');

        if( $attributes['from'] == $attributes['var'] )
        {
            trigger("{foreach} tag 'var' and 'from' attributes cannot be set to the same value", E_USER_NOTICE);
        }

        $attributes['from'] = $this->ParseVars($attributes['from']);
        $attributes['var'] = $this->ParseVars($attributes['var']);
        $attributes['counter'] = empty($attributes['counter']) ? null : $this->ParseVars($attributes['counter']);

        return COMPILER_PHP_START . STRING_LF_UNIX .
               'if( is_array(' . $attributes['from'] . ') ):' . STRING_LF_UNIX .
               ($attributes['counter'] ? '    ' . $attributes['counter'] . ' = 0;' . STRING_LF_UNIX : '') .
               '    foreach( ' . $attributes['from'] . ' as ' . $attributes['var'] . ' ):' . STRING_LF_UNIX .
               ($attributes['counter'] ? '    ' . $attributes['counter'] . '++;' . STRING_LF_UNIX : '') .
               COMPILER_PHP_END;
    }

    function CheckForUnexpected($stack, $expected_tag, $current_tag)
    {
        if( end($stack) != $expected_tag )
        {
            trigger_error('Unexpected {' . $current_tag . '}', E_USER_NOTICE);
        }
    }

    function PushTag(&$stack, $tag)
    {
        $stack[] = $tag;
    }

    function PopTag(&$stack, $expected_tag)
    {
        $popped_tag = array_pop($stack);

        if( $popped_tag === null )
        {
            trigger_error("Mismatched {/$expected_tag} tag", E_USER_NOTICE);
        }
        else if( $popped_tag != $expected_tag )
        {
            trigger_error("Unexpected {/$expected_tag} tag", E_USER_NOTICE);
        }
    }

    function ParseTag($tag)
    {
        $parsed_tag = null;

        switch($tag[0])
        {
            case COMPILER_VARIABLE_TAG:
                $parsed_tag = array();
                $parsed_tag['tag'] = $tag;
                $parsed_tag['modifiers'] = null;

                // Check for tag modifiers
                if( preg_match('~([^|]+)\|(.*)$~s', $parsed_tag['tag'], $matches) )
                {
                    $parsed_tag['tag'] = $matches[1];
                    $parsed_tag['modifiers'] = isset($matches[2]) ? $matches[2] : null;
                }
                break;

            case COMPILER_STRING_TAG:
                $parsed_tag = array();
                $parsed_tag['tag'] = 'string';
                $parsed_tag['term'] = $this->DeQuote($tag);
                break;

            default:
                // Separate the tag name from it's attributes
                if( preg_match('~([^\s]+)(\s+(.*))?$~s', $tag, $matches) )
                {
                    $parsed_tag = array();
                    $parsed_tag['tag'] = $matches[1];
                    $parsed_tag['attributes'] = isset($matches[3]) ? $matches[3] : array();

                    if( !empty($parsed_tag['attributes']) )
                    {
                        if( preg_match_all('~([a-z_ ]+=[^=].*?)(?=(?:\s+[a-z_]+\s*=)|$)~i', $parsed_tag['attributes'], $matches) )
                        {
                            $parsed_tag['attributes'] = array();

                            foreach( $matches[1] as $match )
                            {
                                $equals_pos = strpos($match, '=');
                                $attr_name = $this->DeQuote(trim(substr($match, 0, $equals_pos)));
                                $attr_value = $this->DeQuote(trim(substr($match, $equals_pos + 1)));

                                $parsed_tag['attributes'][strtolower($attr_name)] = $attr_value;
                            }
                        }
                    }
                }
        }

        return $parsed_tag;
    }

    function ParseVars($variable, $modifiers = null, $is_variable = false)
    {
        $parsed_var = preg_replace(array('~\$([a-z0-9_]+)~',
                                         '~(\$this->vars(\[\'?[a-z0-9_]+\'?\])+)\.([a-z0-9_]+)~i',
                                         ),
                                   array("\$this->vars['\\1']",
                                         "\\1['\\3']"), $variable);

        if( $is_variable && (!isset($this->defines['htmlspecialchars']) || $this->defines['htmlspecialchars'] === true) && !stristr($modifiers, 'rawhtml') && !stristr($modifiers, 'htmlspecialchars') )
        {
            $modifiers = (empty($modifiers) ? '' : "$modifiers|") . 'htmlspecialchars';
        }

        $modifiers = preg_replace('~\|?rawhtml~i', '', $modifiers);

        // Process modifiers
        if( !empty($modifiers) )
        {
            foreach( explode('|', $modifiers) as $modifier )
            {
                if( preg_match('~^([a-z0-9_\->\$]+)(\((.*?)\))?$~i', $modifier, $matches) )
                {
                    $function = $matches[1];
                    $arguments = isset($matches[3]) ? ',' . $matches[3] : '';

                    if( !empty($arguments) )
                    {
                        $arguments = $this->ParseVars($arguments);
                    }

                    $parsed_var = "$function($parsed_var$arguments)";
                }
            }
        }

        return $parsed_var;
    }

    function ParseVarsInString($string)
    {
        return str_replace(array("'' . ", " . ''"), '', "'" . preg_replace('~(\$this->vars(\[.*?\])+)~', '\' . $1 . \'', $this->ParseVars($string)) . "'");
    }

    function DeQuote($string)
    {
        if( ($string[0] == "'" || $string[0] == '"') && substr($string, -1) == $string[0] )
        {
            return substr($string, 1, -1);
        }
        else
        {
            return $string;
        }
    }

    function Quote($string, $quote = "'")
    {
        return $quote . str_replace($quote, "\\" . $quote, $string) . $quote;
    }

    function GetCurrentLine()
    {
        return $this->current_line;
    }

    function GetErrors()
    {
        return $this->errors;
    }

    function VerifyRequiredAttributes($required, &$attributes, $tag)
    {
        foreach( $required as $r )
        {
            if( !isset($attributes[$r]) || string_is_empty($attributes[$r]) )
            {
                trigger_error("{".$tag."} tag is missing the '$r' attribute", E_USER_NOTICE);
            }
        }
    }

    function VerifyVariableAttributes($variables, &$attributes, $tag)
    {
        foreach( $variables as $v )
        {
            if( isset($attributes[$v]) && !preg_match('~^\$[\w]+$~', $attributes[$v]) )
            {
                trigger_error("{".$tag."} tag attribute '$v' must be set to a variable name", E_USER_NOTICE);
            }
        }
    }

    function VerifyHasAttributes(&$attributes, $tag)
    {
        if( !is_array($attributes) || count($attributes) < 1 )
        {
            trigger_error("{" . $tag . "} tag is missing it's attributes", E_USER_NOTICE);
        }
    }

    function ToBoolean($input)
    {
        switch(strtolower($input))
        {
            case 'true':
                return true;

            case 'false':
                return false;
        }

        return $input;
    }
}

?>