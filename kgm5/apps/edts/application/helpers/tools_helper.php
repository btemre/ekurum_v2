<?php
defined('BASEPATH') or exit('No direct script access allowed');

### POST VERISINI TEMIZLEME (BOŞLUK TEMİZLENMEZ) #######
function replacePost($post)
{
  $subject = (string)($post ?? '');
  $p1 = array("'", '"', "<", ">", "'", "^", "/", ":", ";", "(", ")", "{", "}", "=", "é", "½", "refresh", "location", "select", "union", "from", "\n", "&nbsp;");
  $p2 = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
  return str_replace($p1, $p2, $subject);
}

function replaceHtml($post)
{
  $subject = (string)($post ?? '');
  $p1 = array("'", '"', "<", ">", "'", "^", "/", ";", "(", ")", "{", "}", "=", "é", "½", "refresh", "location", "select", "union", "from", "\n", "&nbsp;");
  $p2 = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
  return str_replace($p1, $p2, $subject);
}

function replaceHtml_Slash($post)
{
  $subject = (string)($post ?? '');
  $p1 = array("'", '"', "<", ">", "'", "^", ";", "(", ")", "{", "}", "=", "é", "½", "refresh", "location", "select", "union", "from", "\n", "&nbsp;");
  $p2 = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
  return str_replace($p1, $p2, $subject);
}

### POST VERISINI TEMIZLEME - PAROLA POSTLARI TEMIZLEME
function replacePass($post)
{
  $subject = (string)($post ?? '');
  $p1 = array("'", '"', "<", ">", "/", "é", "½", "refresh", "location", "\n", "&nbsp;", " ", "select", "union", "from");
  $p2 = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
  return str_replace($p1, $p2, $subject);
}

function isJSON($string)
{
  return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}


function validDate($date, $format = 'Y-m-d H:i:s')
{
  $d = DateTime::createFromFormat($format, $date);
  return $d && $d->format($format) == $date;
}

### TIME() DEGERINI YY-mm-dd HH:ii:ss FORMATINDAKI TARIHE DONUSTURUR.
function timeToDate($tmDeger = FALSE, $format = "Y-m-d H:i:s")
{
  $tmL = "";
  if ($tmDeger != FALSE && @is_numeric($tmDeger)) {
    $dt = new DateTime('@' . (int)$tmDeger);
    $dt->setTimezone(new DateTimeZone('Europe/Istanbul'));
    $tmL = $dt->format($format);
  }
  return $tmL;
}


### TIME() DEGERINI YY-mm-dd HH:ii:ss FORMATINDAKI TARIHE DONUSTURUR.
function timeToDateFormat($tmDeger = FALSE, $format = "Y-m-d H:i:s")
{
  static $tz = null;
  if ($tz === null) $tz = new DateTimeZone('Europe/Istanbul');
  $tmL = "";
  if ($tmDeger != FALSE && @is_numeric($tmDeger)) {
    $dt = new DateTime('@' . (int)$tmDeger);
    $dt->setTimezone($tz);
    $tmL = $dt->format($format);
  }
  return $tmL;
}


### Y-m-d H:i:s FORMATINDAKI TARIHIN TIME() DEGERINI BULUR
function dateToTime($dateT = FALSE)
{
  $tarihT = "";
  if ($dateT != FALSE) {
    $tText = @explode(" ", $dateT);
    list($y, $a, $g)     = @explode("-", $tText[0]);
    list($sa, $da, $san)  = @explode(":", $tText[1]);
    $tz = new DateTimeZone('Europe/Istanbul');
    $dt = @DateTime::createFromFormat('Y-m-d H:i:s', "$y-$a-$g $sa:$da:$san", $tz);
    $tarihT = $dt ? $dt->getTimestamp() : @mktime($sa, $da, $san, $a, $g, $y);
  }
  return $tarihT;
}


