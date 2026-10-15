<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	if (!function_exists('my_log')) {
		function my_log($mystr, $foldr)
		{
			$str = date('Y-m-d G:i:s') . ' / ' . $mystr . ' / ' . getClientIPAdd() . ' / ' . $_SESSION['uname'];
			$date = date('m_d_Y');
			file_put_contents('./assets/logs/'.$foldr.'/_' . $date . '.txt', $str . PHP_EOL, FILE_APPEND);
		}
	}


	


	function getClientIPAdd()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}
