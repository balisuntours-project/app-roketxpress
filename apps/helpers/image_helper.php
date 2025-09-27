<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function resizeImage($file, $w, $h, $crop=FALSE, $extension='png') {
	
    list($width, $height)	=	getimagesize($file);
    $r						=	$width / $height;
	
    if($crop) {
		
        if($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
		
    } else {
		
        if($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
	
	switch($extension) {
		case "png"	:
			$src   = imagecreatefrompng($file);
			$dst = imagecreatetruecolor($newwidth, $newheight);
			imagealphablending( $dst, FALSE );
			imagesavealpha( $dst, TRUE );
			imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			imagepng($dst, $file, 8);
			break;
		case "jpg"	:
		case "jpeg"	:
			$src = imagecreatefromjpeg($file);
			$dst = imagecreatetruecolor($newwidth, $newheight);
			imagealphablending( $dst, FALSE );
			imagesavealpha( $dst, TRUE );
			imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			imagejpeg($dst, $file, 100);
			break;
		default:
	}
	
    return true;
}