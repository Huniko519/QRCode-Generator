<?php
/**
 * QRcdr - php QR Code generator
 * include/functions.php
 *
 * PHP version 5.3+
 *
 * @category  PHP
 * @package   QRcdr
 * @author    Nicola Franchini <info@veno.it>
 * @copyright 2015-2019 Nicola Franchini
 * @license   item sold on codecanyon https://codecanyon.net/item/qrcdr-responsive-qr-code-generator/9226839
 * @link      http://veno.es/qrcdr/
 */

/**
 * Get language
 *
 * @param string $default       default lang
 * @param bool   $browserDetect detect browser language
 *
 * @return $lang
 */
function getLang($default, $browserDetect = false)
{
    if ($browserDetect) {
        $browserlang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if (file_exists("lang/".$browserlang.".php")) {
            $lang = $browserlang;
            $_SESSION['lang'] = $lang;   
        }
    }
    if (isset($_GET['lang'])) {
        $lang = $_GET['lang'];
        $_SESSION['lang'] = $lang;
    }
    if (isset($_SESSION['lang'])) {
        $lang = $_SESSION['lang'];
    } else {
        $lang = $default;
    }
    return $lang;
}

/**
 * Get translated string
 *
 * @param string $string key to search
 *
 * @return translated string
 */
function getString($string)
{
    global $_translations;
    $result = '>'.$string.'<';

    if (isset($_translations[$string])) {
        $stringa = $_translations[$string];
        if (strlen($stringa) > 0) {
            $result = $_translations[$string];
        }
    } 
    return $result;
}


/**
 * Set error
 *
 * @param string $error error message
 *
 * @return global error
 */
function setError($error)
{
    global $_ERROR;
    $_ERROR = $error;
}

/**
 * Delete old files
 *
 * @param string $dir the dir to scan
 * @param int    $age files lifetime
 *
 * @return a clean directory
 */
function deleteOldFiles($dir = 'temp/', $age = 3600)
{
    if (file_exists($dir) && $handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) { 
        
            if (file_exists($dir.$file)) {

                if (preg_match("/^.*\.(png|svg|gif|jpeg|jpg|eps)$/i", $file)) {
  
                    $filelastmodified = filemtime($dir.$file);
                    $now = time();
                    $life = $now - $filelastmodified;
                    if ($life > $age) {
                        unlink($dir.$file);
                    }
                }
            }
        }
        closedir($handle); 
    }
}

/**
 * Language menu
 *
 * @param string $type  menu output availabe: 'menu' | 'list'
 * @param string $class optional class to add
 *
 * @return the language menu
 */
function langMenu($type = 'menu', $class = 'langmenu')
{
    $waterdir = 'translations/';
    $langfiles = glob($waterdir.'*.php');

    if ($type == 'menu') {
        $mymenu = '<div class="btn-group '.$class.' ml-auto"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span> '.getString('language').'</button><div class="dropdown-menu dropdown-menu-right" role="menu">';

        foreach ($langfiles as $value) {
            $val = basename($value, '.php');
            $link = '?lang='.$val;
            $mymenu .= '<a class="dropdown-item" href="'.$link.'">'.$val.'</a>';
        }
        $mymenu .='</div></div>';
    } else {
        $mymenu = '<ul class="'.$class.' list-inline">';
        foreach ($langfiles as $value) {
            $val = basename($value, '.php');
            $link = '?lang='.$val; 
            $mymenu .= '<li class="list-inline-item"><a class="btn btn-primary" href="'.$link.'">'.$val.'</a></li>';
        }
        $mymenu .='</ul>';
    }

    return $mymenu;
}
/**
 * Make thumbnails
 *
 * @param string  $original     the original file
 * @param string  $thumbname    the final file
 * @param boolean $destroy      destroy original image
 * @param int     $thumb_width  thumbnail width
 * @param int     $thumb_height thumbnail height
 *
 * @return the image thumbnailed
 */
