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

define('CAPTCHA_COOKIE', 'tradexcaptcha');
define('CAPTCHA_EXPIRES', 300);
define('CAPTCHA_FONT', 'captcha.ttf');
define('CAPTCHA_FONT_SIZE', 30);
define('CAPTCHA_PADDING_TOP', 15);
define('CAPTCHA_PADDING_LEFT', 10);
define('CAPTCHA_CHAR_OFFSET', -4);


class Captcha
{

    var $allowed_chars = array('A', 'B', 'C', 'D', 'E', 'F', 'H', 'J', 'K', 'M', 'N', 'P', 'Q', 'R', 'T', 'U', 'V', 'W', 'X', 'Y', '3', '4', '6', '7', '8', '9');

    var $foreground_color = array(0x00, 0x00, 0x55);

    var $background_color = array(0xFF, 0xFF, 0xFF);

    function Captcha()
    {

    }

    function GenerateAndDisplay()
    {
        global $C;

        // Initial setup
        $string = $this->GenerateCode();
        $font_file = DIR_ASSETS . '/' . CAPTCHA_FONT;
        $box = imagettfbboxextended(CAPTCHA_FONT_SIZE, 0, $font_file, $string);
        $width = $box['width'] + CAPTCHA_PADDING_LEFT * 2;
        $height = $box['height'] + CAPTCHA_PADDING_TOP * 2;


        // Setup the image
        $image = imagecreatetruecolor($width, $height);
        $foreground = imagecolorallocate($image, $this->foreground_color[0], $this->foreground_color[1], $this->foreground_color[2]);
        $background = imagecolorallocate($image, $this->background_color[0], $this->background_color[1], $this->background_color[2]);
        imagealphablending($image, true);
        imagefill($image, 0, 0, $background);


        // Draw characters
        $offset = 0;
        for( $i = 0; $i < strlen($string); $i++ )
        {
            $bb = imagettfbboxextended(CAPTCHA_FONT_SIZE, 0, $font_file, $string[$i]);
            imagettftext($image, CAPTCHA_FONT_SIZE, 0, $box['x'] + CAPTCHA_PADDING_LEFT + $offset, $box['y'] + CAPTCHA_PADDING_TOP + rand(-5,5), $foreground, $font_file, $string[$i]);
            $offset += $bb['width'] + CAPTCHA_CHAR_OFFSET;
        }


        // Warp the text
        $image = $this->Warp($image, $width, $height);


        // Set CAPTCHA cookie
        $session = sha1(uniqid(rand(), true));
        setcookie(CAPTCHA_COOKIE, $session, time() + CAPTCHA_EXPIRES, $C['cookie_path'], $C['cookie_domain']);

        require_once 'textdb.php';
        $db = new CaptchasDB();

        $db->Add(array('session' => $session,
                       'code' => $string,
                       'timestamp' => time()));



        // Output the image
        if( function_exists('imagepng') )
        {
            header('Content-type: image/png');
            imagepng($image);
        }
        else
        {
            header('Content-type: image/jpeg');
            imagejpeg($image, null, 95);
        }
    }

    function Verify()
    {
        global $C;

        require_once 'textdb.php';
        $db = new CaptchasDB();
        $db->DeleteExpired();
        $captcha = $db->Retrieve($_COOKIE[CAPTCHA_COOKIE]);

        require_once 'validator.php';
        $v =& Validator::Get();
        $v->Register(!empty($captcha) && strtoupper($captcha['code']) == strtoupper($_REQUEST['captcha']), VT_IS_TRUE, 'The verification code you entered did not match the characters in the image');

        if( !empty($captcha) )
        {
            $db->Delete($captcha['session']);
            setcookie(CAPTCHA_COOKIE, null, time() - CAPTCHA_EXPIRES, $C['cookie_path'], $C['cookie_domain']);
        }
    }

    function GenerateCode()
    {
        global $C;

        if( $C['flag_captcha_words'] )
        {
            $words = file(DIR_ASSETS . '/captcha-words.txt');
            return strtolower(trim($words[array_rand($words)]));
        }
        else
        {
            $string = '';
            $length = rand($C['captcha_min'], $C['captcha_max']);

            for($i = 1; $i <= $length; $i++ )
            {
                $string .= $this->allowed_chars[array_rand($this->allowed_chars)];
            }

            return strtolower($string);
        }
    }

