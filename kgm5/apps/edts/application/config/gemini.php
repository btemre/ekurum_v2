<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['gemini_api_key'] = getenv('GEMINI_API_KEY') ?: '';
$config['gemini_api_url'] = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
$config['gemini_max_tokens'] = 1024;
