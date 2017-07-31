<?php
function pkDecipher($encrypted){
//用服务器私钥解密

$pi_key=openssl_pkey_get_public(file_get_contents("/usr/lib/ssl/demoCA/server.key"));
openssl_private_decrypt(base64_decode($encrypted),$decrypted,$pi_key);
return $decrypted;

} ?>
