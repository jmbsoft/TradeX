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

define('MAILER_METHOD_PHP', 'PHP mail() function');
define('MAILER_METHOD_SENDMAIL', 'Sendmail');
define('MAILER_METHOD_SMTP', 'SMTP');

define('MAILER_OPTION_SSL', 'ssl');

define('MAILER_KEY_BODY', 'body');
define('MAILER_KEY_SUBJECT', 'subject');


class Mailer extends PHPMailer
{

    var $greeting;

    var $signature;

    function Mailer()
    {
        global $C;

        $this->From = $C['email_address'];
        $this->FromName = $C['email_name'];
        $this->CharSet = 'UTF-8';

        switch( $C['email_method'] )
        {
            case MAILER_METHOD_PHP:
                $this->IsMail();
                break;

            case MAILER_METHOD_SENDMAIL:
                $this->IsSendmail();
                $this->Sendmail = $C['sendmail_path'];
                break;

            case MAILER_METHOD_SMTP:
                $this->IsSMTP();
                $this->Host = $C['smtp_hostname'];
                $this->Port = $C['smtp_port'];
                $this->SMTPSecure = $C['flag_smtp_ssl'] ? MAILER_OPTION_SSL : '';

                $this->Username = $C['smtp_username'];
                $this->Password = $C['smtp_password'];

                if( !string_is_empty($this->Username) )
                {
                    $this->SMTPAuth = true;
                }

                break;
        }

        $this->greeting = file_get_contents(DIR_COMPILED . '/email-global-greeting.tpl');
        $this->signature = file_get_contents(DIR_COMPILED . '/email-global-signature.tpl');
    }

    function Mail($template, &$t, $to, $to_name = '')
    {
        if( !is_array($template) )
        {
            $template_contents = file(DIR_COMPILED . '/' . $template);
            $template = array();
            $template[MAILER_KEY_SUBJECT] = trim(array_shift($template_contents));
            $template[MAILER_KEY_BODY] = join(STRING_BLANK, $template_contents);
        }

        $template[MAILER_KEY_SUBJECT] = $t->Parse($template[MAILER_KEY_SUBJECT]);
        $template[MAILER_KEY_BODY] = $t->Parse($this->greeting) . STRING_LF_UNIX . STRING_LF_UNIX .
                                     $t->Parse($template[MAILER_KEY_BODY]) . STRING_LF_UNIX . STRING_LF_UNIX .
                                     $t->Parse($this->signature);

        $this->AddAddress($to, $to_name);

        $this->Subject = $template[MAILER_KEY_SUBJECT];
        $this->Body = nl2br($template[MAILER_KEY_BODY]);
        $this->AltBody = html_entity_decode(strip_tags(preg_replace('~<br\s*/?>~', STRING_LF_UNIX, $template[MAILER_KEY_BODY])));

        return $this->Send();
    }
}


class PHPMailer {

  /////////////////////////////////////////////////
  // PROPERTIES, PUBLIC
  /////////////////////////////////////////////////

  var $Priority          = 3;

  var $CharSet           = 'iso-8859-1';

  var $ContentType        = 'text/plain';

  var $Encoding          = '8bit';

  var $ErrorInfo         = '';

  var $From              = 'root@localhost';

  var $FromName          = 'Root User';

  var $Sender            = '';

  var $Subject           = '';

  var $Body              = '';

  var $AltBody           = '';

  var $WordWrap          = 0;

  var $Mailer            = 'mail';

  var $Sendmail          = '/usr/sbin/sendmail';

  var $PluginDir         = '';

  var $Version           = "2.0.4";

  var $ConfirmReadingTo  = '';

  var $Hostname          = '';

  var $MessageID         = '';

  /////////////////////////////////////////////////
  // PROPERTIES FOR SMTP
  /////////////////////////////////////////////////

  var $Host        = 'localhost';

  var $Port        = 25;

  var $Helo        = '';

  var $SMTPSecure = "";

  var $SMTPAuth     = false;

  var $Username     = '';

  var $Password     = '';

  var $Timeout      = 10;

  var $SMTPDebug    = false;

  var $SMTPKeepAlive = false;

  var $SingleTo = false;

  /////////////////////////////////////////////////
  // PROPERTIES, PRIVATE
  /////////////////////////////////////////////////

  var $smtp            = NULL;
  var $to              = array();
  var $cc              = array();
  var $bcc             = array();
  var $ReplyTo         = array();
  var $attachment      = array();
  var $CustomHeader    = array();
  var $message_type    = '';
  var $boundary        = array();
  var $language        = array();
  var $error_count     = 0;
  var $LE              = "\n";
  var $sign_cert_file  = "";
  var $sign_key_file   = "";
  var $sign_key_pass   = "";

  /////////////////////////////////////////////////
  // METHODS, VARIABLES
  /////////////////////////////////////////////////

  function IsHTML($bool) {
    if($bool == true) {
      $this->ContentType = 'text/html';
    } else {
      $this->ContentType = 'text/plain';
    }
  }

  function IsSMTP() {
    $this->Mailer = 'smtp';
  }

  function IsMail() {
    $this->Mailer = 'mail';
  }

  function IsSendmail() {
    $this->Mailer = 'sendmail';
  }

  function IsQmail() {
    $this->Sendmail = '/var/qmail/bin/sendmail';
    $this->Mailer = 'sendmail';
  }

  /////////////////////////////////////////////////
  // METHODS, RECIPIENTS
  /////////////////////////////////////////////////

  function AddAddress($address, $name = '') {
    $cur = count($this->to);
    $this->to[$cur][0] = trim($address);
    $this->to[$cur][1] = $name;
  }

  function AddCC($address, $name = '') {
    $cur = count($this->cc);
    $this->cc[$cur][0] = trim($address);
    $this->cc[$cur][1] = $name;
  }

  function AddBCC($address, $name = '') {
    $cur = count($this->bcc);
    $this->bcc[$cur][0] = trim($address);
    $this->bcc[$cur][1] = $name;
  }

  function AddReplyTo($address, $name = '') {
    $cur = count($this->ReplyTo);
    $this->ReplyTo[$cur][0] = trim($address);
    $this->ReplyTo[$cur][1] = $name;
  }

  /////////////////////////////////////////////////
  // METHODS, MAIL SENDING
  /////////////////////////////////////////////////

  function Send() {
    $header = '';
    $body = '';
    $result = true;

    if((count($this->to) + count($this->cc) + count($this->bcc)) < 1) {
      $this->SetError($this->Lang('provide_address'));
      return false;
    }

    if(!empty($this->AltBody)) {
      $this->ContentType = 'multipart/alternative';
    }

    $this->error_count = 0; // reset errors
    $this->SetMessageType();
    $header .= $this->CreateHeader();
    $body = $this->CreateBody();

    if($body == '') {
      return false;
    }

    switch($this->Mailer) {
      case 'sendmail':
        $result = $this->SendmailSend($header, $body);
        break;
      case 'smtp':
        $result = $this->SmtpSend($header, $body);
        break;
      case 'mail':
        $result = $this->MailSend($header, $body);
        break;
      default:
        $result = $this->MailSend($header, $body);
        break;
        //$this->SetError($this->Mailer . $this->Lang('mailer_not_supported'));
        //$result = false;
        //break;
    }

    return $result;
  }

  function SendmailSend($header, $body) {
    if ($this->Sender != '') {
      $sendmail = sprintf("%s -oi -f %s -t", escapeshellcmd($this->Sendmail), escapeshellarg($this->Sender));
    } else {
      $sendmail = sprintf("%s -oi -t", escapeshellcmd($this->Sendmail));
    }

    if(!@$mail = popen($sendmail, 'w')) {
      $this->SetError($this->Lang('execute') . $this->Sendmail);
      return false;
    }

    fputs($mail, $header);
    fputs($mail, $body);

    $result = pclose($mail);
    if (version_compare(phpversion(), '4.2.3') == -1) {
      $result = $result >> 8 & 0xFF;
    }
    if($result != 0) {
      $this->SetError($this->Lang('execute') . $this->Sendmail);
      return false;
    }
    return true;
  }