### $format ile belirtilen Örn:Y-m-d H:i:s FORMATINDAKI TARIHIN TIME() DEGERINI BULUR
function dateToTimeFormat($dateTX = FALSE, $format = "Y-m-d")
{
  $tarihT = "";
  $dateT = trim($dateTX);
  if (strlen($dateT) > 5) {
    $tz = new DateTimeZone('Europe/Istanbul');
    $y = $a = $g = $sa = $da = $san = '00';
    switch ($format) {
      case "Y-m-d":
        if ($dateT != FALSE) {
          list($y, $a, $g) = @explode("-", $dateT);
        }
        break;
      case "d-m-Y":
        if ($dateT != FALSE) {
          list($g, $a, $y) = @explode("-", $dateT);
        }
        break;
      case "Y-m-d H:i:s":
        if ($dateT != FALSE) {
          $tText = @explode(" ", $dateT);
          list($y, $a, $g) = @explode("-", $tText[0]);
          list($sa, $da, $san) = @explode(":", $tText[1]);
        }
        break;
      case "d-m-Y H:i:s":
        if ($dateT != FALSE) {
          $tText = @explode(" ", $dateT);
          list($g, $a, $y) = @explode("-", $tText[0]);
          list($sa, $da, $san) = @explode(":", $tText[1]);
        }
        break;
      case "d.m.Y H:i:s":
        if ($dateT != FALSE) {
          $tText = @explode(" ", $dateT);
          list($g, $a, $y) = @explode(".", $tText[0]);
          list($sa, $da, $san) = @explode(":", $tText[1]);
        }
        break;
      case "Y.m.d":
        if ($dateT != FALSE) {
          list($y, $a, $g) = @explode(".", $dateT);
        }
        break;
      case "d.m.Y":
        if ($dateT != FALSE) {
          list($g, $a, $y) = @explode(".", $dateT);
        }
        break;
      case "Y.m.d H:i:s":
        if ($dateT != FALSE) {
          $tText = @explode(" ", $dateT);
          list($y, $a, $g) = @explode(".", $tText[0]);
          list($sa, $da, $san) = @explode(":", $tText[1]);
        }
        break;
      default:
        if ($dateT != FALSE) {
          list($y, $a, $g) = @explode("-", $dateT);
        }
        break;
    }
    $dt = @DateTime::createFromFormat('Y-m-d H:i:s', "$y-$a-$g $sa:$da:$san", $tz);
    $tarihT = $dt ? $dt->getTimestamp() : '';
  }
  return $tarihT;
}



### HTTP HEADER REQUEST STATUS HATA KODLARI TANIMLAMA
function HttpStatus($code)
{
  $status = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    306 => '(Unused)',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported'
  );

  // gönderilen kod listede yok ise 500 durum kodu gönderilsin.
  return $status[$code] ? $status[$code] : $status[500];
}

### HTTP HEADER REQUEST STATUS HATA KODLARI ATAMA
function SetHeader($code)
{
  header("HTTP/1.1 " . $code . " " . HttpStatus($code));
  header("Content-Type: application/json; charset=utf-8");
}

### IP ADRESINDEN KONUM BILGISI ALMA
function _ipLookup($_ip = FALSE)
{
  $_region = FALSE;
  //VERI ALINAN SITE ADRESI https://www.ipinfodb.com/
  $_lokupUrl = "https://api.ip2location.io/?key=b957d6f066bc38d05e6f21c35a3e6a24&ip=" . $_ip . "&format=json";
  if ($_ip != FALSE) {
    $_region    = file_get_contents($_lokupUrl);
  }
  return $_region;
}


function sys_url($data = false)
{
  $host = $_SERVER['HTTP_HOST'];
  $protocol = $_SERVER['PROTOCOL'] = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
  $url = $protocol . "://" . $host;
  if ($data == false) {
    $url .= "/";
  } else {
    $url .= "/" . $data;
  }
  return $url;
}


function sidebarMenuHeaderIsShow($modul=false){
  $sonuc = "";
  $t = &get_instance();
  $moduleName = $t->router->fetch_class();
  $moduleName   = trim(replacePost($moduleName));
  $modulB = "";
  switch ($moduleName) {
    case "dashboard":
      $modulB = "dashboard";
      break;
    case "durusmalar":
      $modulB = "durusmalar";
      break; 
    case "importexcel":
      $modulB = "importexcel";
      break; 
    default:
      $modulB = "dashboard";
      break;
  }

  if ($modul == $modulB) {
    $sonuc = " here show";
  }
  /*  if ($modul == false && $modulB == "dashborad") {
    $sonuc = " active show";
  } */
  return $sonuc;
}

