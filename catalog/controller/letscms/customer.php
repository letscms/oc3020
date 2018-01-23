<?php

require_once(DIR_SYSTEM . 'engine/restcontroller.php');
class ControllerLetscmsCustomer extends RestController {

  public function forgotten() {
    $this->autherisationCheck();
    $json = array('success' => true);

    $language_id=$this->request->server['HTTP_LANGUAGE_ID'];
    $language_code=$this->request->server['HTTP_LANGUAGE_CODE'];

    $language = new Language($language_code);
    $language->load($language_code);
    $language->load('letscms/customer');
    $language->load('letscms/common');

      if ( $_SERVER['REQUEST_METHOD'] === 'POST'){
          $requestjson = file_get_contents('php://input');
          $requestjson = json_decode($requestjson, true);

          if ($this->customer->isLogged()) {
              $json['error']		= $language->get('text_already_login');
              $json['success']	= false;
          } else {
              $this->load->model('account/customer');
              $language->load('account/forgotten');
              $language->load('mail/forgotten');

              $error = array();
              if (!isset($requestjson['email'])) {
                  $error[] = $this->language->get('error_email');
              } elseif (!$this->model_account_customer->getTotalCustomersByEmail($requestjson['email'])) {
                  $error[] = $this->language->get('error_email');
              }
              if(empty($error)){
                  if(strpos(VERSION, '2.2.') === false) {
                      $password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);

                      $this->model_account_customer->editPassword($requestjson['email'], $password);

                      $subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

                      $message  = sprintf($this->language->get('text_greeting'), $this->config->get('config_name')) . "\n\n";
                      $message .= $this->language->get('text_password') . "\n\n";
                      $message .= $password;

                  } else {

                      $code = token(40);

                      $this->model_account_customer->editCode($requestjson['email'], $code);

                      $subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

                      $message  = sprintf($this->language->get('text_greeting'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";
                      $message .= $this->language->get('text_change') . "\n\n";
                      $message .= $this->url->link('account/reset', 'code=' . $code, true) . "\n\n";
                      $message .= sprintf($this->language->get('text_ip'), $this->request->server['REMOTE_ADDR']) . "\n\n";

                  }

                  if(VERSION == '2.0.0.0' || VERSION == '2.0.1.0' || VERSION == '2.0.1.1') {
                      $mail = new Mail($this->config->get('config_mail'));
                  } else {
                      $mail = new Mail();
                      $mail->protocol = $this->config->get('config_mail_protocol');
                      $mail->parameter = $this->config->get('config_mail_parameter');

                      if(VERSION == '2.0.2.0'){
                          $mail->smtp_hostname = $this->config->get('config_mail_smtp_host');
                      } else {
                          $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                      }

                      $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                      $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                      $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                      $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                  }

                  $mail->setTo($requestjson['email']);
                  $mail->setFrom($this->config->get('config_email'));
                  $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                  $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                  $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                  $mail->send();

                  // Add to activity log
                  $customer_info = $this->model_account_customer->getCustomerByEmail($requestjson['email']);

                  if ($customer_info) {
                      $this->load->model('account/activity');

                      $activity_data = array(
                          'customer_id' => $customer_info['customer_id'],
                          'name'        => $customer_info['firstname'] . ' ' . $customer_info['lastname']
                      );

                      $this->model_account_activity->addActivity('forgotten', $activity_data);
                  }
              } else {
                  $json["error"]		= $error;
                  $json["success"]	= false;
              }
          }
      } else {
          $json["error"]		= $language->get('text_http_post');
          $json["success"]	= false;
      }
      $this->sendResponse($json);
  }

  public function logout()
  {
    $this->autherisationCheck();
    $json = array('success' => true);

    $language_id=$this->request->server['HTTP_LANGUAGE_ID'];
    $language_code=$this->request->server['HTTP_LANGUAGE_CODE'];
    $language = new Language($language_code);
    $language->load($language_code);
    $language->load('letscms/customer');
    $language->load('letscms/common');

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){

			if ($this->customer->isLogged()) {
				$this->event->trigger('pre.customer.logout');

				$this->customer->logout();
				$this->cart->clear();

				unset($this->session->data['wishlist']);
				unset($this->session->data['shipping_address']);
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_address']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['comment']);
				unset($this->session->data['order_id']);
				unset($this->session->data['coupon']);
				unset($this->session->data['reward']);
				unset($this->session->data['voucher']);
				unset($this->session->data['vouchers']);

				$this->event->trigger('post.customer.logout');
			}
		}else {
				$json["error"]		= $this->language->get('text_http_post');
				$json["success"]	= false;
		}
        $this->sendResponse($json);
  }



