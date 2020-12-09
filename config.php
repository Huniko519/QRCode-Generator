<?php
/**
 * QRcdr - php QR Code generator
 * config.php
 *
 * Main configuration settings
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
$_CONFIG = array(
    'lang' => 'en',                             // main language
    'uploads_dir' => 'temp',                    // uploads directory
    'qrcodes_dir' => 'qrcodes',                 // qr codes directory
    'delete_old_files' => true,                 // delete periodically old files
    'file_lifetime' => 24,                      // delete files older than..(hours) from /uploads_dir and /qrcodes_dir
    'uploader' => true,                         // let users upload their own logo
    'upload_max_filesize' => 1000,              // max filesize in Kb
    'thumb_size' => 130,                        // the size of the user's uploaded thumbnail 
    'qr_bgcolor' => '#FFFFFF',                  // default background color for generated qrcodes
    'qr_color' => '#000000',                    // default foreground color for generated qrcodes
    'session_name' => 'qrSession',              // custom session name for the script
    'placeholder' => 'images/placeholder.svg',  // default placeholder
    'link' => true,                             // activate link tab
    'location' => true,                         // activate location tab
    'email' => true,                            // activate email tab
    'text' => true,                             // activate text tab
    'tel' => true,                              // activate telephone tab
    'sms' => true,                              // activate sms tab
    'whatsapp' => true,                         // activate whatsapp tab
    'wifi' => true,                             // activate wifi tab
    'vcard' => true,                            // activate v-card tab
    'paypal' => true,                           // activate PayPal tab
    'bitcoin' => true,                          // activate BitCoin tab
    'default_tab' => '#link',                   // available options: #link | #location | #email | #text | #sms | #wifi | #vcard | #paypal | #bitcoin | #whatsapp
    'detect_browser_lang' => false,             // detect browser language
    'google_api_key' => 'YOUR-API-KEY',         // https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key
    'color_primary' => false,                   // main color, used for buttons and header background. set a #hex color or false to get random colors
    );
