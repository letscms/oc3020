<?php
class ControllerExtensionModuleYousave extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/yousave');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_yousave', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
			'href' => $this->url->link('extension/module/yousave', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/yousave', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_yousave_status'])) {
			$data['module_yousave_status'] = $this->request->post['module_yousave_status'];
		} else {
			$data['module_yousave_status'] = $this->config->get('module_yousave_status');
		}

		if (isset($this->request->post['module_yousave_type'])) {
			$data['module_yousave_type'] = $this->request->post['module_yousave_type'];
		} else {
			$data['module_yousave_type'] = $this->config->get('module_yousave_type');
		}

		if (isset($this->request->post['module_yousave_amount'])) {
			$data['module_yousave_amount'] = $this->request->post['module_yousave_amount'];
		} else {
			$data['module_yousave_amount'] = $this->config->get('module_yousave_amount');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/yousave', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/yousave')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function install(){
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD yousave tinyint(1) NOT NULL DEFAULT '0' AFTER manufacturer_id;");
	}

	public function uninstall(){
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP COLUMN yousave;");
	}
}