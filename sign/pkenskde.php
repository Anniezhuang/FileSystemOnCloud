<?php
function pkCipher($plaintext){
  //用服务器公钥加密$plaintext
  $method="aes-256-cbc";
  $enc_key=bin2hex(openssl_pkey_get_public('../publickey'));
  $enc_options=0;
  $iv_length=openssl_cipher_iv_length($method);
  $iv=openssl_random_pseudo_bytes($iv_length);
  //encrypt user private key and user public key
  $c=openssl_encrypt($plaintext,$method,$enc_key,$enc_options,$iv);
  // 定义“私有”的密文结构
  $saved_c = sprintf('%s$%d$%s$%s', $method, $enc_options, bin2hex($iv), $c);
  return $saved_c;
}

function pkDecipher($ciphertext){
  //用服务器公钥解密$plaintext
  $enc_key=bin2hex(openssl_pkey_get_public('../publickey'));
  list($extracted_method, $extracted_enc_options, $extracted_iv, $extracted_ciphertext) = explode('$', $ciphertext);
  $plaintext = openssl_decrypt($extracted_ciphertext, $extracted_method, $enc_key, $extracted_enc_options, hex2bin($extracted_iv));
  return $plaintext;
}
?>
