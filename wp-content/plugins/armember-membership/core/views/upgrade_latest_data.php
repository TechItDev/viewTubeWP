<?php
global $wpdb, $arm_newdbversion;

if (version_compare($arm_newdbversion, '1.5', '<')) {
    global $wpdb, $wp, $ARMember;
    $pt_log_table = $ARMember->tbl_arm_payment_log;
    $bt_log_table = $ARMember->tbl_arm_bank_transfer_log;

    $wpdb->query("ALTER TABLE `{$pt_log_table}` ADD `arm_coupon_on_each_subscriptions` TINYINT(1) NULL DEFAULT '0' AFTER `arm_coupon_discount_type`; ");

    $wpdb->query("ALTER TABLE `{$bt_log_table}` ADD `arm_coupon_on_each_subscriptions` TINYINT(1) NULL DEFAULT '0' AFTER `arm_coupon_discount_type`; ");

}

if (version_compare($arm_newdbversion, '1.8', '<')) {
     global $arm_global_settings, $arm_member_forms;

     $all_global_settings = $arm_global_settings->arm_get_all_global_settings();
     $all_global_settings['general_settings']['spam_protection'] = 1;
     $new_global_settings_result = $all_global_settings;
     update_option('arm_global_settings', $new_global_settings_result);
     
    $old_preset_fields     = get_option("arm_preset_form_fields");
    $old_preset_fields     = maybe_unserialize(maybe_unserialize($old_preset_fields));
    $default_preset_fields = $arm_member_forms->arm_default_preset_user_fields();
    if (isset($default_preset_fields['country']['options']) && !empty($default_preset_fields['country']['options']) && isset($old_preset_fields['default']['country'])) {
        $old_preset_fields['default']['country']['options'] = $default_preset_fields['country']['options'];
	
	$updated_preset_fields = $old_preset_fields;
	update_option("arm_preset_form_fields", $updated_preset_fields);
    }
}
if (version_compare($arm_newdbversion, '2.1', '<')) {
    global $wpdb, $wp, $ARMember;
    $pt_log_table = $ARMember->tbl_arm_payment_log;
    $bt_log_table = $ARMember->tbl_arm_bank_transfer_log;
    $arm_bank_table_log_flag=get_option('arm_bank_table_log_flag');

    $arm_old_plan_row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$pt_log_table."' AND column_name = 'arm_old_plan_id'");
    if(empty($arm_old_plan_row)){
        $wpdb->query("ALTER TABLE `{$pt_log_table}` ADD `arm_old_plan_id` bigint(20) NOT NULL DEFAULT '0' AFTER `arm_plan_id`");
    }    

    $arm_payment_cycle_row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$pt_log_table."' AND column_name = 'arm_payment_cycle'");
    if(empty($arm_payment_cycle_row)){
        $wpdb->query("ALTER TABLE `{$pt_log_table}` ADD `arm_payment_cycle` INT(11) NOT NULL DEFAULT '0' AFTER `arm_payment_mode`");
    }

    $arm_bank_name_row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$pt_log_table."' AND column_name = 'arm_bank_name'");
    if(empty($arm_bank_name_row)){
        $wpdb->query("ALTER TABLE `{$pt_log_table}` ADD `arm_bank_name` VARCHAR(255) DEFAULT NULL AFTER `arm_payment_cycle`");
    }
    $arm_account_name_row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$pt_log_table."' AND column_name = 'arm_account_name'");
    if(empty($arm_account_name_row)){
        $wpdb->query("ALTER TABLE `{$pt_log_table}` ADD `arm_account_name` VARCHAR(255) DEFAULT NULL AFTER `arm_bank_name`");
    }
    $arm_additional_info_row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$pt_log_table."' AND column_name = 'arm_additional_info'");
    if(empty($arm_additional_info_row)){
        $wpdb->query("ALTER TABLE `{$pt_log_table}` ADD `arm_additional_info` LONGTEXT AFTER `arm_account_name`");
    }
    $arm_first_name_row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$pt_log_table."' AND column_name = 'arm_first_name'");
    if(empty($arm_first_name_row)){
        $wpdb->query("ALTER TABLE `{$pt_log_table}` ADD `arm_first_name` VARCHAR(255) DEFAULT NULL AFTER `arm_user_id`");
    }
    $arm_last_name_row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$pt_log_table."' AND column_name = 'arm_last_name'");
    if(empty($arm_last_name_row)){
        $wpdb->query("ALTER TABLE `{$pt_log_table}` ADD `arm_last_name` VARCHAR(255) DEFAULT NULL AFTER `arm_first_name`");
    }

    $arm_payment_transfer_mode_row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$pt_log_table."' AND column_name = 'arm_payment_transfer_mode'");
    if(empty($arm_payment_transfer_mode_row)) {
        $wpdb->query("ALTER TABLE `{$pt_log_table}` ADD `arm_payment_transfer_mode` VARCHAR( 255 ) NULL AFTER `arm_additional_info`");
    }

    if(empty($arm_bank_table_log_flag)){
        
        update_option('arm_bank_table_log_flag','1');

        $btquery = "SELECT * FROM `" . $bt_log_table . "`";
        $bt_payment_log = $wpdb->get_results($btquery, ARRAY_A);
        if(count($bt_payment_log)>0){
            foreach ($bt_payment_log as $bt_payment_log_data) {
                $arm_first_name=get_user_meta($bt_payment_log_data["arm_user_id"],'first_name',true);
                $arm_last_name=get_user_meta($bt_payment_log_data["arm_user_id"],'last_name',true);
                $arm_payment_mode=(!empty($bt_payment_log_data["arm_payment_mode"]))? $bt_payment_log_data["arm_payment_mode"]:'one_time';
                $arm_payment_type=(!empty($bt_payment_log_data["arm_payment_mode"]) && $bt_payment_log_data["arm_payment_mode"]=='manual_subscription')?'subscription':'one_time';
                $bt_insert_result=$wpdb->insert($pt_log_table, array(
                    'arm_invoice_id' => $bt_payment_log_data["arm_invoice_id"],
                    'arm_user_id' => $bt_payment_log_data["arm_user_id"],
                    'arm_first_name' => $arm_first_name,
                    'arm_last_name' => $arm_last_name,
                    'arm_plan_id' => $bt_payment_log_data["arm_plan_id"],
                    'arm_old_plan_id' =>$bt_payment_log_data["arm_old_plan_id"],
                    'arm_payer_email' => $bt_payment_log_data["arm_payer_email"],
                    'arm_transaction_id' => $bt_payment_log_data["arm_transaction_id"],
                    'arm_transaction_payment_type'=>$arm_payment_type,
                    'arm_payment_mode' => $arm_payment_mode,
                    'arm_payment_type' => $arm_payment_type,
                    'arm_payment_gateway' => 'bank_transfer',
                    'arm_payment_cycle' => $bt_payment_log_data["arm_payment_cycle"],
                    'arm_bank_name' => $bt_payment_log_data["arm_bank_name"],
                    'arm_account_name' => $bt_payment_log_data["arm_account_name"],
                    'arm_additional_info' => $bt_payment_log_data["arm_additional_info"],
                    'arm_amount' => $bt_payment_log_data["arm_amount"],
                    'arm_currency' => $bt_payment_log_data["arm_currency"],
                    'arm_extra_vars' => $bt_payment_log_data["arm_extra_vars"],
                    'arm_coupon_code' => $bt_payment_log_data["arm_coupon_code"],
                    'arm_coupon_discount' => $bt_payment_log_data["arm_coupon_discount"],
                    'arm_coupon_discount_type' => $bt_payment_log_data["arm_coupon_discount_type"],
                    'arm_coupon_on_each_subscriptions' => $bt_payment_log_data["arm_coupon_on_each_subscriptions"],
                    'arm_transaction_status' => $bt_payment_log_data["arm_status"],
                    'arm_is_trial' => $bt_payment_log_data["arm_is_trial"],
                    'arm_display_log' => $bt_payment_log_data["arm_display_log"],
                    'arm_payment_date' => $bt_payment_log_data["arm_created_date"],
                    'arm_created_date'=> $bt_payment_log_data["arm_created_date"],
                ));
                
            }
            
        }
    }    
        
}

