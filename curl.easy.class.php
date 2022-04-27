<?php
class EasyCurl{
	var $ch;
	var $cookies;
	function __construct(){
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_HEADER, 0);
		curl_setopt($this->ch, CURLOPT_VERBOSE, 0);
		//curl_setopt($this->ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:99.0) Gecko/20100101 Firefox/99.0');
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($this->ch, CURLOPT_COOKIESESSION, 1);
		curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, [$this, 'grabCookies']);
	}
	function __destruct(){
		curl_close($this->ch);
	}
	function post($url, $post, $header=[]){
		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_URL, $url);
		$resp = curl_exec($this->ch);
		$stat = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		return [$stat, $resp];
	}
	function get($url, $header=[]){
		curl_setopt($this->ch, CURLOPT_POST, 0);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_URL, $url);
		$resp = curl_exec($this->ch);
		$stat = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		return [$stat, $resp];
	}
	function grabCookies($ch, $line) {
		if(stripos($line, 'set-cookie')>-1){
			preg_match_all('/^Set-Cookie:\s*([^;=\r\n]*)=([^;\r\n]*)/i', $line, $result);
			$i=0;$prev='';
			foreach ($result as $key => $value) {
				if($i==1){
					$prev = $value[0];
				}
				if($i==2){
					$this->cookies[$prev] = $value[0];
					$i=0;
				}else{
					$i++;
				}
			}
		}
		return strlen($line);
	}
	function cookieString(){
		$output = '';
		foreach ($this->cookies as $key => $value) {
			if($output!=''){$output .= '; ';}
			$output .= $key.'='.$value;
		}
		return $output;
	}
}