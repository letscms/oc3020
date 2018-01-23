<?php
require_once(DIR_SYSTEM . 'engine/restcontroller.php');

class ControllerLetscmsCommonHeader extends RestController {
	public function index() {
		$this->autherisationCheck();

		$datajson=array();

		$data['title'] = $this->document->getTitle();

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('letscms/common/header');

		$data['text_home'] = $this->language->get('text_home');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('letscms/account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_letscms_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

		$data['text_account'] = $this->language->get('text_account');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_login'] = $this->language->get('text_login');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_logout'] = $this->language->get('text_logout');
		$data['text_checkout'] = $this->language->get('text_checkout');
		$data['text_category'] = $this->language->get('text_category');
		$data['text_all'] = $this->language->get('text_all');

		$data['home'] = $this->url->link('letscms/common/home');
		$data['wishlist'] = $this->url->link('letscms/account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('letscms/account/account', '', true);
		$data['register'] = $this->url->link('letscms/account/register', '', true);
		$data['login'] = $this->url->link('letscms/account/login', '', true);
		$data['order'] = $this->url->link('letscms/account/order', '', true);
		$data['transaction'] = $this->url->link('letscms/account/transaction', '', true);
		$data['download'] = $this->url->link('letscms/account/download', '', true);
		$data['logout'] = $this->url->link('letscms/account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('letscms/checkout/cart');
		$data['checkout'] = $this->url->link('letscms/checkout/checkout', '', true);
		$data['contact'] = $this->url->link('letscms/information/contact');
		$data['telephone'] = $this->config->get('letscms/config_telephone');

		$datajson['data']=$data;
		if(!empty($datajson['data']))
		{
			$datajson['success']=true;
		} else {
			$datajson['success']=false;
		}
		$this->sendResponse($datajson);
	}
}
