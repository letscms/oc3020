<?php
class ModelLetscmsRestapi extends Model {
  public function editPasswordById($customer_id, $password) {
    $this->db->query("UPDATE " . DB_PREFIX . "customer SET password = '" . $this->db->escape(md5($password)) . "' WHERE customer_id = '" . (int)$customer_id . "'");
  }

  public function editCustomerById($customer_id, $data) {

    $this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "' WHERE customer_id = '" . (int)$customer_id . "'");
  }

  public function getCustomersMod($data = array()) {
    $sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    $implode = array();

    if (!empty($data['filter_name'])) {
      $implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
    }

    if (!empty($data['filter_email'])) {
      $implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
    }

    if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
      $implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
    }

    if (!empty($data['filter_customer_group_id'])) {
      $implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
    }

    if (!empty($data['filter_ip'])) {
      $implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
    }

    if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
      $implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
    }

    if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
      $implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
    }

    if (!empty($data['filter_date_added'])) {
      $implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
    }

    if ($implode) {
      $sql .= " AND " . implode(" AND ", $implode);
    }

    $sort_data = array(
      'name',
      'c.email',
      'customer_group',
      'c.status',
      'c.approved',
      'c.ip',
      'c.date_added'
    );

    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
      $sql .= " ORDER BY " . $data['sort'];
    } else {
      $sql .= " ORDER BY name";
    }

    if (isset($data['order']) && ($data['order'] == 'DESC')) {
      $sql .= " DESC";
    } else {
      $sql .= " ASC";
    }

    if (isset($data['start']) || isset($data['limit'])) {
      if ($data['start'] < 0) {
        $data['start'] = 0;
      }

      if ($data['limit'] < 1) {
        $data['limit'] = 20;
      }

      $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
    }

    $query = $this->db->query($sql);

    return $query->rows;
  }

public function clearTokens($token, $sessionid) {
  if(!empty($token)){
      $this->db->query("DELETE FROM `oauth_access_tokens` where session_id='" . $this->db->escape($sessionid) . "' AND access_token !='" . $this->db->escape($token) . "'");
  }
  $this->db->query("DELETE FROM `oauth_access_tokens` where expires < '" . date('Y-m-d', strtotime("-30 days")) . "'");
}

  public function loginCustomerById($customer_id){
      $query = $this->db->query("SELECT email from " . DB_PREFIX . "customer where customer_id='".(int)$customer_id."'");
      return $query->row;
  }

  public function updateCustomerData($session, $customer_id){
      $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(isset($session->data['cart']) ? serialize($session->data['cart']) : '') . "', wishlist = '" . $this->db->escape(isset($session->data['wishlist']) ? serialize($session->data['wishlist']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");
  }

  public function updateSession($session, $access_token) {
      $query = $this->db->query("Update oauth_access_tokens SET data = '" . $this->db->escape(json_encode($session)) . "', expires = expires WHERE access_token = '" . $access_token . "'");
  }

   public function loadOldToken($access_token) {
      $query = $this->db->query("SELECT * FROM oauth_access_tokens WHERE access_token = '" . $this->db->escape($access_token) . "'");
      return $query->row;
  }

  public function deleteOldToken($access_token) {
      $this->db->query("DELETE FROM `oauth_access_tokens` WHERE access_token = '" . $this->db->escape($access_token) . "'");
  }

  public function loadSessionToNew($session, $access_token) {
      $query = $this->db->query("Update oauth_access_tokens SET data = '" . $this->db->escape($session) . "', expires = expires WHERE access_token = '" . $this->db->escape($access_token) . "'");
  }
}
