<?php
require_once(DIR_SYSTEM . 'engine/restcontroller.php');

class ControllerLetscmsRestapi extends RestController {

    /*Get Oauth token*/
    public function getToken() {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            $this->config->set('config_error_display', 0);

            $this->response->addHeader('Content-Type: application/json');

            /*check rest api is enabled*/
            if (!$this->config->get('module_restapi_status')) {
                $json["error"] = 'API is disabled. Enable it!';
            }

            if(isset($json["error"])){
                echo(json_encode($json));
                exit;
            }

            $requestjson = file_get_contents('php://input');
            $post = json_decode($requestjson, true);
            //print_r($post);die;
            $oldToken = isset($post['old_token']) ? $post['old_token'] : null;
            $oldTokenData = null;
            $this->load->model('letscms/restapi');

            if(!empty($oldToken)){
                $oldTokenData = $this->model_letscms_restapi->loadOldToken($oldToken);
            }

            $server = $this->getOauthServer();
            $token = $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->getParameters();

            if(!empty($oldTokenData)){
                $this->model_letscms_restapi->loadSessionToNew($oldTokenData['data'], $token['access_token']);
                $this->model_letscms_restapi->deleteOldToken($oldToken);
            }

            //clear token table
            $this->clearTokensTable($token['access_token'], session_id());

            unset($token['scope']);
            $token['expires_in'] = (int)$token['expires_in'];

            $this->sendResponse($token);

        }
    }

    /*
    * SESSION FUNCTIONS
    */
    public function session() {

        $this->autherisationCheck();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            //get session details
            $this->getSessionId();
        }
    }

    /*
    * Get current session id
    */
    public function getSessionId() {

        $json = array('success' => true);

        $json['data'] = array('session' => session_id());

        $this->sendResponse($json);
    }
  }
