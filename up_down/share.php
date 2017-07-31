<?php
include ("func/urlsign.php");
include ("../connect/connect2.php");
// include ("../sign/pkdecrypt.php");
include ("func/downfuc.php");

$time=downloadtime();
$path=downfuc($row,$key);

$fid=$_GET["fid"];
$ownerid=$_GET["uid"];
$ffhash=$_GET["fhash"];

$furl="https://websever.com/cloud/up_down/download.php?id="
.$fid."&&time=".$time[0]."&&uid=".$ownerid."&&file=".$ffhash."&&token=".$time[1];

//根据fid到数据库找到加密文件的密钥$keyc，$keyc被服务器的公钥加密，需先用服务器的私钥解密得到$key
$sql="select keyc from download where fid=$fid";
$enkey=mysqli_query($con,$sql);
$key=pkDecipher($enkey);

//对url做HMAC和数字签名
$params=substr($furl,strpos($furl,'?'));
$urlsign=urlsign($params,$key);

$sqldownload = "INSERT INTO download (furl,fid,urlpost_time,urlsign)
            VALUES (\"$furl\",$fid,\"$time[0]\",\"$urlsign\")";

$pdo->exec($sqldownload);

alert($furl);

 ?>
