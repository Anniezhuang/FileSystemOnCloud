<?php

include ("../connect/connect.php");
include ("func/encryptFile.php");
include ("../sign/pkencrypt.php");
include ("../sign/sign.php");
include ("func/urlsign.php");
include ("func/randomkey.php");

session_start();

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

function rebuild($tmpfile,$name)
{
  if(substr(strrchr($name,"."),1)=="bmp")
  {
    try {
      $im=imagecreatefromwbmp($tmpfile);
      imagewbmp($im, $tmpfile);
      imagedestroy($im);

    } catch (Error $e) {
      echo "<center>文件类型出错</center>";
      echo "<center><a href='../index/index.php'>点此返回</a></center>";
    }

  }
  if(substr(strrchr($name,"."),1)=="png")
  {
    try {
      $im = imagecreatefrompng($tmpfile);
      imagepng($im, $tmpfile);
      imagedestroy($im);
    } catch (Error $e) {
      echo "<center>文件类型出错</center>";
      echo "<center><a href='../index/index.php'>点此返回</a></center>";
    }
  }
  if(substr(strrchr($name,"."),1)=="jpeg"||substr(strrchr($name,"."),1)=="jpg")
  {
    try {
      $im=imagecreatefromjpeg($tmpfile);
      imagejpeg($im, $tmpfile);
      imagedestroy($im);
    } catch (Error $e) {
      echo "<center>文件类型出错</center>";
      echo "<center><a href='../index/index.php'>点此返回</a></center>";
      echo $e->getMessage();
    }

  }
  if(substr(strrchr($name,"."),1)=="gif")
  {
    try {
      $im = imagecreatefromgif($tmpfile);
      imagegif($im, $tmpfile);
      imagedestroy($im);
    } catch (Error $e) {
      echo "<center>文件类型出错</center>";
      echo "<center><a href='../index/index.php'>点此返回</a></center>";
    }
  }
}

if(isset($_POST['submit']))
{
  $file=$_FILES['file'];
  $name=$file['name'];  //取得文件名称
  $type=$file['type']; //取得文件类型
  $size=$file['size'];  //取得文件长度
  $tmpfile=$file['tmp_name'];  //文件上传上来到临时文件的路径
  $key=randomkey();

  if($tmpfile and is_uploaded_file($tmpfile))//判断上传文件是否为空，文件是不是上传的文件
  {
    $filetem=fopen($tmpfile,"rb");
    $databin=fread($filetem,$size);
    fclose($filetem);

    // 判断文件类型，文件后缀名准确性，是否登录，是否小于 10MB
    if (isset($ftype[$type])
    && ($size < 10000000) && isset($_SESSION["user_id"])
    &&($ftype[$type]==substr(strrchr($name,"."),1)||($ftype[$type]=="jpeg"&&substr(strrchr($name,"."),1)=="jpg")))
    {
      if ($_FILES["file"]["error"] > 0)
      {
        echo "错误：: " . $_FILES["file"]["error"] . "<br>";
      }
      else
      {
        $ownerid = $_SESSION["user_id"];
        $fnew_name=hash('sha1',$name);

        if (!file_exists("/var/www/html/cloud/file/$ownerid"))
        {
          mkdir("/var/www/html/cloud/file/$ownerid");
        }

        if (file_exists("/var/www/html/cloud/file/$ownerid/".$fnew_name))
        {
          echo "<br><br><center>文件名重复<br><br></center>";
          echo "<center><a href='../index/index.php'>请重新上传</a></center>";
        }
        else
        {
          $file_dest="/var/www/html/cloud/file/$ownerid/".$fnew_name;
          rebuild($tmpfile,$name);
          $file_dest=encryptFile($tmpfile, $key,$file_dest);
          $keyhash=hash('sha256',$key);

          //用服务器的公钥加密上传文件时用的对称密钥
          $keyc=pkCipher($key);

          //先用服务器的私钥对文件的hash进行签名，然后用用户的私钥再签名
          $fhash=hash('sha256',$databin);
          $fsign=sign($fhash,$ownerid);

          $fid=mt_rand(1,100000);
          while(($pdo->query("select * from filesystem where $fid=fid"))->rowCount()!=0)
          {
            $fid=mt_rand(1,100000);
          }

          $sql = "INSERT INTO filesystem (fid,uid,fnew_name,forign_name,ftype,fsign,keyhash,fhash,fpost_time,keyc)
          VALUES (:fid,:uid,:fnew_name,:forign_name,:ftype,:fsign,:keyhash,:fhash,NOW(),:keyc)";

          $stmt=$pdo->prepare($sql);
          $stmt->bindParam(':fid',$fid);
          $stmt->bindParam(':uid',$ownerid);
          $stmt->bindParam('fnew_name',$fnew_name);
          $stmt->bindParam(':forign_name',$name);
          $stmt->bindParam(':ftype',$type);
          $stmt->bindParam(':fsign',$fsign);
          $stmt->bindParam(':keyhash',$keyhash);
          $stmt->bindParam(':fhash',$fhash);
          $stmt->bindParam(':keyc',$keyc);

          if(!$stmt->execute())
          {
            // echo $sql;
            //print_r($stmt->errorInfo());
            echo "<br><br><center>上传失败！<br><br></center>";
            echo "<center><a href='../index/index.php'>重试</a></center>";
          }
          else
          {
            echo "<center>上传成功！</center>";
            echo "上传文件名: " . $_FILES["file"]["name"] . "<br>";
            echo "文件类型: " . $_FILES["file"]["type"] . "<br>";
            echo "文件大小: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
            echo "<center><a href='../index/index.php'>点此返回</a></center>";
          }
        }
      }
    }
    else
    {
      echo $ftype[$_FILES["file"]["type"]];
      echo $_FILES["file"]["type"];
      echo "<center>错误提示：未登录或文件类型出错！<br><br><a href='../index/index.php'>点此返回</a></center>";
    }
  }
  else
  {
    echo "<center>请选择小于10MB的图片或文档！<br><br><a href='../index/index.php'>点此返回</a></center>";
  }

}

else
{
  echo "<center>请选择小于10MB的图片或文档！<br><br><a href='../index/index.php'>点此返回</a></center>";
}

?>
