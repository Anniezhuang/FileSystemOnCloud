<html>
<head>
<meta charset="utf-8">
<title>没想好名字的网站</title>
</head>
<body>
<form action="time.php" method="post" >
	<?php
	//echo $_SERVER["REQUEST_URI"];
	$url=$_SERVER["REQUEST_URI"];
	$params=substr($url,strpos($url,'?'));
	echo $params;
	?>
	<br>
  <br>
	<label for="password">提取密码：</label>
	<input type="password" name="password" id="pw" required><br><br>
	<?php
	include ("../connect/connect2.php");
	include ("func/urlsign.php");

	$out=urlsign($params,$_POST['password']);
  $checkurl = "SELECT urlsign FROM download where furl=$url ";
	$result=mysqli_query($con,$checkurl);
  if(mysqli_num_rows($result)>0)
	{
		$row=mysqli_fetch_assoc($result);
		$u=$row["urlsign"];
		if($u==$out)
		{
			?>
			<input type="submit" name="submit" value="提交">
		<?php }
	}
		?>
		<br>
		<!-- <input type="submit" name="submit" value="提交"> -->

</form>
</body>
</html>
