<?php  
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 
*/
class Webhook extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();

		$this->load->model('BotUserModel');
		$this->load->model('BotProductModel');

		$this->load->library('graph_api');
		$this->load->library('wit_api');
	}

	function index() {
     
		try {
			if (isset($_GET["hub_mode"]) && $_GET["hub_mode"] == "subscribe") {
		        echo ($_GET["hub_challenge"]);
		    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		    	$input_json = file_get_contents('php://input');
		        $input = json_decode($input_json, true);
		        
		        $sender_id = ( isset($input['entry'][0]['messaging'][0]['sender']['id']) ) 
		        				? $input['entry'][0]['messaging'][0]['sender']['id'] 
		        				: FALSE;
		        
		        if ( ! $sender_id ) {
		  
		            echo json_encode(array(
		                'status' => 'fail',
		                'messege' => 'Unauthorized access.'
		            ));
		            return;
		        }

		        $msg_data = $this->get_msg($input, $sender_id);

		        $this->process_request($msg_data);
			    
			} else {
			        echo "whats on browser";
			}
    	}

		catch(Exception $e) {
		}
	}	


	function get_msg($input, $sender_id) {

		$message_echo = ( isset($input['entry'][0]['messaging'][0]['message']['is_echo']) ) ? trim($input['entry'][0]['messaging'][0]['message']['is_echo']) : '';
            
        $message_delivery = ( isset($input['entry'][0]['messaging'][0]['delivery']['mids'][0]) ) ? trim($input['entry'][0]['messaging'][0]['delivery']['mids'][0]) : '';
        
        $message_read = ( isset($input['entry'][0]['messaging'][0]['read']['watermark']) ) ? trim($input['entry'][0]['messaging'][0]['read']['watermark']) : '';
        
        $message_ref = ( isset($input['entry'][0]['messaging'][0]['referral']['ref']) ) ? trim($input['entry'][0]['messaging'][0]['referral']['ref']) : '';
        
        $message_linking = ( isset($input['entry'][0]['messaging'][0]['account_linking']['status']) ) ? trim($input['entry'][0]['messaging'][0]['account_linking']['status']) : '';

        if ($message_echo != '' || $message_delivery != '' || $message_read != '' || $message_ref != '' || $message_linking != '') {
          
            exit();
        }
        
        $received_messege = ( isset($input['entry'][0]['messaging'][0]['message']['text']) ) ? trim($input['entry'][0]['messaging'][0]['message']['text']) : '';
        
        $postback_payload = ( isset($input['entry'][0]['messaging'][0]['postback']['payload']) ) ? trim($input['entry'][0]['messaging'][0]['postback']['payload']) : '';
        
        $quick_postback_payload = ( isset($input['entry'][0]['messaging'][0]['message']['quick_reply']['payload']) ) ? trim($input['entry'][0]['messaging'][0]['message']['quick_reply']['payload']) : '';
        
        $received_location = ( isset($input['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']) ) ? $input['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates'] : FALSE;

        $msg_data = array("platform" => "messenger", "sender_id" => $sender_id);
        
        if ($postback_payload != '') {
            // button click
            $msg_data["type"] = "postback";
            $msg_data["data"] = $postback_payload;
        } else if ($quick_postback_payload != '') {
            // quick button click
            $msg_data["type"] = "postback";
            $msg_data["data"] = $quick_postback_payload;
        } else if ($received_location) {
            // quick button location
            $msg_data["type"] = "location_postback";
            $msg_data["data"] = $received_location;
        } else if ($received_messege != '') {
            $msg_data["type"] = "text";
            $msg_data["data"] = $received_messege;
        } else {
            exit();
        }

        return $msg_data;
	}

	function process_request($msg_data) {

		$sender_id = $msg_data['sender_id'];

		$user = $this->BotUserModel->get_user($sender_id);

		if( ! $user) {

			$data = $this->graph_api->api_user($sender_id);

			$insert_data = array(
				'sender_id' => $sender_id ,
				'fname'		=> $data['first_name'] ,
				'lname'		=> $data['last_name'] ,
				'expected'	=> ''
			);

			$insert = $this->BotUserModel->insert_user($data);
			if ( $insert == TRUE) {
				
				$user = $this->BotUserModel->get_user($sender_id);
			}
		}

		switch ($msg_data["type"]) {

			case 'text':

				$wit_entity = $this->wit_api->get_wit_entity($msg_data['data']);

				if ( isset($wit_entity['entities']['greetings'])) {

    				$greet = "Hello, Welcome to E-Commerce Chat!";
    				$this->graph_api->sendText($sender_id, $greet);
    			}

    			else if ( $wit_entity['entities']['intent'][0]['value'] == "show" ) {

    				if ( isset($wit_entity['entities']['watch'][0]['value'])) {
    					$brand = $wit_entity['entities']['watch'][0]['value'];
    					
    					$data = $this->BotProductModel->get_brand($brand);

    					$this->graph_api->send_watchTemplate($sender_id, $data);	
    				} 
                    else {
                        $this->graph_api->sendText($msg_data["sender_id"], "Showing you some watch");
                    } 
    			}

    			else if ( $wit_entity['entities']['intent'][0]['value'] == "checkout" ) {

                    	$cart = $this->BotProductModel->get_cart($sender_id);

                        $this->graph_api->send_cartTemplate($sender_id, $data);
                        $this->graph_api->send_quickButton($sender_id, "If Confirm, Kindly click below button");
                }
				break;

			case 'postback':
				# code...
				break;
			
			default:
				# code...
				break;
		}
	}

}

?>