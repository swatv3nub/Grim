<?php
namespace Grim\Utils;

class Notifier
{
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/notifications.php';
    }

    public function send($subject, $message)
    {
        if ($this->config['email']['enabled']) {
            $this->sendEmail($subject, $message);
        }
        if ($this->config['slack']['enabled']) {
            $this->sendSlack($message);
        }
    }

    private function sendEmail($subject, $message)
    {
        $to = $this->config['email']['to'];
        $from = $this->config['email']['from'];
        $headers = "From: $from\r\nContent-Type: text/plain; charset=UTF-8";
        // Optionally use SMTP settings if configured (not implemented here)
        mail($to, $subject, $message, $headers);
    }

    private function sendSlack($message)
    {
        $webhook = $this->config['slack']['webhook_url'];
        if (!$webhook) return;
        $payload = json_encode(['text' => $message]);
        $ch = curl_init($webhook);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}
