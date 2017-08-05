<?php
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

	?>
