<?php
// Find .tga files from the specified directory and convert it to png (remove the tga)

$directory = '../public/images/game/itemTypes';

if (! is_dir($directory)) {
  exit('Invalid diretory path');
}

foreach(scandir($directory) as $file) {
  if ('.' === $file) continue;
  if ('..' === $file) continue;
  if(strstr($file, '.tga')) {
    convertImage($directory . '/' . $file[0]);
  }
}

function convertImage($image) {
  $im = new Imagick($image);
  $im->setImageFormat("png");
  $im->writeImage(str_replace(".tga",".png", $image));
  unlink($image);
}