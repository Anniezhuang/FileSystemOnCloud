<?php
include ("func/urlsign.php");
include ("../sign/pkdecrypt.php");
include ("func/downfuc.php");
include ("../connect/connect2.php");

?>

<html>
<head>
  <meta charset="utf-8">
  <title>云端文件存储系统</title>
</head>
<body>
  <?php
  $url=$_SERVER["REQUEST_URI"];
  $url=urldecode($url);
  $params=substr($url,strpos($url,'?'));

  $fid=$_GET["id"];
  $r=mysqli_fetch_object(mysqli_query($con,"SELECT keyc from filesystem where fid=$fid limit 1"));
  $keyc=$r->keyc;
  $urlverify=urlsign($params,pkDecipher($keyc));
  // echo pkDecipher($keyc)."<br>";
  // echo "urlsign=$urlverify<br>";

  $result=mysqli_query($con,"SELECT urlsign FROM download where fid=$fid ");

  if(mysqli_num_rows($result)>0)
  {
    $row=mysqli_fetch_assoc($result);
    $urlsign=$row["urlsign"];
    // echo "urlsign=$urlsign<br>";
    if($urlsign==$urlverify)
    {
      $res=mysqli_fetch_assoc(mysqli_query($con,"select * from filesystem where fid=$fid"));

      $restime=mysqli_fetch_assoc(mysqli_query($con,"select * from download where fid=$fid"));
      $time=date("Y-m-d H:i:s",time());
      $checktime=$restime['urlpost_time']+3600*24;
      if($time<$checktime)
      {
        $pass=pkDecipher($keyc);
        $path1=downfuc($res,$pass);
        $path=downfileinfo($res);
        
        echo "<center>文件名：".$res["forign_name"]."</center><br>";
        echo "<center><a href=$path1>下载文件</a></center><br>";
        echo "<center><a href=$path[0]>文件签名</a></center><br>";
        echo "<center><a href=$path[1]>文件散列值</a></center><br>";

      }
      else
      {
        echo "链接已过期";
        echo "<a href=\"../index/index.php\">返回主页</a>";
      }
    }
    else
    {
      echo "url有误";
      echo "<a href=\"../index/index.php\">返回主页</a>";
    }
  }
  else
  {
    echo "<a href=\"download.php\">请输入正确的url到地址栏中</a>";
  }
  ?>
</body>
</html>
