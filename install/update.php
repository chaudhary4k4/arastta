<?php
/**
 * @package        Arastta eCommerce
 * @copyright      Copyright (C) 2015 Arastta Association. All rights reserved. (arastta.org)
 * @credits        See CREDITS.txt for credits and other copyright notices.
 * @license        GNU General Public License version 3; see LICENSE.txt
 */
 
# VERSION = the current version
# $version = the version to be updated
 
// 1.0.3 changes;
if (version_compare(VERSION, '1.0.3', '<')) {
    // Delete language english directory
    $this->filesystem->remove(DIR_LANGUAGE . 'english');
    $this->filesystem->remove(DIR_CATALOG . 'language/english');
    
    // Update language directory name
    $this->db->query("UPDATE `" . DB_PREFIX . "language` SET `directory` = 'en-GB' WHERE `code` = 'en';");

    // Add field ('params') Addon table
    $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "addon`");
    
    if (!in_array('params', $query->rows)) {
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "addon` ADD COLUMN `params` TEXT DEFAULT NULL");
    }
}

// 1.1.0 changes;
if (version_compare(VERSION, '1.1.0', '<')) {
    // Update the user groups
    $user_groups = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group");

    foreach ($user_groups->rows as $user_group) {
        $user_group['permission'] = unserialize($user_group['permission']);
        
        $user_group['permission']['access'][] = 'tool/system_info';
        $user_group['permission']['modify'][] = 'tool/system_info';
        
        $user_group['permission']['access'][] = 'feed/facebook_store';
        $user_group['permission']['modify'][] = 'feed/facebook_store';
        
        $user_group['permission']['dashboard'] = array(
            '0' => 'dashboard/charts',
            '1' => 'dashboard/online',
            '2' => 'dashboard/recenttabs',
            '3' => 'dashboard/customer',
            '4' => 'dashboard/order',
            '5' => 'dashboard/sale',
            '6' => 'dashboard/map',
        );
        
        $this->db->query("UPDATE " . DB_PREFIX . "user_group SET name = '" . $this->db->escape($user_group['name']) . "', permission = '" . $this->db->escape(serialize($user_group['permission'])) . "' WHERE user_group_id = '" . (int)$user_group['user_group_id'] . "'");
    }
    
    // Update the modules
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module`");

    foreach ($query->rows as $module) {
        $module_setting = unserialize($module['setting']);
        
        if (isset($module_setting['product']) || ($module['code'] == 'featured')) {
            $module_setting['feed'] = 1;
            
            $this->db->query("UPDATE `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($module_setting['name']) . "', `setting` = '" . $this->db->escape(serialize($module_setting)) . "' WHERE `module_id` = '" . (int)$module['module_id'] . "'");
        }
    }
}

// 1.1.3 changes;
if (version_compare(VERSION, '1.1.3', '<')) {
    // Delete modification table
    $this->db->query("DROP TABLE `" . DB_PREFIX . "modification`");
}

