<?php
/**
 * QRcdr - php QR Code generator
 * lib/class-qrcdr.php
 *
 * PHP version 5.3+
 *
 * @category  PHP
 * @package   QRcdr
 * @author    Nicola Franchini <info@veno.it>
 * @copyright 2015-2019 Nicola Franchini
 * @license   item sold on codecanyon https://codecanyon.net/item/qrcdr-responsive-qr-code-generator/9226839
 * @version   3.3
 * @link      http://veno.es/qrcdr/
 */

/**
 * Main QRcdr class
 *
 * @category  PHP
 * @package   QRcdr
 * @author    Nicola Franchini <info@veno.it>
 * @copyright 2015-2019 Nicola Franchini
 * @license   item sold on codecanyon https://codecanyon.net/item/qrcdr-responsive-qr-code-generator/9226839
 * @link      http://veno.es/qrcdr/
 */
class QRcdr extends QRcode
{
    /**
     * Create PNG
     *
     * @param string $text         text
     * @param bool   $outfile      outfile
     * @param num    $level        level
     * @param num    $size         size
     * @param num    $margin       margin
     * @param bool   $saveandprint save and print
     * @param string $back_color   back_color
     * @param string $fore_color   fore_color
     * @param bool   $style        style
     *
     * @return PNG
     */
    public static function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint = false, $back_color = 0xFFFFFF, $fore_color = 0x000000, $style = false) 
    {
        $enc = QRencdr::factory($level, $size, $margin, $back_color, $fore_color);
        return $enc->encodePNG($text, $outfile, false, $style);
    }

    /**
     * Create SVG
     *
     * @param string $text         text
     * @param bool   $outfile      outfile
     * @param num    $level        level
     * @param num    $size         size
     * @param num    $margin       margin
     * @param bool   $saveandprint save and print
     * @param string $back_color   back_color
     * @param string $fore_color   fore_color
     * @param bool   $style        style
     *
     * @return SVG
     */
    public static function svg($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint = false, $back_color = 0xFFFFFF, $fore_color = 0x000000, $style = false)
    {
        $enc = QRencdr::factory($level, $size, $margin, $back_color, $fore_color);
        return $enc->encodeSVG($text, $outfile, false, $style);
    }
}



class QRencdr extends QRencode
{

    public static function factory($level = QR_ECLEVEL_L, $size = 3, $margin = 4, $back_color = 0xFFFFFF, $fore_color = 0x000000, $cmyk = false)
    {
        $enc = new QRencdr();
        $enc->size = $size;
        $enc->margin = $margin;
        $enc->fore_color = $fore_color;
        $enc->back_color = $back_color;
        $enc->cmyk = $cmyk;
        
        switch ($level.'') {
        case '0':
        case '1':
        case '2':
        case '3':
                $enc->level = $level;
            break;
        case 'l':
        case 'L':
                $enc->level = QR_ECLEVEL_L;
            break;
        case 'm':
        case 'M':
                $enc->level = QR_ECLEVEL_M;
            break;
        case 'q':
        case 'Q':
                $enc->level = QR_ECLEVEL_Q;
            break;
        case 'h':
        case 'H':
                $enc->level = QR_ECLEVEL_H;
            break;
        }
        
        return $enc;
    }
    
    //----------------------------------------------------------------------
    public function encodePNG($intext, $outfile = false, $saveandprint = false, $style = false)
    {
        try {
        
            ob_start();
            $tab = $this->encode($intext);
            $err = ob_get_contents();
            ob_end_clean();
            
            if ($err != '')
                QRtools::log($outfile, $err);
            
            $maxSize = (int)(QR_PNG_MAXIMUM_SIZE / (count($tab)+2*$this->margin));
            
            QRimg::png($tab, $outfile, min(max(1, $this->size), $maxSize), $this->margin, $saveandprint, $this->back_color, $this->fore_color, $style);
        
        } catch (Exception $e) {
        
            QRtools::log($outfile, $e->getMessage());
        
        }
    }

