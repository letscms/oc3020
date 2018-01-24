<?php

require_once(DIR_SYSTEM . 'engine/restcontroller.php');

class ControllerLetscmsCommonCategories extends RestController {

  public function index()
  {

		$this->autherisationCheck();

		$this->load->model('letscms/catalog/category');

		$this->load->model('letscms/catalog/product');

		$this->load->model('letscms/tool/image');
		$language_id=$this->getLanguageId();

		$data['data'] = array();

		$categories = $this->model_letscms_catalog_category->getCategories(0,0,$language_id);

		foreach ($categories as $category) {
			$children_data = array();

			$children = $this->model_letscms_catalog_category->getCategories($category['category_id'],1,$language_id);

			foreach ($children as $child) {
				$filter_data = array(
					'filter_category_id'  => $child['category_id'],
					'filter_sub_category' => true
				);

				$children_info = array(
					'category_id' => $child['category_id'],
					'title'        => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_letscms_catalog_product->getTotalProducts($filter_data) . ')' : '')
				);

				if ($child['image']) {
					$children_info['thumb'] = $this->model_letscms_tool_image->resize($child['image'], $this->config->get('theme_default_image_category_width'), $this->config->get('theme_default_image_category_height'));
				} else {
				$children_info['thumb'] = $this->model_letscms_tool_image->resize('no_image.png', $this->config->get('theme_default_image_category_width'), $this->config->get('theme_default_image_category_height'));
			}

				$children_data[] = $children_info;
			}

			$filter_data = array(
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true
			);

			$category_info = array(
				'category_id' => $category['category_id'],
				'title'        => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_letscms_catalog_product->getTotalProducts($filter_data) . ')' : ''),
				'subCollection'    => $children_data
			);

			if ($category['image']) {
				$category_info['thumb'] = $this->model_letscms_tool_image->resize($category['image'], $this->config->get('theme_default_image_category_width'), $this->config->get('theme_default_image_category_height'));
			} else {
				$category_info['thumb'] = $this->model_letscms_tool_image->resize('no_image.png', $this->config->get('theme_default_image_category_width'), $this->config->get('theme_default_image_category_height'));
			}

			$data['data'][] = $category_info;
		}

		if(!empty($data['data']))
		{
			$data['success']=true;
		} else {
			$data['success']=false;
		}
    $this->sendResponse($data);

  }

}
