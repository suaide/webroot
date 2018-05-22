<?php
$img_filename = $_GET["file"];
if(isset($_GET["size"]))
{
}
header('Content-type: image/png');
header('Content-length: '.filesize($img_filename));
$file_pointer = fopen($img_filename, 'rb');
fpassthru($file_pointer);
fclose($file_pointer);
?>
