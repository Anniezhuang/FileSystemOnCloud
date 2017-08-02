<?php
function urlsign($url,$key)
{
	// $keyhash=hash('sha256',$key);
	$hma=hash_hmac("sha256",$url,$key);
	// $pri=openssl_pkey_get_private(file_get_contents('../server.key'));
	// if(openssl_sign($hma,$out,$pri))return $out;
	return $hma;

}

function downloadtime()
{
	$t=time();
	$time=date("Y-m-d H:i:s",$t);
	mt_srand($t);
	$randomtime=mt_rand();
	$thash=hash('sha1',$randomtime);
	$padding = array('0' =>$time,'1'=>$thash);
	return $padding;
}

 ?>
