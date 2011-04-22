<?php
class tx_odsfacebook_div {
	function __construct($config){
		$this->api_key=$config['api_key'];
		$this->client_id=$config['client_id'];
		$this->client_secret=$config['client_secret'];
		$this->config['user_data'][0]->access_token=$config['access_token'];
	}

	function getLoginLink($backlink){
		$data=array(
			'api_key'=>$this->api_key,
			'connect_display'=>'popup',
			'v'=>'1.0',
			'next'=>$backlink,
			'cancel_url'=>$backlink,
			'fbconnect'=>'true',
			'return_session'=>'true',
// 			'req_perms'=>'read_stream,publish_stream,manage_pages,offline_access',
			'req_perms'=>'read_stream,offline_access',
		);
		return 'http://www.facebook.com/login.php?'.http_build_query($data);
	}

	function isLoggedIn(){
		return true;
	}

	function getAppAccessToken(){
		if(!$this->config['application_data']){
			$data=array(
				'grant_type'=>'client_credentials',
				'client_id'=>$this->client_id,
				'client_secret'=>$this->client_secret,
			);
			$ret=$this->fetch('https://graph.facebook.com/oauth/access_token',$data);

			parse_str($ret,$app);
			$this->config['application_data']=$app;
		}
		return $this->config['application_data']['access_token'];
	}

	function getUserAccessToken(){
		if(!$this->config['user_data'][0]->access_token){
			$data=array(
				'client_id'=>$this->client_id,
				'client_secret'=>$this->client_secret,
				'sessions'=>$this->config['session_key'],
			);
			$ret=$this->fetch('https://graph.facebook.com/oauth/exchange_sessions',$data);

			$user=json_decode($ret);
			$this->config['user_data']=$user;
		}
		return $this->config['user_data'][0]->access_token;
	}

	function fetch_data($script,$data=array(),$method='GET',$page=0){
		/*
			Return:
			false on connection error, decode error or api error
			string on success
		*/
		$ret=false;
		if($this->isLoggedIn()){
			$data['access_token']=$page ? $this->config['pages'][$page]->access_token : $this->getUserAccessToken();
			$url='https://graph.facebook.com/'.$script.($data && $method=='GET' ? '?'.http_build_query($data) : '');

			$response=$this->fetch($url,$data && $method=='POST' ? $data : false);
			if($response){
				$data=json_decode($response);
				if($data){
					$ret=$data;
				}else{
					$this->error='Decode error.';
				}
			}else{
				$this->error='No response.';
			}
		}else{
			$this->error='You are not logged in.';
		}

		return $ret;
	}

	function fetch($url,$post=false,$header=false,$agent=false){
// echo '<i>SEND: '.$url.'</i><br />';var_dump($post,$header,$agent);
		$curl=curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_VERBOSE, false);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		if($agent) curl_setopt($curl, CURLOPT_USERAGENT,$agent);
		if(substr($url,0,6)=='https:'){
			curl_setopt($curl,CURLOPT_PORT,443);
			curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,true);
		}
		if($post){
			curl_setopt($curl,CURLOPT_POST,true);
// 			curl_setopt($curl,CURLOPT_POSTFIELDS,is_array($post) ? http_build_query($post) : $post);
			curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
		}
		if($header){
			curl_setopt($curl,CURLOPT_HTTPHEADER,is_array($header) ? $header : array($header));
		}

//		$file=fopen('php://output','w');
//		curl_setopt($curl, CURLOPT_WRITEHEADER, $file);

		$ret=curl_exec($curl);

		if($ret===false) $this->error=curl_error($curl);

		curl_close($curl);
// echo('<b>RECEIVE: '.$ret.'</b><br/>');
		return $ret;
	}

	function json_decode2($json)
	{ 
		// Author: walidator.info 2009
		$comment = false;
		$out = '$x=';
		
		for ($i=0; $i<strlen($json); $i++)
		{
			if (!$comment)
			{
				if ($json[$i] == '{')        $out .= ' array("';
				else if ($json[$i] == '}')    $out .= '")';
				else if ($json[$i] == ':')    $out .= '"=>"';
				else if ($json[$i] == '"')    $out .= '';
				else if ($json[$i] == ',')    $out .= '","';
				else                         $out .= $json[$i];           
			}
			else $out .= $json[$i];
		}
		eval($out . ';');
		return $x;
	}
}
?>