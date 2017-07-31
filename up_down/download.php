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
<form action="" method="post" >
	<?php
	//echo $_SERVER["REQUEST_URI"];
	$url=$_SERVER["REQUEST_URI"];
	// $params=substr($url,strpos($url,'?'));
	// echo $params;
	?>
	<br>
  <br>
	<?php
	$splt=explode("&&",$url);
	$fid=explode("=",$splt[0][1]);
	$curl="SELECT * from filesystem where fid=$fid limit 1";
	$cc=mysqli_connect($con,$curl);
	$r=mysqli_fetch_assoc($cc);
	$out=urlsign($params,pkDecipher($r["keyc"]));

  $checkurl = "SELECT urlsign FROM download where fid=$fid ";
	$result=mysqli_query($con,$checkurl);
  if(mysqli_num_rows($result)>0)
	{
		$row=mysqli_fetch_assoc($result);
		$u=$row["urlsign"];
		if($u==$out)
		{
			$c="select * from filesystem where fid=$fid";
			$up=mysqli_query($con,$c);
		  $res=mysqli_fetch_assoc($up);
			$t=time();
			$ti=date("Y-m-d H:i:s",$t);

			$rule=$res['fpost_time']+3600*24;
		  if($ti<$rule)
			  {
					$pass=pkDecipher($r["keyc"]);
					$path1=downfuc($res,$pass);
					$path=downfileinfo($res);
					echo "<center>文件名：".$res["forign_name"]."</center><br>";
					echo "<center><a href=$path1>文件</a></center><br>";
					echo "<center><a href=$path[0]>文件签名</a></center><br>";
					echo "<center><a href=$path[1]>文件散列值</a></center><br>";

			?>
			<input type="submit" name="submit" value="提交">
		<?php }
		else{
			echo "已过期";?>
			<a href="../login/c.php">返回主页</a>
	<?php 	}

	}else {
		echo "url有误";

		?>
		<a href="../login/c.php">返回主页</a>
		<?php }
	}else{
		?><a href="download.php">重新输入</a>
	<?php } ?>
		<br>
		<!-- <input type="submit" name="submit" value="提交"> -->


</form>
</body>
</html>
