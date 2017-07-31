<?php

function decrypt($encrypted,$key){
  if(preg_match('/.*$.*$.*$.*/', $encrypted) !== 1) {
			fprintf(STDERR, "无法解密的密文格式\n");
			exit(1);
	}
  $enc_key=bin2hex($key);
	list($extracted_method, $extracted_enc_options, $extracted_iv, $extracted_ciphertext) = explode('$', $encrypted);
	$pubUsr = openssl_decrypt($extracted_ciphertext, $extracted_method, $enc_key, $extracted_enc_options, hex2bin($extracted_iv));

  return $pubUsr;
}

function verify($file,$s){
    $s=base64_decode($s);
    $pri=openssl_pkey_get_private(file_get_contents('./server.key'));

    //read $h and $uid from $file
    $fp=fopen($file);
    $h=fgets($fp,256);
    $uid=fgets($fp);
    if(feof($fp)){
      flose($fp);
    }

    $pdo = new PDO('mysql:host=127.0.0.1;dbname=ssl', 'root', 'root');
    $sql = "SELECT pubkey FROM user where uid='$uid'";
    foreach($pdo->query($sql) as $row){
  		$usrPub=decrypt($row["pubkey"],$pri);
    }

    if (openssl_verify($h, $s, $usrPub) == 1){
        return true;
    }
    else{
        return false;
    }
}


if(isset($_POST['submit']))
{
  $f=$_FILES['file'];
  $ftemp=$f["tmp_name"];
  $signature=$_FILES['signature'];

  if(verify($ftemp,$signature)){
    echo "签名验证成功，请放心使用文件";
  }
  else{
    echo "签名验证失败";
  }
}

?>
