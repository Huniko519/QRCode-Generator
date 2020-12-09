<?php
/**
 * QRcdr - php QR Code generator
 * get.php
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
require dirname(dirname(__FILE__))."/config.php";
session_name($_CONFIG['session_name']);
session_start();
require dirname(dirname(__FILE__))."/include/functions.php";
$dir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.$_CONFIG['qrcodes_dir'].'/';
 
$path = (isset($_GET['path']) ? $_GET['path'] : false);
 
if (!preg_match('/^[a-z0-9]+.[a-z]{2,3}$/i', $path)) {
    $path = false;
} else {
    $file = $dir.$path;
}
 
if (!file_exists($file)) {
    $_SESSION['error'] = "The file is not here: ".$file;
    header('Location:index.php');
    exit;
} else {
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-type: Application/octet-stream");
    header("Content-Disposition: attachment; filename= ".$path);
    header("Content-Length: ".filesize($file));
    header("Content-Transfer-Encoding: binary");
    if (ob_get_level() > 0) {
        ob_end_flush();
    }
    readfile($file);
    exit;
} 
header('Location:index.php');
exit;