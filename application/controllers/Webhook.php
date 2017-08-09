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
		$this->load->model('BotCartModel');
		$this->load->model('BotOrderModel');

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
				$expected = $user->expected;

				if ($expected != '') {
					switch ($expected) {

						case 'address':
							$this->BotUserModel->update_expected($sender_id, '');

							$insertdata = array(
								'user_id' 	=> $user->id ,
								'name'		=> $user->fname . ' ' . $user->lname ,
								'email'		=> '' ,
								'address'	=> $msg_data['data']
							);
							$insert_id = $this->BotOrderModel->insert_order($insertdata);

							if ($insert_id) {

								$cart = $this->BotCartModel->get_cart($sender_id);
								foreach ($cart as $value) {
									$data = array(
										'order_id'	 => $insert_id ,
										'product_id' => $value->product_id ,
										'quantity'	 => '1'
									);

									$this->BotOrderModel->insert_orderDetails($data);

									$this->BotCartModel->delete_product($value->product_id);
								}
								$this->BotUserModel->update_expected($sender_id, "email");
								$this->graph_api->sendText($sender_id, "Provide Email");
							}
					
							break;
						
						case 'email':
							$this->BotUserModel->update_expected($sender_id, '');

							$email = $msg_data['data'];
							$user_id = $user->id;
							$this->BotOrderModel->update_order($user_id, $email);

							$this->graph_api->sendText($sender_id, 'Have a Nice Day');
							break;
					}
				
				}

				else
				{
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

	                	$cart = $this->BotCartModel->get_cart($sender_id);
	                	
	                	if ($cart) {
	                		$this->graph_api->send_cartTemplate($sender_id, $cart);
	                    	$this->graph_api->send_quickButton($sender_id, "If Confirm, Kindly click below button");	
	                	} else {
	                		$this->graph_api->sendText($sender_id, "cart is Empty");
	                	}
	                        
	                }
	            }    
				break;

			case 'postback':
				$fb_input = explode("/", $msg_data["data"]); 

				switch ($fb_input[0]) {
					case 'info':
						$data = $this->BotProductModel->get_product($fb_input[1]);
						$text = $this->infoText($data);

						$this->graph_api->sendText($sender_id, $text);

						break;
					
					case 'cart':
						$data = $this->BotProductModel->get_product($fb_input[1]);
						$insertdata = array(
							'sender_id' 	=> $sender_id ,
							'product_id'	=> $data->id ,
							'product_name'	=> $data->name ,
							'product_price'	=> $data->price ,
							'product_image'	=> $data->image
						);

						$insert = $this->BotCartModel->insert_cart($insertdata);

						if ($insert ==  TRUE) {

							$text = $this->cartText($data);
							$this->graph_api->sendText($sender_id, $text);
						}

						break;

					case 'remove':
						$data = $this->BotCartModel->get_cartproduct($fb_input[1]);

						$delete = $this->BotCartModel->remove_product($fb_input[1]);
						if ($delete == TRUE) {
							
							$text = $this->removeText($data);
							$this->graph_api->sendText($sender_id, $text);
						}

						break;

					case 'proceed':

						$this->BotUserModel->update_expected($sender_id, "address");
						$this->graph_api->sendText($sender_id, "Provide Delivery Address");

						break;
				}
				break;
			
			default:
				# code...
				break;
		}
	}

	function infoText($data) {

		$text = 'Product Info :
Price :  ' . $data->price . '
Description :  '.$data->description ;

		return $text;
	}

	function cartText($data) {

		$text = 'successfully Added to Cart :

Prdct Id : ' .$data->id . '
Prdct Name : ' . $data->name . '
Price : ' . $data->price . '
Dscrptn : ' .$data->description;

		return $text;

	}

	function removeText($data) {

		$text = 'Product removed from CART:

Product : ' .$data->product_name. '
Price : ' .$data->product_price ;

		return $text;
	}

}

?>