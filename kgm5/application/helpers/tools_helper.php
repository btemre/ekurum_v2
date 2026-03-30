<?php

defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\SMTP;

use PHPMailer\PHPMailer\Exception;

require 'Exception.php';

require 'PHPMailer.php';

require 'SMTP.php';

### POST VERISINI TEMIZLEME (BOŞLUK TEMİZLENMEZ) #######

function replacePost($post)

{

  $p1 = array("'", '"', "<", ">", "'", "^", "/", ":", ";", "(", ")", "{", "}", "=", "é", "½", "refresh", "location", "select", "union", "from", "\n", "&nbsp;");

  $p2 = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");

  $p3 = str_replace($p1, $p2, $post);

  return $p3;

}



### POST VERISINI TEMIZLEME - PAROLA POSTLARI TEMIZLEME

function replacePass($post)

{

  $p1 = array("'", '"', "<", ">", "/", "é", "½", "refresh", "location", "\n", "&nbsp;", " ", "select", "union", "from");

  $p2 = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "");

  $p3 = str_replace($p1, $p2, $post);

  return $p3;

}



function replaceHtml($post)

{

  $p1 = array("'", '"', "é", "½", "refresh", "location", "&nbsp;", "select", "union", "from");

  $p2 = array("", "", "", "", "", "", "", "", "", "");

  $p3 = str_replace($p1, $p2, $post);

  return $p3;

}





function isJSON($string)

{

  return is_string($string) && is_array(json_decode($string, true)) ? true : false;

}





function validDate($date, $format = 'Y-m-d H:i:s')

{

  $d = @DateTime::createFromFormat($format, $date);

  return @$d && $d->format($format) == $date;

}





### TIME() DEGERINI YY-mm-dd HH:ii:ss FORMATINDAKI TARIHE DONUSTURUR.

function timeToDate($tmDeger = FALSE, $format = "Y-m-d H:i:s")

{

  $tmL = date("Y-m-d H:i:s", time());

  if ($tmDeger != FALSE && @is_numeric($tmDeger)) {

    $tmL = date($format, $tmDeger);

  }

  return $tmL;

}





### TIME() DEGERINI YY-mm-dd HH:ii:ss FORMATINDAKI TARIHE DONUSTURUR.

function timeToDateFormat($tmDeger = FALSE, $format = "Y-m-d H:i:s")

{

  $tmL = date("Y-m-d H:i:s", time());

  if ($tmDeger != FALSE && @is_numeric($tmDeger)) {

    $tmL = date($format, $tmDeger);

  }

  return $tmL;

}







### YY-mm-dd HH:ii:ss FORMATINDAKI TARIHIN TIME() DEGERINI BULUR

function dateToTime($dateT = FALSE)

{

  $tarihT = time();

  if ($dateT != FALSE) {

    $tText = @explode(" ", $dateT);

    list($y, $a, $g) = @explode("-", $tText[0]);

    list($sa, $da, $san) = @explode(":", $tText[1]);

    $tarihT = @mktime($sa, $da, $san, $a, $g, $y);

  }

  return $tarihT;

}





### $format ile belirtilen Örn:Y-m-d H:i:s FORMATINDAKI TARIHIN TIME() DEGERINI BULUR

function dateToTimeFormat($dateTX = FALSE, $format = "Y-m-d")

{

  $tarihT = ""; //time();

  $dateT = trim($dateTX);

  $y = $a = $g = $sa = $da = $san = "";

  if (strlen($dateT) > 5) {

    switch ($format) {

      case "Y-m-d":

        if ($dateT != FALSE) {

          $tText = $dateT;

          list($y, $a, $g) = @explode("-", $tText);

          list($sa, $da, $san) = array("00", "00", "00");

          $tarihT = @mktime($sa, $da, $san, $a, $g, $y);

        }

        break;

      case "d-m-Y":

        if ($dateT != FALSE) {

          $tText = $dateT;

          list($g, $a, $y) = @explode("-", $tText);

          list($sa, $da, $san) = array("00", "00", "00");

          $tarihT = @mktime($sa, $da, $san, $a, $g, $y);

        }

        break;

      case "Y-m-d H:i:s":

        if ($dateT != FALSE) {

          $tText = @explode(" ", $dateT);

          list($y, $a, $g) = @explode("-", $tText[0]);

          list($sa, $da, $san) = @explode(":", $tText[1]);

          $tarihT = @mktime($sa, $da, $san, $a, $g, $y);

        }

        break;

      case "d-m-Y H:i:s":

        if ($dateT != FALSE) {

          $tText = @explode(" ", $dateT);

          list($g, $a, $y) = @explode("-", $tText[0]);

          list($sa, $da, $san) = @explode(":", $tText[1]);

          $tarihT = @mktime($sa, $da, $san, $a, $g, $y);

        }

        break;

      case "d.m.Y H:i:s":

        if ($dateT != FALSE) {

          $tText = @explode(" ", $dateT);

          list($g, $a, $y) = @explode(".", $tText[0]);

          list($sa, $da, $san) = @explode(":", $tText[1]);

          $tarihT = @mktime($sa, $da, $san, $a, $g, $y);

        }

        break;

      case "Y.m.d":

        if ($dateT != FALSE) {

          $tText = $dateT;

          list($y, $a, $g) = @explode(".", $tText);

          list($sa, $da, $san) = array("00", "00", "00");

          $tarihT = @mktime($sa, $da, $san, $a, $g, $y);

        }

        break;

      case "d.m.Y":

        if ($dateT != FALSE) {

          $tText = $dateT;

          list($g, $a, $y) = @explode(".", $tText);

          list($sa, $da, $san) = array("00", "00", "00");

          $tarihT = @mktime($sa, $da, $san, $a, $g, $y);

        }

        break;

      case "Y.m.d H:i:s":

        if ($dateT != FALSE) {

          $tText = @explode(" ", $dateT);

          list($y, $a, $g) = @explode(".", $tText[0]);

          list($sa, $da, $san) = @explode(":", $tText[1]);

          $tarihT = @mktime($sa, $da, $san, $a, $g, $y);

        }

        break;

      case "d.m.Y H:i:s":

        if ($dateT != FALSE) {

          $tText = @explode(" ", $dateT);

          list($g, $a, $y) = @explode(".", $tText[0]);

          list($sa, $da, $san) = @explode(":", $tText[1]);

          $tarihT = @mktime($sa, $da, $san, $a, $g, $y);

        }

        break;

      default:

        if ($dateT != FALSE) {

          $tText = $dateT;

          list($y, $a, $g) = @explode("-", $tText);

          list($sa, $da, $san) = array("00", "00", "00");

          $tarihT = @mktime($sa, $da, $san, $a, $g, $y);

        }

        break;

    }

  }

  return @$tarihT;

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

    $_region = file_get_contents($_lokupUrl);

  }

  return $_region;

}



