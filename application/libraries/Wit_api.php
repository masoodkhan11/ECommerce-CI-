<?php  
/**
* 
*/
class Wit_api {
		
	function __construct()
	{
		
	}

	function get_wit_entity($user_input){

		$user_text = urlencode($user_input);
		$witURL = 'https://api.wit.ai/message?v=10/07/2017&q='.$user_text ;

		$ch = curl_init();
		$header = array('Authorization: Bearer PEVVZM57FCT5SN2CID7HKPJSAWDOMAPA');

		curl_setopt($ch, CURLOPT_URL, $witURL);
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

		$server_output = curl_exec ($ch); 

		curl_close ($ch);

		$output = json_decode($server_output, true);

		return $output;
	}
}

?>