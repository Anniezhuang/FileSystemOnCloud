<?php
include ("../connect/connect2.php");
include ("/func/downfuc.php");
include ("../sign/pkencrypt.php");

$url=$_SERVER["REQUEST_URI"];
$pass=$_POST["password"];

$splt=explode("&&",$url);
$uid=explode("=",$splt[3])[1];
$fid=explode("=",$splt[0])[1];

$checktime="select * from filesystem where furl=$url limit 1";
$res=mysqli_query($con,$checktime);
$row=mysqli_fetch_assoc($res);

$Psw=hash("sha256",$pass);
if($Psw==$row['keyhash'])
{
  if(stotrtime(time())<stotrtime(ftime))
  {
    if($row["fdownloadnumber"]>0)
    {
       $update="UPDATE download SET flive=[value-1] where furl=$url";
       $up=mysqli_query($con,$update);

       $sql = "SELECT * FROM filesystem where $fid=fid";
       $res=mysqli_query($con,$sql);

       $path1=downfuc($res,$pass);
       $path=downfileinfo($res);
       echo "<center>文件名：".$res["forign_name"]."</center><br>";
       echo "<center><a href=$path1>下载文件</a></center><br>";
       echo "<center><a href=$path[0]>下载文件签名</a></center><br>";//待完成
       echo "<center><a href=$path[1]>下载文件散列值</a></center><br>";//待完成
    }
    else {

        echo "下载次数已用完\n";?>
        <a href="c.html">返回主页</a>
  <?php
}
      }
      else{
        echo "";?>

        <a href="c.html">已过期</a>
  <?php    }

    }
    else {
    echo "密码错误";?>
    <a href="download.php">重新输入</a>
<?php
  }
  ?>