    //----------------------------------------------------------------------
    public function encodeSVG($intext, $outfile = false, $saveandprint = false, $style = false)
    {
        try {
        
            ob_start();
            $tab = $this->encode($intext);
            $err = ob_get_contents();
            ob_end_clean();
            
            if ($err != '')
                QRtools::log($outfile, $err);
            
            $maxSize = (int)(QR_PNG_MAXIMUM_SIZE / (count($tab)+2*$this->margin));

            QRvct::svg($tab, $outfile, min(max(1, $this->size), $maxSize), $this->margin, $saveandprint, $this->back_color, $this->fore_color, $style);
        
        } catch (Exception $e) {
        
            QRtools::log($outfile, $e->getMessage());
        
        }
    }

}


class QRimg extends QRimage
{
 //----------------------------------------------------------------------
    public static function png($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4, $saveandprint=FALSE, $back_color, $fore_color, $style = false)
    {
        $image = self::image($frame, $pixelPerPoint, $outerFrame, $back_color, $fore_color, $style);

        if ($filename === false) {
            Header("Content-type: image/png");
            ImagePng($image);
        } else {
            if ($saveandprint===true) {
                ImagePng($image, $filename);
                header("Content-type: image/png");
                ImagePng($image);
            } else {
                ImagePng($image, $filename);
            }
        }

        ImageDestroy($image);
    }

