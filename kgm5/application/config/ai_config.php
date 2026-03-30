<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['ai_gemini_api_key']     = 'AIzaSyBqHkdH_HU9yB17ZfbSl3oimdgc1KfXFEw';
$config['ai_gemini_model']       = 'gemini-2.0-flash';
$config['ai_gemini_endpoint']    = 'https://generativelanguage.googleapis.com/v1beta/models/';

$config['ai_max_tokens']         = 2048;
$config['ai_temperature']        = 0.7;

$config['ai_rate_limit_per_user']  = 50;
$config['ai_rate_limit_per_tenant'] = 500;
$config['ai_rate_limit_period']    = 'daily';

$config['ai_log_enabled']        = TRUE;
$config['ai_log_table']          = 'r8t_sys_ai_logs';

$config['ai_default_language']   = 'tr';

$config['ai_kvkk_mask_fields']   = array(
    'tc_kimlik', 'telefon', 'cep_telefon', 'eposta',
    'adres', 'iban', 'kredi_kart'
);

$config['ai_allowed_tables']     = array(
    'r8t_edts_durusmalar',
    'r8t_edys_dosya',
    'r8t_edys_dosya_mahkemeler',
    'r8t_sys_mahkemeler'
);

$config['ai_blocked_tables']     = array(
    'r8t_users',
    'r8t_sys_group_permissions',
    'r8t_sys_statu_permissions',
    'r8t_sys_userlogs'
);

$config['ai_request_timeout']    = 30;
