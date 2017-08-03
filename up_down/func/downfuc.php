<?php
$ftype=array("image/gif"=>"gif",
"image/jpeg"=>"jpeg",
"image/jpg"=>"jpg",
"image/pjpeg"=>"jpg",
"image/x-png"=>"png",
"image/png"=>"png",
"image/bmp"=>"bmp",
"application/pdf"=>"pdf",
"application/msword"=>"doc",
"application/vnd.ms-powerpoint"=>"ppt",
"application/vnd.openxmlformats-officedocument.presentationml.presentation"=>"pptx",
"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"=>"xlsx",
"application/vnd.openxmlformats-officedocument.wordprocessingml.document"=>"docx",
"application/vnd.ms-excel"=>"xls");
include ("decryptFile.php");
function downfuc($fileinfo,$key)
{
  if(!file_exists("/var/www/html/cloud/download/"))
  {
    mkdir("/var/www/html/cloud/download/");
  }

  $source="/var/www/html/cloud/file/".$fileinfo["uid"]."/".$fileinfo["fnew_name"];
  $dest="/var/www/html/cloud/download/".$fileinfo["forign_name"];
  $dest=decryptFile($source, $key, $dest);

  $new_url="downloadfile.php?file=".$fileinfo["forign_name"];

  return $new_url;

}

function downfileinfo($fileinfo)
{
  if(!file_exists("/var/www/html/cloud/download/"))
  {
    mkdir("/var/www/html/cloud/download");
  }

  $filehash=fopen("/var/www/html/cloud/download/hash".$fileinfo["fid"].".txt","a+");
  $filesign=fopen("/var/www/html/cloud/download/sign".$fileinfo["fid"].".txt","a+");

  fwrite($filehash, $fileinfo["fhash"]);
  fwrite($filehash, $fileinfo["uid"]);
  fwrite($filesign,$fileinfo["fsign"]);

  fclose($filehash);
  fclose($filesign);

  $path = array('0'=>"downloadfile.php?file=sign".$fileinfo["fid"].".txt",'1' =>"downloadfile.php?file=hash".$fileinfo["fid"].".txt");

  return $path;

}



// function deletefile($filename)
// {
//   $path="/var/www/html/cloud/download/$filename";
//   unlink($path);
// }
