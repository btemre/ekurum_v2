<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * EDTS AI Assistant - Gemini API integration with live SQL data
 * RAG pattern + Text-to-SQL + Summary
 */
class Ai_assistant extends CI_Controller
{
    public $userData = false;
    public $viewFolder = 'ai_assistant_v';

    public function __construct()
    {
        parent::__construct();
        $this->load->model("auth_model");
        $this->load->model("durusmalar_model");
        $this->userData = $this->auth_model->userData;
    }

    /**
     * Full-page AI Assistant view
     */
    public function index()
    {
        if ($this->userData === false) {
            redirect(sys_url("login"));
            exit;
        }
        if (!isAllowedViewApp("edts")) {
            $this->session->set_flashdata("alertToastr", [
                "title" => "Hata!",
                "text" => "EDTS uygulaması için yetkiniz bulunmuyor.",
                "type" => "error"
            ]);
            redirect(sys_url("home"));
            exit;
        }

        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "view";
        $viewData->userData = $this->userData;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    /**
     * API: Quick summary (for dashboard cards)
     */
    public function api_summary()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($this->userData === false || !isAllowedViewApp("edts")) {
            echo json_encode(['success' => false, 'data' => []]);
            return;
        }

        try {
            $avukatTop = $this->durusmalar_model->durusmaAvukatBazli();
            $memurTop = $this->durusmalar_model->durusmaMemurBazli();
            $haftalik = $this->durusmalar_model->durusmaListesiHaftalik();

            $topAvukat = $avukatTop[0] ?? null;
            $topMemur = $memurTop[0] ?? null;
            $haftalikSayi = is_array($haftalik) ? count($haftalik) : 0;

            echo json_encode([
                'success' => true,
                'data' => [
                    'en_cok_avukat' => $topAvukat ? (array) $topAvukat : null,
                    'en_cok_memur' => $topMemur ? (array) $topMemur : null,
                    'bu_hafta_durusma_sayisi' => $haftalikSayi,
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    /**
     * API: Ask question - fetches dashboard data, sends to Gemini, returns answer
     */
    public function api_ask()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');

        if ($this->userData === false) {
            echo json_encode([
                'success' => false,
                'text' => '',
                'error' => 'Oturum açmanız gerekiyor.'
            ]);
            return;
        }

        if (!isAllowedViewApp("edts")) {
            echo json_encode([
                'success' => false,
                'text' => '',
                'error' => 'EDTS uygulaması için yetkiniz bulunmuyor.'
            ]);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $question = trim($input['question'] ?? $input['soru'] ?? '');
        $history = $input['history'] ?? [];

        if (empty($question)) {
            echo json_encode([
                'success' => false,
                'text' => '',
                'error' => 'Lütfen bir soru girin.'
            ]);
            return;
        }

        $context = $this->getDashboardContext();
        $this->config->load('gemini');
        $config = [
            'gemini_api_key' => $this->config->item('gemini_api_key') ?: getenv('GEMINI_API_KEY'),
            'gemini_api_url' => $this->config->item('gemini_api_url') ?: 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent',
        ];

        $this->load->library('Gemini', $config);
        $result = $this->gemini->generateContentWithHistory($question, $context, $history);

        echo json_encode([
            'success' => $result['success'],
            'text' => $result['text'],
            'error' => $result['error']
        ]);
    }

    /**
     * Fetch dashboard-relevant data for Gemini context (max ~12K chars)
     */
    private function getDashboardContext()
    {
        $context = [];
        $toArray = function ($r) { return (array) $r; };

        try {
            $avukatBazli = $this->durusmalar_model->durusmaAvukatBazli();
            if ($avukatBazli) {
                $context['avukat_bazli'] = array_map($toArray, $avukatBazli);
            }

            $memurBazli = $this->durusmalar_model->durusmaMemurBazli();
            if ($memurBazli) {
                $context['memur_bazli'] = array_slice(array_map($toArray, $memurBazli), 0, 10);
            }

            $haftalik = $this->durusmalar_model->durusmaListesiHaftalik();
            if ($haftalik) {
                $context['bu_hafta_durusmalar'] = array_slice(array_map($toArray, $haftalik), 0, 15);
            }

            $aylik = $this->durusmalar_model->durusmaListesiAylik();
            if ($aylik) {
                $context['aylik_durusma'] = array_map($toArray, $aylik);
            }

            $tarafBazli = $this->durusmalar_model->durusmaTarafBazli();
            if ($tarafBazli) {
                $context['taraf_bazli'] = array_slice(array_map($toArray, $tarafBazli), 0, 8);
            }

            $islemBazli = $this->durusmalar_model->durusmaIslemBazli();
            if ($islemBazli) {
                $context['islem_bazli'] = array_slice(array_map($toArray, $islemBazli), 0, 10);
            }

            $mahkemeBazli = $this->durusmalar_model->durusmaMahkemeBazli();
            if ($mahkemeBazli) {
                $context['mahkeme_bazli'] = array_map($toArray, $mahkemeBazli);
            }

            $aylar = ['', 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
            $dtNow = new DateTime('now', new DateTimeZone('Europe/Istanbul'));
            $context['tarih_bilgisi'] = [
                'bugun' => $dtNow->format('Y-m-d'),
                'yil' => $dtNow->format('Y'),
                'ay' => $dtNow->format('n'),
                'ay_adi' => $aylar[(int) $dtNow->format('n')],
            ];
        } catch (Exception $e) {
            $context['error'] = $e->getMessage();
        }

        return $context;
    }

    /**
     * API: Text-to-SQL - Gemini generates SQL, we validate and execute (read-only)
     */
    public function api_text_to_sql()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');

        if ($this->userData === false || !isAllowedViewApp("edts")) {
            echo json_encode(['success' => false, 'text' => '', 'error' => 'Yetkiniz yok.', 'data' => []]);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $question = trim($input['question'] ?? $input['soru'] ?? '');

        if (empty($question)) {
            echo json_encode(['success' => false, 'text' => '', 'error' => 'Lütfen bir soru girin.', 'data' => []]);
            return;
        }

        $schema = $this->getTableSchema();
        $this->config->load('gemini');
        $config = [
            'gemini_api_key' => $this->config->item('gemini_api_key') ?: getenv('GEMINI_API_KEY'),
            'gemini_api_url' => $this->config->item('gemini_api_url') ?: 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent',
        ];

        $this->load->library('Gemini', $config);
        $result = $this->gemini->generateSql($question, $schema);

        if (!$result['success']) {
            echo json_encode([
                'success' => false,
                'text' => '',
                'error' => $result['error'],
                'data' => []
            ]);
            return;
        }

        $sql = trim($result['sql'] ?? '');
        $sql = preg_replace('/;.*$/s', '', $sql);
        $sql = trim(rtrim($sql, ';'));
        if (!$this->validateSql($sql)) {
            echo json_encode([
                'success' => false,
                'text' => '',
                'error' => 'Yapay Zeka\'ya sor kapsamında oluşturma, silme veya güncelleme işlemlerine (toplu veya tekil) izin verilmemektedir. Yalnızca okuma (SELECT) sorguları çalıştırılabilir.',
                'data' => []
            ]);
            return;
        }

        try {
            $q = $this->db->query($sql);
            $rows = $q ? $q->result_array() : [];
            $text = $this->formatSqlResult($rows, $question);
            echo json_encode([
                'success' => true,
                'text' => $text,
                'error' => '',
                'data' => array_slice($rows, 0, 50),
                'row_count' => count($rows)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'text' => '',
                'error' => 'Sorgu hatası: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    private function getTableSchema()
    {
        return [
            'r8t_edts_durusmalar' => [
                'd_id' => 'int PK',
                'd_esasno' => 'string',
                'd_mahkeme' => 'string',
                'd_dosyano' => 'string',
                'd_dosyaturu' => 'string',
                'd_durusmatarihi' => 'unix timestamp',
                'd_avukat' => 'string',
                'd_avukatid' => 'int',
                'd_memur' => 'string',
                'd_memurid' => 'int',
                'd_taraf' => 'string',
                'd_tarafbilgisi' => 'string',
                'd_islem' => 'string',
                'd_status' => 'int (1=aktif)',
                'd_adddate' => 'unix timestamp',
            ],
            'r8t_users' => [
                'u_id' => 'int PK',
                'u_name' => 'string',
                'u_lastname' => 'string',
                'u_surname' => 'string',
            ],
        ];
    }

    /**
     * Yapay Zeka'ya sor / AI Assistant: Kesinlikle toplu veya tekil oluşturma, silme, güncelleme yasak.
     * Sadece SELECT (okuma) sorgularına izin verilir.
     */
    private function validateSql($sql)
    {
        $sqlUpper = preg_replace('/\s+/', ' ', strtoupper(trim($sql)));
        if (preg_match('/\b(INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|TRUNCATE|REPLACE|GRANT|REVOKE|EXEC|EXECUTE|CALL|LOCK\s|UNLOCK\s)\b/', $sqlUpper)) {
            return false;
        }
        if (!preg_match('/^\s*SELECT\b/', $sqlUpper)) {
            return false;
        }
        $allowed = ['R8T_EDTS_DURUSMALAR', 'R8T_USERS'];
        $hasAllowed = false;
        foreach ($allowed as $t) {
            if (strpos($sqlUpper, $t) !== false) {
                $hasAllowed = true;
                break;
            }
        }
        return $hasAllowed;
    }

    private function formatSqlResult($rows, $question)
    {
        $count = count($rows);
        if ($count === 0) {
            return "Sorgu sonucu: 0 kayıt bulundu.";
        }
        $summary = "Sorgu sonucu: {$count} kayıt bulundu.\n\n";
        $cols = array_keys($rows[0] ?? []);
        $summary .= implode(' | ', $cols) . "\n";
        $summary .= str_repeat('-', 60) . "\n";
        foreach (array_slice($rows, 0, 10) as $r) {
            $summary .= implode(' | ', array_map(function ($v) {
                return is_null($v) ? '-' : substr((string) $v, 0, 30);
            }, $r)) . "\n";
        }
        if ($count > 10) {
            $summary .= "\n... ve " . ($count - 10) . " kayıt daha.";
        }
        return $summary;
    }
}
