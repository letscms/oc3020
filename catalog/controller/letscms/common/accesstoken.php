<?php

require_once(DIR_SYSTEM . 'engine/restcontroller.php');

class ControllerLetscmsCommonAccesstoken extends RestController {

	public function create() {
        $json = array('success' => true);
        $this->load->language('ost/common');

		$client_id=$this->config->get('module_restapi_client_id');
	    $client_secret=$this->config->get('module_restapi_client_secret');

       if($json["success"]){
				 
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,HTTPS_SERVER."index.php?route=letscms/restapi/gettoken");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,"grant_type=client_credentials&client_id=".$client_id."&client_secret=".$client_secret);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$server_output = curl_exec ($ch);

				$accessjson=json_decode($server_output);
    
				$json['accesstoken']=$accessjson->access_token;
				$json['expires_in'] = (int)$accessjson->expires_in;

        } else {
            $json["error"]= $error;
            $json["success"]= false;

        }

        /*} else {
            $json["error"]		= $this->lanagugae->get('error_method_post');
            $json["success"]	= false;

        }*/

        $this->sendResponse($json);

    }

}