    function Warp($img, $width, $height)
    {
        $center = $width / 2;

        $img2 = imagecreatetruecolor($width, $height);
        $foreground = imagecolorallocate($img2, $this->foreground_color[0], $this->foreground_color[1], $this->foreground_color[2]);
        $background = imagecolorallocate($img2, $this->background_color[0], $this->background_color[1], $this->background_color[2]);
        imagealphablending($img2, true);
        imagefill($img2, 0, 0, $background);

        // periods
        $rand1 = mt_rand(750000,1200000)/10000000;
        $rand2 = mt_rand(750000,1200000)/10000000;
        $rand3 = mt_rand(750000,1200000)/10000000;
        $rand4 = mt_rand(750000,1200000)/10000000;

        // phases
        $rand5 = mt_rand(0,31415926)/10000000;
        $rand6 = mt_rand(0,31415926)/10000000;
        $rand7 = mt_rand(0,31415926)/10000000;
        $rand8 = mt_rand(0,31415926)/10000000;

        // amplitudes
        $rand9 = mt_rand(330,420)/110;
        $rand10 = mt_rand(330,450)/110;

        //wave distortion
        for( $x = 0; $x < $width; $x++ )
        {
            for( $y = 0; $y < $height; $y++ )
            {
                $sx=$x+(sin($x*$rand1+$rand5)+sin($y*$rand3+$rand6))*$rand9-$width/2+$center+1;
                $sy=$y+(sin($x*$rand2+$rand7)+sin($y*$rand4+$rand8))*$rand10;

                if( $sx<0 || $sy<0 || $sx>=$width-1 || $sy>=$height-1 )
                {
                    continue;
                }
                else
                {
                    $color=imagecolorat($img, $sx, $sy) & 0xFF;
                    $color_x=imagecolorat($img, $sx+1, $sy) & 0xFF;
                    $color_y=imagecolorat($img, $sx, $sy+1) & 0xFF;
                    $color_xy=imagecolorat($img, $sx+1, $sy+1) & 0xFF;
                }

                if( $color==255 && $color_x==255 && $color_y==255 && $color_xy==255 )
                {
                    continue;
                }
                else if($color==0 && $color_x==0 && $color_y==0 && $color_xy==0)
                {
                    $newred=$this->foreground_color[0];
                    $newgreen=$this->foreground_color[1];
                    $newblue=$this->foreground_color[2];
                }
                else
                {
                    $frsx=$sx-floor($sx);
                    $frsy=$sy-floor($sy);
                    $frsx1=1-$frsx;
                    $frsy1=1-$frsy;

                    $newcolor=($color*$frsx1*$frsy1+
                               $color_x*$frsx*$frsy1+
                               $color_y*$frsx1*$frsy+
                               $color_xy*$frsx*$frsy);

                    if( $newcolor > 255 )
                    {
                        $newcolor=255;
                    }
                    $newcolor=$newcolor/255;
                    $newcolor0=1-$newcolor;

                    $newred=$newcolor0* $this->foreground_color[0]+$newcolor*$this->background_color[0];
                    $newgreen=$newcolor0* $this->foreground_color[1]+$newcolor*$this->background_color[1];
                    $newblue=$newcolor0* $this->foreground_color[2]+$newcolor*$this->background_color[2];
                }

                imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newred, $newgreen, $newblue));
            }
        }

        imagedestroy($img);

        return $img2;
    }
}

function imagettfbboxextended($size, $angle, $fontfile, $text)
{
    $bbox = imagettfbbox($size, $angle, $fontfile, $text);

    //calculate x baseline
    if( $bbox[0] >= -1 )
    {
        $bbox['x'] = abs($bbox[0] + 1) * -1;
    }
    else
    {
        $bbox['x'] = abs($bbox[0] + 2);
    }

    //calculate actual text width
    $bbox['width'] = abs($bbox[2] - $bbox[0]);
    if( $bbox[0] < -1 )
    {
        $bbox['width'] = abs($bbox[2]) + abs($bbox[0]) - 1;
    }

    //calculate y baseline
    $bbox['y'] = abs($bbox[5] + 1);

    //calculate actual text height
    $bbox['height'] = abs($bbox[7]) - abs($bbox[1]);
    if( $bbox[3] > 0 )
    {
        $bbox['height'] = abs($bbox[7] - $bbox[1]) - 1;
    }

    return $bbox;
}

?>