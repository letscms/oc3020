<?php
class ModelExtensionModuleRestapi extends Model {
	public function install() {
    $this->db->query("CREATE TABLE IF NOT EXISTS oauth_clients (client_id VARCHAR(80) NOT NULL, client_secret VARCHAR(80) NOT NULL, redirect_uri VARCHAR(2000) NOT NULL, grant_types VARCHAR(80), scope VARCHAR(100), user_id VARCHAR(80), CONSTRAINT clients_client_id_pk PRIMARY KEY (client_id))");
		$this->db->query("CREATE TABLE IF NOT EXISTS oauth_access_tokens (access_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), session_id VARCHAR(40), data TEXT, CONSTRAINT access_token_pk PRIMARY KEY (access_token))");
		$this->db->query("CREATE TABLE IF NOT EXISTS oauth_scopes (scope TEXT, is_default BOOLEAN)");
	}

	public function uninstall() {
    $this->db->query("DROP TABLE oauth_clients");
		$this->db->query("DROP TABLE oauth_access_tokens");
		$this->db->query("DROP TABLE oauth_scopes");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'restapi'");
	}

  public function setOauthClient($clientid, $clientsecret) {
      $this->db->query("DELETE FROM `oauth_clients`");
      $this->db->query("INSERT INTO `oauth_clients` SET client_id = '" . $this->db->escape($clientid) . "', client_secret = '" . $this->db->escape($clientsecret)."'");
  }

}
