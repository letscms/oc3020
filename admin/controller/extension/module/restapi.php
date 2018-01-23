<?php
class ControllerExtensionModuleRestapi extends Controller {
	private $error = array();

	public function index() {
		
		$this->load->language('extension/module/restapi');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('extension/module/restapi');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_restapi', $this->request->post);
			$this->model_extension_module_restapi->setOauthClient($this->request->post['module_restapi_client_id'], $this->request->post['module_restapi_client_secret']);
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/restapi', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/restapi', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);


		if (isset($this->request->post['module_restapi_status'])) {
			$data['module_restapi_status'] = $this->request->post['module_restapi_status'];
		} else {
			$data['module_restapi_status'] = $this->config->get('module_restapi_status');
		}

	 	if (isset($this->request->post['module_restapi_client_id'])) {
          $data['module_restapi_client_id'] = $this->request->post['module_restapi_client_id'];
      	} else {
          $data['module_restapi_client_id'] = $this->config->get('module_restapi_client_id');
      	}

      	if (isset($this->request->post['module_restapi_client_secret'])) {
          $data['module_restapi_client_secret'] = $this->request->post['module_restapi_client_secret'];
      	} else {
          $data['module_restapi_client_secret'] = $this->config->get('module_restapi_client_secret');
      	}

      	if (isset($this->request->post['module_restapi_token_ttl'])) {
          $data['module_restapi_token_ttl'] = $this->request->post['module_restapi_token_ttl'];
      	} else {
          $data['module_restapi_token_ttl'] = $this->config->get('module_restapi_token_ttl');
      	}


      	if (isset($error['warning'])) {
				$data['error_warning'] = $error['warning'];
		} else {
				$data['error_warning'] = '';
		}

      	if (!isset($data['module_restapi_token_ttl'])) {
          $data['module_restapi_token_ttl'] = 2628000;
      	}


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/restapi', $data));
	}


	public function install()
	{
		$this->load->model('extension/module/restapi');
		$this->model_extension_module_restapi->install();
	}

	public function uninstall()
	{
		$this->load->model('extension/module/restapi');
		$this->model_extension_module_restapi->uninstall();
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/restapi')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}