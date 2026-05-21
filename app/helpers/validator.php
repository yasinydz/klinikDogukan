<?php
/**
 * app/helpers/validator.php
 *
 * Merkezi input validasyon sınıfı.
 * Tüm formlar bu sınıfı kullanmalı.
 */

class Validator
{
    private array $errors = [];
    private array $data   = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // ── Zincirleme Kurallar ───────────────────────────────────

    public function required(string $field, string $label)
    {
        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value === '') {
            $this->errors[$field] = "{$label} zorunludur.";
        }

        return $this;
    }

    public function minLength(string $field, int $min, string $label)
    {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = trim((string) ($this->data[$field] ?? ''));

        if (mb_strlen($value, 'UTF-8') < $min) {
            $this->errors[$field] = "{$label} en az {$min} karakter olmalıdır.";
        }

        return $this;
    }

    public function maxLength(string $field, int $max, string $label)
    {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = trim((string) ($this->data[$field] ?? ''));

        if (mb_strlen($value, 'UTF-8') > $max) {
            $this->errors[$field] = "{$label} en fazla {$max} karakter olabilir.";
        }

        return $this;
    }

    public function email(string $field, string $label)
    {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value === '') {
            return $this;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Geçerli bir {$label} adresi girin.";
        }

        return $this;
    }

    public function phone(string $field, string $label)
    {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value === '') {
            return $this;
        }

        // Türkiye telefon formatı: +90 ile başlayan veya 0 ile başlayan 10-11 hane
        $cleaned = preg_replace('/[\s\-\(\)\+]/', '', $value);

        if (!preg_match('/^(90|0)?[1-9][0-9]{9}$/', $cleaned)) {
            $this->errors[$field] = "Geçerli bir {$label} numarası girin.";
        }

        return $this;
    }

    public function date(string $field, string $label, bool $futureOnly = false)
    {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value === '') {
            return $this;
        }

        $date = DateTime::createFromFormat('Y-m-d', $value);

        if (!$date || $date->format('Y-m-d') !== $value) {
            $this->errors[$field] = "Geçerli bir {$label} seçin.";
            return $this;
        }

        if ($futureOnly) {
            $today = new DateTime('today');
            if ($date < $today) {
                $this->errors[$field] = "{$label} bugün veya sonrası olmalıdır.";
            }
        }

        return $this;
    }

    public function time(string $field, string $label)
    {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value === '') {
            return $this;
        }

        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            $this->errors[$field] = "Geçerli bir {$label} seçin.";
        }

        return $this;
    }

    public function inList(string $field, array $allowed, string $label)
    {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = (string) ($this->data[$field] ?? '');

        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[$field] = "Geçerli bir {$label} seçin.";
        }

        return $this;
    }

    public function accepted(string $field, string $label)
    {
        $value = $this->data[$field] ?? '';

        if (!in_array($value, ['1', 'on', 'yes', 'true', true, 1], true)) {
            $this->errors[$field] = "{$label} kabul edilmelidir.";
        }

        return $this;
    }

    public function numeric(string $field, string $label)
    {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = (string) ($this->data[$field] ?? '');

        if ($value !== '' && !is_numeric($value)) {
            $this->errors[$field] = "{$label} sayısal olmalıdır.";
        }

        return $this;
    }

    // ── Sonuç ─────────────────────────────────────────────────

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return reset($this->errors) ?: '';
    }

    /**
     * Doğrulanmış ve temizlenmiş değeri döndürür.
     */
    public function get(string $field, $default = '')
    {
        $value = $this->data[$field] ?? $default;

        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }
}
