# <center>云端文件存储系统的关键技术</center> #
<br>

## 1. 判断密码强度 ##
```php
	//检查密码强度
	function filterPsw($field)
	{
	  $strength=0;    //表示密码强度
	  $length = strlen($field);    //密码长度
	  //长度小于7，直接判定为弱口令
	  if($length<7) return $strength;
	  if($length >= 8 && $length <= 15) $strength += 10;
	  if($length >= 16 && $length <=36) $strength += 20;
	  // 判断是否全为大写字母或全为小写字母
	  if(strtolower($field) != $field or strtoupper($field) != $field)    $strength += 10;
	  // get the numbers in the password
	  preg_match_all('/[0-9]/', $field, $numbers);
	  $strength += count($numbers[0]);
	  /*** check for special chars ***/
	  preg_match_all('[|!@#$%&*//=?,;.:-_+~^]', $field, $specialchars);
	  $strength += sizeof($specialchars[0]);
	  /*** get the number of unique chars ***/
	  $chars = str_split($field);  //str_split change string to array
	  $num_unique_chars = sizeof( array_unique($chars) );  //remove duplicate values from an array
	  $strength += $num_unique_chars * 2;
	  /*** strength is a number 1-10; ***/
	  $strength = $strength > 99 ? 99 : $strength;
	  $strength = floor($strength / 10 + 1);
	
	  return $strength;
	}
```
## 2. 生成用户公私钥 ##
```php
	function genKeys(){
	    $config=array(
	      "digest_alg"=>"sha256",
	      "private_key_bits"=>1024,
	      "private_key_type"=>OPENSSL_KEYTYPE_RSA
	    );
	    $res=openssl_pkey_new($config);
	    openssl_pkey_export($res,$privKey);
	    $pubKey=openssl_pkey_get_details($res);
	    $pubKey=$pubKey["key"];
	    $keys=array("SK"=>$privKey,"PK"=>$pubKey);
	    return $keys;
	}
```
## 3. 生成服务器的公私钥 ##
- 原来是使用自签发的server.key和server.crt作为服务器的公私钥，但考虑到证书有效期一般为5年，不可能5年后重新对所有文件的对称密钥重新加解密，于是生成服务器的公私钥
- 需要保证服务器公私钥的绝对安全
## 4. 用户公私钥的安全存储 ##
- 服务器的私钥作为对称密钥，对用户的公私钥加密，然后把用户的公私钥存到数据库中
```php
		function pkCipher($plaintext){
		  include ("../connect/connect.php");
		  $method="aes-256-cbc";
		  $enc_key=bin2hex(file_get_contents('../privatekey'));
		  $enc_options=0;
		  $iv_length=openssl_cipher_iv_length($method);
		  $iv=openssl_random_pseudo_bytes($iv_length);
		  //encrypt user private key and user public key
		  $c=openssl_encrypt($plaintext,$method,$enc_key,$enc_options,$iv);
		  // 定义“私有”的密文结构
		  $saved_c = sprintf('%s@%d@%s@%s', $method, $enc_options, bin2hex($iv), $c);
		  return $saved_c;
		}
```
## 5. 存储用户登录密码 ##
```php
    //密码加盐
    $salt=openssl_random_pseudo_bytes(1024);
    $saltedPsw=hash("sha256",($psw.$salt));
```
## 6. 判断用户登录状态 ##
```php
	session_start();
	$_SESSION['user_id']=$row['uid'];
	$_SESSION['username']=$row['username'];
	```
## 7. 文件上传判断 ##
- 具体在upload.php
- 文件需小于10MB
- 只能上传图片和office文档
- 判断文件类型和后缀名是否一致，不一致不允许上传，匿名用户不允许上传
```php
		if (isset($ftype[$type])&& ($size < 10000000) 
			&& isset($_SESSION["user_id"])
		    &&($ftype[$type]==substr(strrchr($name,"."),1)
			||($ftype[$type]=="jpeg"&&substr(strrchr($name,"."),1)=="jpg")))
		{
		
		}
```