// 1.2.0 changes;
if (version_compare(VERSION, '1.2.0', '<')) {
    // Update user table
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "user` MODIFY `username` VARCHAR(100)");
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "user` MODIFY `password` VARCHAR(100)");
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "user` MODIFY `email` VARCHAR(100)");
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "user` ADD `params` text AFTER `date_added`");
    
    // Update stock status table
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "stock_status` ADD `color` VARCHAR(32) NOT NULL AFTER `name`");
    
    // Added stock status default color
    $this->db->query("UPDATE `" . DB_PREFIX . "stock_status` SET `color` = '#FF0000' WHERE `stock_status_id` = '5'");
    $this->db->query("UPDATE `" . DB_PREFIX . "stock_status` SET `color` = '#FFA500' WHERE `stock_status_id` = '6'");
    $this->db->query("UPDATE `" . DB_PREFIX . "stock_status` SET `color` = '#008000' WHERE `stock_status_id` = '7'");
    $this->db->query("UPDATE `" . DB_PREFIX . "stock_status` SET `color` = '#FFFF00' WHERE `stock_status_id` = '8'");
    
    // Insert email template for return request
    $this->db->query("INSERT INTO `" . DB_PREFIX . "email` SET `text` = 'Return Request', `text_id` = '1', `context` = 'added', `type` = 'return', `status` = '1'");
    $email_id = $this->db->getLastId();
    $this->db->query("INSERT INTO `" . DB_PREFIX . "email_description` SET `email_id` = '" . $email_id . "', `name` = '{store_name} - Product Return Request', `description` = '&lt;div style=&quot;width:695px;&quot;&gt;&lt;p style=&quot;margin-top:0px;margin-bottom:20px&quot;&gt;You have a new product return request.&lt;/p&gt;            &lt;table style=&quot;border-collapse:collapse; width: 690px;border-top:1px solid #dddddd;border-left:1px solid #dddddd;margin-bottom:20px&quot;&gt;    &lt;thead&gt;      &lt;tr&gt;        &lt;td style=&quot;font-size:12px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;background-color:#efefef;font-weight:bold;text-align:left;padding:7px;color:#222222&quot; colspan=&quot;2&quot;&gt;Order Details&lt;/td&gt;      &lt;/tr&gt;    &lt;/thead&gt;    &lt;tbody&gt;      &lt;tr&gt;        &lt;td style=&quot;font-size:12px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;text-align:left;padding:7px&quot;&gt;&lt;b&gt;Order ID:&amp;nbsp;&lt;/b&gt;&lt;span style=&quot;font-size: 13px; line-height: 18px; text-align: right;&quot;&gt;{order_id}&lt;/span&gt;&lt;br&gt;          &lt;b&gt;Date Ordered:&lt;/b&gt;&amp;nbsp;&lt;span style=&quot;font-size: 13px; line-height: 18px; text-align: right;&quot;&gt;{date_ordered}&lt;/span&gt;&lt;br&gt;&lt;b style=&quot;color: rgb(0, 0, 0); font-family: arial, sans-serif; line-height: normal;&quot;&gt;Customer&lt;/b&gt;&lt;b&gt;:&lt;/b&gt;&amp;nbsp;&lt;span style=&quot;font-size: 13px; line-height: 18px; text-align: right;&quot;&gt;{firstname} {lastname}&lt;/span&gt;&lt;br&gt;&lt;b style=&quot;color: rgb(0, 0, 0); font-family: arial, sans-serif; line-height: normal;&quot;&gt;E-mail&lt;/b&gt;&lt;b&gt;:&lt;/b&gt;&amp;nbsp;&lt;span style=&quot;font-size: 13px; line-height: 18px; text-align: right;&quot;&gt;{email}&lt;/span&gt;&lt;br&gt;&lt;b&gt;Telephone:&lt;/b&gt;&amp;nbsp;&lt;span style=&quot;font-size: 13px; line-height: 18px; text-align: right;&quot;&gt;{telephone}&lt;/span&gt;&lt;br&gt;&lt;/td&gt;      &lt;/tr&gt;    &lt;/tbody&gt;  &lt;/table&gt;&lt;table style=&quot;border-collapse:collapse; width: 690px;border-top:1px solid #dddddd;border-left:1px solid #dddddd;margin-bottom:20px&quot;&gt;    &lt;thead&gt;      &lt;tr&gt;        &lt;td style=&quot;font-size:12px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;background-color:#efefef;font-weight:bold;text-align:left;padding:7px;color:#222222&quot;&gt;Product&lt;/td&gt;                &lt;td style=&quot;font-size:12px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;background-color:#efefef;font-weight:bold;text-align:left;padding:7px;color:#222222&quot;&gt;Model&lt;/td&gt;&lt;td style=&quot;font-size:12px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;background-color:#efefef;font-weight:bold;text-align: right;padding:7px;color:#222222&quot;&gt;Quantity&lt;/td&gt;              &lt;/tr&gt;    &lt;/thead&gt;    &lt;tbody&gt;      &lt;tr&gt;        &lt;td style=&quot;width: 40%;font-size:12px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;text-align:left;padding:7px&quot;&gt;&lt;span style=&quot;font-size: 13px; line-height: 18px; text-align: right;&quot;&gt;{product}&lt;/span&gt;&lt;br&gt;&lt;/td&gt;                &lt;td style=&quot;width: 40%;font-size:12px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;text-align:left;padding:7px&quot;&gt;&lt;span style=&quot;font-size: 13px; line-height: 18px; text-align: right;&quot;&gt;{model}&lt;/span&gt;&lt;/td&gt;&lt;td style=&quot;font-size:12px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;text-align: right;padding:7px&quot;&gt;&lt;span style=&quot;font-size: 13px; line-height: 18px; text-align: right;&quot;&gt;{quantity}&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;&lt;p&gt;&lt;/p&gt;&lt;p&gt;&lt;/p&gt;&lt;table style=&quot;border-collapse:collapse; width: 690px;border-top:1px solid #dddddd;border-left:1px solid #dddddd;margin-bottom:20px&quot;&gt;  &lt;thead&gt;      &lt;tr&gt;        &lt;td style=&quot;font-size:12px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;background-color:#efefef;font-weight:bold;text-align:left;padding:7px;color:#222222&quot; colspan=&quot;2&quot;&gt;Return Details&lt;/td&gt;      &lt;/tr&gt;    &lt;/thead&gt;  &lt;tbody&gt;		&lt;tr&gt;			&lt;td style=&quot;font-size:12px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;text-align:left;padding:7px&quot;&gt;  &lt;b&gt;Return Reason:&amp;nbsp;&lt;/b&gt;  &lt;span style=&quot;font-size: 13px; line-height: 18px; text-align: right;&quot;&gt;{return_reason}&lt;/span&gt;  &lt;br&gt;  &lt;b&gt;Opened:&amp;nbsp;&lt;/b&gt;  &lt;span style=&quot;font-size: 13px; line-height: 18px; text-align: right;&quot;&gt;{opened}&lt;/span&gt;  &lt;br&gt;&lt;br&gt;  &lt;span style=&quot;font-size: 13px; line-height: 18px; text-align: right;&quot;&gt;{comment}&lt;/span&gt;&lt;/td&gt;		&lt;/tr&gt;	&lt;/tbody&gt;&lt;/table&gt;	&lt;/div&gt;', `status` = '1', `language_id` = '1'");

    // Insert email template for out of stock
    $this->db->query("INSERT INTO `" . DB_PREFIX . "email` SET `text` = 'Out Of Stock', `text_id` = '1', `context` = 'out', `type` = 'stock', `status` = '1'");
    $email_id = $this->db->getLastId();
    $this->db->query("INSERT INTO `" . DB_PREFIX . "email_description` SET `email_id` = '" . $email_id . "', `name` = '{store_name} - {total_products} Product(s) Out Of Stock', `description` = 'Hello,&lt;br&gt;&lt;br&gt;We would like to notify that you have &lt;b&gt;{total_products}&lt;/b&gt; product(s) out of stock in store&nbsp;&lt;strong&gt;{store_name}&lt;/strong&gt;.&lt;br&gt;&lt;br&gt;You can view them by clicking on Notifications icon -&gt; Out of Stock, or browse to Catalog -&gt; Products and filter quantity = 0.&lt;br&gt;&lt;br&gt;Best Regards,&lt;br&gt;&lt;br&gt;The {store_name} team', `status` = '1', `language_id` = '1'");
    
    // Extension/Theme manager
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "addon` CHANGE `addon_files` `files` TEXT NULL");
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "extension` ADD `info` TEXT NULL AFTER `code`, ADD `params` TEXT NULL AFTER `info`");
    $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "theme` (
  `theme_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL,
  `info` text NULL,
  `params` text NULL,
  PRIMARY KEY (`theme_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

    // Update params of addons
    $addons = $this->db->query("SELECT * FROM `" . DB_PREFIX . "addon`")->rows;
    foreach ($addons as $addon) {
        $params = json_decode($addon['params'], true);
        
        // Update to language_id
        if (isset($params['localisation/language'])) {
            $params['language_id'] = $params['localisation/language'];
            unset($params['localisation/language']);
        }
        
        // Set theme/extension ids
        $params['theme_ids'] = array();
        $params['extension_ids'] = array();
            
        $this->db->query("UPDATE `" . DB_PREFIX . "addon` SET `params` = '" . json_encode($params) . "' WHERE `addon_id` = '" . $addon['addon_id'] . "'");
    }
    
    // Invoices
    $this->db->query("CREATE TABLE `" . DB_PREFIX . "invoice` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `invoice_date` datetime NOT NULL,
  PRIMARY KEY (`invoice_id`),
  KEY `order_id` (`order_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
    $this->db->query("CREATE TABLE `" . DB_PREFIX . "invoice_history` (
  `invoice_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `notify` tinyint(1) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`invoice_history_id`),
  KEY `invoice_id` (`invoice_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

    $orders = $this->db->query("SELECT order_id, date_added FROM `" . DB_PREFIX . "order` WHERE `invoice_no` <> ''")->rows;
    foreach ($orders as $order) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "invoice SET invoice_date = '" . $this->db->escape($order['date_added']) . "', order_id = '" . (int) $order['order_id'] . "'");
    }
    
    $this->db->query("INSERT INTO `" . DB_PREFIX . "email` SET `text` = 'Invoice Mail', `text_id` = '1', `context` = 'invoice.mail', `type` = 'invoice', `status` = '1'");
    $email_id = $this->db->getLastId();
    $this->db->query("INSERT INTO `" . DB_PREFIX . "email_description` SET `email_id` = '" . $email_id . "', `name` = 'Invoice for your {order_id} order', `description` = '&lt;p&gt;Hello {customer},&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;Thank you for your interest in {store_name} products. You can find the invoice in the attachment.&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;Please reply to this e-mail if you have any questions.&lt;br&gt;&lt;/p&gt;', `status` = '1', `language_id` = '1'");
    
    $this->db->query("INSERT INTO `" . DB_PREFIX . "email` SET `text` = 'Invoice History', `text_id` = '2', `context` = 'invoice.history', `type` = 'invoice', `status` = '1'");
    $email_id = $this->db->getLastId();
    $this->db->query("INSERT INTO `" . DB_PREFIX . "email_description` SET `email_id` = '" . $email_id . "', `name` = '{store_name} Invoice - {invoice_no} History', `description` = '&lt;p&gt;Invoice No.:&amp;nbsp;{invoice_no}&lt;/p&gt;&lt;p&gt;Invoice Date:&amp;nbsp;{date_added}&lt;br&gt;&lt;/p&gt;&lt;p&gt;The comments for your invoice are:&lt;/p&gt;&lt;p&gt;{comment}&lt;/p&gt;&lt;p&gt;Please reply to this email if you have any questions.&lt;br&gt;&lt;/p&gt;', `status` = '1', `language_id` = '1'");
    
    // Update user groups
    $user_groups = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group");

    foreach ($user_groups->rows as $user_group) {
        $user_group['permission'] = unserialize($user_group['permission']);
        
        $user_group['permission']['access'][] = 'appearance/theme';
        $user_group['permission']['modify'][] = 'appearance/theme';
        
        $user_group['permission']['access'][] = 'extension/extension';
        $user_group['permission']['modify'][] = 'extension/extension';
        
        $user_group['permission']['access'][] = 'sale/invoice';
        $user_group['permission']['modify'][] = 'sale/invoice';

        $user_group['permission']['access'][] = 'editor/summernote';
        $user_group['permission']['modify'][] = 'editor/summernote';        
        
        $user_group['permission']['access'][] = 'editor/tinymce';
        $user_group['permission']['modify'][] = 'editor/tinymce';
        
        $this->db->query("UPDATE " . DB_PREFIX . "user_group SET name = '" . $this->db->escape($user_group['name']) . "', permission = '" . $this->db->escape(serialize($user_group['permission'])) . "' WHERE user_group_id = '" . (int)$user_group['user_group_id'] . "'");
    }
    
    // Update users
    $users = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user");

    foreach ($users->rows as $user) {
        $params = '{"theme":"basic","basic_mode_message":"show","language":"' . $this->session->data['admin_language'] . '","editor":"summernote"}';
        
        $this->db->query("UPDATE " . DB_PREFIX . "user SET params = '" . $this->db->escape($params) . "' WHERE user_id = '" . (int)$user['user_id'] . "'");
    }
    
    if (!file_exists(DIR_ADMIN . 'controller/module/pp_login.php')) {
        $this->filesystem->remove(DIR_CATALOG . 'event/app/pp_login.php');
    }
}

// 1.2.4 changes;
if (version_compare(VERSION, '1.2.4', '<')) {
    // Update download table
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "download` CHANGE `filename` `filename` varchar(255) NOT NULL");
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "download` CHANGE `mask` `mask` varchar(255) NOT NULL");
}
