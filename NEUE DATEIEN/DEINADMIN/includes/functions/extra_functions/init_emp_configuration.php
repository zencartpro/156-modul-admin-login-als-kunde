<?php
// -----
// Part of the Encrypted Master Password plugin, provided by lat9@vinosdefrutastropicales.com
// Copyright (C) 2014, Vinos de Frutas Tropicales
//
// @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
//
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

if (!defined ('EMP_LOGIN_ADMIN_ID')) {
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . "
                  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added) 
                  VALUES ('Encrypted Master Password: Single Admin ID', 'EMP_LOGIN_ADMIN_ID', '1', 'Specify the ID of an admin user that is permitted to use the Encrypted Master Password feature. Set the value to 0 to disable the <em>Single Admin ID</em> feature.<br /><br /><b>Default: 1</b><br />', 1, '300', NOW(), NOW())");
  $db->Execute ("REPLACE INTO " . TABLE_CONFIGURATION_LANGUAGE . "
                  (configuration_title, configuration_key, configuration_description, configuration_language_id) 
                  VALUES ('Login als Kunde: Nur für einen bestimmten Admin erlaubt?', 'EMP_LOGIN_ADMIN_ID', 'Wenn das Login als Kunde nur für einen ganz bestimmten Admin erlaubt sein soll, dann geben Sie hier die ID dieses Admin Users an (Standard: 1). Stellen Sie hier auf 0, wenn Sie diese Funktion nicht nutzen wollen und alle Admins das nutzen dürfen.',	43)");
}

if (!defined ('EMP_LOGIN_ADMIN_PROFILE_ID')) {
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added) 
                VALUES ('Encrypted Master Password: Admin Profile ID', 'EMP_LOGIN_ADMIN_PROFILE_ID', '1', 'Specify the admin <em>User Profile IDs</em> that are permitted to use the Encrypted Master Password feature &mdash; all admins that are in these profiles are permitted. Enter the value as a packed, comma-separated list of Admin Profile IDs, e.g. <b>1,2,3</b>. Set the value to 0 to disable the <em>Admin Profile ID</em> feature.<br /><br /><b>Default: 1 (All Superusers)</b><br />', 1, '301', NOW(), NOW())");
                
  $db->Execute ("REPLACE INTO " . TABLE_CONFIGURATION_LANGUAGE . "
                (configuration_title, configuration_key, configuration_description, configuration_language_id) 
                VALUES ('Login als Kunde: Nur für bestimmte Admingruppe erlaubt?', 'EMP_LOGIN_ADMIN_PROFILE_ID', 'Wenn das Login als Kunde nur für Admins in einer bestimmten Gruppe erlaubt sein soll (Standard: Superuser), dann stellen Sie hier auf 1. Wenn Sie weitere Adminprofile berechtigen wollen, dann geben Sie eine kommagetrennte Liste der IDs der Adminprofile ein.<br/>Wenn Sie dieses Feature nicht nutzen wollen, stellen Sie auf 0.',	43)");
} else {
  $db->Execute ("UPDATE " . TABLE_CONFIGURATION . "
                    SET configuration_description = 'Specify the admin <em>User Profile IDs</em> that are permitted to use the Encrypted Master Password feature &mdash; all admins that are in these profiles are permitted. Enter the value as a packed, comma-separated list of Admin Profile IDs, e.g. <b>1,2,3</b>. Set the value to 0 to disable the <em>Admin Profile ID</em> feature.<br /><br /><b>Default: 1 (All Superusers)</b><br />'
                  WHERE configuration_key = 'EMP_LOGIN_ADMIN_PROFILE_ID'");                 

}