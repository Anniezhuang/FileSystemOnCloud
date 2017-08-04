<?php
function decrypt($encrypted,$key){
  // if(preg_match('/.*@.*@.*@.*/', $encrypted) !== 1) {
  //   fprintf(STDERR, "无法解密的密文格式\n");
  //   exit(1);
  // }
  $enc_key=bin2hex($key);
  list($extracted_method, $extracted_enc_options, $extracted_iv, $extracted_ciphertext) = explode('@', $encrypted);
  $pubUsr = openssl_decrypt($extracted_ciphertext, $extracted_method, $enc_key, $extracted_enc_options, hex2bin($extracted_iv));

  return $pubUsr;
}

function verify($fhash,$sign,$hashverify){
  include '../connect/connect2.php';

  $sign=base64_decode($sign);
  $serverprikey=file_get_contents('../privatekey');

  //read $h and $uid from $fhash
  $fp=fopen($fhash,"r");
  $h=fread($fp,64);
  $uid=fread($fp,filesize($fhash)-64);

  $r=mysqli_fetch_object(mysqli_query($con,"SELECT pubkey FROM user where uid='$uid'"));
  // echo $h;
  // echo $r->pubkey;
  // echo "<br>uuuid=".$uid;
  $usrPub=decrypt($r->pubkey,$serverprikey);
  if (openssl_verify($h, $sign, $usrPub) == 1&&$h=$hashverify){
    return true;
  }
  else{
    return false;
  }
}


if(isset($_POST['submit']))
{
  $fhash=$_FILES['filehash'];
  $fhashtmp=$fhash["tmp_name"];

  $signtem=fopen($_FILES['signature']['tmp_name'],"rb");
  $signature=fread($signtem,$_FILES['signature']['size']);
  fclose($signtem);

  $tmpfile=$_FILES['file']['tmp_name'];
  $filetem=fopen($tmpfile,"rb");
  $data=fread($filetem,$_FILES['file']['size']);
  fclose($filetem);
  $hashverify=hash('sha256',$data);


  if(verify($fhashtmp,$signature,$hashverify)){
    echo "签名验证成功，请放心使用文件";
  }
  else{
    echo "签名验证失败";
  }
}

?>