  function MailSend($header, $body) {

    $to = '';
    for($i = 0; $i < count($this->to); $i++) {
      if($i != 0) { $to .= ', '; }
      $to .= $this->AddrFormat($this->to[$i]);
    }

    $toArr = explode(',', $to);

    $params = sprintf("-oi -f %s", $this->Sender);
    if ($this->Sender != '' && strlen(ini_get('safe_mode')) < 1) {
      $old_from = ini_get('sendmail_from');
      ini_set('sendmail_from', $this->Sender);
      if ($this->SingleTo === true && count($toArr) > 1) {
        foreach ($toArr as $key => $val) {
          $rt = @mail($val, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header, $params);
        }
      } else {
        $rt = @mail($to, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header, $params);
      }
    } else {
      if ($this->SingleTo === true && count($toArr) > 1) {
        foreach ($toArr as $key => $val) {
          $rt = @mail($val, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header, $params);
        }
      } else {
        $rt = @mail($to, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header);
      }
    }

    if (isset($old_from)) {
      ini_set('sendmail_from', $old_from);
    }

    if(!$rt) {
      $this->SetError($this->Lang('instantiate'));
      return false;
    }

    return true;
  }

  function SmtpSend($header, $body) {
    //include_once($this->PluginDir . 'class.smtp.php');
    $error = '';
    $bad_rcpt = array();

    if(!$this->SmtpConnect()) {
      return false;
    }

    $smtp_from = ($this->Sender == '') ? $this->From : $this->Sender;
    if(!$this->smtp->Mail($smtp_from)) {
      $error = $this->Lang('from_failed') . $smtp_from;
      $this->SetError($error);
      $this->smtp->Reset();
      return false;
    }

    for($i = 0; $i < count($this->to); $i++) {
      if(!$this->smtp->Recipient($this->to[$i][0])) {
        $bad_rcpt[] = $this->to[$i][0];
      }
    }
    for($i = 0; $i < count($this->cc); $i++) {
      if(!$this->smtp->Recipient($this->cc[$i][0])) {
        $bad_rcpt[] = $this->cc[$i][0];
      }
    }
    for($i = 0; $i < count($this->bcc); $i++) {
      if(!$this->smtp->Recipient($this->bcc[$i][0])) {
        $bad_rcpt[] = $this->bcc[$i][0];
      }
    }

    if(count($bad_rcpt) > 0) { // Create error message
      for($i = 0; $i < count($bad_rcpt); $i++) {
        if($i != 0) {
          $error .= ', ';
        }
        $error .= $bad_rcpt[$i];
      }
      $error = $this->Lang('recipients_failed') . $error;
      $this->SetError($error);
      $this->smtp->Reset();
      return false;
    }

    if(!$this->smtp->Data($header . $body)) {
      $this->SetError($this->Lang('data_not_accepted'));
      $this->smtp->Reset();
      return false;
    }
    if($this->SMTPKeepAlive == true) {
      $this->smtp->Reset();
    } else {
      $this->SmtpClose();
    }

    return true;
  }

  function SmtpConnect() {
    if($this->smtp == NULL) {
      $this->smtp = new SMTP();
    }

    $this->smtp->do_debug = $this->SMTPDebug;
    $hosts = explode(';', $this->Host);
    $index = 0;
    $connection = ($this->smtp->Connected());

    while($index < count($hosts) && $connection == false) {
      $hostinfo = array();
      if(eregi('^(.+):([0-9]+)$', $hosts[$index], $hostinfo)) {
        $host = $hostinfo[1];
        $port = $hostinfo[2];
      } else {
        $host = $hosts[$index];
        $port = $this->Port;
      }

      if($this->smtp->Connect(((!empty($this->SMTPSecure))?$this->SMTPSecure.'://':'').$host, $port, $this->Timeout)) {
        if ($this->Helo != '') {
          $this->smtp->Hello($this->Helo);
        } else {
          $this->smtp->Hello($this->ServerHostname());
        }

        $connection = true;
        if($this->SMTPAuth) {
          if(!$this->smtp->Authenticate($this->Username, $this->Password)) {
            $this->SetError($this->Lang('authenticate'));
            $this->smtp->Reset();
            $connection = false;
          }
        }
      }
      $index++;
    }
    if(!$connection) {
      $this->SetError($this->Lang('connect_host'));
    }

    return $connection;
  }

  function SmtpClose() {
    if($this->smtp != NULL) {
      if($this->smtp->Connected()) {
        $this->smtp->Quit();
        $this->smtp->Close();
      }
    }
  }

  function SetLanguage($lang_type, $lang_path = 'language/') {
    if(file_exists($lang_path.'phpmailer.lang-'.$lang_type.'.php')) {
      include($lang_path.'phpmailer.lang-'.$lang_type.'.php');
    } elseif (file_exists($lang_path.'phpmailer.lang-en.php')) {
      include($lang_path.'phpmailer.lang-en.php');
    } else {
      $PHPMAILER_LANG = array();
      $PHPMAILER_LANG["provide_address"]      = 'You must provide at least one ' .
      $PHPMAILER_LANG["mailer_not_supported"] = ' mailer is not supported.';
      $PHPMAILER_LANG["execute"]              = 'Could not execute: ';
      $PHPMAILER_LANG["instantiate"]          = 'Could not instantiate mail function.';
      $PHPMAILER_LANG["authenticate"]         = 'SMTP Error: Could not authenticate.';
      $PHPMAILER_LANG["from_failed"]          = 'The following From address failed: ';
      $PHPMAILER_LANG["recipients_failed"]    = 'SMTP Error: The following ' .
      $PHPMAILER_LANG["data_not_accepted"]    = 'SMTP Error: Data not accepted.';
      $PHPMAILER_LANG["connect_host"]         = 'SMTP Error: Could not connect to SMTP host.';
      $PHPMAILER_LANG["file_access"]          = 'Could not access file: ';
      $PHPMAILER_LANG["file_open"]            = 'File Error: Could not open file: ';
      $PHPMAILER_LANG["encoding"]             = 'Unknown encoding: ';
      $PHPMAILER_LANG["signing"]              = 'Signing Error: ';
    }
    $this->language = $PHPMAILER_LANG;

    return true;
  }

  /////////////////////////////////////////////////
  // METHODS, MESSAGE CREATION
  /////////////////////////////////////////////////

  function AddrAppend($type, $addr) {
    $addr_str = $type . ': ';
    $addr_str .= $this->AddrFormat($addr[0]);
    if(count($addr) > 1) {
      for($i = 1; $i < count($addr); $i++) {
        $addr_str .= ', ' . $this->AddrFormat($addr[$i]);
      }
    }
    $addr_str .= $this->LE;

    return $addr_str;
  }

  function AddrFormat($addr) {
    if(empty($addr[1])) {
      $formatted = $this->SecureHeader($addr[0]);
    } else {
      $formatted = $this->EncodeHeader($this->SecureHeader($addr[1]), 'phrase') . " <" . $this->SecureHeader($addr[0]) . ">";
    }

    return $formatted;
  }

