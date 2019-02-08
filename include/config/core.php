<?php

	function resize_image($filename, $resolution) {

		if (file_exists($filename)) {

			$src_image = imagecreatefromjpeg($filename);
			$src_w = imagesx($src_image);
			$src_h = imagesy($src_image);


			$ratio = ($resolution / $src_w);
			$dst_w = $resolution;
			$dst_h = ($src_h * $ratio);

			if($dst_h > $resolution){
				$ratio = ($resolution / $src_h);
				$dst_h = $resolution;
				$dst_w = ($src_w * $ratio);
			}

			if ($src_image) {
				$dst_image = imagecreatetruecolor($dst_w, $dst_h);
				imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
				imagejpeg($dst_image, $filename, 90);
			}
		}
	}
	function crop_image($filename, $resolution) {

		if (file_exists($filename)) {

			$src_image = imagecreatefromjpeg($filename);
			$src_w = imagesx($src_image);
			$src_h = imagesy($src_image);

			if($src_h > $src_w){
				$ratio = ($resolution / $src_w);
				$dst_w = $resolution;
				$dst_h = ($src_h * $ratio);

				$extra = $dst_h - $dst_w;
				$x_axis = 0;
				$y_axis = round($extra/2);

			} else {

				$ratio = ($resolution / $src_h);
				$dst_h = $resolution;
				$dst_w = ($src_w * $ratio);

				$extra = $dst_w - $dst_h;
				$x_axis = round($extra/2);
				$y_axis = 0;
			}

			if ($src_image) {
				$dst_image = imagecreatetruecolor($resolution, $resolution+25);
				imagecopyresampled($dst_image, $src_image, 0, 0, $x_axis, $y_axis, $dst_w, $dst_h, $src_w, $src_h);
				imagejpeg($dst_image, $filename, 60);
			}
		}
	}

	function scale_image($file) {
		$source_pic = $file;
		$max_width = 172;
		$max_height = 185;

		list($width, $height, $image_type) = getimagesize($file);

		switch ($image_type) {
			case 1: $src = imagecreatefromgif($file); break;
			case 2: $src = imagecreatefromjpeg($file);  break;
			case 3: $src = imagecreatefrompng($file); break;
			default: return '';  break;
		}

		$x_ratio = $max_width / $width;
		$y_ratio = $max_height / $height;

		if( ($width <= $max_width) && ($height <= $max_height) ){
			$tn_width = $width;
			$tn_height = $height;
		} elseif (($x_ratio * $height) < $max_height){
			$tn_height = ceil($x_ratio * $height);
			$tn_width = $max_width;
		}	else {
			$tn_width = ceil($y_ratio * $width);
			$tn_height = $max_height;
		}

		$tmp = imagecreatetruecolor($tn_width,$tn_height);

		/* Check if this image is PNG or GIF, then set if Transparent*/
		if(($image_type == 1) OR ($image_type==3)){
			imagealphablending($tmp, false);
			imagesavealpha($tmp,true);
			$transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
			imagefilledrectangle($tmp, 0, 0, $tn_width, $tn_height, $transparent);
		}
		imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$width,$height);
		
		ob_start();

		switch ($image_type) {
			case 1: imagegif($tmp); break;
			case 2: imagejpeg($tmp, NULL, 100);  break; // best quality
			case 3: imagepng($tmp, NULL, 0); break; // no compression
			default: echo ''; break;
		}

		$final_image = ob_get_contents();
		ob_end_clean();
		return $final_image;
	}