    //----------------------------------------------------------------------
    private static function image($frame, $pixelPerPoint = 4, $outerFrame = 4, $back_color = 0xFFFFFF, $fore_color = 0x000000, $style = false)
    {
        $stylearray = array('plus', 'circle', '3d');
        $style = in_array($style, $stylearray) ? $style : false;
        
        $dot = 1;
        if ($style) {
            $dot = $pixelPerPoint;
        }
        $outerFrame = $outerFrame * $dot;

        $h = count($frame);
        $w = strlen($frame[0]);

        $imgW = $w*$dot + $outerFrame*2;
        $imgH = $h*$dot + $outerFrame*2;

        $base_image = ImageCreate($imgW, $imgH);

        // convert a hexadecimal color code into decimal eps format (green = 0 1 0, blue = 0 0 1, ...)
        $r1 = round((($fore_color & 0xFF0000) >> 16), 5);
        $b1 = round((($fore_color & 0x00FF00) >> 8), 5);
        $g1 = round(($fore_color & 0x0000FF), 5);

        // convert a hexadecimal color code into decimal eps format (green = 0 1 0, blue = 0 0 1, ...)
        $r2 = round((($back_color & 0xFF0000) >> 16), 5);
        $b2 = round((($back_color & 0x00FF00) >> 8), 5);
        $g2 = round(($back_color & 0x0000FF), 5);

        // Set a lighter color than the foreground
        $r1light = $r1+(255-$r1)/2;
        $g1light = $g1+(255-$g1)/2;
        $b1light = $b1+(255-$b1)/2;

        $col[0] = ImageColorAllocate($base_image, $r2, $b2, $g2);
        $col[1] = ImageColorAllocate($base_image, $r1, $b1, $g1);
        $col[2] = ImageColorAllocate($base_image, $r1light, $g1light, $b1light);

        imagefill($base_image, 0, 0, $col[0]);

        $plusline = (int)min(max(($dot/8), 1), 8);
        $arrowline = (int)min(max(($dot/3), 1), 8);

        switch ($style) {
        case 'plus':
            for ($y=0; $y<$h; $y++) {
                for ($x=0; $x<$w; $x++) {
                    if ($frame[$y][$x] == '1') {
                        // orizz line
                        $initx = $x*$dot+$outerFrame;
                        $endx = $x*$dot+$outerFrame+$dot;
                        $inity = $y*$dot+$outerFrame+($dot/2)-$plusline;
                        $endy = $y*$dot+$outerFrame+($dot/2)+$plusline;
                        imagefilledrectangle($base_image, $initx, $inity, $endx, $endy, $col[1]);
                        // vert line
                        $initx = $x*$dot+$outerFrame+($dot/2)-$plusline;
                        $endx = $x*$dot+$outerFrame+($dot/2)+$plusline;
                        $inity = $y*$dot+$outerFrame;
                        $endy = $y*$dot+$outerFrame+$dot;
                        imagefilledrectangle($base_image, $initx, $inity, $endx, $endy, $col[1]);
                    }
                }
            }
            break;
        case 'circle':
            for ($y=0; $y<$h; $y++) {
                for ($x=0; $x<$w; $x++) {
                    if ($frame[$y][$x] == '1') {
                        imagefilledellipse($base_image, ($x*$dot-($dot/2))+$outerFrame+$dot, ($y*$dot-($dot/2))+$outerFrame+$dot, $dot, $dot, $col[1]);
                    }
                }
            }
            break;
        case '3d':
            for ($y=0; $y<$h; $y++) {
                for ($x=0; $x<$w; $x++) {
                    if ($frame[$y][$x] == '1') {
                        /*
                        // PINI
                        $points = array(
                            $x*$dot+$outerFrame+($dot/2), $y*$dot+$outerFrame,
                            $x*$dot+$outerFrame, $y*$dot+$outerFrame+$dot,
                            $x*$dot+$outerFrame+$dot, $y*$dot+$outerFrame+$dot
                        );
                        imagefilledpolygon( $base_image, $points, 3, $col[1] );

                        // ARROW DOWN
                        $points = array(
                            $x*$dot+$outerFrame, $y*$dot+$outerFrame+($dot/2),
                            // $x*$dot+$outerFrame+$arrowline, $y*$dot+$outerFrame+($dot/2),
                            $x*$dot+$outerFrame+$arrowline, $y*$dot+$outerFrame,
                            $x*$dot+$outerFrame+$dot-$arrowline, $y*$dot+$outerFrame,
                            $x*$dot+$outerFrame+$dot-$arrowline, $y*$dot+$outerFrame+($dot/2),
                            $x*$dot+$outerFrame+$dot, $y*$dot+$outerFrame+($dot/2),
                            $x*$dot+$outerFrame+($dot/2), $y*$dot+$outerFrame+$dot,
                        );
                        imagefilledpolygon( $base_image, $points, 6, $col[1] );

                        // ARROW UP
                        $points = array(
                            $x*$dot+$outerFrame, $y*$dot+$outerFrame+($dot/2)+$arrowline,
                            $x*$dot+$outerFrame+$arrowline, $y*$dot+$outerFrame+($dot/2)+$arrowline,
                            $x*$dot+$outerFrame+$arrowline, $y*$dot+$outerFrame+$dot, // bottom
                            $x*$dot+$outerFrame+$dot-$arrowline, $y*$dot+$outerFrame+$dot, // bottom
                            $x*$dot+$outerFrame+$dot-$arrowline, $y*$dot+$outerFrame+($dot/2)+$arrowline,
                            $x*$dot+$outerFrame+$dot, $y*$dot+$outerFrame+($dot/2)+$arrowline,
                            $x*$dot+$outerFrame+($dot/2), $y*$dot+$outerFrame,
                        );
                        imagefilledpolygon( $base_image, $points, 7, $col[1] );
                        */

                        // TRIANGOLI 3d
                        $points = array(
                        $x * $dot + $outerFrame + $dot, $y * $dot + $outerFrame,
                        $x*$dot+$outerFrame, $y*$dot+$outerFrame+$dot,
                        $x*$dot+$outerFrame+$dot, $y*$dot+$outerFrame+$dot
                        );
                        imagefilledpolygon($base_image, $points, 3, $col[2]);
                        // 3D
                        $points = array(
                        $x*$dot+$outerFrame+$dot, $y*$dot+$outerFrame, // Point 1 (x, y)
                        $x*$dot+$outerFrame, $y*$dot+$outerFrame+$dot, // Point 2 (x, y)
                        $x*$dot+$outerFrame, $y*$dot+$outerFrame // Point 3 (x, y)
                        );
                        imagefilledpolygon($base_image, $points, 3, $col[1]);
                    }
                }
            }
            break;
        
        default:
            for ($y=0; $y<$h; $y++) {
                for ($x=0; $x<$w; $x++) {
                    if ($frame[$y][$x] == '1') {
                        ImageSetPixel($base_image, $x+$outerFrame, $y+$outerFrame, $col[1]);
                    }
                }
            }
            break;
        }

        if (!$style) {
            $target_image = ImageCreate($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
            ImageCopyResized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);            
        } else {
            $target_image = ImageCreate($imgW, $imgH);
            ImageCopyResized($target_image, $base_image, 0, 0, 0, 0, $imgW, $imgH, $imgW, $imgH);
        }
        ImageDestroy($base_image);

        return $target_image;
    }

}