  function WrapText($message, $length, $qp_mode = false) {
    $soft_break = ($qp_mode) ? sprintf(" =%s", $this->LE) : $this->LE;
    // If utf-8 encoding is used, we will need to make sure we don't
    // split multibyte characters when we wrap
    $is_utf8 = (strtolower($this->CharSet) == "utf-8");

    $message = $this->FixEOL($message);
    if (substr($message, -1) == $this->LE) {
      $message = substr($message, 0, -1);
    }

    $line = explode($this->LE, $message);
    $message = '';
    for ($i=0 ;$i < count($line); $i++) {
      $line_part = explode(' ', $line[$i]);
      $buf = '';
      for ($e = 0; $e<count($line_part); $e++) {
        $word = $line_part[$e];
        if ($qp_mode and (strlen($word) > $length)) {
          $space_left = $length - strlen($buf) - 1;
          if ($e != 0) {
            if ($space_left > 20) {
              $len = $space_left;
              if ($is_utf8) {
                $len = $this->UTF8CharBoundary($word, $len);
              } elseif (substr($word, $len - 1, 1) == "=") {
                $len--;
              } elseif (substr($word, $len - 2, 1) == "=") {
                $len -= 2;
              }
              $part = substr($word, 0, $len);
              $word = substr($word, $len);
              $buf .= ' ' . $part;
              $message .= $buf . sprintf("=%s", $this->LE);
            } else {
              $message .= $buf . $soft_break;
            }
            $buf = '';
          }
          while (strlen($word) > 0) {
            $len = $length;
            if ($is_utf8) {
              $len = $this->UTF8CharBoundary($word, $len);
            } elseif (substr($word, $len - 1, 1) == "=") {
              $len--;
            } elseif (substr($word, $len - 2, 1) == "=") {
              $len -= 2;
            }
            $part = substr($word, 0, $len);
            $word = substr($word, $len);

            if (strlen($word) > 0) {
              $message .= $part . sprintf("=%s", $this->LE);
            } else {
              $buf = $part;
            }
          }
        } else {
          $buf_o = $buf;
          $buf .= ($e == 0) ? $word : (' ' . $word);

          if (strlen($buf) > $length and $buf_o != '') {
            $message .= $buf_o . $soft_break;
            $buf = $word;
          }
        }
      }
      $message .= $buf . $this->LE;
    }

    return $message;
  }

  function UTF8CharBoundary($encodedText, $maxLength) {
    $foundSplitPos = false;
    $lookBack = 3;
    while (!$foundSplitPos) {
      $lastChunk = substr($encodedText, $maxLength - $lookBack, $lookBack);
      $encodedCharPos = strpos($lastChunk, "=");
      if ($encodedCharPos !== false) {
        // Found start of encoded character byte within $lookBack block.
        // Check the encoded byte value (the 2 chars after the '=')
        $hex = substr($encodedText, $maxLength - $lookBack + $encodedCharPos + 1, 2);
        $dec = hexdec($hex);
        if ($dec < 128) { // Single byte character.
          // If the encoded char was found at pos 0, it will fit
          // otherwise reduce maxLength to start of the encoded char
          $maxLength = ($encodedCharPos == 0) ? $maxLength :
          $maxLength - ($lookBack - $encodedCharPos);
          $foundSplitPos = true;
        } elseif ($dec >= 192) { // First byte of a multi byte character
          // Reduce maxLength to split at start of character
          $maxLength = $maxLength - ($lookBack - $encodedCharPos);
          $foundSplitPos = true;
        } elseif ($dec < 192) { // Middle byte of a multi byte character, look further back
          $lookBack += 3;
        }
      } else {
        // No encoded character found
        $foundSplitPos = true;
      }
    }
    return $maxLength;
  }

  function SetWordWrap() {
    if($this->WordWrap < 1) {
      return;
    }

    switch($this->message_type) {
      case 'alt':

      case 'alt_attachments':
        $this->AltBody = $this->WrapText($this->AltBody, $this->WordWrap);
        break;
      default:
        $this->Body = $this->WrapText($this->Body, $this->WordWrap);
        break;
    }
  }

  function CreateHeader() {
    $result = '';

    $uniq_id = md5(uniqid(time()));
    $this->boundary[1] = 'b1_' . $uniq_id;
    $this->boundary[2] = 'b2_' . $uniq_id;

    $result .= $this->HeaderLine('Date', $this->RFCDate());
    if($this->Sender == '') {
      $result .= $this->HeaderLine('Return-Path', trim($this->From));
    } else {
      $result .= $this->HeaderLine('Return-Path', trim($this->Sender));
    }

    if($this->Mailer != 'mail') {
      if(count($this->to) > 0) {
        $result .= $this->AddrAppend('To', $this->to);
      } elseif (count($this->cc) == 0) {
        $result .= $this->HeaderLine('To', 'undisclosed-recipients:;');
      }
    }

    $from = array();
    $from[0][0] = trim($this->From);
    $from[0][1] = $this->FromName;
    $result .= $this->AddrAppend('From', $from);

    if((($this->Mailer == 'sendmail') || ($this->Mailer == 'mail')) && (count($this->cc) > 0)) {
      $result .= $this->AddrAppend('Cc', $this->cc);
    }

    if((($this->Mailer == 'sendmail') || ($this->Mailer == 'mail')) && (count($this->bcc) > 0)) {
      $result .= $this->AddrAppend('Bcc', $this->bcc);
    }

    if(count($this->ReplyTo) > 0) {
      $result .= $this->AddrAppend('Reply-To', $this->ReplyTo);
    }

    if($this->Mailer != 'mail') {
      $result .= $this->HeaderLine('Subject', $this->EncodeHeader($this->SecureHeader($this->Subject)));
    }

    if($this->MessageID != '') {
      $result .= $this->HeaderLine('Message-ID',$this->MessageID);
    } else {
      $result .= sprintf("Message-ID: <%s@%s>%s", $uniq_id, $this->ServerHostname(), $this->LE);
    }
    $result .= $this->HeaderLine('X-Priority', $this->Priority);
    $result .= $this->HeaderLine('X-Mailer', 'PHPMailer (phpmailer.sourceforge.net) [version ' . $this->Version . ']');

    if($this->ConfirmReadingTo != '') {
      $result .= $this->HeaderLine('Disposition-Notification-To', '<' . trim($this->ConfirmReadingTo) . '>');
    }

    // Add custom headers
    for($index = 0; $index < count($this->CustomHeader); $index++) {
      $result .= $this->HeaderLine(trim($this->CustomHeader[$index][0]), $this->EncodeHeader(trim($this->CustomHeader[$index][1])));
    }
    if (!$this->sign_key_file) {
      $result .= $this->HeaderLine('MIME-Version', '1.0');
      $result .= $this->GetMailMIME();
    }

    return $result;
  }

  function GetMailMIME() {
    $result = '';
    switch($this->message_type) {
      case 'plain':
        $result .= $this->HeaderLine('Content-Transfer-Encoding', $this->Encoding);
        $result .= sprintf("Content-Type: %s; charset=\"%s\"", $this->ContentType, $this->CharSet);
        break;
      case 'attachments':

      case 'alt_attachments':
        if($this->InlineImageExists()){
          $result .= sprintf("Content-Type: %s;%s\ttype=\"text/html\";%s\tboundary=\"%s\"%s", 'multipart/related', $this->LE, $this->LE, $this->boundary[1], $this->LE);
        } else {
          $result .= $this->HeaderLine('Content-Type', 'multipart/mixed;');
          $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
        }
        break;
      case 'alt':
        $result .= $this->HeaderLine('Content-Type', 'multipart/alternative;');
        $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
        break;
    }

    if($this->Mailer != 'mail') {
      $result .= $this->LE.$this->LE;
    }

    return $result;
  }

