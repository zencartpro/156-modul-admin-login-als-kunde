<?php
// -----
// Part of the Encrypted Master Password plugin, provided by lat9@vinosdefrutastropicales.com
//
// Copyright (C) 2013-2019 Vinos de Frutas Tropicales
//
// @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
//
// -----
// When entered via the "Place Order" button from the admin, the customer's email address is posted but somehow (on PHP 5.4)
// doesn't get recorded in the $GLOBALS array (which is where the default input field values are gathered).
//
if (isset ($_POST['email_address'])) {
  $GLOBALS['email_address'] = $_POST['email_address'];
  
}

class emp_order_observer extends base 
{
    function __construct() 
    {
        // -----
        // If an EMP admin is currently logged into the customer's account, let that admin know who s/he is shopping for.
        //
        if (isset($_SESSION['emp_admin_id'])) {
            $shopping_for_name = $_SESSION['customer_first_name'] . ' ' . $_SESSION['customer_last_name'];
            if (!defined('EMP_SHOPPING_FOR_MESSAGE_SEVERITY')) {
                define('EMP_SHOPPING_FOR_MESSAGE_SEVERITY', 'success');
            }
            $severity = EMP_SHOPPING_FOR_MESSAGE_SEVERITY;
            if (!in_array($severity, array('success', 'caution', 'warning', 'error'))) {
                $severity = 'success';
            }
            $GLOBALS['messageStack']->add('header', sprintf(EMP_SHOPPING_FOR_MESSAGE, $shopping_for_name, $_SESSION['emp_customer_email_address']), $severity);
        }
        
        // -----
        // Watch for EMP-related events so long as it's been configured.
        //
        if (defined('EMP_LOGIN_ADMIN_PROFILE_ID') && defined('EMP_LOGIN_ADMIN_ID')) {
            $this->attach($this, array('NOTIFY_ORDER_DURING_CREATE_ADDED_ORDER_COMMENT', 'NOTIFY_PROCESS_3RD_PARTY_LOGINS'));
        }
    }
  

    function update (&$class, $eventID, $p1a, &$p2, &$p3) 
    {
        global $db, $sniffer;
        switch ($eventID) {
            // -----
            // Monitoring the notifier just after the order-status record for the order has been created.
            // The 'updated_by' field is set to the logged-in EMP admin's name, so long as the field has
            // been previously added to the table.
            //
            case 'NOTIFY_ORDER_DURING_CREATE_ADDED_ORDER_COMMENT':
        if ($sniffer->field_exists(TABLE_ORDERS_STATUS_HISTORY, 'updated_by') && isset($_SESSION['emp_admin_id'])) {
          $admin_id_sql = 'admin_id = :adminid:';
          $admin_id_sql = $db->bindVars($admin_id_sql, ':adminid:', $_SESSION['emp_admin_id'], 'integer');

          $admin_info = $db->Execute("SELECT admin_name FROM " . TABLE_ADMIN . " WHERE $admin_id_sql LIMIT 1");
          $admin_name = (($admin_info->EOF) ? '' : $admin_info->fields['admin_name']) . ' [' . $_SESSION['emp_admin_id'] . ']';

          $orders_id_sql = 'orders_id = :ordersID';
          $orders_id_sql = $db->bindVars ($orders_id_sql, ':ordersID', $p1a['orders_id'], 'integer');

                    $osh_info = $db->Execute(
                        "SELECT MAX(orders_status_history_id) as orders_status_history_id 
                           FROM " . TABLE_ORDERS_STATUS_HISTORY . " 
                          WHERE $orders_id_sql"
                    );
                  
                    if (!$osh_info->EOF) {
                        $db->Execute(
                            "UPDATE " . TABLE_ORDERS_STATUS_HISTORY . " 
                                SET updated_by = '$admin_name' 
                              WHERE orders_status_history_id = " . $osh_info->fields['orders_status_history_id'] . "
                              LIMIT 1"
                        );
          }
        }
        break;
        
      // -----
      // Issued by the login page's header_php.php when a login attempt did not match a customer's credentials; see
      // if this is an EMP-enabled admin login attempt.  Email address and password are in POST variables, $p3 indicates whether or
      // not the login is currently authorized.
      //
            // $p1 ... contains the customer's email address
      // $p2 ... contains the password entered on the login screen
      // $p3 ... contains the binary flag that indicates whether or not the current login is authorized.
      //
            case 'NOTIFY_PROCESS_3RD_PARTY_LOGINS':
                if (!$p3 && zen_not_null($p2)) {
                    $pwd2 = htmlspecialchars($p2, ENT_COMPAT, CHARSET);
                    $check = $db->Execute(
                        "SELECT admin_id, admin_pass 
                           FROM " . TABLE_ADMIN . " 
                          WHERE admin_id = " . (int)EMP_LOGIN_ADMIN_ID . "
                          LIMIT 1"
                    );
                    if (!$check->EOF && (zen_validate_password($p2, $check->fields['admin_pass']) || zen_validate_password($pwd2, $check->fields['admin_pass']))) {
                        $p3 = true;
                        $_SESSION['emp_admin_login'] = true;
                        $_SESSION['emp_admin_id'] = EMP_LOGIN_ADMIN_ID;
                        
                    } else {
                        $admin_profiles = $db->Execute(
                            "SELECT admin_id, admin_pass 
                               FROM " . TABLE_ADMIN . " 
                              WHERE admin_profile IN (" . preg_replace('/[^0-9,]/', '', EMP_LOGIN_ADMIN_PROFILE_ID) . ")"
                        );
                        while (!$admin_profiles->EOF && !$p3) {
                            $p3 = (zen_validate_password($p2, $admin_profiles->fields['admin_pass']) || zen_validate_password($pwd2, $admin_profiles->fields['admin_pass']));
                            if ($p3) {
                                $_SESSION['emp_admin_login'] = true;
                                $_SESSION['emp_admin_id'] = $admin_profiles->fields['admin_id'];
                            }
                            $admin_profiles->MoveNext();
                        }
                    }
                  
                    if ($p3) {
                        $_SESSION['emp_customer_email_address'] = $p1a;
                        $sql_data_array = array( 
                            'access_date' => 'now()',
                            'admin_id' => $_SESSION['emp_admin_id'],
                            'page_accessed' => 'login.php',
                            'page_parameters' => '',
                            'ip_address' => substr($_SERVER['REMOTE_ADDR'],0,45),
                            'gzpost' => gzdeflate(json_encode(array('action' => 'emp_admin_login', 'customer_email_address' => $p1a)), 7),
                            'flagged' => 0,
                            'attention' => '',
                            'severity' => 'info',
                            'logmessage' => 'EMP admin login',
                        );
                        zen_db_perform(TABLE_ADMIN_ACTIVITY_LOG, $sql_data_array);
                    }
                }
                break;

            default:
                break;
        }
    }
}
