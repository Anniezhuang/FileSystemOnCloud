<?php
function urlsign($ur,$key)
{
	$keyhash=hash('sha256',$key);
	$hma=hash_hmac("sha256",$ur,$keyhash);
	$pri=openssl_pkey_get_private(file_get_contents('/usr/lib/ssl/demoCA/server.key'));
	if(openssl_sign($hma,$out,$pri))return $out;

}


 ?>
