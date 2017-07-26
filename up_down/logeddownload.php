
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>没想好名字的网站</title>
  <body>
    <!-- <body style="background-image:url()"> -->
    <p><?php echo"hkl";?>的下载页面</p>
    <?php
    session_start();
    include "../connect/connect2.php";
    $checkfile="select * from filesystem where uid=8";
    $check=mysqli_query($p,$checkfile);
    if(mysqli_num_rows($check)>0)
    {
      $i=1;
      while($i<=mysqli_num_rows($check))
      {
        $row=mysqli_fetch_assoc($check);
        $name=$row["forign_name"];
        $key=pkDecipher($row["keyc"]);
        $path=downfuc($row,$key);
        echo "$name\n";
        ?>
        <a href=$path>下载</a>
        <?php
        echo "<br>";
        $i+=1;
      }
      echo "<center><a href='c.php'>返回主页面</a></center>";
    }
    else {
      echo "还没有上传过文件";
      echo "</center><a href='c.php'>返回主页面</a></center>";
    }

    ?>
  </body>
</head>
</html>
