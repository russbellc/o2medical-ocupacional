<?php
$upload_dir = "upload/";
$img = $_POST['hidden_data'];
print $img;
$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace(' ', '+', $img);
$data = base64_decode($img);
$file = $upload_dir .'48357829'. ".png";
$success = file_put_contents($file, $data);
print $success ? $file : 'Unable to save the file.';
?>