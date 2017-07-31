<?php
function urlsign($ur,$key)
{
	$keyhash=hash('sha256',$key);
	$hma=hash_hmac("sha256",$ur,$keyhash);
	$pri=openssl_pkey_get_private(file_get_contents('../sign/server.key'));
	if(openssl_sign($hma,$out,$pri))return $out;

}

function downloadtime()
{
	$t=time();
	$time=date("Y-m-d H:i:s",$t);
	$randomtime=mt_srand($time);
	$thash=hash('sha1',$randomtime);
	$padding = array('0' =>$time,'1'=>$thash);
	return $padding;
}

 ?>