- 文件类型是图片的，重新生成图片，防止图片内容加入其它东西
```php
		//重新生成图片
		function rebuild($tmpfile,$name)
		{
		  if(substr(strrchr($name,"."),1)=="bmp")
		  {
		    try {
		      $im=imagecreatefromwbmp($tmpfile);
		      imagewbmp($im, $tmpfile);
		      imagedestroy($im);
		
		    } catch (Error $e) {
		      echo "<center>文件类型出错</center>";
		      echo "<center><a href='../index/index.php'>点此返回</a></center>";
		    }
		
		  }
		  if(substr(strrchr($name,"."),1)=="png")
		  {
		    try {
		      $im = imagecreatefrompng($tmpfile);
		      imagepng($im, $tmpfile);
		      imagedestroy($im);
		    } catch (Error $e) {
		      echo "<center>文件类型出错</center>";
		      echo "<center><a href='../index/index.php'>点此返回</a></center>";
		    }
		  }
		  if(substr(strrchr($name,"."),1)=="jpeg"||substr(strrchr($name,"."),1)=="jpg")
		  {
		    try {
		      $im=imagecreatefromjpeg($tmpfile);
		      imagejpeg($im, $tmpfile);
		      imagedestroy($im);
		    } catch (Error $e) {
		      echo "<center>文件类型出错</center>";
		      echo "<center><a href='../index/index.php'>点此返回</a></center>";
		      echo $e->getMessage();
		    }
		
		  }
		  if(substr(strrchr($name,"."),1)=="gif")
		  {
		    try {
		      $im = imagecreatefromgif($tmpfile);
		      imagegif($im, $tmpfile);
		      imagedestroy($im);
		    } catch (Error $e) {
		      echo "<center>文件类型出错</center>";
		      echo "<center><a href='../index/index.php'>点此返回</a></center>";
		    }
		  }
		}
```
## 8. 文件加密 ##
```php
	define('FILE_ENCRYPTION_BLOCKS', 10000);
	function encryptFile($source, $key, $dest)
	{
	    $key = substr(sha1($key, true), 0, 16);
	    $iv = openssl_random_pseudo_bytes(16);
	
	    $error = false;
	    if ($fpOut = fopen($dest, 'a')) {
	        // Put the initialzation vector to the beginning of the file
	        fwrite($fpOut, $iv);
	        if ($fpIn = fopen($source, 'rb')) {
	            while (!feof($fpIn)) {
	                $plaintext = fread($fpIn, 16 * FILE_ENCRYPTION_BLOCKS);
	                $ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
	                // Use the first 16 bytes of the ciphertext as the next initialization vector
	                $iv = substr($ciphertext, 0, 16);
	                fwrite($fpOut, $ciphertext);
	            }
	            fclose($fpIn);
	        } else {
	            $error = true;
	        }
	        fclose($fpOut);
	    } else {
	        $error = true;
	    }
	
	    return $error ? false : $dest;
	}
```

## 9. 文件解密 ##
```php
	define('FILE_ENCRYPTION_BLOCKS', 10000);
	function decryptFile($source, $key, $dest)
	{
	    $key = substr(sha1($key, true), 0, 16);
	    $error = false;
	    if ($fpOut = fopen($dest, 'a')) {
	        if ($fpIn = fopen($source, 'rb')) {
	            // Get the initialzation vector from the beginning of the file
	            $iv = fread($fpIn, 16);
	            while (!feof($fpIn)) {
	                // we have to read one block more for decrypting than for encrypting
	                $ciphertext = fread($fpIn, 16 * (FILE_ENCRYPTION_BLOCKS + 1));
	                $plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
	                // Use the first 16 bytes of the ciphertext as the next initialization vector
	                $iv = substr($ciphertext, 0, 16);
	                fwrite($fpOut, $plaintext);
	            }
	            fclose($fpIn);
	        } else {
	            $error = true;
	        }
	        fclose($fpOut);
	    } else {
	        $error = true;
	    }
	
	    return $error ? false : $dest;
	}
```

