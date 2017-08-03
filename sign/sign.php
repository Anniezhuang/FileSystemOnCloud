<?php
function Decipher($ciphertext){
  //用服务器公钥解密$plaintext
  $enc_key=bin2hex(file_get_contents('../publickey'));

  list($extracted_method, $extracted_enc_options, $extracted_iv, $extracted_ciphertext) = explode('$', $ciphertext);
  $plaintext = openssl_decrypt($extracted_ciphertext, $extracted_method, $enc_key, $extracted_enc_options, hex2bin($extracted_iv));
  return $plaintext;
}

function sign($h,$uid){
  //sign by using server private key
  //$enc_key=bin2hex(openssl_pkey_get_private(file_get_contents('server.key')));

  //connect sql
  include ("../connect/connect.php");
  $sql = "SELECT pubkey,privkey FROM user where uid='$uid'";

  foreach($pdo->query($sql) as $row){
    //decrypt user secret keys
    //if(preg_match('/.*$.*$.*$.*/', $row['privkey']) !== 1) {
    /* fprintf(STDERR, "无法解密的密文格式\n");
    exit(1);*/
    $enc_key=Decipher($row["privkey"]);
    // echo $enc_key.'<br><br>3';
  }
  // 解析密文结构，提取解密所需各个字段
  list($extracted_method, $extracted_enc_options, $extracted_iv, $extracted_ciphertext) = explode('$', $enc_key);
  $privUsr = openssl_decrypt($extracted_ciphertext, $extracted_method, $enc_key, $extracted_enc_options, hex2bin($extracted_iv));
  // echo $privUsr.'<br><br>';
  openssl_sign($h, $signature, $privUsr);

  $pdo=null;

  return base64_encode($signature);
}



?>