if(version_compare($arm_newdbversion, '2.4', '<')) {
    
    global $wpdb, $wp, $ARMember;

    $arm_pt_log_table = $ARMember->tbl_arm_payment_log;
    $arm_entries_table = $ARMember->tbl_arm_entries;
    $arm_subscription_plans_table = $ARMember->tbl_arm_subscription_plans;
    $arm_activity_table = $ARMember->tbl_arm_activity;
    $arm_membership_setup_table = $ARMember->tbl_arm_membership_setup;

    $arm_add_payment_log_col = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_pt_log_table."' AND column_name = 'arm_is_post_payment'");
    if(empty($arm_add_payment_log_col)){
        $wpdb->query("ALTER TABLE `{$arm_pt_log_table}` ADD `arm_is_post_payment` TINYINT(1) NOT NULL DEFAULT '0' AFTER `arm_is_trial`");
    }
    
    $arm_add_payment_log_col = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_pt_log_table."' AND column_name = 'arm_paid_post_id'");
    if(empty($arm_add_payment_log_col)){
        $wpdb->query("ALTER TABLE `{$arm_pt_log_table}` ADD `arm_paid_post_id` BIGINT(20) NOT NULL DEFAULT '0' AFTER `arm_is_post_payment`");
    }
    
    $arm_add_entries_col = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_entries_table."' AND column_name = 'arm_is_post_entry'");
    if(empty($arm_add_entries_col)){
        $wpdb->query("ALTER TABLE `{$arm_entries_table}` ADD `arm_is_post_entry` TINYINT(1) NOT NULL DEFAULT '0' AFTER `arm_plan_id`");
    }
    
    $arm_add_entries_col = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_entries_table."' AND column_name = 'arm_paid_post_id'");
    if(empty($arm_add_entries_col)){
        $wpdb->query("ALTER TABLE `{$arm_entries_table}` ADD `arm_paid_post_id` BIGINT(20) NOT NULL DEFAULT '0' AFTER `arm_is_post_entry`");
    }

    $arm_add_subscription_plans = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_subscription_plans_table."' AND column_name = 'arm_subscription_plan_post_id'");
    if(empty($arm_add_subscription_plans)){
        $wpdb->query("ALTER TABLE `{$arm_subscription_plans_table}` ADD `arm_subscription_plan_post_id` BIGINT(20) NOT NULL DEFAULT '0' AFTER `arm_subscription_plan_role`");
    }

    $arm_add_activity_post_id = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_activity_table."' AND column_name = 'arm_paid_post_id'");
    if(empty($arm_add_activity_post_id)){
        $wpdb->query("ALTER TABLE `{$arm_activity_table}` ADD `arm_paid_post_id` BIGINT(20) NOT NULL DEFAULT '0' AFTER `arm_item_id`");
    }

    $arm_add_setup_type = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_membership_setup_table."' AND column_name = 'arm_setup_type'");
    if(empty($arm_add_setup_type)){
        $wpdb->query("ALTER TABLE `{$arm_membership_setup_table}` ADD `arm_setup_type` TINYINT(1) NOT NULL DEFAULT '0' AFTER `arm_setup_name`");
    }
}

