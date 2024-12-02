<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class WhoisQuery extends BaseController
{
    public function index($whoisServer,$domain):string
    {
    $port = 43; // Whois sunucusu için standart port
    $timeout = 10; // Zaman aşımı süresi (saniye)

    // fsockopen ile bağlantı aç
    $fp = fsockopen($whoisServer, $port, $errno, $errstr, $timeout);

    if (!$fp) {
        return "Whois sunucusuna bağlanılamadı: $errstr ($errno)";
    }

    // Whois sunucusuna domain adını gönder
    fwrite($fp, $domain . "\r\n");

    // Whois cevabını oku
    $response = '';
    while (!feof($fp)) {
        $response .= fgets($fp, 128);
    }

    // Bağlantıyı kapat
    fclose($fp);

    return $response ?: "Whois sunucusundan cevap alınamadı.";
    }
}
