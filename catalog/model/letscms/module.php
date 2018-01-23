<?php
class ModelLetscmsModule extends Model {

  public function getModulesByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `name`");
		return $query->rows;
	}

}
