<?php

require_once(DIR_SYSTEM . 'engine/restcontroller.php');

class ControllerLetscmsFeatured extends RestController {

  public function index($setting) {
    $this->autherisationCheck();
    $this->load->language('extension/module/featured');

    $this->load->model('catalog/product');

    $this->load->model('tool/image');

    $data['products'] = array();

    if (!$setting['limit']) {
      $setting['limit'] = 4;
    }

    if (!empty($setting['product'])) {
      $product_results = array_slice($setting['product'], 0, (int)$setting['limit']);
      $product_data=array();
      foreach ($product_results as $product_id) {
        $product_info = $this->model_catalog_product->getProduct($product_id);
        if ($product_info) {
          $product_data['product_id'] = (int)$product_result['product_id'];
          $product_data['manufacturer'] = $product_info['manufacturer'];
          $product_data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
          $product_data['model'] = $product_info['model'];
          $product_data['reward'] = $product_info['reward'];
          $product_data['points'] = $product_info['points'];
          $product_data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

          if ($product_info['quantity'] <= 0) {
            $product_data['stock'] = $product_info['stock_status'];
          } elseif ($this->config->get('config_stock_display')) {
            $product_data['stock'] = $product_info['quantity'];
          } else {
            $product_data['stock'] = $this->language->get('text_instock');
          }

          $this->load->model('tool/image');

          if ($product_info['image']) {
            $product_data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
          } else {
            $product_data['popup'] = '';
          }

          if ($product_info['image']) {
            $product_data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));
          } else {
            $product_data['thumb'] = '';
          }

          $product_data['images'] = array();

          $results = $this->model_catalog_product->getProductImages($product_result['product_id']);

          foreach ($results as $result) {
            $product_data['images'][] = array(
              'popup' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')),
              'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'))
            );
          }

          if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
            $product_data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
          } else {
            $product_data['price'] = false;
          }

          if ((float)$product_info['special']) {
            $product_data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
          } else {
            $product_data['special'] = false;
          }

          if ($this->config->get('config_tax')) {
            $product_data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
          } else {
            $product_data['tax'] = false;
          }

          $discounts = $this->model_catalog_product->getProductDiscounts($product_result['product_id']);

          $product_data['discounts'] = array();

          foreach ($discounts as $discount) {
            $product_data['discounts'][] = array(
              'quantity' => $discount['quantity'],
              'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
            );
          }

          $product_data['options'] = array();

          foreach ($this->model_catalog_product->getProductOptions($product_result['product_id']) as $option) {
            $product_option_value_data = array();

            foreach ($option['product_option_value'] as $option_value) {
              if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                  $price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
                } else {
                  $price = false;
                }

                $product_option_value_data[] = array(
                  'product_option_value_id' => $option_value['product_option_value_id'],
                  'option_value_id'         => $option_value['option_value_id'],
                  'name'                    => $option_value['name'],
                  'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
                  'price'                   => $price,
                  'price_prefix'            => $option_value['price_prefix']
                );
              }
            }

            $product_data['options'][] = array(
              'product_option_id'    => $option['product_option_id'],
              'product_option_value' => $product_option_value_data,
              'option_id'            => $option['option_id'],
              'name'                 => $option['name'],
              'type'                 => $option['type'],
              'value'                => $option['value'],
              'required'             => $option['required']
            );
          }

          if ($product_info['minimum']) {
            $product_data['minimum'] = $product_info['minimum'];
          } else {
            $product_data['minimum'] = 1;
          }

          $product_data['review_status'] = $this->config->get('config_review_status');

          if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
            $product_data['review_guest'] = true;
          } else {
            $product_data['review_guest'] = false;
          }

          if ($this->customer->isLogged()) {
            $product_data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
          } else {
            $product_data['customer_name'] = '';
          }

          $product_data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
          $product_data['rating'] = (int)$product_info['rating'];

          // Captcha
          if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
            $product_data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
          } else {
            $product_data['captcha'] = '';
          }

          $product_data['share'] = $this->url->link('product/product', 'product_id=' . (int)$product_result['product_id']);

          $product_data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($product_result['product_id']);
          $data['products'][]=$product_data;
        }
      }
      $data['success']=true;
    } else {
      $data['success']=false;
    }
    $this->sendResponse($data);
  }

}
