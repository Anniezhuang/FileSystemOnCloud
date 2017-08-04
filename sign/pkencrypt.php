<?php
function pkCipher($plaintext){
//用服务器公钥加密

$pub_key=openssl_pkey_get_public(file_get_contents("../publickey"));
openssl_public_encrypt($plaintext,$encrypted,$pub_key);
$encrypted=base64_encode($encrypted);//因为加密后是乱码,所以base64一下
return $encrypted;
}


 ?>