function makeThumb(
    $original = false, 
    $thumbname = 'thumb.png', 
    $destroy = false, 
    $thumb_width = 80, 
    $thumb_height = 80
) {

    if ($original == false) {
        setError(getString('error_getting_original_image'));
        return false;
    }
    if (!file_exists($original) && !file_exists($thumbname)) {
        setError($original." ".getString('does_not_exists'));
        unset($_SESSION['logo']);
        return false;
    }

    if (file_exists($thumbname)) {
        if (file_exists($original)) {
            unlink($original);
        }
        return $thumbname;
    }

    list($width, $height) = getimagesize($original);
    $image = imagecreatefromstring(file_get_contents($original));
    $width = imagesx($image);
    $height = imagesy($image);

    $original_aspect = $width / $height;
    $thumb_aspect = $thumb_width / $thumb_height;

    if ($original_aspect >= $thumb_aspect) {
        // If image is wider than thumbnail (in aspect ratio sense)
        $new_height = $thumb_height;
        $new_width = $width / ($height / $thumb_height);
    } else {
        // If the thumbnail is wider than the image
        $new_width = $thumb_width;
        $new_height = $height / ($width / $thumb_width);
    }
    $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

    imagealphablending($thumb, false);
    imagesavealpha($thumb, true);

    $color = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
    imagefill($thumb, 0, 0, $color);

    // Resize and crop
    imagecopyresampled(
        $thumb,
        $image,
        0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
        0 - ($new_height - $thumb_height) / 2, // Center the image vertically
        0, 0,
        $new_width, $new_height,
        $width, $height
    );

    imagepng($thumb, $thumbname, 0);
    imagedestroy($thumb);

    if ($destroy == true) {
        unlink($original);
    }
    setError(getString('thumb_created'));
    return $thumbname;
}

/**
 * Set transparent background to QRcode
 *
 * @param string $image qrcode to change
 *
 * @return QRcode + watermark
 */
function transparentBg($image) 
{
    $image_new = imagecreatefromstring(file_get_contents($image));

    $white = imagecolorexact($image_new, 255, 255, 255);
    imagecolortransparent($image_new, $white);

    imagepng($image_new, $image);
    imagedestroy($image_new);
    return $image;
}

/**
 * Add watermark
 *
 * @param string $back  qrcode
 * @param string $front watermark
 *
 * @return QRcode + watermark
 */
function mergeImages($back = false, $front = false) 
{
    if ($back == false || !file_exists($back)) {
        setError(getString('error_getting_qrcode_image').": ".$back);
        return false;
    }

    if ($front == false || !file_exists($front)) {
        setError(getString('error_getting_watermark').": ".$front);
        return false;
    }
    $frame = imagecreatefromstring(file_get_contents($back));

    $image = imagecreatefrompng($front);

    $frame_width = imagesx($frame);
    $frame_height = imagesy($frame);

    $thumb_width = $frame_width/4;
    $thumb_height = $frame_height/4;

    $width = imagesx($image);
    $height = imagesy($image);

    $dest_x = ($frame_width/2) - ($thumb_width/2);
    $dest_y = ($frame_height/2) - ($thumb_height/2);

    $fframe = imagecreatetruecolor($frame_width, $frame_height);
    imagealphablending($fframe, false);
    imagesavealpha($fframe, true);

    imagecopyresampled($fframe, $frame, 0, 0, 0, 0, $frame_width, $frame_height, $frame_width, $frame_height); 

    $iimage = imagecreatetruecolor($width, $height);
    imagealphablending($iimage, false);
    imagesavealpha($iimage, true);

    imagecopyresampled($iimage, $image, 0, 0, 0, 0, $width, $height, $width, $height); 

    imagealphablending($fframe, true);
    imagealphablending($iimage, true);

    imagecopyresampled(
        $fframe, $iimage,
        $dest_x, $dest_y, 0, 0,
        $thumb_width, $thumb_height,
        $width, $height
    );

    imagepng($fframe, $back, 0);
    imagedestroy($fframe);
    imagedestroy($iimage);
    imagedestroy($image);

    //setError("image merged");
    return basename($back);
}

