<?php

include ("../connect/connect.php");
include ("func/encryptFile.php");
include ("../sign/pkencrypt.php");
include ("../sign/sign.php");
include ("func/urlsign.php");

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

if(isset($_POST['submit']))
{
  $file=$_FILES['file'];
  $name=$file['name'];  //取得图片名称
  $type=$file['type']; //取得图片类型
  $size=$file['size'];  //取得图片长度
  $tmpfile=$file['tmp_name'];  //图片上传上来到临时文件的路径
  $key = $_POST["password"];

  if($tmpfile and is_uploaded_file($tmpfile))
  {  //判断上传文件是否为空，文件是不是上传的文件//读取图片流
    $filetem=fopen($tmpfile,"rb");
    $databin=fread($filetem,$size);
    fclose($filetem);

    if (isset($ftype[$_FILES["file"]["type"]])
    && ($_FILES["file"]["size"] < 10000000) && isset($_SESSION["user_id"]))  // 小于 10MB
    {
      if ($_FILES["file"]["error"] > 0)
      {
        echo "错误：: " . $_FILES["file"]["error"] . "<br>";
      }
      else
      {
        $ownerid = $_SESSION["user_id"];
        $fnew_name=hash('sha1',$name);

        if (!file_exists("/var/www/html/cloud/file/$ownerid"))//待登录测试，把test1改为$ownerid
        {
          mkdir("/var/www/html/cloud/file/$ownerid");
        }

        if (file_exists("/var/www/html/cloud/file/$ownerid/".$fnew_name))
        {
          echo "<br><br><center>上传失败已上传该文件，或者文件名重复<br><br></center>";
          echo "<center><a href='upload.html'>请重新上传</a></center>";
        }
        else
        {
          $file_dest="/var/www/html/cloud/file/$ownerid/".$fnew_name;
          $file_dest=encryptFile($tmpfile, $key,$file_dest);
          $keyhash=hash('sha256',$key);

          //用服务器的公钥加密上传文件时用的对称密钥
          $keyc=pkCipher($key);

          //先用服务器的私钥对文件的hash进行签名，然后用用户的私钥再签名
          $fhash=hash('sha256',$databin);
          $ffhash='0x'.$fhash;
          $fsign=sign($fhash,$ownerid,$key);
          $khash='0x'.$keyhash;

          $fid=mt_rand(1,100000);
          while(($pdo->query("select * from filesystem where $fid=fid"))->rowCount()!=0)
          {
            $fid=mt_rand(1,100000);
          }

          $flive=$_POST['times'];
          $deadtime=$_POST['deadtime'];

          //对$furl进行HMAC和数字签名验证有效性
          $furl="https://websever.com/cloud/up_down/download.php?id=".$fid."&&deadtime=".$deadtime."&&livetimes=".$flive."&&uid=".$ownerid."&&file=".$ffhash;
          $params=substr($furl,strpos($furl,'?'));
          $urlsign=urlsign($params,$key);

          $sql = "INSERT INTO filesystem (fid,uid,fnew_name,forign_name,ftype,fsign,keyhash,fhash,fpost_time)
          VALUES (:fid,:uid,:fnew_name,:forign_name,:ftype,:fsign,:keyhash,:fhash,NOW())";

          // $sqldownload = "INSERT INTO download (furl,fid,deadtime,flive)
          // VALUES (:furl,:fid,:deadtime,:flive)";

            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':fid',$fid);
            $stmt->bindParam(':uid',$ownerid);
            $stmt->bindParam('fnew_name',$fnew_name);
            $stmt->bindParam(':forign_name',$name);
            $stmt->bindParam(':ftype',$type);
            $stmt->bindParam(':fsign',$fsign);
            $stmt->bindParam(':keyhash',$khash);
            $stmt->bindParam(':fhash',$ffhash);

            // $stat=$pdo->prepare($sqldownload);
            // $stat->bindParam(':furl',$furl);
            // $stat->bindParam(':fid',$fid);
            // $stat->bindParam(':deadtime',$deadtime);
            // $stat->bindParam(':flive',$flive);
            $sqldownload = "INSERT INTO download (furl,fid,deadtime,flive,urlsign,keyc)
            VALUES (\"$furl\",$fid,\"$deadtime\",$flive,\"$urlsign\",\"$keyc\")";
            $stat=$pdo->exec($sqldownload);

            if(!$stmt->execute())//&&!$stat->execute()
            {
              // echo $sql;
              print_r($stmt->errorInfo());
              echo "<br><br><center>上传失败！<br><br></center>";
              echo "<center><a href='upload.html'>重试</a></center>";
            }
            else
            {
              echo "<center>上传成功！</center>";
              echo "文件下载地址：".$furl."<br>";
              // echo "文件临时存储的位置: " . $_FILES["file"]["tmp_name"] . "<br>";
              echo "上传文件名: " . $_FILES["file"]["name"] . "<br>";
              // echo $sqldownload;
              echo "文件类型: " . $_FILES["file"]["type"] . "<br>";
              echo "文件大小: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
              echo "<center><a href='upload.html'>点此返回</a></center>";
            }
          }
        }
      }
      else
      {
        echo $ftype[$_FILES["file"]["type"]];
        echo $_FILES["file"]["type"];
        echo "<center>请选择图片或文档！<br><br><a href='upload.html'>点此返回</a></center>";
      }
    }
    else
    {
      echo "<center>请选择小于10MB的图片或文档！<br><br><a href='upload.html'>点此返回</a></center>";
    }

  }

  else
  {
    echo "<center>请选择小于10MB的图片或文档！<br><br><a href='upload.html'>点此返回</a></center>";
  }

  ?>
