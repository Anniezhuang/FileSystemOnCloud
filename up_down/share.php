<?php
include ("func/urlsign.php");
include ("../connect/connect2.php");
include ("../sign/pkdecrypt.php");
include ("func/downfuc.php");
session_start();

$fid=$_GET["fid"];
$ownerid=$_GET["uid"];
$ffhash=$_GET["fhash"];
if(isset($_SESSION["user_id"])&&$ownerid==$_SESSION["user_id"]){

  $time=downloadtime();

  $furl="https://websever.com/cloud/up_down/download.php?id="
  .$fid."&&time=".$time[0]."&&uid=".$ownerid."&&file=".$ffhash."&&token=".$time[1];

  //根据fid到数据库找到加密文件的密钥$keyc，$keyc被服务器的公钥加密，需先用服务器的私钥解密得到$key
  $sql="select keyc from download where fid=$fid";
  $enkey=mysqli_query($con,$sql);
  $key=pkDecipher($enkey);

  //对url做HMAC和数字签名
  $params=substr($furl,strpos($furl,'?'));
  $result=mysqli_fetch_object(mysqli_query($con,"SELECT keyc from filesystem where fid=$fid limit 1"));
  $urlsign=urlsign($params,pkDecipher($result->keyc));

  //如果数据表download有文件下载的数据，判断url是否有效，如果无效，更新url，如果有效继续续使用这个url
  //如果不存在文件下载的数据，则生成url，插入相关数据
  if(!mysqli_fetch_object(mysqli_query($con,"select * from download where fid=$fid")))
  {
    $sqldownload = "INSERT INTO download (furl,fid,urlpost_time,urlsign)
    VALUES (\"$furl\",$fid,\"$time[0]\",\"$urlsign\")";
    mysqli_query($con,$sqldownload);
    // echo pkDecipher($result->keyc)."<br>";
    // echo "$params<br>";
    // echo "$urlsign<br>";
    echo $furl;

  }
  else
  {
    $timenow=date("Y-m-d H:i:s",time());
    $request=mysqli_query($con,"select urlpost_time,furl from download where fid=$fid limit 1");
    $res=mysqli_fetch_object($request);
    $validtime=$res->urlpost_time+3600*24;
    // echo $timenow;
    // echo $res->urlpost_time;
    if($timenow<$validtime)
    {
      echo $res->furl;
    }
    else
    {
      mysqli_query($con,"update download set furl=$furl,urlsign=$urlsign,urlpost_time=$time[0] where fid=$fid");
      echo $furl;
    }
  }
}
else
{
  echo "请登录";
}

?>