/**
 * Random color part
 *
 * @return random color part
 */
function randomColorPart()
{
    return str_pad(dechex(mt_rand(20, 200)), 2, '0', STR_PAD_LEFT);
}

/**
 * Random color
 *
 * @return random color
 */
function randomColor()
{
    return '#'.randomColorPart() . randomColorPart() . randomColorPart();
}


/**
 * Increases or decreases the brightness of a color by a percentage of the current brightness.
 *
 * @param string $hexCode       Supported formats: `#FFF`, `#FFFFFF`, `FFF`, `FFFFFF`
 * @param float  $adjustPercent A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
 *
 * @return string
 */
function adjustBrightness($hexCode, $adjustPercent)
{
    $hexCode = ltrim($hexCode, '#');

    if (strlen($hexCode) == 3) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }
    $hexCode = array_map('hexdec', str_split($hexCode, 2));

    foreach ($hexCode as & $color) {
        $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
        $adjustAmount = ceil($adjustableLimit * $adjustPercent);

        $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
    }
    return '#' . implode($hexCode);
}

/**
 * Convertt color hex to rgb
 *
 * @param string $htmlCode to convert
 *
 * @return RGB color
 */
function HTMLToRGB($htmlCode)
{
    if($htmlCode[0] == '#')
      $htmlCode = substr($htmlCode, 1);

    if (strlen($htmlCode) == 3) {
        $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
    }

    $r = hexdec($htmlCode[0] . $htmlCode[1]);
    $g = hexdec($htmlCode[2] . $htmlCode[3]);
    $b = hexdec($htmlCode[4] . $htmlCode[5]);

    return $b + ($g << 0x8) + ($r << 0x10);
}

/**
 * Converto color RGB to HSL
 *
 * @param string $RGB to convert
 *
 * @return HSL color
 */
function RGBToHSL($RGB)
{
    $r = 0xFF & ($RGB >> 0x10);
    $g = 0xFF & ($RGB >> 0x8);
    $b = 0xFF & $RGB;

    $r = ((float)$r) / 255.0;
    $g = ((float)$g) / 255.0;
    $b = ((float)$b) / 255.0;

    $maxC = max($r, $g, $b);
    $minC = min($r, $g, $b);

    $l = ($maxC + $minC) / 2.0;

    if ($maxC == $minC) {
        $s = 0;
        $h = 0;
    } else {
        if ($l < .5) {
            $s = ($maxC - $minC) / ($maxC + $minC);
        } else {
            $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
        }
        if ($r == $maxC) {
            $h = ($g - $b) / ($maxC - $minC);
        }
        if ($g == $maxC) {
            $h = 2.0 + ($b - $r) / ($maxC - $minC);
        }
        if ($b == $maxC) {
            $h = 4.0 + ($r - $g) / ($maxC - $minC);
        }
        $h = $h / 6.0;
    }

    $h = (int)round(255.0 * $h);
    $s = (int)round(255.0 * $s);
    $l = (int)round(255.0 * $l);

    return (object) Array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
}

/**
 * Convert color RGB to HSL
 *
 * @param string $selector   css selector
 * @param array  $attributes css attributes
 *
 * @return css rule
 */
function setCss($selector = false, $attributes = array())
{
    $print = '';
    if ($selector && !empty($attributes)) {
        $print = $selector.'{';
        foreach ($attributes as $key => $value) {
            $print .= $key.':'.$value.';';
        }
        $print .= '}';
    }
    return $print;
}

/**
 * Get main color
 *
 * @param bool $primary primary color
 *
 * @return main color
 */
function getMainColor($primary = false)
{
    $maincolor = $primary ? $primary : randomColor();
    $getcolor = filter_input(INPUT_GET, "color", FILTER_SANITIZE_STRING);
    $maincolor = $getcolor ? $getcolor : $maincolor;
    return $maincolor;
}
/**
 * Output inline css
 *
 * @param bool $primary primary color
 *
 * @return css output
 */