function sidebarSegmentIsShow($modul=false, $segment=false){
  $sonuc = "";
  $t = &get_instance();
  $moduleName = $t->router->fetch_class();
  $moduleName   = trim(replacePost($moduleName));
  $urlModule   = $t->uri->segment(1);
  $segmentB   = $t->uri->segment(2);
  $modulB = "";
  switch ($urlModule) {
    case "dashboard":
      $modulB = "dashboard";
      break;
    case "durusmalar":
      $modulB = "durusmalar";
      break; 
    case "importexcel":
      $modulB = "importexcel";
      break; 
    default:
      $modulB = "dashboard";
      break;
  }

  if ($modul == $modulB && $segment==$segmentB) {
    $sonuc = " active";
  }
  /*  if ($modul == false && $modulB == "dashborad") {
    $sonuc = " active show";
  } */
  return $sonuc;

}


function asideMenuIsShow($modul = false)
{
  $sonuc = "";
  $t = &get_instance();
  $moduleName = $t->router->fetch_class();
  $moduleName   = trim(replacePost($moduleName));
  $modulB = "";
  switch ($moduleName) {
    case "dashboard":
      $modulB = "dashboard";
      break;
    default:
      $modulB = "dashboard";
      break;
  }

  if ($modul == $modulB) {
    $sonuc = " active show";
  }
  /*  if ($modul == false && $modulB == "dashborad") {
    $sonuc = " active show";
  } */
  return $sonuc;
}


function asideTabTitleIsShow($modul = false)
{
  $sonuc = "";
  $t = &get_instance();
  $moduleName = $t->router->fetch_class();
  $moduleName   = trim(replacePost($moduleName));
  $modulB = "";
  switch ($moduleName) {
    case "dashboard":
      $modulB = "dashboard";
      break;
    default:
      $modulB = "dashboard";
      break;
  }

  if ($modul == $modulB) {
    $sonuc = " active";
  }
  /* 
  if ($modul == false && $modulB == "dashborad") {
    $sonuc = " active";
  }

 */
  return $sonuc;
}

function toupperText($text = false)
{
  $_text  = $text;
  $old = array("ı", "ğ", "ü", "ş", "ö", "ç");
  $new = array("I", "Ğ", "{U}", "Ş", "Ö", "Ç");
  if ($text != false) {
    $text = str_replace("Ü", "{U}", $text);
    $text = str_replace($old, $new, $text);
    $text = strtoupper($text);
    $text = str_replace("{U}", "Ü", $text);
    $_text = $text;
  }
  return $_text;
}

function headerAppList()
{
  $t = &get_instance();
  $t->load->model("RT_model");
  $appList = $t->RT_model->ek_get_all(
    "r8t_sys_apps",
    array(
      "a_status"      => 1
    )
  );
  return $appList;
}



function etAyracsizYazdir($item = false)
{
  $_text = "";
  if ($item != false) {
    $_texts = @explode("@", $item);
    for ($i = 0; $i <= count($_texts) - 2; $i++) {
      if (stripos($_text, trim($_texts[$i])) < 1) {
        if ($i == 0) {
          $_text .= trim($_texts[$i]);
        } elseif ($i > 0 && $i != (count($_texts) - 2)) {
          $_text .= " " . trim($_texts[$i]);
        } else {
          $_text .= " " . trim($_texts[$i]);
        }
      }
    }
  }
  $_text = str_replace(',,', ',', $_text);
  return $_text;
}

function uploadFile($img, $list)
{
  $input = $img;
  $klasor = $list;
  $target_dir = $klasor;

  $target_file = $target_dir . basename($_FILES[$input]["name"]);
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
  $filename   = uniqid();
  $extension  = pathinfo($_FILES[$input]["type"] == "png" || $_FILES[$input]["type"] == "jpeg" || $_FILES[$input]["type"] == "jpg" || $_FILES[$input]["type"] == "gif", PATHINFO_EXTENSION);
  $basename   =  $filename. $extension . "." . $imageFileType;
  $yeniyol = $target_dir . $basename;
  move_uploaded_file($_FILES[$input]["tmp_name"], $yeniyol);
  return $basename;
}