class QRvct extends QRvect
{
    //----------------------------------------------------------------------
    public static function svg($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4,$saveandprint=FALSE, $back_color, $fore_color, $style = false)
    {
        $vect = self::vectSVG($frame, $pixelPerPoint, $outerFrame, $back_color, $fore_color, $style);
        
        if ($filename === false) {
            header("Content-Type: image/svg+xml");
            //header('Content-Disposition: attachment, filename="qrcode.svg"');
            echo $vect;
        } else {
            if ($saveandprint===true) {
                QRtools::save($vect, $filename);
                header("Content-Type: image/svg+xml");
                //header('Content-Disposition: filename="'.$filename.'"');
                echo $vect;
            } else {
                QRtools::save($vect, $filename);
            }
        }
    }

    //----------------------------------------------------------------------
    private static function vectSVG($frame, $pixelPerPoint = 4, $outerFrame = 4, $back_color = 0xFFFFFF, $fore_color = 0x000000, $style = false)
    {
        $h = count($frame);
        $w = strlen($frame[0]);
        $dot = $pixelPerPoint; // Pixels per dot
        $radius = $dot/2;

        $h = count($frame);
        $w = strlen($frame[0]);

        $imgW = $w + $outerFrame*2;
        $imgH = $h + $outerFrame*2;

        $output = '<?xml version="1.0" encoding="utf-8"?>'."\n".
        '<svg version="1.1" baseProfile="full"  width="'.$imgW * $pixelPerPoint.'" height="'.$imgH * $pixelPerPoint.'" viewBox="0 0 '.$imgW * $pixelPerPoint.' '.$imgH * $pixelPerPoint.'"
         xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ev="http://www.w3.org/2001/xml-events">'."\n".
        '<desc></desc>'."\n";

        $output = '<?xml version="1.0" encoding="utf-8"?>'."\n".
        '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN" "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">'."\n".
        '<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" width="'.$imgW * $pixelPerPoint.'" height="'.$imgH * $pixelPerPoint.'" viewBox="0 0 '.$imgW * $pixelPerPoint.' '.$imgH * $pixelPerPoint.'">'."\n".
        '<desc></desc>'."\n";
            
        if (!empty($back_color)) {
            $backgroundcolor = str_pad(dechex($back_color), 6, "0", STR_PAD_LEFT);
            $output .= '<rect width="'.$imgW * $pixelPerPoint.'" height="'.$imgH * $pixelPerPoint.'" fill="#'.$backgroundcolor.'" cx="0" cy="0" />'."\n";
        }

        $plusline = (int)min(max(($dot/8), 1), 8);
        $polypoints1 = '0,0 '.$dot.',0 0,'.$dot;
        $polypoints2 = '0,'.$dot.' '.$dot.',0 '.$dot.','.$dot;

        $lighter = adjustBrightness(str_pad(dechex($fore_color), 6, "0", STR_PAD_LEFT), 0.5);

        $output .= 
        '<defs>'."\n".
        '<rect id="p" width="'.$pixelPerPoint.'" height="'.$pixelPerPoint.'" />'."\n".
        '<circle id="circle" r="'.$radius.'" stroke-width="0" fill="'.str_pad(dechex($fore_color), 6, "0", STR_PAD_LEFT).'" />'."\n".
        '<rect id="hor" width="'.$pixelPerPoint.'" height="'.($plusline*2).'" />'."\n".
        '<rect id="ver" width="'.($plusline*2).'" height="'.$pixelPerPoint.'" />'."\n".
        '<polygon id="3d1" points="'.$polypoints1.'" stroke-width="0" fill="'.str_pad(dechex($fore_color), 6, "0", STR_PAD_LEFT).'" />'."\n".
        '<polygon id="3d2" points="'.$polypoints2.'" stroke-width="0" fill="'.$lighter.'" />'."\n".
        '</defs>'."\n".
        '<g fill="#'.str_pad(dechex($fore_color), 6, "0", STR_PAD_LEFT).'">'."\n";

        switch ($style) {
        case 'plus':
            for ($i=0; $i<$h; $i++) {
                for ($j=0; $j<$w; $j++) {
                    if ($frame[$i][$j] == '1') {
                        // Plus
                        $y = ($i + $outerFrame) * $pixelPerPoint;
                        $x = ($j + $outerFrame) * $pixelPerPoint;
                        $output .= '<use x="'.$x.'" y="'.($y + $pixelPerPoint/2-$plusline).'" xlink:href="#hor" />'."\n";
                        $output .= '<use x="'.($x + $pixelPerPoint/2-$plusline).'" y="'.$y.'" xlink:href="#ver" />'."\n";
                    }
                }
            }
            break;

        case 'circle':
            for ($i=0; $i<$h; $i++) {
                for ($j=0; $j<$w; $j++) {
                    if ($frame[$i][$j] == '1') {
                        // Circle
                        $y = ($i + $outerFrame) * $pixelPerPoint + $radius;
                        $x = ($j + $outerFrame) * $pixelPerPoint + $radius;
                        $output .= '<use x="'.$x.'" y="'.$y.'" xlink:href="#circle" />'."\n";
                    }
                }
            }
            break;

        case '3d':
            for ($i=0; $i<$h; $i++) {
                for ($j=0; $j<$w; $j++) {
                    if ($frame[$i][$j] == '1') {
                        // Circle
                        $y = ($i + $outerFrame) * $pixelPerPoint;
                        $x = ($j + $outerFrame) * $pixelPerPoint;
                        $output .= '<use x="'.$x.'" y="'.$y.'" xlink:href="#3d1" />'."\n";
                        $output .= '<use x="'.$x.'" y="'.$y.'" xlink:href="#3d2" />'."\n";
                    }
                }
            }
            break;

        default:
            for ($i=0; $i<$h; $i++) {
                for ($j=0; $j<$w; $j++) {
                    if ($frame[$i][$j] == '1') {
                        // square
                        $y = ($i + $outerFrame) * $pixelPerPoint;
                        $x = ($j + $outerFrame) * $pixelPerPoint;
                        $output .= '<use x="'.$x.'" y="'.$y.'" xlink:href="#p" />'."\n";
                    }
                }
            }
            break;
        }

        // Convert the matrix into pixels
        // for ($i=0; $i<$h; $i++) {
        //     for ($j=0; $j<$w; $j++) {
        //         if ($frame[$i][$j] == '1') {

        //             // Circle
        //             // $y = ($i + $outerFrame) * $pixelPerPoint;
        //             // $x = ($j + $outerFrame) * $pixelPerPoint;
        //             // $output .= '<use x="'.$x.'" y="'.$y.'" xlink:href="#p" />'."\n";
        //             // $output .= '<use x="'.$x.'" y="'.$y.'" xlink:href="#circle" />'."\n";

        //             // Plus
        //             $y = ($i + $outerFrame) * $pixelPerPoint;
        //             $x = ($j + $outerFrame) * $pixelPerPoint;
        //             $output .= '<use x="'.$x.'" y="'.($y + $pixelPerPoint/2-$plusline).'" xlink:href="#hor" />'."\n";
        //             $output .= '<use x="'.($x + $pixelPerPoint/2-$plusline).'" y="'.$y.'" xlink:href="#ver" />'."\n";
        //         }
        //     }
        // }
        $output .= '</g>'."\n".'</svg>';

        return $output;
    }
}
