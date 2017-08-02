
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>云端文件存储系统</title>
  <body>
    <?php
    session_start();
    include ("../connect/connect2.php");
    include ("../sign/pkdecrypt.php");
    include ("func/downfuc.php");

    $name=$_SESSION['username'];
    echo "<center><p>".$name."的文件列表</p></center>";

    $uid=$_SESSION['user_id'];
    $checkfile="select * from filesystem where uid=$uid";
    $check=mysqli_query($con,$checkfile);
    if(mysqli_num_rows($check)>0)
    {
      $i=1;
      while($i<=mysqli_num_rows($check))
      {

        $row=mysqli_fetch_assoc($check);
        $name=$row["forign_name"];
        echo "$i\n";
        echo "$name\n";
        $key=pkDecipher($row["keyc"]);
        $path=downfuc($row,$key);

        $fid=$row["fid"];
        $fhash=$row["fhash"];
        $uuid=$row["uid"];

        echo "<a href=\"share.php?fid=$fid&&fhash=$fhash&&uid=$uuid\">分享</a>
        <a href=$path>下载</a>";

        echo "<br>";
        $i+=1;
      }
      //<a href='../login/c.php'>返回主页面</a>
    }
    else {
      echo "还没有上传过文件";
      //  echo "<a href='c.php'>返回主页面</a>";
    }

    ?>
  </body>
</head>
</html>
