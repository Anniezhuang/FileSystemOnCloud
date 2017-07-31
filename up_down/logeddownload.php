
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>云端文件存储系统</title>
  <body>
    <?php
    session_start();
    $name=$_SESSION['username'];echo "$name<p>的下载页面</p>";?>

  <?php
   include ("../connect/connect2.php");
   include ("../sign/pkdecrypt.php");
  //  include ("func/downfuc.php");
  function downfuc($fileinfo,$key)
  {
    include ("decryptFile.php");

    if(!file_exists("/var/www/html/cloud/download/"))
    {
      mkdir("/var/www/html/cloud/download/");
    }

    $source="/var/www/html/cloud/file/".$fileinfo["uid"]."/".$fileinfo["fnew_name"];
    $dest="/var/www/html/cloud/download/".$fileinfo["forign_name"];
    $dest=decryptFile($source, $key, $dest);

    $new_url="https://websever.com/cloud/download/".$fileinfo["forign_name"];

    return $new_url;

  }
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
    ?>
    <a href="share.php" fid=$row["fid"] fhash=$row["fhash"] uid=$row["uid"] >分享</a>
    <a href=$path>下载</a>
    <?php
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