  function CreateBody() {
    $result = '';
    if ($this->sign_key_file) {
      $result .= $this->GetMailMIME();
    }

    $this->SetWordWrap();

    switch($this->message_type) {
      case 'alt':
        $result .= $this->GetBoundary($this->boundary[1], '', 'text/plain', '');
        $result .= $this->EncodeString($this->AltBody, $this->Encoding);
        $result .= $this->LE.$this->LE;
        $result .= $this->GetBoundary($this->boundary[1], '', 'text/html', '');
        $result .= $this->EncodeString($this->Body, $this->Encoding);
        $result .= $this->LE.$this->LE;
        $result .= $this->EndBoundary($this->boundary[1]);
        break;
      case 'plain':
        $result .= $this->EncodeString($this->Body, $this->Encoding);
        break;
      case 'attachments':
        $result .= $this->GetBoundary($this->boundary[1], '', '', '');
        $result .= $this->EncodeString($this->Body, $this->Encoding);
        $result .= $this->LE;
        $result .= $this->AttachAll();
        break;
      case 'alt_attachments':
        $result .= sprintf("--%s%s", $this->boundary[1], $this->LE);
        $result .= sprintf("Content-Type: %s;%s" . "\tboundary=\"%s\"%s", 'multipart/alternative', $this->LE, $this->boundary[2], $this->LE.$this->LE);
        $result .= $this->GetBoundary($this->boundary[2], '', 'text/plain', '') . $this->LE; // Create text body
        $result .= $this->EncodeString($this->AltBody, $this->Encoding);
        $result .= $this->LE.$this->LE;
        $result .= $this->GetBoundary($this->boundary[2], '', 'text/html', '') . $this->LE; // Create the HTML body
        $result .= $this->EncodeString($this->Body, $this->Encoding);
        $result .= $this->LE.$this->LE;
        $result .= $this->EndBoundary($this->boundary[2]);
        $result .= $this->AttachAll();
        break;
    }

    if($this->IsError()) {
      $result = '';
    } else if ($this->sign_key_file) {
      $file = tempnam("", "mail");
      $fp = fopen($file, "w");
      fwrite($fp, $result);
      fclose($fp);
      $signed = tempnam("", "signed");

      if (@openssl_pkcs7_sign($file, $signed, "file://".$this->sign_cert_file, array("file://".$this->sign_key_file, $this->sign_key_pass), null)) {
        $fp = fopen($signed, "r");
        $result = fread($fp, filesize($this->sign_key_file));
        $result = '';
        while(!feof($fp)){
          $result = $result . fread($fp, 1024);
        }
        fclose($fp);
      } else {
        $this->SetError($this->Lang("signing").openssl_error_string());
        $result = '';
      }

      unlink($file);
      unlink($signed);
    }

    return $result;
  }

  function GetBoundary($boundary, $charSet, $contentType, $encoding) {
    $result = '';
    if($charSet == '') {
      $charSet = $this->CharSet;
    }
    if($contentType == '') {
      $contentType = $this->ContentType;
    }
    if($encoding == '') {
      $encoding = $this->Encoding;
    }
    $result .= $this->TextLine('--' . $boundary);
    $result .= sprintf("Content-Type: %s; charset = \"%s\"", $contentType, $charSet);
    $result .= $this->LE;
    $result .= $this->HeaderLine('Content-Transfer-Encoding', $encoding);
    $result .= $this->LE;

    return $result;
  }

  function EndBoundary($boundary) {
    return $this->LE . '--' . $boundary . '--' . $this->LE;
  }

  function SetMessageType() {
    if(count($this->attachment) < 1 && strlen($this->AltBody) < 1) {
      $this->message_type = 'plain';
    } else {
      if(count($this->attachment) > 0) {
        $this->message_type = 'attachments';
      }
      if(strlen($this->AltBody) > 0 && count($this->attachment) < 1) {
        $this->message_type = 'alt';
      }
      if(strlen($this->AltBody) > 0 && count($this->attachment) > 0) {
        $this->message_type = 'alt_attachments';
      }
    }
  }

  function HeaderLine($name, $value) {
    return $name . ': ' . $value . $this->LE;
  }

  function TextLine($value) {
    return $value . $this->LE;
  }

  /////////////////////////////////////////////////
  // CLASS METHODS, ATTACHMENTS
  /////////////////////////////////////////////////

  function AddAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream') {
    if(!@is_file($path)) {
      $this->SetError($this->Lang('file_access') . $path);
      return false;
    }

    $filename = basename($path);
    if($name == '') {
      $name = $filename;
    }

    $cur = count($this->attachment);
    $this->attachment[$cur][0] = $path;
    $this->attachment[$cur][1] = $filename;
    $this->attachment[$cur][2] = $name;
    $this->attachment[$cur][3] = $encoding;
    $this->attachment[$cur][4] = $type;
    $this->attachment[$cur][5] = false; // isStringAttachment
    $this->attachment[$cur][6] = 'attachment';
    $this->attachment[$cur][7] = 0;