function setMainColor($primary = false)
{
    $maincolor = getMainColor($primary);

    $maintext = '#F6F6F6';
    $rgb = HTMLToRGB($maincolor);
    $hsl = RGBToHSL($rgb);
    $linkcolor = $maincolor;

    if ($hsl->lightness > 200) {
        $maintext = '#333';
        $linkcolor = $maintext;
    }

    if ($maincolor) {
        $maincolor_dark = adjustBrightness($maincolor, -0.3);
        $output = '<style type="text/css">';

        $output .= setCss('a, .btn-link', array('color'=> $linkcolor));
        $output .= setCss(
            'a:hover, a:active, a:focus, .btn-link:hover, .btn-link:active, .btn-link:focus',
            array(
                'color'=> $maincolor_dark
            )
        );
        // $output .= setCss(
        //     '.bg-primary a:hover, .bg-primary a:active, .bg-primary a:focus,.bg-primary .btn-link:hover, .bg-primary .btn-link:active, .bg-primary .btn-link:focus',
        //     array(
        //         'color'=> $maintext
        //     )
        // );
        $output .= setCss(
            '.bg-primary, .nav-pills .nav-link.active, .nav-pills .show > .nav-link',
            array(
                'color'=> $maintext,
                'background-color'=> $maincolor.'!important',
            )
        );
        $output .= setCss(
            '.btn-primary',
            array(
                'border-color'=> $maincolor,
                'background-color'=> $maincolor,
                'color'=> $maintext,
            )
        );
        $output .= setCss(
            '.btn-outline-primary',
            array(
                'border-color'=> $maincolor,
                'color'=> $maincolor,
            )
        );
        $output .= setCss(
            '.btn-primary:hover,.btn-primary:active,.btn-primary:focus,.btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show > .btn-primary.dropdown-toggle,.btn-outline-primary:hover,.btn-outline-primary:active,.btn-outline-primary:focus,.btn-outline-primary:not(:disabled):not(.disabled).active, .btn-outline-primary:not(:disabled):not(.disabled):active, .show > .btn-outline-primary.dropdown-toggle',
            array(
                'border-color'=> $maincolor_dark,
                'background-color'=> $maincolor_dark,
                'color'=> $maintext
            )
        );
        $output .= setCss(
            '.btn-outline-light',
            array(
                'border-color'=> $maintext,
                'color'=> $maintext,
            )
        );
        $output .= setCss(
            '.btn-outline-light:hover,.btn-outline-light:active,.btn-outline-light:focus,.btn-outline-light:not(:disabled):not(.disabled).active, .btn-outline-light:not(:disabled):not(.disabled):active, .show > .btn-outline-light.dropdown-toggle',
            array(
                'background-color'=> $maintext,
                'border-color'=> $maintext,
                'color'=> $maincolor,
            )
        );
        $output .= '</style>';
    }
    return $output;
}

/**
 * Get BTC reates
 *
 * @return help text
 */
function getBtcRates()
{
    $cache_file = 'include/btc-rates.json';
    $remote_json = "https://bitpay.com/api/rates";

    if (file_exists($cache_file)) {
        // if file is 24 hours (86400 seconds) old then regenerate.
        if (time() - filemtime($cache_file) > 86400) {
            $json = file_get_contents($remote_json);
            file_put_contents($cache_file, $json);
        } else {
            $json = file_get_contents($cache_file);
        }
    } else {
        $json = file_get_contents($remote_json);
        file_put_contents($cache_file, $json);
    }
    
    $data = json_decode($json);
    $dollar = $btc = 0;

    foreach ($data as $obj) {
        if ($obj->code == 'USD') {
            $btc = $obj->rate;
        }
    }
    $dollar = round(1/$btc, 8);

    $output = '<small class="form-text text-muted">1 BTC = ' . $btc . ' USD<br />';
    $output .= '1 USD = '.$dollar.' BTC</small>';
    $output .= '<small class="form-text text-muted">Last update: '. date('F d Y', filemtime($cache_file)).'</small>';

    return $output;
}
