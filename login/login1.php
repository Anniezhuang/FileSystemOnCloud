<?php
include ("../connect/connect2.php");
//session_start();

header("Content-Type:text/html;charset=utf8");
$username=$_POST['username'];
$password=$_POST['password'];
// if(!isset($_SESSION['user_id']))

$sql = "SELECT * FROM user ";
$result=mysqli_query($con,$sql);
if(mysqli_num_rows($result)>0)
{
  $i=1;
  while($i<=mysqli_num_rows($result))
  {
    $row=mysqli_fetch_assoc($result);
    $u=$row["username"];
    $hash=$row["psw"];//密码+salt的hash结果
    $salt=$row["salt"];
    if($u==$username)
    {
      $test=hash("sha256",($password.$salt));
      if($hash==$test)
      {
        echo "\n恭喜成功登录，".$row["username"];
        //  $_SESSION['user_id']=$row['uid'];
        //  $_SESSION['username']=$row['username'];

        //  setcookie('user_id',$row['uid'],time()+3600);
        //  setcookie('username',$row['username'],time()+3600);
        //  echo $_COOKIE["user_id"]."and".$_COOKIE["username"];
        include ("loginsuccess.php");
        exit;
      }
      else
      {
        echo "朋友啊你输错密码了，赶紧想想密码";
        include ("login.php");
        //  echo "<a href="login.php">重新输入</a>";
        exit;
      }
    }
    $i++;
  }
}

echo "\nsorry.你还是先注册吧，用户名根本不存在的";
//echo "<a href="register.php">注册</a>";
include ("register.php");
exit;

// else {
//   echo "$username";
//   echo "已登录";
//   include ("loginsuccess.php");
//   //href
// }
