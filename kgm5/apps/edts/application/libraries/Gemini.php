<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Simple Gemini API client using cURL
 * Uses Google Generative AI API - no Composer required
 */
class Gemini
{
    protected $api_key;
    protected $api_url;

    public function __construct($config = [])
    {
        $this->api_key = $config['gemini_api_key'] ?? getenv('GEMINI_API_KEY') ?: '';
        $this->api_url = ($config['gemini_api_url'] ?? 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent') . '?key=' . $this->api_key;
    }

    /**
     * Generate content from Gemini API
     * @param string $prompt The prompt to send
     * @param array|string $context Optional context data (array or JSON string)
     * @return array ['success' => bool, 'text' => string, 'error' => string]
     */
    public function generateContent($prompt, $context = [])
    {
        return $this->generateContentWithHistory($prompt, $context, []);
    }

    /**
     * Generate content with conversation history (multi-turn)
     * @param string $prompt Current user question
     * @param array|string $context Data context
     * @param array $history [['q'=>'...','a'=>'...'], ...] previous Q&A pairs
     * @return array ['success' => bool, 'text' => string, 'error' => string]
     */
    public function generateContentWithHistory($prompt, $context = [], $history = [])
    {
        if (empty($this->api_key)) {
            return ['success' => false, 'text' => '', 'error' => 'GEMINI_API_KEY yapılandırılmamış'];
        }

        $contextStr = is_string($context) ? $context : json_encode($context, JSON_UNESCAPED_UNICODE);
        $systemInstruction = "Sen EDTS (Duruşma Takip Sistemi) için bir veri asistanısın. Sadece verilen verilere dayanarak yanıt ver. Bilmediğin bir şey varsa 'Verilerde bu bilgi bulunmuyor' de. Yanıtları kısa, net ve Türkçe olarak ver. Sayıları ve istatistikleri düzgün formatla. Liste gerekiyorsa madde işaretleri kullan.";

        $contents = [];
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $systemInstruction . "\n\nAşağıdaki verileri kullan:\n\n" . $contextStr . "\n\n(Verileri inceledim, sorularıma hazırım.)"]]
        ];
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => 'Anladım. Verilere dayalı sorularınızı yanıtlamaya hazırım.']]
        ];

        foreach ($history as $h) {
            $q = trim($h['q'] ?? $h['question'] ?? '');
            $a = trim($h['a'] ?? $h['answer'] ?? '');
            if ($q !== '' && $a !== '') {
                $contents[] = ['role' => 'user', 'parts' => [['text' => $q]]];
                $contents[] = ['role' => 'model', 'parts' => [['text' => $a]]];
            }
        }

        $contents[] = ['role' => 'user', 'parts' => [['text' => $prompt]]];

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'maxOutputTokens' => 2048,
                'temperature' => 0.4,
            ]
        ];

        $ch = curl_init($this->api_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 45,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $err = json_decode($response, true);
            $errorMsg = $err['error']['message'] ?? 'API hatası';
            return ['success' => false, 'text' => '', 'error' => $errorMsg];
        }

        $data = json_decode($response, true);
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        return ['success' => true, 'text' => trim($text), 'error' => ''];
    }

    /**
     * Generate SQL from natural language (Text-to-SQL)
     * @param string $question User question
     * @param array $schema Table schema {'table'=>['col'=>'type',...]}
     * @return array ['success'=>bool, 'sql'=>string, 'error'=>string]
     */
    public function generateSql($question, $schema = [])
    {
        if (empty($this->api_key)) {
            return ['success' => false, 'sql' => '', 'error' => 'GEMINI_API_KEY yapılandırılmamış'];
        }

        $schemaStr = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $prompt = "Sadece MySQL SELECT sorgusu üret. Başka açıklama yazma. Sadece tek bir SQL cümlesi döndür.\n\n";
        $prompt .= "Tablo şeması:\n" . $schemaStr . "\n\n";
        $prompt .= "Kullanıcı sorusu: " . $question . "\n\n";
        $prompt .= "Kurallar: Sadece r8t_edts_durusmalar ve r8t_users tablolarını kullan. d_status=1 ile aktif kayıtları filtrele. Unix timestamp için FROM_UNIXTIME() kullan. YEAR(), MONTH() vb. fonksiyonlar kullanılabilir.";

        $payload = [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => [
                'maxOutputTokens' => 512,
                'temperature' => 0.1,
            ]
        ];

        $ch = curl_init($this->api_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $err = json_decode($response, true);
            return ['success' => false, 'sql' => '', 'error' => $err['error']['message'] ?? 'API hatası'];
        }

        $data = json_decode($response, true);
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $text = trim($text);
        $text = preg_replace('/^```sql\s*/i', '', $text);
        $text = preg_replace('/\s*```\s*$/i', '', $text);
        $text = trim($text);

        return ['success' => true, 'sql' => $text, 'error' => ''];
    }
}
