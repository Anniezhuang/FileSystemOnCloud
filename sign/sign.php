<?php
function Decipher($ciphertext){
  //用服务器私钥对称解密$ciphertext
  $enc_key=bin2hex(file_get_contents('../privatekey'));
  list($extracted_method, $extracted_enc_options, $extracted_iv, $extracted_ciphertext) = explode('@', $ciphertext);
  $plaintext = openssl_decrypt($extracted_ciphertext, $extracted_method, $enc_key, $extracted_enc_options, hex2bin($extracted_iv));
  return $plaintext;
}

function sign($h,$uid){
  //connect sql
  include ("../connect/connect2.php");
  $r=mysqli_fetch_object(mysqli_query($con,"SELECT privkey FROM user where uid='$uid'"));
  $privUsr=Decipher($r->privkey);
  openssl_sign($h, $signature, $privUsr);

  return base64_encode($signature);
}

?>