    return true;
  }

  function AttachAll() {

    $mime = array();

    for($i = 0; $i < count($this->attachment); $i++) {

      $bString = $this->attachment[$i][5];
      if ($bString) {
        $string = $this->attachment[$i][0];
      } else {
        $path = $this->attachment[$i][0];
      }

      $filename    = $this->attachment[$i][1];
      $name        = $this->attachment[$i][2];
      $encoding    = $this->attachment[$i][3];
      $type        = $this->attachment[$i][4];
      $disposition = $this->attachment[$i][6];
      $cid         = $this->attachment[$i][7];

      $mime[] = sprintf("--%s%s", $this->boundary[1], $this->LE);
      $mime[] = sprintf("Content-Type: %s; name=\"%s\"%s", $type, $this->EncodeHeader($this->SecureHeader($name)), $this->LE);
      $mime[] = sprintf("Content-Transfer-Encoding: %s%s", $encoding, $this->LE);

      if($disposition == 'inline') {
        $mime[] = sprintf("Content-ID: <%s>%s", $cid, $this->LE);
      }

      $mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", $disposition, $this->EncodeHeader($this->SecureHeader($name)), $this->LE.$this->LE);

      if($bString) {
        $mime[] = $this->EncodeString($string, $encoding);
        if($this->IsError()) {
          return '';
        }
        $mime[] = $this->LE.$this->LE;
      } else {
        $mime[] = $this->EncodeFile($path, $encoding);
        if($this->IsError()) {
          return '';
        }
        $mime[] = $this->LE.$this->LE;
      }
    }

    $mime[] = sprintf("--%s--%s", $this->boundary[1], $this->LE);

    return join('', $mime);
  }

  function EncodeFile ($path, $encoding = 'base64') {
    if(!@$fd = fopen($path, 'rb')) {
      $this->SetError($this->Lang('file_open') . $path);
      return '';
    }
    $magic_quotes = get_magic_quotes_runtime();
    set_magic_quotes_runtime(0);
    $file_buffer = fread($fd, filesize($path));
    $file_buffer = $this->EncodeString($file_buffer, $encoding);
    fclose($fd);
    set_magic_quotes_runtime($magic_quotes);

    return $file_buffer;
  }

  function EncodeString ($str, $encoding = 'base64') {
    $encoded = '';
    switch(strtolower($encoding)) {
      case 'base64':

        $encoded = chunk_split(base64_encode($str), 76, $this->LE);
        break;
      case '7bit':
      case '8bit':
        $encoded = $this->FixEOL($str);
        if (substr($encoded, -(strlen($this->LE))) != $this->LE)
          $encoded .= $this->LE;
        break;
      case 'binary':
        $encoded = $str;
        break;
      case 'quoted-printable':
        $encoded = $this->EncodeQP($str);
        break;
      default:
        $this->SetError($this->Lang('encoding') . $encoding);
        break;
    }
    return $encoded;
  }

  function EncodeHeader ($str, $position = 'text') {
    $x = 0;

    switch (strtolower($position)) {
      case 'phrase':
        if (!preg_match('/[\200-\377]/', $str)) {

          $encoded = addcslashes($str, "\0..\37\177\\\"");
          if (($str == $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str)) {
            return ($encoded);
          } else {
            return ("\"$encoded\"");
          }
        }
        $x = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
        break;
      case 'comment':
        $x = preg_match_all('/[()"]/', $str, $matches);

      case 'text':
      default:
        $x += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
        break;
    }

    if ($x == 0) {
      return ($str);
    }

    $maxlen = 75 - 7 - strlen($this->CharSet);

    if (strlen($str)/3 < $x) {
      $encoding = 'B';
      if (function_exists('mb_strlen') && $this->HasMultiBytes($str)) {
     // Use a custom function which correctly encodes and wraps long
     // multibyte strings without breaking lines within a character
        $encoded = $this->Base64EncodeWrapMB($str);
      } else {
        $encoded = base64_encode($str);
        $maxlen -= $maxlen % 4;
        $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
      }
    } else {
      $encoding = 'Q';
      $encoded = $this->EncodeQ($str, $position);
      $encoded = $this->WrapText($encoded, $maxlen, true);
      $encoded = str_replace('='.$this->LE, "\n", trim($encoded));
    }

    $encoded = preg_replace('/^(.*)$/m', " =?".$this->CharSet."?$encoding?\\1?=", $encoded);
    $encoded = trim(str_replace("\n", $this->LE, $encoded));

    return $encoded;
  }

  function HasMultiBytes($str) {
    if (function_exists('mb_strlen')) {
      return (strlen($str) > mb_strlen($str, $this->CharSet));
    } else { // Assume no multibytes (we can't handle without mbstring functions anyway)
      return False;
    }
  }

  function Base64EncodeWrapMB($str) {
    $start = "=?".$this->CharSet."?B?";
    $end = "?=";
    $encoded = "";

    $mb_length = mb_strlen($str, $this->CharSet);
    // Each line must have length <= 75, including $start and $end
    $length = 75 - strlen($start) - strlen($end);
    // Average multi-byte ratio
    $ratio = $mb_length / strlen($str);
    // Base64 has a 4:3 ratio
    $offset = $avgLength = floor($length * $ratio * .75);

    for ($i = 0; $i < $mb_length; $i += $offset) {
      $lookBack = 0;

      do {
        $offset = $avgLength - $lookBack;
        $chunk = mb_substr($str, $i, $offset, $this->CharSet);
        $chunk = base64_encode($chunk);
        $lookBack++;
      }
      while (strlen($chunk) > $length);

      $encoded .= $chunk . $this->LE;
    }

    // Chomp the last linefeed
    $encoded = substr($encoded, 0, -strlen($this->LE));
    return $encoded;
  }

  function EncodeQP( $input = '', $line_max = 76, $space_conv = false ) {
    $hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
    $lines = preg_split('/(?:\r\n|\r|\n)/', $input);
    $eol = "\r\n";
    $escape = '=';
    $output = '';
    while( list(, $line) = each($lines) ) {
      $linlen = strlen($line);
      $newline = '';
      for($i = 0; $i < $linlen; $i++) {
        $c = substr( $line, $i, 1 );
        $dec = ord( $c );
        if ( ( $i == 0 ) && ( $dec == 46 ) ) { // convert first point in the line into =2E
          $c = '=2E';
        }
        if ( $dec == 32 ) {
          if ( $i == ( $linlen - 1 ) ) { // convert space at eol only
            $c = '=20';
          } else if ( $space_conv ) {
            $c = '=20';
          }
        } elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
          $h2 = floor($dec/16);
          $h1 = floor($dec%16);
          $c = $escape.$hex[$h2].$hex[$h1];
        }
        if ( (strlen($newline) + strlen($c)) >= $line_max ) { // CRLF is not counted
          $output .= $newline.$escape.$eol; //  soft line break; " =\r\n" is okay
          $newline = '';
          // check if newline first character will be point or not
          if ( $dec == 46 ) {
            $c = '=2E';
          }
        }
        $newline .= $c;
      } // end of for
      $output .= $newline.$eol;
    } // end of while
    return $output;
  }

  function EncodeQ ($str, $position = 'text') {

    $encoded = preg_replace("[\r\n]", '', $str);

    switch (strtolower($position)) {
      case 'phrase':
        $encoded = preg_replace("/([^A-Za-z0-9!*+\/ -])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
        break;
      case 'comment':
        $encoded = preg_replace("/([\(\)\"])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
      case 'text':
      default:

        $encoded = preg_replace('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/e',
              "'='.sprintf('%02X', ord('\\1'))", $encoded);
        break;
    }

    $encoded = str_replace(' ', '_', $encoded);

    return $encoded;
  }

  function AddStringAttachment($string, $filename, $encoding = 'base64', $type = 'application/octet-stream') {

    $cur = count($this->attachment);
    $this->attachment[$cur][0] = $string;
    $this->attachment[$cur][1] = $filename;
    $this->attachment[$cur][2] = $filename;
    $this->attachment[$cur][3] = $encoding;
    $this->attachment[$cur][4] = $type;
    $this->attachment[$cur][5] = true; // isString
    $this->attachment[$cur][6] = 'attachment';
    $this->attachment[$cur][7] = 0;
  }

  function AddEmbeddedImage($path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream') {

    if(!@is_file($path)) {
      $this->SetError($this->Lang('file_access') . $path);
      return false;
    }

    $filename = basename($path);
    if($name == '') {
      $name = $filename;
    }

    $cur = count($this->attachment);
    $this->attachment[$cur][0] = $path;
    $this->attachment[$cur][1] = $filename;
    $this->attachment[$cur][2] = $name;
    $this->attachment[$cur][3] = $encoding;
    $this->attachment[$cur][4] = $type;
    $this->attachment[$cur][5] = false;
    $this->attachment[$cur][6] = 'inline';
    $this->attachment[$cur][7] = $cid;

    return true;
  }

  function InlineImageExists() {
    $result = false;
    for($i = 0; $i < count($this->attachment); $i++) {
      if($this->attachment[$i][6] == 'inline') {
        $result = true;
        break;
      }
    }

    return $result;
  }

  /////////////////////////////////////////////////
  // CLASS METHODS, MESSAGE RESET
  /////////////////////////////////////////////////

  function ClearAddresses() {
    $this->to = array();
  }

  function ClearCCs() {
    $this->cc = array();
  }

  function ClearBCCs() {
    $this->bcc = array();
  }

  function ClearReplyTos() {
    $this->ReplyTo = array();
  }

  function ClearAllRecipients() {
    $this->to = array();
    $this->cc = array();
    $this->bcc = array();
  }

  function ClearAttachments() {
    $this->attachment = array();
  }

  function ClearCustomHeaders() {
    $this->CustomHeader = array();
  }

  /////////////////////////////////////////////////
  // CLASS METHODS, MISCELLANEOUS
  /////////////////////////////////////////////////

  function SetError($msg) {
    $this->error_count++;
    $this->ErrorInfo = $msg;
  }

  function RFCDate() {
    $tz = date('Z');
    $tzs = ($tz < 0) ? '-' : '+';
    $tz = abs($tz);
    $tz = (int)($tz/3600)*100 + ($tz%3600)/60;
    $result = sprintf("%s %s%04d", date('D, j M Y H:i:s'), $tzs, $tz);

    return $result;
  }

  function ServerVar($varName) {
    global $HTTP_SERVER_VARS;
    global $HTTP_ENV_VARS;

    if(!isset($_SERVER)) {
      $_SERVER = $HTTP_SERVER_VARS;
      if(!isset($_SERVER['REMOTE_ADDR'])) {
        $_SERVER = $HTTP_ENV_VARS; // must be Apache
      }
    }

    if(isset($_SERVER[$varName])) {
      return $_SERVER[$varName];
    } else {
      return '';
    }
  }

  function ServerHostname() {
    if ($this->Hostname != '') {
      $result = $this->Hostname;
    } elseif ($this->ServerVar('SERVER_NAME') != '') {
      $result = $this->ServerVar('SERVER_NAME');
    } else {
      $result = 'localhost.localdomain';
    }

    return $result;
  }

  function Lang($key) {
    if(count($this->language) < 1) {
      $this->SetLanguage('en'); // set the default language
    }

    if(isset($this->language[$key])) {
      return $this->language[$key];
    } else {
      return 'Language string failed to load: ' . $key;
    }
  }

  function IsError() {
    return ($this->error_count > 0);
  }

  function FixEOL($str) {
    $str = str_replace("\r\n", "\n", $str);
    $str = str_replace("\r", "\n", $str);
    $str = str_replace("\n", $this->LE, $str);
    return $str;
  }

  function AddCustomHeader($custom_header) {
    $this->CustomHeader[] = explode(':', $custom_header, 2);
  }

  function MsgHTML($message,$basedir='') {
    preg_match_all("/(src|background)=\"(.*)\"/Ui", $message, $images);
    if(isset($images[2])) {
      foreach($images[2] as $i => $url) {
        // do not change urls for absolute images (thanks to corvuscorax)
        if (!preg_match('/^[A-z][A-z]*:\/\//',$url)) {
          $filename = basename($url);
          $directory = dirname($url);
          ($directory == '.')?$directory='':'';
          $cid = 'cid:' . md5($filename);
          $fileParts = explode("\.", $filename);
          $ext = $fileParts[1];
          $mimeType = $this->_mime_types($ext);
          if ( strlen($basedir) > 1 && substr($basedir,-1) != '/') { $basedir .= '/'; }
          if ( strlen($directory) > 1 && substr($directory,-1) != '/') { $directory .= '/'; }
          if ( $this->AddEmbeddedImage($basedir.$directory.$filename, md5($filename), $filename, 'base64',$mimeType) ) {
            $message = preg_replace("/".$images[1][$i]."=\"".preg_quote($url, '/')."\"/Ui", $images[1][$i]."=\"".$cid."\"", $message);
          }
        }
      }
    }
    $this->IsHTML(true);
    $this->Body = $message;
    $textMsg = trim(strip_tags(preg_replace('/<(head|title|style|script)[^>]*>.*?<\/\\1>/s','',$message)));
    if ( !empty($textMsg) && empty($this->AltBody) ) {
      $this->AltBody = html_entity_decode($textMsg);
    }
    if ( empty($this->AltBody) ) {
      $this->AltBody = 'To view this email message, open the email in with HTML compatibility!' . "\n\n";
    }
  }

  function _mime_types($ext = '') {
    $mimes = array(
      'ai'    =>  'application/postscript',
      'aif'   =>  'audio/x-aiff',
      'aifc'  =>  'audio/x-aiff',
      'aiff'  =>  'audio/x-aiff',
      'avi'   =>  'video/x-msvideo',
      'bin'   =>  'application/macbinary',
      'bmp'   =>  'image/bmp',
      'class' =>  'application/octet-stream',
      'cpt'   =>  'application/mac-compactpro',
      'css'   =>  'text/css',
      'dcr'   =>  'application/x-director',
      'dir'   =>  'application/x-director',
      'dll'   =>  'application/octet-stream',
      'dms'   =>  'application/octet-stream',
      'doc'   =>  'application/msword',
      'dvi'   =>  'application/x-dvi',
      'dxr'   =>  'application/x-director',
      'eml'   =>  'message/rfc822',
      'eps'   =>  'application/postscript',
      'exe'   =>  'application/octet-stream',
      'gif'   =>  'image/gif',
      'gtar'  =>  'application/x-gtar',
      'htm'   =>  'text/html',
      'html'  =>  'text/html',
      'jpe'   =>  'image/jpeg',
      'jpeg'  =>  'image/jpeg',
      'jpg'   =>  'image/jpeg',
      'hqx'   =>  'application/mac-binhex40',
      'js'    =>  'application/x-javascript',
      'lha'   =>  'application/octet-stream',
      'log'   =>  'text/plain',
      'lzh'   =>  'application/octet-stream',
      'mid'   =>  'audio/midi',
      'midi'  =>  'audio/midi',
      'mif'   =>  'application/vnd.mif',
      'mov'   =>  'video/quicktime',
      'movie' =>  'video/x-sgi-movie',
      'mp2'   =>  'audio/mpeg',
      'mp3'   =>  'audio/mpeg',
      'mpe'   =>  'video/mpeg',
      'mpeg'  =>  'video/mpeg',
      'mpg'   =>  'video/mpeg',
      'mpga'  =>  'audio/mpeg',
      'oda'   =>  'application/oda',
      'pdf'   =>  'application/pdf',
      'php'   =>  'application/x-httpd-php',
      'php3'  =>  'application/x-httpd-php',
      'php4'  =>  'application/x-httpd-php',
      'phps'  =>  'application/x-httpd-php-source',
      'phtml' =>  'application/x-httpd-php',
      'png'   =>  'image/png',
      'ppt'   =>  'application/vnd.ms-powerpoint',
      'ps'    =>  'application/postscript',
      'psd'   =>  'application/octet-stream',
      'qt'    =>  'video/quicktime',
      'ra'    =>  'audio/x-realaudio',
      'ram'   =>  'audio/x-pn-realaudio',
      'rm'    =>  'audio/x-pn-realaudio',
      'rpm'   =>  'audio/x-pn-realaudio-plugin',
      'rtf'   =>  'text/rtf',
      'rtx'   =>  'text/richtext',
      'rv'    =>  'video/vnd.rn-realvideo',
      'sea'   =>  'application/octet-stream',
      'shtml' =>  'text/html',
      'sit'   =>  'application/x-stuffit',
      'so'    =>  'application/octet-stream',
      'smi'   =>  'application/smil',
      'smil'  =>  'application/smil',
      'swf'   =>  'application/x-shockwave-flash',
      'tar'   =>  'application/x-tar',
      'text'  =>  'text/plain',
      'txt'   =>  'text/plain',
      'tgz'   =>  'application/x-tar',
      'tif'   =>  'image/tiff',
      'tiff'  =>  'image/tiff',
      'wav'   =>  'audio/x-wav',
      'wbxml' =>  'application/vnd.wap.wbxml',
      'wmlc'  =>  'application/vnd.wap.wmlc',
      'word'  =>  'application/msword',
      'xht'   =>  'application/xhtml+xml',
      'xhtml' =>  'application/xhtml+xml',
      'xl'    =>  'application/excel',
      'xls'   =>  'application/vnd.ms-excel',
      'xml'   =>  'text/xml',
      'xsl'   =>  'text/xml',
      'zip'   =>  'application/zip'
    );
    return ( ! isset($mimes[strtolower($ext)])) ? 'application/octet-stream' : $mimes[strtolower($ext)];
  }

  function set ( $name, $value = '' ) {
    if ( isset($this->$name) ) {
      $this->$name = $value;
    } else {
      $this->SetError('Cannot set or reset variable ' . $name);
      return false;
    }
  }

  function getFile($filename) {
    $return = '';
    if ($fp = fopen($filename, 'rb')) {
      while (!feof($fp)) {
        $return .= fread($fp, 1024);
      }
      fclose($fp);
      return $return;
    } else {
      return false;
    }
  }

  function SecureHeader($str) {
    $str = trim($str);
    $str = str_replace("\r", "", $str);
    $str = str_replace("\n", "", $str);
    return $str;
  }

  function Sign($cert_filename, $key_filename, $key_pass) {
    $this->sign_cert_file = $cert_filename;
    $this->sign_key_file = $key_filename;
    $this->sign_key_pass = $key_pass;
  }

}


class POP3
{

  var $POP3_PORT = 110;

  var $POP3_TIMEOUT = 30;

  var $CRLF = "\r\n";

  var $do_debug = 2;

  var $host;

  var $port;

  var $tval;

  var $username;

  var $password;

  var $pop_conn;
  var $connected;
  var $error;     //  Error log array


  function POP3 ()
    {
      $this->pop_conn = 0;
      $this->connected = false;
      $this->error = null;
    }

  function Authorise ($host, $port = false, $tval = false, $username, $password, $debug_level = 0)
  {
    $this->host = $host;

    //  If no port value is passed, retrieve it
    if ($port == false)
    {
      $this->port = $this->POP3_PORT;
    }
    else
    {
      $this->port = $port;
    }

    //  If no port value is passed, retrieve it
    if ($tval == false)
    {
      $this->tval = $this->POP3_TIMEOUT;
    }
    else
    {
      $this->tval = $tval;
    }

    $this->do_debug = $debug_level;
    $this->username = $username;
    $this->password = $password;

    //  Refresh the error log
      $this->error = null;

      //  Connect
    $result = $this->Connect($this->host, $this->port, $this->tval);

    if ($result)
    {
      $login_result = $this->Login($this->username, $this->password);

      if ($login_result)
      {
        $this->Disconnect();

        return true;
      }

    }

    //  We need to disconnect regardless if the login succeeded
    $this->Disconnect();

    return false;
  }

  function Connect ($host, $port = false, $tval = 30)
    {
    //  Are we already connected?
    if ($this->connected)
    {
      return true;
    }


    set_error_handler(array(&$this, 'catchWarning'));

    //  Connect to the POP3 server
    $this->pop_conn = fsockopen($host,    //  POP3 Host
                  $port,    //  Port #
                  $errno,   //  Error Number
                  $errstr,  //  Error Message
                  $tval);   //  Timeout (seconds)

    //  Restore the error handler
    restore_error_handler();

    //  Does the Error Log now contain anything?
    if ($this->error && $this->do_debug >= 1)
    {
        $this->displayErrors();
    }

    //  Did we connect?
      if ($this->pop_conn == false)
      {
        //  It would appear not...
        $this->error = array(
          'error' => "Failed to connect to server $host on port $port",
          'errno' => $errno,
          'errstr' => $errstr
        );

        if ($this->do_debug >= 1)
        {
          $this->displayErrors();
        }

        return false;
      }

      //  Increase the stream time-out

      //  Check for PHP 4.3.0 or later
      if (version_compare(phpversion(), '4.3.0', 'ge'))
      {
        stream_set_timeout($this->pop_conn, $tval, 0);
      }
      else
      {
        //  Does not work on Windows
        if (substr(PHP_OS, 0, 3) !== 'WIN')
        {
          socket_set_timeout($this->pop_conn, $tval, 0);
        }
      }

    //  Get the POP3 server response
      $pop3_response = $this->getResponse();

      //  Check for the +OK
      if ($this->checkResponse($pop3_response))
      {
      //  The connection is established and the POP3 server is talking
      $this->connected = true;
        return true;
      }

    }

    function Login ($username = '', $password = '')
    {
      if ($this->connected == false)
      {
        $this->error = 'Not connected to POP3 server';

        if ($this->do_debug >= 1)
        {
          $this->displayErrors();
        }
      }

      if (empty($username))
      {
        $username = $this->username;
      }

      if (empty($password))
      {
        $password = $this->password;
      }

    $pop_username = "USER $username" . $this->CRLF;
    $pop_password = "PASS $password" . $this->CRLF;

      //  Send the Username
      $this->sendString($pop_username);
      $pop3_response = $this->getResponse();

      if ($this->checkResponse($pop3_response))
      {
        //  Send the Password
        $this->sendString($pop_password);
        $pop3_response = $this->getResponse();

        if ($this->checkResponse($pop3_response))
        {
          return true;
        }
        else
        {
          return false;
        }
      }
      else
      {
        return false;
      }
    }

    function Disconnect ()
    {
      $this->sendString('QUIT');

      fclose($this->pop_conn);
    }


    function getResponse ($size = 128)
    {
      $pop3_response = fgets($this->pop_conn, $size);

      return $pop3_response;
    }

    function sendString ($string)
    {
      $bytes_sent = fwrite($this->pop_conn, $string, strlen($string));

      return $bytes_sent;

    }

    function checkResponse ($string)
    {
      if (substr($string, 0, 3) !== '+OK')
      {
        $this->error = array(
          'error' => "Server reported an error: $string",
          'errno' => 0,
          'errstr' => ''
        );

        if ($this->do_debug >= 1)
        {
          $this->displayErrors();
        }

        return false;
      }
      else
      {
        return true;
      }

    }

    function displayErrors ()
    {
      echo '<pre>';

      foreach ($this->error as $single_error)
    {
        print_r($single_error);
    }

      echo '</pre>';
    }

  function catchWarning ($errno, $errstr, $errfile, $errline)
  {
    $this->error[] = array(
      'error' => "Connecting to the POP3 server raised a PHP warning: ",
      'errno' => $errno,
      'errstr' => $errstr
    );
  }

  //  End of class
}


class SMTP
{

  var $SMTP_PORT = 25;

  var $CRLF = "\r\n";

  var $do_debug;       # the level of debug to perform

  var $do_verp = false;

  var $smtp_conn;      # the socket to the server
  var $error;          # error if any on the last call
  var $helo_rply;      # the reply the server sent to us for HELO


  function SMTP() {
    $this->smtp_conn = 0;
    $this->error = null;
    $this->helo_rply = null;

    $this->do_debug = 0;
  }


  function Connect($host,$port=0,$tval=30) {
    # set the error val to null so there is no confusion
    $this->error = null;

    # make sure we are __not__ connected
    if($this->connected()) {
      # ok we are connected! what should we do?
      # for now we will just give an error saying we
      # are already connected
      $this->error = array("error" => "Already connected to a server");
      return false;
    }

    if(empty($port)) {
      $port = $this->SMTP_PORT;
    }

    #connect to the smtp server
    $this->smtp_conn = fsockopen($host,    # the host of the server
                                 $port,    # the port to use
                                 $errno,   # error number if any
                                 $errstr,  # error message if any
                                 $tval);   # give up after ? secs
    # verify we connected properly
    if(empty($this->smtp_conn)) {
      $this->error = array("error" => "Failed to connect to server",
                           "errno" => $errno,
                           "errstr" => $errstr);
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": $errstr ($errno)" . $this->CRLF;
      }
      return false;
    }

    # sometimes the SMTP server takes a little longer to respond
    # so we will give it a longer timeout for the first read
    // Windows still does not have support for this timeout function
    if(substr(PHP_OS, 0, 3) != "WIN")
     socket_set_timeout($this->smtp_conn, $tval, 0);

    # get any announcement stuff
    $announce = $this->get_lines();

    # set the timeout  of any socket functions at 1/10 of a second
    //if(function_exists("socket_set_timeout"))
    //   socket_set_timeout($this->smtp_conn, 0, 100000);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $announce;
    }

    return true;
  }

  function Authenticate($username, $password) {
    // Start authentication
    fputs($this->smtp_conn,"AUTH LOGIN" . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($code != 334) {
      $this->error =
        array("error" => "AUTH not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }

    // Send encoded username
    fputs($this->smtp_conn, base64_encode($username) . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($code != 334) {
      $this->error =
        array("error" => "Username not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }

    // Send encoded password
    fputs($this->smtp_conn, base64_encode($password) . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($code != 235) {
      $this->error =
        array("error" => "Password not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }

    return true;
  }

  function Connected() {
    if(!empty($this->smtp_conn)) {
      $sock_status = socket_get_status($this->smtp_conn);
      if($sock_status["eof"]) {
        # hmm this is an odd situation... the socket is
        # valid but we are not connected anymore
        if($this->do_debug >= 1) {
            echo "SMTP -> NOTICE:" . $this->CRLF .
                 "EOF caught while checking if connected";
        }
        $this->Close();
        return false;
      }
      return true; # everything looks good
    }
    return false;
  }

  function Close() {
    $this->error = null; # so there is no confusion
    $this->helo_rply = null;
    if(!empty($this->smtp_conn)) {
      # close the connection and cleanup
      fclose($this->smtp_conn);
      $this->smtp_conn = 0;
    }
  }


  function Data($msg_data) {
    $this->error = null; # so no confusion is caused

    if(!$this->connected()) {
      $this->error = array(
              "error" => "Called Data() without being connected");
      return false;
    }

    fputs($this->smtp_conn,"DATA" . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 354) {
      $this->error =
        array("error" => "DATA command not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }

    # the server is ready to accept data!
    # according to rfc 821 we should not send more than 1000
    # including the CRLF
    # characters on a single line so we will break the data up
    # into lines by \r and/or \n then if needed we will break
    # each of those into smaller lines to fit within the limit.
    # in addition we will be looking for lines that start with
    # a period '.' and append and additional period '.' to that
    # line. NOTE: this does not count towards are limit.

    # normalize the line breaks so we know the explode works
    $msg_data = str_replace("\r\n","\n",$msg_data);
    $msg_data = str_replace("\r","\n",$msg_data);
    $lines = explode("\n",$msg_data);

    # we need to find a good way to determine is headers are
    # in the msg_data or if it is a straight msg body
    # currently I am assuming rfc 822 definitions of msg headers
    # and if the first field of the first line (':' sperated)
    # does not contain a space then it _should_ be a header
    # and we can process all lines before a blank "" line as
    # headers.
    $field = substr($lines[0],0,strpos($lines[0],":"));
    $in_headers = false;
    if(!empty($field) && !strstr($field," ")) {
      $in_headers = true;
    }

    $max_line_length = 998; # used below; set here for ease in change

    while(list(,$line) = @each($lines)) {
      $lines_out = null;
      if($line == "" && $in_headers) {
        $in_headers = false;
      }
      # ok we need to break this line up into several
      # smaller lines
      while(strlen($line) > $max_line_length) {
        $pos = strrpos(substr($line,0,$max_line_length)," ");

        # Patch to fix DOS attack
        if(!$pos) {
          $pos = $max_line_length - 1;
        }

        $lines_out[] = substr($line,0,$pos);
        $line = substr($line,$pos + 1);
        # if we are processing headers we need to
        # add a LWSP-char to the front of the new line
        # rfc 822 on long msg headers
        if($in_headers) {
          $line = "\t" . $line;
        }
      }
      $lines_out[] = $line;

      # now send the lines to the server
      while(list(,$line_out) = @each($lines_out)) {
        if(strlen($line_out) > 0)
        {
          if(substr($line_out, 0, 1) == ".") {
            $line_out = "." . $line_out;
          }
        }
        fputs($this->smtp_conn,$line_out . $this->CRLF);
      }
    }

    # ok all the message data has been sent so lets get this
    # over with aleady
    fputs($this->smtp_conn, $this->CRLF . "." . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 250) {
      $this->error =
        array("error" => "DATA not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }
    return true;
  }

  function Expand($name) {
    $this->error = null; # so no confusion is caused

    if(!$this->connected()) {
      $this->error = array(
            "error" => "Called Expand() without being connected");
      return false;
    }

    fputs($this->smtp_conn,"EXPN " . $name . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 250) {
      $this->error =
        array("error" => "EXPN not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }

    # parse the reply and place in our array to return to user
    $entries = explode($this->CRLF,$rply);
    while(list(,$l) = @each($entries)) {
      $list[] = substr($l,4);
    }

    return $list;
  }

  function Hello($host="") {
    $this->error = null; # so no confusion is caused

    if(!$this->connected()) {
      $this->error = array(
            "error" => "Called Hello() without being connected");
      return false;
    }

    # if a hostname for the HELO was not specified determine
    # a suitable one to send
    if(empty($host)) {
      # we need to determine some sort of appopiate default
      # to send to the server
      $host = "localhost";
    }

    // Send extended hello first (RFC 2821)
    if(!$this->SendHello("EHLO", $host))
    {
      if(!$this->SendHello("HELO", $host))
          return false;
    }

    return true;
  }

  function SendHello($hello, $host) {
    fputs($this->smtp_conn, $hello . " " . $host . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER: " . $this->CRLF . $rply;
    }

    if($code != 250) {
      $this->error =
        array("error" => $hello . " not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }

    $this->helo_rply = $rply;

    return true;
  }

  function Help($keyword="") {
    $this->error = null; # to avoid confusion

    if(!$this->connected()) {
      $this->error = array(
              "error" => "Called Help() without being connected");
      return false;
    }

    $extra = "";
    if(!empty($keyword)) {
      $extra = " " . $keyword;
    }

    fputs($this->smtp_conn,"HELP" . $extra . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 211 && $code != 214) {
      $this->error =
        array("error" => "HELP not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }

    return $rply;
  }

  function Mail($from) {
    $this->error = null; # so no confusion is caused

    if(!$this->connected()) {
      $this->error = array(
              "error" => "Called Mail() without being connected");
      return false;
    }

    $useVerp = ($this->do_verp ? "XVERP" : "");
    fputs($this->smtp_conn,"MAIL FROM:<" . $from . ">" . $useVerp . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 250) {
      $this->error =
        array("error" => "MAIL not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }
    return true;
  }

  function Noop() {
    $this->error = null; # so no confusion is caused

    if(!$this->connected()) {
      $this->error = array(
              "error" => "Called Noop() without being connected");
      return false;
    }

    fputs($this->smtp_conn,"NOOP" . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 250) {
      $this->error =
        array("error" => "NOOP not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }
    return true;
  }

  function Quit($close_on_error=true) {
    $this->error = null; # so there is no confusion

    if(!$this->connected()) {
      $this->error = array(
              "error" => "Called Quit() without being connected");
      return false;
    }

    # send the quit command to the server
    fputs($this->smtp_conn,"quit" . $this->CRLF);

    # get any good-bye messages
    $byemsg = $this->get_lines();

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $byemsg;
    }

    $rval = true;
    $e = null;

    $code = substr($byemsg,0,3);
    if($code != 221) {
      # use e as a tmp var cause Close will overwrite $this->error
      $e = array("error" => "SMTP server rejected quit command",
                 "smtp_code" => $code,
                 "smtp_rply" => substr($byemsg,4));
      $rval = false;
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $e["error"] . ": " .
                 $byemsg . $this->CRLF;
      }
    }

    if(empty($e) || $close_on_error) {
      $this->Close();
    }

    return $rval;
  }

  function Recipient($to) {
    $this->error = null; # so no confusion is caused

    if(!$this->connected()) {
      $this->error = array(
              "error" => "Called Recipient() without being connected");
      return false;
    }

    fputs($this->smtp_conn,"RCPT TO:<" . $to . ">" . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 250 && $code != 251) {
      $this->error =
        array("error" => "RCPT not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }
    return true;
  }

  function Reset() {
    $this->error = null; # so no confusion is caused

    if(!$this->connected()) {
      $this->error = array(
              "error" => "Called Reset() without being connected");
      return false;
    }

    fputs($this->smtp_conn,"RSET" . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 250) {
      $this->error =
        array("error" => "RSET failed",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }

    return true;
  }

  function Send($from) {
    $this->error = null; # so no confusion is caused

    if(!$this->connected()) {
      $this->error = array(
              "error" => "Called Send() without being connected");
      return false;
    }

    fputs($this->smtp_conn,"SEND FROM:" . $from . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 250) {
      $this->error =
        array("error" => "SEND not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }
    return true;
  }

  function SendAndMail($from) {
    $this->error = null; # so no confusion is caused

    if(!$this->connected()) {
      $this->error = array(
          "error" => "Called SendAndMail() without being connected");
      return false;
    }

    fputs($this->smtp_conn,"SAML FROM:" . $from . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 250) {
      $this->error =
        array("error" => "SAML not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }
    return true;
  }

  function SendOrMail($from) {
    $this->error = null; # so no confusion is caused

    if(!$this->connected()) {
      $this->error = array(
          "error" => "Called SendOrMail() without being connected");
      return false;
    }

    fputs($this->smtp_conn,"SOML FROM:" . $from . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 250) {
      $this->error =
        array("error" => "SOML not accepted from server",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }
    return true;
  }

  function Turn() {
    $this->error = array("error" => "This method, TURN, of the SMTP ".
                                    "is not implemented");
    if($this->do_debug >= 1) {
      echo "SMTP -> NOTICE: " . $this->error["error"] . $this->CRLF;
    }
    return false;
  }

  function Verify($name) {
    $this->error = null; # so no confusion is caused

    if(!$this->connected()) {
      $this->error = array(
              "error" => "Called Verify() without being connected");
      return false;
    }

    fputs($this->smtp_conn,"VRFY " . $name . $this->CRLF);

    $rply = $this->get_lines();
    $code = substr($rply,0,3);

    if($this->do_debug >= 2) {
      echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
    }

    if($code != 250 && $code != 251) {
      $this->error =
        array("error" => "VRFY failed on name '$name'",
              "smtp_code" => $code,
              "smtp_msg" => substr($rply,4));
      if($this->do_debug >= 1) {
        echo "SMTP -> ERROR: " . $this->error["error"] .
                 ": " . $rply . $this->CRLF;
      }
      return false;
    }
    return $rply;
  }


  function get_lines() {
    $data = "";
    while($str = @fgets($this->smtp_conn,515)) {
      if($this->do_debug >= 4) {
        echo "SMTP -> get_lines(): \$data was \"$data\"" .
                 $this->CRLF;
        echo "SMTP -> get_lines(): \$str is \"$str\"" .
                 $this->CRLF;
      }
      $data .= $str;
      if($this->do_debug >= 4) {
        echo "SMTP -> get_lines(): \$data is \"$data\"" . $this->CRLF;
      }
      # if the 4th character is a space then we are done reading
      # so just break the loop
      if(substr($str,3,1) == " ") { break; }
    }
    return $data;
  }

}


?>