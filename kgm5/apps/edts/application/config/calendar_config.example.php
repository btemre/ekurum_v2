<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| Takvim bildirimleri (e-posta / push) ayarları.
| Bu dosyayı calendar_config.php olarak kopyalayıp düzenleyin.
*/

$config['calendar_smtp_host']     = 'smtp.example.com';
$config['calendar_smtp_user']     = '';
$config['calendar_smtp_pass']     = '';
$config['calendar_smtp_port']     = 587;
$config['calendar_smtp_crypto']   = 'tls';
$config['calendar_mail_from']    = 'noreply@example.com';
$config['calendar_mail_from_name']= 'EDTS Takvim';

/*
| Web Push (VAPID) - push bildirimleri için.
| minishlink/web-push kurulumu ve public/private key üretimi gerekir.
*/
$config['calendar_push_vapid_public']  = '';
$config['calendar_push_vapid_private'] = '';
