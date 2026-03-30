<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Takvim extends CI_Controller
{
    public $viewFolder = '';
    public $userData   = false;

    public function __construct()
    {
        parent::__construct();
        $this->viewFolder = 'takvim_v';
        $this->load->model('auth_model');
        $this->load->model('durusmalar_model');
        $this->load->model('takvim_model');
        $this->userData = $this->auth_model->userData;
    }

    private function _getUserId()
    {
        if (!$this->userData) {
            return 0;
        }
        if (isset($this->userData->userInfo->u_id) && (int) $this->userData->userInfo->u_id > 0) {
            return (int) $this->userData->userInfo->u_id;
        }
        if (isset($this->userData->userB->u_id) && (int) $this->userData->userB->u_id > 0) {
            return (int) $this->userData->userB->u_id;
        }
        return 0;
    }

    private function _checkAuth()
    {
        if ($this->userData === false) {
            $this->_jsonErr(401, 'Oturum bulunamadı.');
        }
        if (!isAllowedViewApp('edts')) {
            $this->_jsonErr(403, 'EDTS uygulamasına erişim yetkiniz yok.');
        }
    }

    private function _jsonErr($code, $message)
    {
        SetHeader($code);
        echo json_encode(array('success' => false, 'code' => $code, 'message' => $message));
        exit;
    }

    /**
     * Takvim sayfası
     */
    public function index()
    {
        if ($this->userData === false) {
            redirect(sys_url('login'));
            exit;
        }
        if (!isAllowedViewApp('edts')) {
            $alert = array(
                'title' => 'Hata!',
                'text'  => 'EDTS uygulaması için görüntüleme yetkiniz bulunmuyor.',
                'type'  => 'error'
            );
            $this->session->set_flashdata('alertToastr', $alert);
            redirect(sys_url('home'));
            exit;
        }

        $viewData = new stdClass();
        $viewData->viewFolder    = $this->viewFolder;
        $viewData->subViewFolder = 'list';
        $viewData->userData      = $this->userData;
        $viewData->moduleName    = 'takvim';
        $viewData->canWrite      = isDbAllowedWriteModule('durusmalar');

        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    /**
     * FullCalendar için olay listesi (GET start, end ISO tarih)
     */
    public function events()
    {
        $this->_checkAuth();

        $start = $this->input->get('start');
        $end   = $this->input->get('end');
        if (empty($start) || empty($end)) {
            $this->_jsonErr(400, 'start ve end parametreleri gerekli.');
        }

        $startTs = strtotime($start);
        $endTs   = strtotime($end);
        if ($startTs === false || $endTs === false) {
            $this->_jsonErr(400, 'Geçersiz tarih formatı.');
        }

        $avukatId = $this->input->get('avukat_id');
        $avukatId = ($avukatId !== null && $avukatId !== '') ? (int) $avukatId : null;
        if ($avukatId !== null && $avukatId <= 0) {
            $avukatId = null;
        }

        $rows = $this->durusmalar_model->getEventsForCalendar($startTs, $endTs, $avukatId);
        $events = array();

        $tz = new DateTimeZone('Europe/Istanbul');
        foreach ($rows as $r) {
            $ts = (int) $r->d_durusmatarihi;
            $dtObj = new DateTime('@' . $ts);
            $dtObj->setTimezone($tz);
            $timeStr = $dtObj->format('H:i');
            $title = trim($r->d_dosyano . ' · ' . $r->d_mahkeme);
            if (strlen($title) > 35) {
                $title = substr($title, 0, 32) . '…';
            }
            $dtEnd = new DateTime('@' . ($ts + 3600));
            $dtEnd->setTimezone($tz);
            $events[] = array(
                'id'             => (int) $r->d_id,
                'title'          => $title,
                'start'          => $dtObj->format('Y-m-d\TH:i:s'),
                'end'            => $dtEnd->format('Y-m-d\TH:i:s'),
                'extendedProps'  => array(
                    'd_id'         => (int) $r->d_id,
                    'd_dosyano'    => $r->d_dosyano,
                    'd_esasno'     => $r->d_esasno,
                    'd_mahkeme'    => $r->d_mahkeme,
                    'd_memur'      => $r->d_memur,
                    'd_avukat'     => $r->d_avukat,
                    'd_taraf'      => $r->d_taraf,
                    'd_islem'      => $r->d_islem,
                    'd_dosyaturu'  => $r->d_dosyaturu,
                ),
            );
        }

        SetHeader(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($events);
        exit;
    }

    /**
     * Tek duruşma detayı (modal için JSON). GET veya POST id.
     */
    public function eventDetail($id = 0)
    {
        $this->_checkAuth();
        $id = (int) ($id ?: $this->input->get_post('id'));
        if ($id <= 0) {
            $this->_jsonErr(400, 'Geçersiz kayıt.');
        }
        $row = $this->durusmalar_model->ek_get('r8t_edts_durusmalar', array('d_id' => $id, 'd_status' => 1));
        if (!$row) {
            $this->_jsonErr(404, 'Kayıt bulunamadı.');
        }
        $ts = (int) $row->d_durusmatarihi;
        $dtDetail = new DateTime('@' . $ts);
        $dtDetail->setTimezone(new DateTimeZone('Europe/Istanbul'));
        $out = array(
            'id'              => (int) $row->d_id,
            'dosyano'         => $row->d_dosyano,
            'dosyaturu'       => $row->d_dosyaturu,
            'mahkeme'         => $row->d_mahkeme,
            'durusmatarihi'   => $dtDetail->format('d.m.Y H:i'),
            'esasno'          => $row->d_esasno,
            'memur'           => $row->d_memur,
            'avukat'          => $row->d_avukat,
            'taraf'           => $row->d_taraf,
            'islem'           => $row->d_islem,
            'tarafbilgisi'    => $row->d_tarafbilgisi,
            'aciklama'        => $row->d_aciklama,
        );
        SetHeader(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success' => true, 'data' => $out));
        exit;
    }

    /**
     * Günlük/haftalık yoğunluk (GET start, end, granularity=day|week)
     */
    public function density()
    {
        $this->_checkAuth();

        $start = $this->input->get('start');
        $end   = $this->input->get('end');
        $gran  = $this->input->get('granularity');
        if (empty($start) || empty($end)) {
            $this->_jsonErr(400, 'start ve end parametreleri gerekli.');
        }
        $startTs = strtotime($start);
        $endTs   = strtotime($end);
        if ($startTs === false || $endTs === false) {
            $this->_jsonErr(400, 'Geçersiz tarih formatı.');
        }
        if ($gran !== 'week') {
            $gran = 'day';
        }

        $avukatId = $this->input->get('avukat_id');
        $avukatId = ($avukatId !== null && $avukatId !== '') ? (int) $avukatId : null;
        if ($avukatId !== null && $avukatId <= 0) {
            $avukatId = null;
        }

        $data = $this->durusmalar_model->getDensity($startTs, $endTs, $gran, $avukatId);
        SetHeader(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success' => true, 'data' => $data));
        exit;
    }

    /**
     * Bildirim tercihlerini getir (JSON).
     */
    public function getNotificationPrefs()
    {
        $this->_checkAuth();
        $userId = $this->_getUserId();
        $prefs  = $this->takvim_model->getNotificationPrefs($userId);
        $out    = array(
            'email_enabled'    => 1,
            'push_enabled'     => 0,
            'reminder_minutes' => 1440,
            'quiet_hours_start' => null,
            'quiet_hours_end'   => null,
        );
        if ($prefs) {
            $out['email_enabled']     = (int) $prefs->email_enabled;
            $out['push_enabled']      = (int) $prefs->push_enabled;
            $out['reminder_minutes']  = (int) $prefs->reminder_minutes;
            $out['quiet_hours_start'] = $prefs->quiet_hours_start;
            $out['quiet_hours_end']   = $prefs->quiet_hours_end;
        }
        SetHeader(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success' => true, 'data' => $out));
        exit;
    }

    /**
     * Bildirim tercihlerini kaydet (POST).
     */
    public function saveNotificationPrefs()
    {
        $this->_checkAuth();
        if ($this->input->method() !== 'post') {
            $this->_jsonErr(405, 'POST gerekli.');
        }
        $userId = $this->_getUserId();
        $input  = array(
            'email_enabled'     => $this->input->post('email_enabled'),
            'push_enabled'      => $this->input->post('push_enabled'),
            'reminder_minutes'  => $this->input->post('reminder_minutes'),
            'quiet_hours_start' => $this->input->post('quiet_hours_start'),
            'quiet_hours_end'   => $this->input->post('quiet_hours_end'),
        );
        $this->takvim_model->saveNotificationPrefs($userId, $input);
        SetHeader(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success' => true));
        exit;
    }

    /**
     * ICS feed için token veya token URL döndür (JSON).
     */
    public function getFeedToken()
    {
        $this->_checkAuth();
        $userId = $this->_getUserId();
        $token  = $this->takvim_model->getOrCreateFeedToken($userId);
        if (!$token) {
            $this->_jsonErr(500, 'Token oluşturulamadı.');
        }
        $base = $this->config->item('base_url');
        $feedUrl = rtrim($base, '/') . '/takvim/feed/' . $token;
        SetHeader(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success' => true, 'token' => $token, 'feed_url' => $feedUrl));
        exit;
    }

    /**
     * ICS takvim feed (token ile; oturum gerekmez).
     */
    public function feed($token = '')
    {
        if (empty($token)) {
            header('HTTP/1.0 404 Not Found');
            exit;
        }
        $userId = $this->takvim_model->getUserIdByFeedToken($token);
        if (!$userId) {
            header('HTTP/1.0 404 Not Found');
            exit;
        }
        $startTs = strtotime('-1 year');
        $endTs   = strtotime('+2 years');
        $rows    = $this->durusmalar_model->getEventsForCalendar($startTs, $endTs);
        $ics     = $this->_buildIcs($rows);
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename="edts-durusmalar.ics"');
        header('Cache-Control: no-cache, must-revalidate');
        echo $ics;
        exit;
    }

    private function _buildIcs($rows)
    {
        $lines = array(
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//EDTS Takvim//TR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        );
        foreach ($rows as $r) {
            $ts   = (int) $r->d_durusmatarihi;
            $end  = $ts + 3600;
            $uid  = 'd' . $r->d_id . '@edts';
            $dtStart = gmdate('Ymd\THis\Z', $ts);
            $dtEnd   = gmdate('Ymd\THis\Z', $end);
            $summary = $r->d_dosyano . ' - ' . $r->d_mahkeme;
            $summary = str_replace(array("\r", "\n", ','), array('', ' ', ' '), $summary);
            $desc = $r->d_esasno . ' ' . $r->d_mahkeme . ' ' . $r->d_islem;
            $desc = str_replace(array("\r", "\n"), array('', ' '), $desc);
            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:' . $uid;
            $lines[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');
            $lines[] = 'DTSTART:' . $dtStart;
            $lines[] = 'DTEND:' . $dtEnd;
            $lines[] = 'SUMMARY:' . $summary;
            $lines[] = 'DESCRIPTION:' . $desc;
            $lines[] = 'END:VEVENT';
        }
        $lines[] = 'END:VCALENDAR';
        return implode("\r\n", $lines);
    }

    /**
     * Cron ile çağrılır: yaklaşan duruşmalar için e-posta/push bildirimi gönderir.
     * Sadece CLI'dan çalıştırılmalı (sistem cron). HTTP ile çağrı 403 döner.
     * Örnek (her 5 dk): php index.php takvim sendReminders
     */
    public function sendReminders()
    {
        if (!$this->input->is_cli_request()) {
            SetHeader(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('success' => false, 'message' => 'sendReminders only allowed via CLI'));
            exit;
        }

        $lockDir = defined('APPPATH') ? APPPATH . 'cache' : sys_get_temp_dir();
        $lockFile = rtrim($lockDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'send_reminders.lock';
        $minIntervalSec = 120;
        if (file_exists($lockFile) && (time() - (int) @filemtime($lockFile)) < $minIntervalSec) {
            exit;
        }
        @file_put_contents($lockFile, (string) time());

        $now = time();
        $this->config->load('calendar_config', true);
        $prefsRows = $this->db->query("SELECT DISTINCT reminder_minutes FROM r8t_edts_calendar_notification_prefs WHERE email_enabled = 1 OR push_enabled = 1")->result();
        if (!$prefsRows) {
            exit;
        }

        $notifyCount = 0;
        foreach ($prefsRows as $row) {
            $remMin = (int) $row->reminder_minutes;
            if ($remMin <= 0) {
                continue;
            }
            $windowStart = $now;
            $windowEnd   = $now + ($remMin * 60);
            $hearings    = $this->durusmalar_model->getEventsForCalendar($windowStart, $windowEnd);
            if (empty($hearings)) {
                continue;
            }
            $users = $this->takvim_model->getUsersWithReminderPrefs($remMin);
            foreach ($users as $user) {
                $userId = (int) $user->user_id;
                foreach ($hearings as $h) {
                    $dId = (int) $h->d_id;
                    if (!empty($user->email_enabled) && !empty($user->email) && !$this->takvim_model->alreadySent($userId, $dId, 'email')) {
                        $this->_sendReminderEmail($user->email, $h);
                        $this->takvim_model->logNotification($userId, $dId, 'email');
                        $notifyCount++;
                        if ($notifyCount % 25 === 0) {
                            sleep(1);
                        }
                    }
                    if (!empty($user->push_enabled) && !$this->takvim_model->alreadySent($userId, $dId, 'push')) {
                        if ($this->_sendReminderPush($userId, $h)) {
                            $this->takvim_model->logNotification($userId, $dId, 'push');
                            $notifyCount++;
                            if ($notifyCount % 25 === 0) {
                                sleep(1);
                            }
                        }
                    }
                }
            }
        }
        exit;
    }

    private function _sendReminderEmail($toEmail, $hearing)
    {
        $ts = (int) $hearing->d_durusmatarihi;
        $dtMail = new DateTime('@' . $ts);
        $dtMail->setTimezone(new DateTimeZone('Europe/Istanbul'));
        $dateStr = $dtMail->format('d.m.Y H:i');
        $subject = 'Duruşma hatırlatması: ' . $hearing->d_dosyano . ' - ' . $hearing->d_mahkeme;
        $body = "Duruşma tarihi: " . $dateStr . "\n";
        $body .= "Dosya No: " . $hearing->d_dosyano . "\n";
        $body .= "Mahkeme: " . $hearing->d_mahkeme . "\n";
        $body .= "Esas No: " . $hearing->d_esasno . "\n";

        $this->config->load('calendar_config', true);
        $smtpHost = $this->config->item('calendar_smtp_host', 'calendar_config');
        if ($smtpHost) {
            $this->load->library('email');
            $this->email->initialize(array(
                'protocol'  => 'smtp',
                'smtp_host' => $smtpHost,
                'smtp_user' => $this->config->item('calendar_smtp_user', 'calendar_config'),
                'smtp_pass' => $this->config->item('calendar_smtp_pass', 'calendar_config'),
                'smtp_port' => $this->config->item('calendar_smtp_port', 'calendar_config') ?: 587,
                'smtp_crypto'=> $this->config->item('calendar_smtp_crypto', 'calendar_config') ?: 'tls',
                'mailtype'  => 'text',
                'charset'   => 'utf-8',
            ));
            $this->email->from($this->config->item('calendar_mail_from', 'calendar_config'), $this->config->item('calendar_mail_from_name', 'calendar_config'));
            $this->email->to($toEmail);
            $this->email->subject($subject);
            $this->email->message($body);
            $this->email->send();
        } else {
            $from = $this->config->item('calendar_mail_from', 'calendar_config') ?: 'noreply@localhost';
            $headers = "From: " . $from . "\r\nContent-Type: text/plain; charset=utf-8";
            @mail($toEmail, $subject, $body, $headers);
        }
    }

    /**
     * @return bool True if at least one push was sent
     */
    private function _sendReminderPush($userId, $hearing)
    {
        $subs = $this->takvim_model->getPushSubscriptionsByUserIds(array($userId));
        if (empty($subs)) {
            return false;
        }
        $this->config->load('calendar_config', true);
        $vapidPublic = $this->config->item('calendar_push_vapid_public', 'calendar_config');
        if (empty($vapidPublic)) {
            return false;
        }
        if (!class_exists('Minishlink\WebPush\WebPush')) {
            return false;
        }
        $payload = json_encode(array(
            'title' => 'Duruşma hatırlatması',
            'body'  => $hearing->d_dosyano . ' - ' . $hearing->d_mahkeme . ' ' . timeToDateFormat($hearing->d_durusmatarihi, 'd.m.Y H:i'),
        ));
        $sent = false;
        foreach ($subs as $sub) {
            if ((int) $sub->user_id !== $userId) {
                continue;
            }
            try {
                $webPush = new \Minishlink\WebPush\WebPush(array(
                    'VAPID' => array(
                        'publicKey'  => $vapidPublic,
                        'privateKey' => $this->config->item('calendar_push_vapid_private', 'calendar_config'),
                    ),
                ));
                $webPush->sendOneSubscription(
                    new \Minishlink\WebPush\Subscription($sub->endpoint, $sub->p256dh, $sub->auth),
                    $payload
                );
                $sent = true;
            } catch (Exception $e) {
                // continue
            }
        }
        return $sent;
    }

    /**
     * Takvim dışa aktarımı: günlük, haftalık, aylık dönem veya tarih aralığı; Excel veya PDF.
     * GET/POST: period=day|week|month + date=YYYY-MM-DD VEYA start_date + end_date (YYYY-MM-DD), format=excel|pdf
     */
    public function export()
    {
        $this->_checkAuth();

        $startDate = $this->input->get_post('start_date');
        $endDate   = $this->input->get_post('end_date');
        $period = $this->input->get_post('period');
        $date   = $this->input->get_post('date');
        $format = $this->input->get_post('format');

        if (!in_array($format, array('excel', 'pdf'), true)) {
            $this->_jsonErr(400, 'Geçersiz format. excel veya pdf gerekli.');
        }

        $tz = new DateTimeZone('Europe/Istanbul');

        if (!empty($startDate) && !empty($endDate)) {
            $startDt = DateTime::createFromFormat('Y-m-d', $startDate, $tz);
            $endDt   = DateTime::createFromFormat('Y-m-d', $endDate, $tz);
            if (!$startDt || !$endDt) {
                $this->_jsonErr(400, 'Geçersiz tarih formatı. start_date ve end_date YYYY-MM-DD olmalı.');
            }
            $startDt->setTime(0, 0, 0);
            $endDt->setTime(23, 59, 59);
            if ($startDt > $endDt) {
                $this->_jsonErr(400, 'Başlangıç tarihi bitiş tarihinden sonra olamaz.');
            }
        } else {
            if (!in_array($period, array('day', 'week', 'month'), true) || empty($date)) {
                $this->_jsonErr(400, 'Geçersiz parametreler: period (day|week|month), date (YYYY-MM-DD), format (excel|pdf) gerekli.');
            }
            $dt = DateTime::createFromFormat('Y-m-d', $date, $tz);
            if (!$dt) {
                $this->_jsonErr(400, 'Geçersiz tarih formatı. YYYY-MM-DD kullanın.');
            }
            if ($period === 'day') {
                $startDt = clone $dt;
                $startDt->setTime(0, 0, 0);
                $endDt = clone $dt;
                $endDt->setTime(23, 59, 59);
            } elseif ($period === 'week') {
                $dayOfWeek = (int) $dt->format('N');
                $mondayOffset = $dayOfWeek - 1;
                $startDt = clone $dt;
                $startDt->modify('-' . $mondayOffset . ' days');
                $startDt->setTime(0, 0, 0);
                $endDt = clone $startDt;
                $endDt->modify('+6 days');
                $endDt->setTime(23, 59, 59);
            } else {
                $startDt = clone $dt;
                $startDt->modify('first day of this month');
                $startDt->setTime(0, 0, 0);
                $endDt = clone $startDt;
                $endDt->modify('last day of this month');
                $endDt->setTime(23, 59, 59);
            }
        }

        $startTs = $startDt->getTimestamp();
        $endTs   = $endDt->getTimestamp();

        $avukatId = $this->input->get_post('avukat_id');
        $avukatId = ($avukatId !== null && $avukatId !== '') ? (int) $avukatId : null;
        if ($avukatId !== null && $avukatId <= 0) {
            $avukatId = null;
        }

        $rows = $this->durusmalar_model->getEventsForCalendar($startTs, $endTs, $avukatId);

        $exportPlain = function ($v) {
            if ($v === null || $v === '') {
                return '';
            }
            $v = html_entity_decode((string) $v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $v = strip_tags($v);
            return trim(preg_replace('/\s+/', ' ', $v));
        };

        $headers = array('Dosya No', 'Tarih / Saat', 'Mahkeme', 'Esas No', 'Dosya Türü', 'İşlem', 'Taraf', 'Avukat', 'Memur');
        $dataRows = array();
        foreach ($rows as $r) {
            $ts = (int) $r->d_durusmatarihi;
            $dataRows[] = array(
                $exportPlain($r->d_dosyano),
                function_exists('timeToDateFormat') ? timeToDateFormat($ts, 'd.m.Y H:i') : date('d.m.Y H:i', $ts),
                $exportPlain($r->d_mahkeme),
                $exportPlain($r->d_esasno),
                $exportPlain($r->d_dosyaturu),
                $exportPlain($r->d_islem),
                $exportPlain($r->d_taraf),
                $exportPlain($r->d_avukat),
                $exportPlain($r->d_memur),
            );
        }

        $periodLabel = (!empty($startDate) && !empty($endDate)) ? 'Tarih aralığı' : ($period === 'day' ? 'Günlük' : ($period === 'week' ? 'Haftalık' : 'Aylık'));
        $rangeLabel = $startDt->format('d.m.Y') . ' - ' . $endDt->format('d.m.Y');

        if ($format === 'pdf') {
            $title = 'Takvim Dışa Aktarım - ' . $periodLabel . ' (' . $rangeLabel . ')';
            $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . htmlspecialchars($title) . '</title>';
            $html .= '<style>body{font-family:DejaVu Sans,sans-serif;font-size:10px;margin:12px;}';
            $html .= 'table{border-collapse:collapse;width:100%;}th,td{border:1px solid #333;padding:4px 6px;text-align:left;}th{background:#eee;}';
            $html .= '@media print{body{margin:0;} table{page-break-inside:auto;} tr{page-break-inside:avoid;}}</style></head><body>';
            $html .= '<h1 style="font-size:14px;margin-bottom:8px;">' . htmlspecialchars($title) . '</h1>';
            $html .= '<p style="margin-bottom:8px;">Toplam: ' . count($dataRows) . ' kayıt. Tarih: ' . date('d.m.Y H:i') . '</p>';
            $html .= '<table><thead><tr>';
            foreach ($headers as $h) {
                $html .= '<th>' . htmlspecialchars($h) . '</th>';
            }
            $html .= '</tr></thead><tbody>';
            foreach ($dataRows as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $html .= '<td>' . htmlspecialchars((string) $cell) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody></table></body></html>';
            header('Content-Type: text/html; charset=UTF-8');
            header('Cache-Control: max-age=0');
            echo $html;
            exit;
        }

        $sep = ';';
        $csv = "\xEF\xBB\xBF";
        $csv .= implode($sep, array_map(function ($h) use ($sep) {
            return '"' . str_replace('"', '""', $h) . '"';
        }, $headers)) . "\r\n";
        foreach ($dataRows as $row) {
            $csv .= implode($sep, array_map(function ($v) use ($sep) {
                $v = (string) $v;
                return '"' . str_replace('"', '""', $v) . '"';
            }, $row)) . "\r\n";
        }

        $suffix = (!empty($startDate) && !empty($endDate)) ? 'aralik' : ($period === 'day' ? 'gun' : ($period === 'week' ? 'hafta' : 'ay'));
        $filename = 'takvim_export_' . $startDt->format('Y-m-d') . '_' . $suffix . '.xlsx';
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        echo $csv;
        exit;
    }

    /**
     * Push aboneliği kaydet (POST: endpoint, keys[p256dh], keys[auth]).
     */
    public function savePushSubscription()
    {
        $this->_checkAuth();
        if ($this->input->method() !== 'post') {
            $this->_jsonErr(405, 'POST gerekli.');
        }
        $userId = $this->_getUserId();
        $endpoint = $this->input->post('endpoint');
        $keys = $this->input->post('keys');
        if (empty($endpoint) || empty($keys['p256dh']) || empty($keys['auth'])) {
            $this->_jsonErr(400, 'endpoint ve keys gerekli.');
        }
        $userAgent = $this->input->user_agent();
        $this->takvim_model->savePushSubscription($userId, $endpoint, $keys['p256dh'], $keys['auth'], $userAgent);
        SetHeader(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success' => true));
        exit;
    }
}
