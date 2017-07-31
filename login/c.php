<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>云端文件存储系统</title>
  <style>
  ul{
  position:absolute;
  right:100px;
  list-style-type:none;
  margin:10;
  padding:0;
  overflow:hidden;
}

li{
  float:left;
}

a:link,a:visited{
  display:block;
  width:120px;
  font-weight:bold;
  color:#54FF95;
  background-color:;
  text-align:center;
  padding:4px;
  text-decoration:none;
  text-transform:uppercase;
}
a:hover,a:active{
background-color:#FFC125;}


#content{
  position: relative;
  left: 400px;
  top: 50px;
}
</style>
</head>
<body style="background-image:url(../1.jpg)">
  <div id="container">
  <div >
      <h1 style="line-height:50px;font-family:verdana; color:#EEB422; font-size:50px; text-align:center">文件共享大法好</h1>
        <hr width="100%" >
        </div>
        <ul>
        <li><a href="../register/register.php" style="color:white;font-size:20px">注册</a>

        <br>
        <li><a href="../up_down/download.php" style="color:white;font-size:20px">下载</a>
        <br>
        <br>
        <li><a href="../sign/verify.php" style="color:white;font-size:20px">验证签名</a><br>
         <div id="footer" style="clear:both;text-align:center;color:#EEB422">
          出品：黄连庄</div>
        </div>

      <br>

      <div id="content" style=" font-size:35px;background-color:white-space;height:500px;width:500px;float:center;color:#EEB422">

  <fieldset width="300px">
    <legend>用户登录</legend>
<form border="1" border-color="#00CCCC"  name="login" method="post" action = "login1.php">
    <p style="text-align:center">
        用户名 <input id ='username' name="username" type="text"  required/>
    </p>
    <p style="text-align:center">
        密码   <input id ='password' name="password" type="password" class="input" required/>
    </p>
        <p style="text-align:center">
        <input  width="100px" height="50px" type= 'image' src="../2.jpg" name="submit" value="提交"/>
    </p>
</form>
</fieldset>
      </div>
