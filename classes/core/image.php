<?php
namespace core;
class image {

	public function __construct() {

	}


	public static function newResize($newWidth, $targetFile, $originalFile) {

		$info = getimagesize($originalFile);
		$mime = $info['mime'];

		switch ($mime) {
			case 'image/jpeg':
				$image_create_func = 'imagecreatefromjpeg';
				$image_save_func = 'imagejpeg';
				$new_image_ext = 'jpg';
				break;

			case 'image/png':
				$image_create_func = 'imagecreatefrompng';
				$image_save_func = 'imagepng';
				$new_image_ext = 'png';
				break;

			case 'image/gif':
				$image_create_func = 'imagecreatefromgif';
				$image_save_func = 'imagegif';
				$new_image_ext = 'gif';
				break;

			default:
				throw Exception('Unknown image type.');
		}

		$img = $image_create_func($originalFile);
		list($width, $height) = getimagesize($originalFile);

		$newHeight = ($height / $width) * $newWidth;
		$tmp = imagecreatetruecolor($newWidth, $newHeight);
		imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

		$black = imagecolorallocate($tmp, 0, 0, 0);
		imagecolortransparent($tmp, $black);

		if (file_exists($targetFile)) {
			unlink($targetFile);
		}
		$image_save_func($tmp, "$targetFile");
	}



	public function resize($old_path, $new_path, $width, $height, $real_ratio = false) {
		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($old_path);
		$upload_image = false;
		switch ($source_image_type) {
			case IMAGETYPE_GIF:
				$upload_image = imagecreatefromgif($old_path);
				break;
			case IMAGETYPE_JPEG:
				$upload_image = imagecreatefromjpeg($old_path);
				break;
			case IMAGETYPE_PNG:
				$upload_image = imagecreatefrompng($old_path);
				break;
		}

		if ($upload_image === false) {
			return false;
		}

		if(!$real_ratio) {
			$source_aspect_ratio = $source_image_width / $source_image_height;
			$thumbnail_aspect_ratio = $width / $height;
			if ($source_image_width <= $width && $source_image_height <= $height) {
				$thumbnail_image_width = $source_image_width;
				$thumbnail_image_height = $source_image_height;
			} elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
				$thumbnail_image_width = (int) ($height * $source_aspect_ratio);
				$thumbnail_image_height = $height;
			} else {
				$thumbnail_image_width = $width;
				$thumbnail_image_height = (int) ($width / $source_aspect_ratio);
			}
		}
		else {
			$thumbnail_image_width = $width;
			$thumbnail_image_height = $height;
			$source_image_width = $width;
			$source_image_height = $height;
		}

		$new_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
		imagecopyresampled($new_image, $upload_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
		imagejpeg($new_image, $new_path, 100);
		imagedestroy($upload_image);
		imagedestroy($new_image);
		return true;
	}

	public function delete($old_path) {
		unlink($old_path);
	}

	public function crop($old_path, $new_path, $width, $height, $crop_x, $crop_y, $crop_w, $crop_h, $watermark = "") {
		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($old_path);
		// Prepare canvas
		$canvas = imagecreatetruecolor( $width, $height );
		$cropped = false;
		switch ($source_image_type) {
			case IMAGETYPE_GIF:
				$cropped = imagecreatefromgif( $old_path );
				break;
			case IMAGETYPE_JPEG:
				$cropped = imagecreatefromjpeg( $old_path );
				break;
			case IMAGETYPE_PNG:
				$cropped = imagecreatefrompng( $old_path );
				break;
		}

		imagecopyresampled($canvas, $cropped, 0, 0, $crop_x, $crop_y, $width, $height, $crop_w, $crop_h);

		// WATERMARK
		if(!empty($watermark)) {
			// creating png image of watermark
			$watermark = imagecreatefromjpeg('/var/www/hrtorget/public/images/watermark.jpg');

			// getting dimensions of watermark image
			$watermark_width = imagesx($watermark);
			$watermark_height = imagesy($watermark);

			// blending the images together
			imagealphablending($canvas, true);
			imagealphablending($watermark, true);

			// placing the watermark 5px from bottom and right
			$dest_x = $width - $watermark_width - 5;
			$dest_y = $height - $watermark_height - 5;

			// creating the new image
			imagecopy($canvas, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
		}

		// Save the cropped image
		imagejpeg( $canvas, $new_path, 100);
		// Clear the memory of the tempory images
		imagedestroy( $canvas );
		imagedestroy( $cropped );
	}

}