## 10. 使用服务器的公钥对文件上传的随机对称密钥加密 ##
- 生成随机密钥，文件使用该密钥进行对称加密，只存储文件密文
```
		function randomkey($length=8)
		{
			$key="";
		  $pattern = '1234567890abcdefghijklmnopqrstuvwxyz
		  ABCDEFGHIJKLOMNOPQRSTUVWXYZ,./&amp;l
		  t;&gt;?;#:@~[]{}-_=+)(*&amp;^%$£!';    //字符池
		    for($i=0; $i<$length; $i++)
		    {
		      $key .= $pattern{mt_rand(0,35)};    //生成php随机数
		    }
		    return $key;
		  }
```
- 服务器的公钥对随机密钥加密
```php
		function pkCipher($plaintext){
		//用服务器公钥加密
		
		$pub_key=openssl_pkey_get_public(file_get_contents("../publickey"));
		openssl_public_encrypt($plaintext,$encrypted,$pub_key);
		$encrypted=base64_encode($encrypted);//因为加密后是乱码,所以base64一下
		return $encrypted;
		}
```

## 11. 使用服务器的私钥对文件的随机对称密钥解密 ##
```php
		function pkDecipher($encrypted)
		{
			//用服务器私钥解密
			$pi_key=openssl_pkey_get_private(file_get_contents("../privatekey"));
			openssl_private_decrypt(base64_decode($encrypted),$decrypted,$pi_key);
			return $decrypted;
		}
		```
## 12. 使用用户的私钥对文件的哈希值签名 ##
- 首先使用服务器的私钥作为对称密钥，解密用户的私钥
```php
		function Decipher($ciphertext){
		  //用服务器私钥对称解密$ciphertext
		  $enc_key=bin2hex(file_get_contents('../privatekey'));
		  list($extracted_method, $extracted_enc_options, $extracted_iv, $extracted_ciphertext) = explode('@', $ciphertext);
		  $plaintext = openssl_decrypt($extracted_ciphertext, $extracted_method, $enc_key, $extracted_enc_options, hex2bin($extracted_iv));
		  return $plaintext;
		}
```
- 使用用户的私钥对文件的hash签名
```php
		function sign($h,$uid)
		{
		  //connect sql
		  include ("../connect/connect2.php");
		  $r=mysqli_fetch_object(mysqli_query($con,"SELECT privkey FROM user where uid='$uid'"));
		  $privUsr=Decipher($r->privkey);
		  openssl_sign($h, $signature, $privUsr);
		
		  return base64_encode($signature);
		}
```
## 13. 对文件url进行hmac认证 ##
- 生成用户分享文件的url
- url有效期24小时
- 对url进行hmac认证，hmac的key为文件上传时的随机对称密钥
```php
		function urlsign($url,$key)
		{
			$hma=hash_hmac("sha256",$url,$key);
			return $hma;
		}
```
## 14. 验证文件的完整性 ##
- 具体见文件夹*sign*中的*verify.php*
- 匿名用户可下载文件，文件的哈希值，文件的签名
- 用户可通过上传上述三个文件进行完整性的验证
- 验证用户下载的文件为原文件，并且文件的数字签名有效
- 首先，需要用服务器的私钥作为对称密钥，对上传该文件的用户的公钥进行解密
- 接着，使用用户公钥验证文件签名的正确性
- 同时，还需验证文件是否被篡改，即比较文件哈希值的不变
## 15. 防止sql注入
```php
	$sql = "INSERT INTO filesystem (fid,uid,fnew_name,forign_name,ftype,fsign,keyhash,fhash,fpost_time,keyc)
	VALUES (:fid,:uid,:fnew_name,:forign_name,:ftype,:fsign,:keyhash,:fhash,NOW(),:keyc)";
	
	$stmt=$pdo->prepare($sql);
	$stmt->bindParam(':fid',$fid);
	$stmt->bindParam(':uid',$ownerid);
	$stmt->bindParam('fnew_name',$fnew_name);
	$stmt->bindParam(':forign_name',$name);
	$stmt->bindParam(':ftype',$type);
	$stmt->bindParam(':fsign',$fsign);
	$stmt->bindParam(':keyhash',$keyhash);
	$stmt->bindParam(':fhash',$fhash);
	$stmt->bindParam(':keyc',$keyc);
	```
## 16. 数据库展示 ##
- download表
![](https://github.com/kjAnny/FileSystemOnCloud/blob/master/db_table/download%E8%A1%A8.JPG?raw=true)
- filesystem表
![](https://github.com/kjAnny/FileSystemOnCloud/blob/master/db_table/filesystem%E8%A1%A8.JPG?raw=true)
- user表<br>
![](https://github.com/kjAnny/FileSystemOnCloud/blob/master/db_table/user%E8%A1%A8.JPG?raw=true)