function sysDurumYaz($durum = false)

{

  $_durum = (int) trim(replacePost($durum));

  $_durumX = "Bilinmiyor";

  switch ($_durum) {

    case -1:

      $_durumX = "Arşivde";

      break;

    case 0:

      $_durumX = "Pasif";

      break;

    case 1:

      $_durumX = "Aktif";

      break;

    default:

      $_durumX = "Bilinmiyor";

      break;

  }

  return $_durumX;

}



function metinBul($metin, $ilk, $son, $ilkSonDahil = true)

{

  $ilkPos = stripos($metin, $ilk);

  //echo $metin;

  if ($ilkPos === false) {

    return false;

  }



  if (!$ilkSonDahil) {

    $ilkPos += strlen($ilk);

  }

  $metin2 = substr($metin, $ilkPos);

  //echo $metin2;

  $sonPos = stripos($metin2, $son);



  if ($sonPos === false) {

    $sonuc = $metin2;

  } else {

    if ($ilkSonDahil) {

      $sonPos += strlen($son);

    }

    $sonuc = substr($metin2, 0, $sonPos);

  }

  return $sonuc;

}



function headerAppList()

{

  $t = &get_instance();

  $t->load->model("RT_model");

  $appList = $t->RT_model->ek_get_all(

    "r8t_sys_apps",

    array(

      "a_status" => 1

    )

  );

  return $appList;

}




function MailGonder($posta_mail, $posta_konu, $posta_mesaj)
{

  $mail = new PHPMailer();
  try {
    //Sunucu ayarları
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;           // Hata ayıklamak için debug etkin
    $mail->isSMTP(); // SMTP kullanarak gönderim
    $mail->Host = 'smtp.gmail.com'; // SMTP sunucusu
    $mail->SMTPAuth = true; // SMTP kimlik doğrulaması etkin
    $mail->Username = 'ekurumcom@gmail.com'; // SMTP kullanıcısı
    $mail->Password = 'mlaulseerucbenbh'; // SMTP şifre
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS ile şifreleme etkin
    $mail->Port = 587; // SMTP port
    //Karakter ayarları
    $mail->CharSet = "utf-8"; //Türkçe karakter sorununun önüne geçecektir.
    $mail->Encoding = "base64";
    // Alıcılar
    $mail->setFrom($posta_mail, $posta_konu);
    $mail->addAddress($posta_mail, $posta_konu); // Alıcı
    //$mail->addReplyTo('info@ornek.com', 'Bilgi');
    //$mail->addCC('cc@ornek.com');
    //$mail->addBCC('bcc@ornek.com');
    // İçerik
    $mail->isHTML(true); // Mail HTML formatında olacaktır.
    $mail->Subject = $posta_konu;
    $mail->Body = $posta_mesaj;
    $mail->AltBody = 'non-HTML mail istemcileri için mesaj gövdesidir.';
    $mail->send();
    echo '<div class="alert alert-success alert-solid shadow" role="alert"><strong>Mesaj Gönderildi!</strong></div>';
  } catch (Exception $e) {
    echo "<div class='alert alert-success alert-solid shadow'>Mesajınız gönderilemedi. Mailer Hata:</div> {$mail->ErrorInfo}";
  }
}





function generateResetToken() {

  $length = rand(6, 10);

  $characters = '0123456789';

  $token = '';



  for ($i = 0; $i < $length; $i++) {

      $index = rand(0, strlen($characters) - 1);

      $token .= $characters[$index];

  }



  return $token;

}



