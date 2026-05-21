<?php
/**
 * app/helpers/flash.php
 *
 * Session tabanlı tek kullanımlık flash mesajlar.
 * Redirect sonrası kullanıcıya bilgi göstermek için.
 */

/**
 * Flash mesaj yazar.
 *
 * @param string $type    'success' | 'error' | 'warning' | 'info'
 * @param string $message Gösterilecek mesaj
 */
function flashSet(string $type, string $message): void
{
    $_SESSION['flash'][] = [
        'type'    => $type,
        'message' => $message,
    ];
}

/**
 * Flash mesajları okur ve session'dan siler.
 *
 * @return array<int, array{type: string, message: string}>
 */
function flashGet(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/**
 * Flash mesajları HTML olarak render eder.
 * CSS class'ları mevcut _alerts.css ile uyumlu.
 */
function flashRender(): void
{
    $messages = flashGet();

    foreach ($messages as $msg) {
        $type    = htmlspecialchars($msg['type'],    ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8');

        echo '<div class="alert__message ' . $type . '" role="alert">';
        echo '<p>' . $message . '</p>';
        echo '</div>';
    }
}

/**
 * Eski sistem ile uyumluluk — $_SESSION['success_message'] / error_message
 * Geçiş dönemi için; yeni kod flashSet/flashGet kullanmalı.
 */
function flashLegacyRender(): void
{
    if (!empty($_SESSION['success_message'])) {
        echo '<div class="alert__message success" role="alert"><p>'
            . htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8')
            . '</p></div>';
        unset($_SESSION['success_message']);
    }

    if (!empty($_SESSION['error_message'])) {
        echo '<div class="alert__message error" role="alert"><p>'
            . htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8')
            . '</p></div>';
        unset($_SESSION['error_message']);
    }
}
