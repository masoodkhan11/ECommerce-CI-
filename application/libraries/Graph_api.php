<?php

/**
* Facebook graph api for messenger chatbot
*/
class Graph_api {
	
	function __construct()
	{
		$this->access_token = 'EAACZAXPhpGGUBADhFmtKGme4ZAqIXZA05b4iH13sIUBUcZAVBXs44CDLG1rMZBOSRK2fMjH9X92czRTuZBZAxW3bOPE0T3JHoTm4hwt8yLAd4Rxuf4qnC9dMy3BsEMJqienrDvkHQaEG0nBGWPse0JULlzqM5j0kyaz7piGMqKqoI1xFZC99mIgT';
	}

	function api_user($sender_id) {
        $url = "https://graph.facebook.com/v2.6/" . $sender_id . "?fields=first_name,last_name&access_token=" .$this->access_token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result, TRUE);

        return $obj;
	}

	function api_call($data)
	{
		$url = "https://graph.facebook.com/v2.6/me/messages?access_token=" . $this->access_token;
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

	    $result = curl_exec($ch); // user will get the message

	    // error_log($result);
	}

	function sendText($sender_id, $text, $quick_replies=FALSE)
	{
		if ( ! $quick_replies) {
			$data = array(
		        "recipient" => array(
		            "id" => $sender_id
		        ),
		        "message" => array(
		            "text" => $text
		        )
		    );
		} else {
			$data = array(
	        	"recipient" => array(
	            	"id" => $sender_id
	        	),
		        "message"		=> array(
		        	"text" 			=> $text ,
	            	"quick_replies" => $quick_replies
	        )
	    );
	}
	    
	    
	    $this->api_call($data);
	}

	function sendCarousel($sender_id, $elements)
	{
		$data = array (
	  		'recipient' => array (
	    		'id' => $sender_id,
	    	),
	  		'message' => array (
	    		'attachment' => array (
	      			'type' 		=> 'template',
	      			'payload'	=> array (
	        			'template_type' => 'generic',
	        			'elements'		=> $elements,
					),
				),
			),
		);

	    $this->api_call($data);
	}

}