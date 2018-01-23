<?php
	header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization,language_id,language_code,session_id');

abstract class RestController extends Controller {

	/*function __construct()
	{
		$headers = apache_request_headers();
		if($headers['session_id']){
			session_id($headers['session_id']);
		}

	}*/



    public function getLanguageId()
    {
        $headers = apache_request_headers();
        foreach($headers as $key=>$value)
        {
            if($key=='language_id')
                return $value;
        }

        return $this->config->get('config_language_id');
    }
    public function autherisationCheck() {

        $this->config->set('config_error_display', 0);

        $this->response->addHeader('Content-Type: application/json');

		$headers = apache_request_headers();

        if (!$this->config->get('module_restapi_status')) {
            $json["error"] = 'API is disabled. Enable it!';
        }

        if(isset($json["error"])){
            echo(json_encode($json));
            exit;
        }

        $this->validateToken();

        $token = $this->getTokenValue();

        $this->update_session($token['access_token'], json_decode($token['data'], true));



    }

    public function getOauthServer() {
        //$dsn      = DB_DRIVER.':dbname='.DB_DATABASE.';host='.DB_HOSTNAME;
        $dsn      = 'mysql:dbname='.DB_DATABASE.';host='.DB_HOSTNAME;
        $username = DB_USERNAME;
        $password = DB_PASSWORD;

        // error reporting (this is a demo, after all!)
        //ini_set('display_errors',1);error_reporting(E_ALL);

        // Autoloading (composer is preferred, but for this example let's just do this)
        require_once(DIR_SYSTEM .'oauth2-server-php/src/OAuth2/Autoloader.php');
        OAuth2\Autoloader::register();

        $config = array(
            'id_lifetime' => $this->config->get('module_restapi_token_ttl'),
            'access_lifetime' => $this->config->get('module_restapi_token_ttl')
        );

        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

        // Pass a storage object or array of storage objects to the OAuth2 server class
        $oauthServer = new OAuth2\Server($storage, $config);

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $oauthServer->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

        return $oauthServer;
    }

    /*Validate Oauth token*/
    public function validateToken(){
			$headers = apache_request_headers();

			if($headers['session_id']){
				$this->session->start('default',$headers['session_id']);
			}
        // Handle a request to a resource and authenticate the access token
        $server = $this->getOauthServer();
        if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $serverResp = $server->getResponse();
            $resp  = array('statusCode'=>$serverResp->getStatusCode(), 'statusText'=>$serverResp->getStatusText());
            echo(json_encode($resp));
            exit;
        }
    }

    /*Get Oauth token*/
    private function getTokenValue() {
        $server = $this->getOauthServer();
        return $server->getAccessTokenData(OAuth2\Request::createFromGlobals());
    }

    public function sendResponse($json) {
        $this->load->model('letscms/restapi');

        if(isset($this->session->data['token_id']) || isset($_SESSION['token_id'])) {
            $token = $this->session->data['token_id'];
            $this->session->data['rest_session_id'] = $this->session->getId();
            $this->model_letscms_restapi->updateSession($this->session->data, $token);
            unset($_SESSION['token_id']);
        }

        if(isset($this->session->data['customer_id']) && !empty($this->session->data['customer_id'])){
            $this->model_letscms_restapi->updateCustomerData($this->session, $this->session->data['customer_id']);
        }

				$json['session_id']=$this->session->getId();
        $this->response->setOutput(json_encode($json));
    }

    //update user session
    function update_session($token,  $data) {

        if(!empty($data)){
            $this->session->data = $data;
        }

        $this->session->data['token_id'] = $token;

        if(strpos(VERSION, '2.1.') !== false) {
            if(!empty($data['rest_session_id'])){
                $this->db->query("UPDATE " . DB_PREFIX . "cart SET session_id = '" . $this->db->escape($this->session->getId()) . "' WHERE session_id = '" . $this->db->escape($data['rest_session_id']) . "'");
            }
        }

        /* Log customer in by Id */
        if(isset($data['customer_id']) && !empty($data['customer_id'])){
            $this->load->model('letscms/restapi');
            $customer_info = $this->model_letscms_restapi->loginCustomerById($data['customer_id']);

            if($customer_info){
                $this->session->data['cart'] = array();
                $this->customer->login($customer_info['email'], "", true);

                if(strpos(VERSION, '2.1.') !== false) {

                    $this->db->query("UPDATE " . DB_PREFIX . "cart SET session_id = '" . $this->db->escape($this->session->getId()) . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");

                    $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                    foreach ($cart_query->rows as $cart) {
                        $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart['cart_id'] . "'");
                        $this->cart->add($cart['product_id'], $cart['quantity'], json_decode($cart['option']), $cart['recurring_id']);
                    }
                }
            }
        }
    }

    function clearTokensTable($token=null, $sessionid=null){
        //delete all previous token to this session and delete all expired session
        $this->load->model('letscms/restapi');
        $this->model_letscms_restapi->clearTokens($token, $sessionid);
    }

    public function returnDeprecated(){
        $json['success'] = false;
        $json['error'] = "This service has been removed for security reasons.Please contact us for more information.";
        echo(json_encode($json));
        exit;
    }
}