  public function login()
  {
    $this->autherisationCheck();
    $json['success']=true;

    $language_id=$this->request->server['HTTP_LANGUAGE_ID'];
    $language_code=$this->request->server['HTTP_LANGUAGE_CODE'];
    $language = new Language($language_code);
    $language->load($language_code);
    $language->load('letscms/customer');
    $language->load('letscms/common');

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
      $requestjson = file_get_contents('php://input');

      $requestjson = json_decode($requestjson, true);
      $requestjson['language_id']=$language_id;
      $requestjson['language_code']=$language_code;

			if ($this->customer->isLogged()) {
				$json['error']		= $this->language->get('text_already_login');
				$json['success']	= false;
			}

      if ($json['success']) {
        if (!$this->customer->login($requestjson['email'], $requestjson['password'])) {
          $json['error']['warning'] = $this->language->get('error_login');
          $json['success']	= false;
        }

        $this->load->model('letscms/customer');

        $customer_info = $this->model_letscms_customer->getCustomerByEmail($requestjson['email']);

        if ($customer_info && !$customer_info['approved']) {
          $json['error']['warning'] = $this->language->get('error_approved');
          $json['success']	= false;
        }
        unset($this->session->data['guest']);

        // Default Addresses
        $this->load->model('account/address');

        if ($this->config->get('config_tax_customer') == 'payment') {
          $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
        }

        if ($this->config->get('config_tax_customer') == 'shipping') {
          $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
        }

        // Add to activity log
        $this->load->model('account/activity');

        $activity_data = array(
          'customer_id' => $this->customer->getId(),
          'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
        );

        $this->model_account_activity->addActivity('login', $activity_data);

        unset($customer_info['password']);
        unset($customer_info['token']);
        unset($customer_info['salt']);

        $customer_info["session"] = session_id();

        // Custom Fields
        $this->load->model('account/custom_field');

        $customer_info['custom_fields'] = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

        if(strpos(VERSION, '2.1.') === false && strpos(VERSION, '2.2.') === false){
          $customer_info['account_custom_field'] = unserialize($customer_info['custom_field']);
        } else {
          $customer_info['account_custom_field'] = json_decode($customer_info['custom_field'], true);
        }

        unset($customer_info['custom_field']);

        $json['data'] = $customer_info;
        }

    } else {
			$json["error"]		= $this->language->get('text_http_post');
			$json["success"]	= false;
		}
    $this->sendResponse($json);
}

  public function register()
  {
		$this->autherisationCheck();


    $language_id=$this->request->server['HTTP_LANGUAGE_ID'];
    $language_code=$this->request->server['HTTP_LANGUAGE_CODE'];
    $language = new Language($language_code);
    $language->load($language_code);
    $language->load('letscms/customer');
    $language->load('letscms/common');

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
      $requestjson = file_get_contents('php://input');

			$requestjson = json_decode($requestjson, true);

      $requestjson['language_id']=$language_id;
      $requestjson['language_code']=$language_code;

      if (!empty($requestjson)) {
        $this->load->model('account/customer');
        $json['success'] = true;

        if ($this->customer->isLogged()) {
    			$json['message']		= $this->language->get('text_already_login');
    			$json['success'] = false;
    		}

        if ($json['success']) {
    			if ((utf8_strlen(trim($requestjson['firstname'])) < 1) || (utf8_strlen(trim($requestjson['firstname'])) > 32)) {
    				$json['error']['firstname'] = $this->language->get('error_firstname');
    			}

    			if ((utf8_strlen(trim($requestjson['lastname'])) < 1) || (utf8_strlen(trim($requestjson['lastname'])) > 32)) {
    				$json['error']['lastname'] = $this->language->get('error_lastname');
    			}

    			if ((utf8_strlen($requestjson['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $requestjson['email'])) {
    				$json['error']['email'] = $this->language->get('error_email');
    			}

    			if ($this->model_account_customer->getTotalCustomersByEmail($requestjson['email'])) {
    				$json['error']['warning'] = $this->language->get('error_exists');
    			}

    			if ((utf8_strlen($requestjson['telephone']) < 3) || (utf8_strlen($requestjson['telephone']) > 32)) {
    				$json['error']['telephone'] = $this->language->get('error_telephone');
    			}

    			if ((utf8_strlen(trim($requestjson['address_1'])) < 3) || (utf8_strlen(trim($requestjson['address_1'])) > 128)) {
    				$json['error']['address_1'] = $this->language->get('error_address_1');
    			}

    			if ((utf8_strlen(trim($requestjson['city'])) < 2) || (utf8_strlen(trim($requestjson['city'])) > 128)) {
    				$json['error']['city'] = $this->language->get('error_city');
    			}

    			$this->load->model('localisation/country');

    			$country_info = $this->model_localisation_country->getCountry($requestjson['country_id']);

    			if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($requestjson['postcode'])) < 2 || utf8_strlen(trim($requestjson['postcode'])) > 10)) {
    				$json['error']['postcode'] = $this->language->get('error_postcode');
    			}

    			if ($requestjson['country_id'] == '') {
    				$json['error']['country'] = $this->language->get('error_country');
    			}

    			if (!isset($requestjson['zone_id']) || $requestjson['zone_id'] == '') {
    				$json['error']['zone'] = $this->language->get('error_zone');
    			}

    			if ((utf8_strlen($requestjson['password']) < 4) || (utf8_strlen($requestjson['password']) > 20)) {
    				$json['error']['password'] = $this->language->get('error_password');
    			}

    			if ($requestjson['confirm'] != $requestjson['password']) {
    				$json['error']['confirm'] = $this->language->get('error_confirm');
    			}

    			if ($this->config->get('config_account_id')) {
    				$this->load->model('catalog/information');

    				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

    				if ($information_info && !isset($requestjson['agree'])) {
    					$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
    				}
    			}

    			// Customer Group
    			if (isset($requestjson['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($requestjson['customer_group_id'], $this->config->get('config_customer_group_display'))) {
    				$customer_group_id = $requestjson['customer_group_id'];
    			} else {
    				$customer_group_id = $this->config->get('config_customer_group_id');
    			}

    			// Custom field validation
    			$this->load->model('account/custom_field');

    			$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

    			foreach ($custom_fields as $custom_field) {
    				if ($custom_field['required'] && empty($data['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
    					$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
    				}
    			}
    		}

        if (!isset($json['error']) && empty($json['error'])) {
          $customer_id = $this->model_account_customer->addCustomer($requestjson);
          $this->load->model('account/customer_group');

    			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

          if ($customer_group_info && !$customer_group_info['approval']) {
    				$this->customer->login($requestjson['email'], $requestjson['password']);

    				$requestjson['session_id'] = session_id();
    				unset($requestjson['password']);
    				unset($requestjson['confirm']);
    				unset($requestjson['agree']);
    				$json['data'] = $requestjson;

    				// Default Payment Address
    				$this->load->model('account/address');

    				$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());

    				if (!empty($data['shipping_address'])) {
    					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
    				}
    			}

          unset($this->session->data['guest']);
    			unset($this->session->data['shipping_method']);
    			unset($this->session->data['shipping_methods']);
    			unset($this->session->data['payment_method']);
    			unset($this->session->data['payment_methods']);

    			// Add to activity log
    			$this->load->model('account/activity');

    			$activity_data = array(
    				'customer_id' => $customer_id,
    				'name'        => $requestjson['firstname'] . ' ' . $requestjson['lastname']
    			);

    			$this->model_account_activity->addActivity('register', $activity_data);

        } else {
          $json["success"]	= false;
        }

      } else {
        $json["success"]	= false;
      }

    } else {
      $json["error"]		= $this->language->get('text_http_post');
      $json["success"]	= false;
    }
    $this->sendResponse($json);
  }
}