if( version_compare($arm_newdbversion, '3.4.2', '<') )
{
    global $ARMember, $wpdb;

    $arm_updt_preset_field_option = 0;
    $get_preset_form_fields = get_option('arm_preset_form_fields');
    if(isset($get_preset_form_fields['default']))
    {
        if(empty($get_preset_form_fields['default']['user_login']['label']))
        {
            $get_preset_form_fields['default']['user_login']['label'] = __('Username', 'ARMember');
            $arm_updt_preset_field_option = 1;
        }
        if(empty($get_preset_form_fields['default']['user_email']['label']))
        {
            $get_preset_form_fields['default']['user_email']['label'] = __('Email Address', 'ARMember');
            $arm_updt_preset_field_option = 1;
        }
        if(empty($get_preset_form_fields['default']['user_pass']['label']))
        {
            $get_preset_form_fields['default']['user_pass']['label'] = __('Password', 'ARMember');
            $arm_updt_preset_field_option = 1;
        }

        if($arm_updt_preset_field_option==1)
        {
            update_option('arm_preset_form_fields', $get_preset_form_fields);

            $arm_form_field_option_arr = array();
            $arm_form_field_option_arr[0]['form_id'] = 101;
            $arm_form_field_option_arr[0]['form_field_slug'] = 'user_login';
            $arm_form_field_option_arr[0]['form_field_label'] = __('Username', 'ARMember');

            $arm_form_field_option_arr[1]['form_id'] = 101;
            $arm_form_field_option_arr[1]['form_field_slug'] = 'user_pass';
            $arm_form_field_option_arr[1]['form_field_label'] = __('Password', 'ARMember');

            $arm_form_field_option_arr[2]['form_id'] = 102;
            $arm_form_field_option_arr[2]['form_field_slug'] = 'user_login';
            $arm_form_field_option_arr[2]['form_field_label'] = __('Username', 'ARMember');

            $arm_form_field_option_arr[3]['form_id'] = 102;
            $arm_form_field_option_arr[3]['form_field_slug'] = 'user_pass';
            $arm_form_field_option_arr[3]['form_field_label'] = __('Password', 'ARMember');

            $arm_form_field_option_arr[4]['form_id'] = 103;
            $arm_form_field_option_arr[4]['form_field_slug'] = 'user_login';
            $arm_form_field_option_arr[4]['form_field_label'] = __('Username OR Email Address', 'ARMember');

            foreach($arm_form_field_option_arr as $arm_form_field_option_val_arr)
            {
                $arm_form_field_form_id = $arm_form_field_option_val_arr['form_id'];
                $arm_form_field_slug = $arm_form_field_option_val_arr['form_field_slug'];
                $form_field_label = $arm_form_field_option_val_arr['form_field_label'];

                $arm_check_form_user_login_arr = $wpdb->get_row("SELECT `arm_form_field_option` FROM `" . $ARMember->tbl_arm_form_field . "` WHERE `arm_form_field_form_id`='".$arm_form_field_form_id."' AND `arm_form_field_slug`='".$arm_form_field_slug."' ", ARRAY_A);
                    
                $arm_form_field_option = maybe_unserialize($arm_check_form_user_login_arr['arm_form_field_option']);
                if(!empty($arm_form_field_option) && is_array($arm_form_field_option))
                {
                    if( empty($arm_form_field_option['label']) )
                    {
                        $arm_form_field_option['label'] = $form_field_label;

                        $update_form_field_data = array('arm_form_field_option' => maybe_serialize($arm_form_field_option) );
                        $form_update = $wpdb->update($ARMember->tbl_arm_form_field, $update_form_field_data, array( 'arm_form_field_form_id' => $arm_form_field_form_id, 'arm_form_field_slug' => $arm_form_field_slug ) );
                    }
                }
            }
        }
    }
}

