<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>云端文件存储系统</title>
  <style>
      #topper{
          background-color:goldenrod;
          font-family: Verdana;
          opacity:0.75;
          text-align:center;
      }
      #left{
          width:250px;
          height:1000px;
          padding:5px;
          opacity:1;
          float:left;
      }
      #section{
          float:center;
          text-align: left;
          padding:5px;
          color: white;
          font-family: Verdana;
          font-size: 30px;
      }
    </style>
</head>

<body style="background-image:url(../1.jpg)">

<div id="topper">
    <span style="float:center; font-size:70px;">RESOURCE SHARING! </span>
    <a href="logout.php" style=" font-size:30px; color:azure; font-family:Verdana; float:right; ">log out</a>
</div>

<div style="opacity:1; height:90px;"></div>
<div id="left"></div>

<div id="section">
    <?php echo "Hello ,". $_POST["username"]."~"; $un=$_POST["username"];
    session_start();
    $_SESSION['user_id']=$row['uid'];
    $_SESSION['username']=$row['username'];
    ?>
    <br>
    <span>You can </span>
     <a href="../up_down/upload.html" style="width:80px; color:gold; font-family:Verdana; float:center "> upload</a>
     <span>files or </span>
     <a href="../up_down/logeddownload.php" style="width:80px;color:gold;font-family:Verdana;float:center">download</a>
     <span> files in the list below ~</span>

</div>

</body>
</html>
