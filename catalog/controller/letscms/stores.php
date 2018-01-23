<?php

require_once(DIR_SYSTEM . 'engine/restcontroller.php');

class ControllerLetscmsStores extends RestController {

  public function index() {
    $this->autherisationCheck();
    $data = array('success' => true);

    $this->load->model('localisation/language');

    $languages = $this->model_localisation_language->getLanguages();

    if(count($languages) == 0){
        $data['success'] 	= false;
        $data['error'] 		= "No language found";
    }else {
      foreach($languages as $language){
        $data['data'][] = array(
            'language_id' => $language['language_id'],
            'name'        => $language['name'],
            'code'        => $language['code'],
            'locale'      => $language['locale'],
            'image'       => $language['image'],
            'directory'   => $language['directory'],
            'filename'    => isset($language['filename']) ? $language['filename'] : '',
            'sort_order'  => $language['sort_order'],
            'status'      => $language['status']
        );
      }

    }
    $this->sendResponse($data);
  }

}