if(version_compare($arm_newdbversion, '3.4.4', '<')) {
    
    global $wpdb, $wp, $ARMember;

    $arm_pt_log_table = $ARMember->tbl_arm_payment_log;
    $arm_entries_table = $ARMember->tbl_arm_entries;
    $arm_subscription_plans_table = $ARMember->tbl_arm_subscription_plans;
    $arm_activity_table = $ARMember->tbl_arm_activity;

    //Add the arm_subscription_plan_gift_status for the Gift
    $arm_add_subscription_plan_gift_status_column = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_subscription_plans_table."' AND column_name = 'arm_subscription_plan_gift_status'");
    if(empty($arm_add_subscription_plan_gift_status_column)) {
        $wpdb->query("ALTER TABLE `{$arm_subscription_plans_table}` ADD `arm_subscription_plan_gift_status` INT(1) NOT NULL DEFAULT '0' AFTER `arm_subscription_plan_post_id`");
    }

    //Add the arm_gift_plan_id for the Gift 
    $arm_add_activity_column = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_activity_table."' AND column_name = 'arm_gift_plan_id'");
    if( empty($arm_add_activity_column) ) {
        $wpdb->query("ALTER TABLE `{$arm_activity_table}` ADD `arm_gift_plan_id` BIGINT(20) NOT NULL DEFAULT '0' AFTER `arm_paid_post_id`");
    }

    // Add column arm_is_gift_payment for gift.
    $arm_add_payment_log_col = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_pt_log_table."' AND column_name = 'arm_is_gift_payment'");
    if(empty($arm_add_payment_log_col)) {
        $wpdb->query("ALTER TABLE `{$arm_pt_log_table}` ADD `arm_is_gift_payment` TINYINT(1) NOT NULL DEFAULT '0' AFTER `arm_paid_post_id`");
    }

    $arm_add_entries_col = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_entries_table."' AND column_name = 'arm_is_gift_entry'");
    if(empty($arm_add_entries_col)) {
        $wpdb->query("ALTER TABLE `{$arm_entries_table}` ADD `arm_is_gift_entry` TINYINT(1) NOT NULL DEFAULT '0' AFTER `arm_paid_post_id`");
    }

    //update setup default style with old colors.
    $arm_setup_qry = "SELECT *  FROM `" . $ARMember->tbl_arm_membership_setup . "`";
    $setup_data = $wpdb->get_results($arm_setup_qry, ARRAY_A);
    if(!empty($setup_data)) {
        $default_setup_style = array(
            'content_width' => '800', 'plan_skin' => 'skin1', 'hide_current_plans' => 0,
            'plan_selection_area' => 'before', 'font_family' => 'Helvetica', 'title_font_size' => 20,
            'title_font_bold' => 0, 'title_font_italic' => '', 'title_font_decoration' => '',
            'description_font_size' => 16, 'description_font_bold' => 0, 'description_font_italic' => '',
            'description_font_decoration' => '', 'price_font_size' => 30, 'price_font_bold' => 0,
            'price_font_italic' => '', 'price_font_decoration' => '', 'summary_font_size' => 16,
            'summary_font_bold' => 0, 'summary_font_italic' => '', 'summary_font_decoration' => '',
            'plan_title_font_color' => '#616161', 'plan_desc_font_color' => '#616161', 'price_font_color' => '#616161',
            'summary_font_color' => '#616161', 'bg_active_color' => '#23b7e5', 'selected_plan_title_font_color' => '#23b7e5',
            'selected_plan_desc_font_color' => '#616161', 'selected_price_font_color' => '#FFFFFF',
        );
        foreach ($setup_data as $setup_data_key => $setup_data_value) {
            $arm_setup_module = maybe_unserialize($setup_data_value['arm_setup_modules']);
            if(is_array($arm_setup_module) && !empty($arm_setup_module)) 
            {
                $arm_setup_module['style'] = $default_setup_style;
                $arm_setup_id = $setup_data_value['arm_setup_id'];
                $db_data = array('arm_setup_modules' => maybe_serialize($arm_setup_module));
                $field_update = $wpdb->update($ARMember->tbl_arm_membership_setup, $db_data, array('arm_setup_id' => $arm_setup_id));
            }
        }
    }

}

