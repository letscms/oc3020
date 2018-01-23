<?php

require_once(DIR_SYSTEM . 'engine/restcontroller.php');
class ControllerLetscmsSlideshow extends RestController {
  public function index()
  {
		$this->autherisationCheck();
    $language_id=$this->request->server['HTTP_LANGUAGE_ID'];
    $language_code=$this->request->server['HTTP_LANGUAGE_CODE'];
    $json = array('success' => true);

    $this->load->model('letscms/module');
    $this->load->model('extension/module');
    $this->load->model('letscms/banner');
    $this->load->model('tool/image');

    $slideshows = $this->model_letscms_module->getModulesByCode('slideshow');
    $data = array();
    $index  = 0;

    if(count($slideshows)){
        foreach($slideshows as $slideshow){
            $module_info = $this->model_extension_module->getModule($slideshow['module_id']);
            $data[$index]['module_id'] = $slideshow['module_id'];
            $data[$index]['name'] = $module_info['name'];
            $data[$index]['banner_id'] = $module_info['banner_id'];
            $data[$index]['width'] = $module_info['width'];
            $data[$index]['height'] = $module_info['height'];
            $data[$index]['status'] = $module_info['status'];

            $data[$index]['banners'] = array();

            $results = $this->model_letscms_banner->getBanner($module_info['banner_id'],$language_id);

            foreach ($results as $result) {
                if (is_file(DIR_IMAGE . $result['image'])) {
                    $data[$index]['banners'][] = array(
                        'title' => $result['title'],
                        'link'  => $result['link'],
                        'image' => $this->model_tool_image->resize($result['image'], $module_info['width'], $module_info['height'])
                    );
                }
            }
            $index++;
        }
        $json['success']=true;
    } else {
      $json['success']=false;
    }
    $json['data'] = $data;
    $this->sendResponse($json);
  }
}
