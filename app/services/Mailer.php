<?php
/**
 * app/services/Mailer.php
 *
 * Merkezi e-posta gönderim servisi.
 * PHPMailer varsa SMTP, yoksa PHP mail() fallback.
 *
 * Kullanım:
 *   require_once BASE_PATH . '/app/services/Mailer.php';
 *   $m = new Mailer();
 *   $m->to('alici@example.com')
 *     ->subject('Konu')
 *     ->template('contact-admin', ['NAME' => 'Ali', ...])
 *     ->send();
 */

class Mailer
{
    private string $to = '';
    private string $subject = '';
    private string $htmlBody = '';
    private string $textBody = '';
    private string $replyTo = '';

    public function to(string $email): self
    {
        $this->to = filter_var(trim($email), FILTER_VALIDATE_EMAIL) ?: '';
        return $this;
    }

    public function subject(string $subject): self
    {
        // Email header injection koruması
        $this->subject = str_replace(["\r", "\n", "\t"], '', trim($subject));
        return $this;
    }

    public function replyTo(string $email): self
    {
        $email = trim($email);
        $this->replyTo = filter_var($email, FILTER_VALIDATE_EMAIL) ?: '';
        return $this;
    }

    /**
     * Template dosyasını yükle ve placeholder'ları değiştir.
     *
     * @param string $templateName  templates/email/ altındaki dosya adı (uzantısız)
     * @param array  $data          Placeholder => değer
     */
    public function template(string $templateName, array $data = []): self
    {
        $templatePath = BASE_PATH . '/templates/email/' . $templateName . '.html';
        $textPath     = BASE_PATH . '/templates/email/' . $templateName . '.txt';

        if (!file_exists($templatePath)) {
            $this->log("Email template bulunamadı: {$templateName}");
            return $this;
        }

        // Base layout
        $basePath = BASE_PATH . '/templates/email/base.html';
        $base = file_exists($basePath) ? file_get_contents($basePath) : '{{CONTENT}}';
        $content = file_get_contents($templatePath);

        // Genel placeholder'lar
        $data['SITE_NAME']          = $data['SITE_NAME']          ?? SITE_NAME;
        $data['SITE_URL']           = $data['SITE_URL']           ?? SITE_URL;
        $data['YEAR']               = $data['YEAR']               ?? date('Y');
        $data['PSYCHOLOGIST_NAME']  = $data['PSYCHOLOGIST_NAME']  ?? PSYCHOLOGIST_NAME;
        $data['CONTACT_PHONE']      = $data['CONTACT_PHONE']      ?? CONTACT_PHONE;
        $data['SUBJECT']            = $data['SUBJECT']            ?? $this->subject;

        // Base'e content yerleştir
        $html = str_replace('{{CONTENT}}', $content, $base);

        // Tüm placeholder'ları değiştir
        // _RAW suffix'li key'ler escape edilmeden yerleştirilir (önceden escape edilmiş HTML içerik için)
        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_RAW')) {
                // Zaten escape edilmiş HTML — doğrudan yerleştir
                $html = str_replace('{{' . $key . '}}', (string) $value, $html);
            } else {
                $html = str_replace(
                    '{{' . $key . '}}',
                    htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'),
                    $html
                );
            }
        }

        $this->htmlBody = $html;

        // Plain text fallback
        if (file_exists($textPath)) {
            $text = file_get_contents($textPath);
            foreach ($data as $key => $value) {
                $text = str_replace('{{' . $key . '}}', (string) $value, $text);
            }
            $this->textBody = $text;
        } else {
            // HTML'den otomatik plain text üret
            $this->textBody = strip_tags(
                str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $this->htmlBody)
            );
            $this->textBody = html_entity_decode($this->textBody, ENT_QUOTES, 'UTF-8');
            $this->textBody = preg_replace('/\n{3,}/', "\n\n", trim($this->textBody));
        }

        return $this;
    }

    /**
     * Doğrudan HTML body ile gönder (template kullanmadan).
     */
    public function body(string $html, string $text = ''): self
    {
        $this->htmlBody = $html;
        $this->textBody = $text ?: strip_tags($html);
        return $this;
    }

    /**
     * Maili gönder.
     * @return bool Başarılı mı
     */
    public function send(): bool
    {
        if ($this->to === '') {
            $this->log('Geçersiz alıcı adresi.');
            return false;
        }

        if ($this->htmlBody === '') {
            $this->log('Email body boş — gönderilmedi.');
            return false;
        }

        try {
            if (defined('MAIL_HOST') && MAIL_HOST !== '' && defined('MAIL_USER') && MAIL_USER !== '') {
                return $this->sendSmtp();
            }
            return $this->sendPhpMail();
        } catch (\Throwable $e) {
            $this->log('Mail gönderim hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * PHP mail() ile multipart/alternative gönder.
     */
    private function sendPhpMail(): bool
    {
        $boundary = md5(uniqid((string) time(), true));

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
        $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_ADDRESS . ">\r\n";

        if ($this->replyTo !== '') {
            $headers .= "Reply-To: {$this->replyTo}\r\n";
        }

        $headers .= "X-Mailer: PsikologSite/1.0\r\n";

        $body  = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($this->textBody)) . "\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($this->htmlBody)) . "\r\n";
        $body .= "--{$boundary}--\r\n";

        // Subject UTF-8 encode
        $encodedSubject = '=?UTF-8?B?' . base64_encode($this->subject) . '?=';

        $result = @mail($this->to, $encodedSubject, $body, $headers);

        if (!$result) {
            $this->log("PHP mail() başarısız — to: {$this->to}, subject: {$this->subject}");
        }

        return $result;
    }

    /**
     * PHPMailer ile SMTP gönder. Yoksa mail() fallback.
     */
    private function sendSmtp(): bool
    {
        $autoload = BASE_PATH . '/vendor/autoload.php';

        if (!file_exists($autoload)) {
            $this->log('PHPMailer (vendor/autoload.php) bulunamadı — mail() fallback kullanılıyor.');
            return $this->sendPhpMail();
        }

        require_once $autoload;

        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            $this->log('PHPMailer class bulunamadı — mail() fallback.');
            return $this->sendPhpMail();
        }

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USER;
        $mail->Password   = MAIL_PASS;
        $mail->SMTPSecure = (defined('MAIL_ENCRYPTION') && MAIL_ENCRYPTION === 'ssl')
            ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
            : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) (defined('MAIL_PORT') ? MAIL_PORT : 587);
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($this->to);

        if ($this->replyTo !== '') {
            $mail->addReplyTo($this->replyTo);
        }

        $mail->isHTML(true);
        $mail->Subject = $this->subject;
        $mail->Body    = $this->htmlBody;
        $mail->AltBody = $this->textBody;

        return $mail->send();
    }

    /**
     * Hata logla.
     */
    private function log(string $message): void
    {
        $logPath = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2))
            . '/storage/logs/app.log';
        $entry = date('Y-m-d H:i:s') . ' [MAIL] ' . $message . PHP_EOL;
        @file_put_contents($logPath, $entry, FILE_APPEND | LOCK_EX);
    }
}