if(version_compare($arm_newdbversion, '3.4.9', '<')) {

    global $wpdb, $ARMember;
    $arm_members_table = $ARMember->tbl_arm_members;

    $arm_add_arm_user_plan_ids_col = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_members_table."' AND column_name = 'arm_user_plan_ids'");
    if(empty($arm_add_arm_user_plan_ids_col)){
        $wpdb->query("ALTER TABLE `{$arm_members_table}` ADD `arm_user_plan_ids` TEXT NULL AFTER `arm_secondary_status`");
    }

    $arm_add_arm_user_suspended_plan_ids_col = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME = '".$arm_members_table."' AND column_name = 'arm_user_suspended_plan_ids'");
    if(empty($arm_add_arm_user_suspended_plan_ids_col)){
        $wpdb->query("ALTER TABLE `{$arm_members_table}` ADD `arm_user_suspended_plan_ids` TEXT NULL AFTER `arm_user_plan_ids`");
    }
    
}

$arm_newdbversion = '3.4.9';
update_option('arm_new_version_installed',1);
update_option('armlite_version', $arm_newdbversion);

$arm_lite_version_updated_date_key = 'arm_lite_version_updated_date_'.$arm_newdbversion;
$arm_lite_version_updated_date = current_time('mysql');
update_option($arm_lite_version_updated_date_key, $arm_lite_version_updated_date);