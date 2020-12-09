<?php
/**
 * QRcdr - php QR Code generator
 * process.php
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
date_default_timezone_set('UTC');
require dirname(dirname(__FILE__))."/config.php";
$lang = $_CONFIG['lang'];
if (file_exists(dirname(dirname(__FILE__))."/translations/".$lang.".php")) {
    include dirname(dirname(__FILE__))."/translations/".$lang.".php";
} else {
    include dirname(dirname(__FILE__))."/translations/en.php";
}

$output_data = false;
$otp = false;

$outdir = $_CONFIG['qrcodes_dir'];
$PNG_TEMP_DIR = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.$outdir.DIRECTORY_SEPARATOR;
$PNG_WEB_DIR = $outdir.'/';

require dirname(dirname(__FILE__))."/lib/phpqrcode.php";
require dirname(dirname(__FILE__))."/lib/class-qrcdr.php";
require dirname(dirname(__FILE__))."/include/functions.php";

$optionlogo   = 'none';
$output_data = false;
$getsection = filter_input(INPUT_POST, "section", FILTER_SANITIZE_STRING);
$setbackcolor = filter_input(INPUT_POST, "backcolor", FILTER_SANITIZE_STRING);
$setfrontcolor = filter_input(INPUT_POST, "frontcolor", FILTER_SANITIZE_STRING);
$optionlogo = filter_input(INPUT_POST, "optionlogo", FILTER_SANITIZE_STRING);
$optionstyle = filter_input(INPUT_POST, "style", FILTER_SANITIZE_STRING);

if ($setbackcolor) {
    $stringbackcolor = $setbackcolor;
}
if ($setfrontcolor) {
    $stringfrontcolor = $setfrontcolor;
}

$backcolor = hexdec(str_replace('#', '0x', $stringbackcolor));
$frontcolor = hexdec(str_replace('#', '0x', $stringfrontcolor));

$level = filter_input(INPUT_POST, "level", FILTER_SANITIZE_STRING);
$size = filter_input(INPUT_POST, "size", FILTER_SANITIZE_STRING);

if (in_array($level, array('L','M','Q','H'))) {
    $errorCorrectionLevel = $level;    
}
if ($size) {
    $matrixPointSize = min(max((int)$size, 4), 32);
}

switch ($getsection) {

case '#text':
    $output_data = filter_input(INPUT_POST, "data", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );
    break;
case '#email':
    $datamailto = filter_input(INPUT_POST, "mailto", FILTER_VALIDATE_EMAIL);
    $datamailsubj = filter_input(INPUT_POST, "subject", FILTER_SANITIZE_STRING);
    $datamailbody = filter_input(INPUT_POST, "body", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    if ($datamailto) {
        $output_data = 'MATMSG:TO:'.$datamailto.';SUB:'.$datamailsubj.';BODY:'.$datamailbody.';;';
    }
    break;
case '#link':
    $output_data = filter_input(INPUT_POST, "link", FILTER_VALIDATE_URL);
    break;
case '#tel':
    $countrycode = filter_input(INPUT_POST, "countrycodetel", FILTER_SANITIZE_STRING);
    $number = filter_input(INPUT_POST, "tel", FILTER_SANITIZE_STRING);
    if ($number) {
        $countrycode = ($countrycode ? '+'.$countrycode : '');
        $output_data = 'tel:'.$countrycode.$number;
    }
    break;

case '#sms':
    $countrycode = filter_input(INPUT_POST, "countrycodesms", FILTER_SANITIZE_STRING);

    $number = filter_input(INPUT_POST, "sms", FILTER_SANITIZE_STRING);
    $bodysms = filter_input(INPUT_POST, "bodysms", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

    if ($number) {
        $countrycode = ($countrycode ? '+'.$countrycode : '');
        $output_data = 'SMSTO:'.$countrycode.$number.':'.$bodysms;
    }
    break;
case '#whatsapp':
    $countrycode = filter_input(INPUT_POST, "wapp_countrycode", FILTER_SANITIZE_STRING);

    $number = filter_input(INPUT_POST, "wapp_number", FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, "wapp_message", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

    if ($number) {
        $output_data = 'https://wa.me/'.$countrycode.$number;
    }

    if ($message) {
        $output_data .= '/?text='.urlencode($message);
    }

    break;
case '#wifi':
    $ssid = filter_input(INPUT_POST, "ssid", FILTER_SANITIZE_STRING);
    $wifipass = filter_input(INPUT_POST, "wifipass", FILTER_SANITIZE_STRING);
    $networktype = filter_input(INPUT_POST, "networktype", FILTER_SANITIZE_STRING);
    $wifihidden = filter_input(INPUT_POST, "wifihidden", FILTER_SANITIZE_STRING);

    if ($ssid) {
        $output_data = 'WIFI:S:'.$ssid.';';

        if ($networktype) {
            $output_data .= 'T:'.$networktype.';';
        }
        if ($wifipass) {
            $output_data .= 'P:'.$wifipass.';';
        }
        if ($wifihidden) {
            $output_data .= 'H:true;';
        }
        $output_data .= ';';
    }
    break;
case '#location':
    $lat = filter_input(INPUT_POST, "lat", FILTER_SANITIZE_STRING);
    $lng = filter_input(INPUT_POST, "lng", FILTER_SANITIZE_STRING);
    if ($lat && $lng) {
        $output_data = "geo:".$lat.",".$lng; 
    }
    break;
case '#vcard':
    $vname = filter_input(INPUT_POST, "vname", FILTER_SANITIZE_STRING);
    $vlast = filter_input(INPUT_POST, "vlast", FILTER_SANITIZE_STRING);
    $name         = $vname.' '.$vlast;
    $sortName     = $vlast.';'.$vname;
    $vversion     = filter_input(INPUT_POST, "vversion", FILTER_SANITIZE_STRING);
    $phone        = filter_input(INPUT_POST, "vphone", FILTER_SANITIZE_STRING);
    $phoneCell    = filter_input(INPUT_POST, "vmobile", FILTER_SANITIZE_STRING);
    $email        = filter_input(INPUT_POST, "vemail", FILTER_VALIDATE_EMAIL);
    $orgName      = filter_input(INPUT_POST, "vcompany", FILTER_SANITIZE_STRING);
    $orgTitle     = filter_input(INPUT_POST, "vtitle", FILTER_SANITIZE_STRING);
    $vurl         = filter_input(INPUT_POST, "vurl", FILTER_SANITIZE_STRING);
    $fax          = filter_input(INPUT_POST, "vfax", FILTER_SANITIZE_STRING);
    $address          = filter_input(INPUT_POST, "vaddress", FILTER_SANITIZE_STRING);
    $addressTown      = filter_input(INPUT_POST, "vcity", FILTER_SANITIZE_STRING);
    $addressPostCode  = filter_input(INPUT_POST, "vcap", FILTER_SANITIZE_STRING);
    $addressCountry   = filter_input(INPUT_POST, "vcountry", FILTER_SANITIZE_STRING);

    if ($vname && $phone) {
        $output_data  = 'BEGIN:VCARD'."\n";
        $output_data .= 'VERSION:'.$vversion."\n";
        $output_data .= 'N;CHARSET=utf-8;ENCODING=8BIT:'.$sortName."\n";
        $output_data .= 'FN;CHARSET=utf-8;ENCODING=8BIT:'.$name."\n";
        $output_data .= 'ORG;CHARSET=utf-8;ENCODING=8BIT:'.$orgName."\n";
        $output_data .= 'TITLE;CHARSET=utf-8;ENCODING=8BIT:'.$orgTitle."\n";

        //$output_data .= 'TEL;WORK;VOICE:'.$phone."\n";
        $output_data .= 'TEL;HOME;VOICE:'.$phone."\n";
        $output_data .= 'TEL;TYPE=CELL:'.$phoneCell."\n";
        $output_data .= 'TEL;TYPE=FAX:'.$fax."\n";

        $output_data .= 'ADR;TYPE=work:'
            .$address.';'
            .$addressTown.';'
            .$addressPostCode.';'
            .$addressCountry
        ."\n";
        $output_data .= 'EMAIL:'.$email."\n";
        $output_data .= 'URL:'.$vurl."\n";

        $output_data .= 'END:VCARD'; 
    }
    break;
case '#paypal':
    $type = filter_input(INPUT_POST, "pp_type", FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, "pp_email", FILTER_VALIDATE_EMAIL);
    $name = filter_input(INPUT_POST, "pp_name", FILTER_SANITIZE_STRING);
    $id = filter_input(INPUT_POST, "pp_id", FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, "pp_price", FILTER_SANITIZE_STRING);
    $currency = filter_input(INPUT_POST, "pp_currency", FILTER_SANITIZE_STRING);
    $shipping = filter_input(INPUT_POST, "pp_shipping", FILTER_SANITIZE_STRING);
    $tax = filter_input(INPUT_POST, "pp_tax", FILTER_SANITIZE_STRING);

    if ($email && $name) {
        $output_data  = 'https://www.paypal.com/cgi-bin/webscr';
        $output_data  .= '?cmd='.$type;
        $output_data  .= '&business='.urlencode($email);
        $output_data  .= '&item_name='.urlencode($name);
        $output_data  .= '&amount='.urlencode($price);
        $output_data  .= '&currency_code='.$currency;

        if ($shipping) {
            $output_data  .= '&shipping='.urlencode($shipping);
        }
        if ($tax) {
            $output_data  .= '&tax_rate='.urlencode($tax);
        }

        if ($type === '_xclick') {
            $output_data  .= '&button_subtype=services';
            $output_data  .= '&bn='.urlencode('PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest');
        } elseif ($type === '_cart') {
            $output_data  .= '&button_subtype=products&add=1';
            $output_data  .= '&bn='.urlencode('PP-ShopCartBF:btn_cart_LG.gif:NonHostedGuest');
        } else {
            $output_data  .= '&bn='.urlencode('PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest');
        }
        $output_data  .= '&lc=US&no_note=0';
    }
    break;
case '#bitcoin':
    $btc_account = filter_input(INPUT_POST, "btc_account", FILTER_SANITIZE_STRING);
    $btc_amount = filter_input(INPUT_POST, "btc_amount", FILTER_SANITIZE_STRING);
    $btc_label = filter_input(INPUT_POST, "btc_label", FILTER_SANITIZE_STRING);
    $btc_message = filter_input(INPUT_POST, "btc_message", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

    if ($btc_account) {
        $output_data = 'bitcoin:'.$btc_account;

        if ($btc_amount) {
            $output_data .= '?amount='.$btc_amount;
        }

        if ($btc_label) {
            $output_data .= '&label='.urlencode($btc_label);
        }

        if ($btc_message) {
            $output_data .= '&message='.urlencode($btc_message);
        }
    }
    break;
}


if ($output_data) {

    $trasp = (isset($_POST['transparent']) ? true : false);

    $filenamepng = $PNG_TEMP_DIR.md5($output_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
    $filenamesvg = $PNG_TEMP_DIR.md5($output_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.svg';
    // $filenameeps = $PNG_TEMP_DIR.md5($output_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.eps';

    // QRcode::png($output_data, $filenamepng, $errorCorrectionLevel, $matrixPointSize, 2, false, $backcolor, $frontcolor);    
    // QRcode::svg($output_data, $filenamesvg, $errorCorrectionLevel, $matrixPointSize, 2, false, $backcolor, $frontcolor);
    // QRcode::eps($output_data, $filenameeps, $errorCorrectionLevel, $matrixPointSize, 2, false, $backcolor, $frontcolor);

    QRcdr::png($output_data, $filenamepng, $errorCorrectionLevel, $matrixPointSize, 2, false, $backcolor, $frontcolor, $optionstyle);    
    QRcdr::svg($output_data, $filenamesvg, $errorCorrectionLevel, $matrixPointSize, 2, false, $backcolor, $frontcolor, $optionstyle);
    // QRcdr::eps($output_data, $filenameeps, $errorCorrectionLevel, $matrixPointSize, 2, false, $backcolor, $frontcolor);

    $finalpng = basename($filenamepng);
    $finalsvg = basename($filenamesvg);
    // $finaleps = basename($filenameeps);

    $mergedimage = false;

    if ($trasp) {
        $finalpng = basename(transparentBg($filenamepng));
    }
    if ($optionlogo && $optionlogo !== 'none') {
        $mergedimage = mergeImages('../'.$PNG_WEB_DIR.$finalpng, '../'.$optionlogo, false);
    }
    if ($mergedimage) {
        $placeholder = $PNG_WEB_DIR.$mergedimage;
    } else {
        $placeholder = $PNG_WEB_DIR.$finalpng;
    }

    $result = array(
        'png'=> $finalpng, 
        'svg'=> $finalsvg, 
        // 'eps'=> $finaleps, 
        'placeholder'=> $placeholder, 
        'optionlogo'=> $optionlogo,
        // 'errore' => QRcode::text($output_data, false, $errorCorrectionLevel, $matrixPointSize, 2),
        /// 'errore'=> $_ERROR,

        );
    if ($otp) {
        $result['otp'] = $otp;
    }

    $result = json_encode($result);
} else {
    $result = json_encode(array('errore'=> getString('provide_more_data'), 'placeholder' => $_CONFIG['placeholder']));
}
echo $result;
