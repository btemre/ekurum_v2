<?php
defined('BASEPATH') or exit('No direct script access allowed');

function valid_required($str)
{
    return is_array($str)
        ? (empty($str) === FALSE)
        : (trim((string) $str) !== '');
}

function valid_date($date, $format = "Y-m-d")
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}


function valid_min_length($str, $val)
{
    if (!is_numeric($val)) {
        return FALSE;
    }

    return ($val <= mb_strlen($str));
}

function valid_max_length($str, $val)
{
    if (!is_numeric($val)) {
        return FALSE;
    }

    return ($val >= mb_strlen($str));
}


function valid_valid_url($str)
{
    if (empty($str)) {
        return FALSE;
    } elseif (preg_match('/^(?:([^:]*)\:)?\/\/(.+)$/', $str, $matches)) {
        if (empty($matches[2])) {
            return FALSE;
        } elseif (!in_array(strtolower($matches[1]), array('http', 'https'), TRUE)) {
            return FALSE;
        }

        $str = $matches[2];
    }

    // Apparently, FILTER_VALIDATE_URL doesn't reject digit-only names for some reason ...
    // See https://github.com/bcit-ci/CodeIgniter/issues/5755
    if (ctype_digit($str)) {
        return FALSE;
    }

    // PHP 7 accepts IPv6 addresses within square brackets as hostnames,
    // but it appears that the PR that came in with https://bugs.php.net/bug.php?id=68039
    // was never merged into a PHP 5 branch ... https://3v4l.org/8PsSN
    if (preg_match('/^\[([^\]]+)\]/', $str, $matches) && !is_php('7') && filter_var($matches[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== FALSE) {
        $str = 'ipv6.host' . substr($str, strlen($matches[1]) + 2);
    }

    return (filter_var('http://' . $str, FILTER_VALIDATE_URL) !== FALSE);
}

function valid_valid_email($str)
{
    if (function_exists('idn_to_ascii') && preg_match('#\A([^@]+)@(.+)\z#', $str, $matches)) {
        $domain = defined('INTL_IDNA_VARIANT_UTS46')
            ? idn_to_ascii($matches[2], 0, INTL_IDNA_VARIANT_UTS46)
            : idn_to_ascii($matches[2]);

        if ($domain !== FALSE) {
            $str = $matches[1] . '@' . $domain;
        }
    }

    return (bool) filter_var($str, FILTER_VALIDATE_EMAIL);
}

function valid_valid_emails($str)
{
    if (strpos($str, ',') === FALSE) {
        return valid_valid_email(trim($str));
    }

    foreach (explode(',', $str) as $email) {
        if (trim($email) !== '' && valid_valid_email(trim($email)) === FALSE) {
            return FALSE;
        }
    }

    return TRUE;
}

function valid_alpha_numeric_spaces($str)
{
    return (bool) preg_match('/^[A-Z0-9 ]+$/i', $str);
}

function valid_alpha_dash($str)
{
    return (bool) preg_match('/^[a-z0-9_-]+$/i', $str);
}

function valid_numeric($str)
{
    return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
}


function valid_integer($str)
{
    return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
}

function valid_decimal($str)
{
    return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
}


function valid_in_list($value, $list)
{
    return in_array($value, explode(',', $list), TRUE);
}

function get_encode_php_tags($str)
{
    return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $str);
}
