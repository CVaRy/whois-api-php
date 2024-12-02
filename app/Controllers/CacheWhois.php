<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CacheWhois extends BaseController
{
    // Cache driver'ını yükle
    protected $cache;

    public function __construct()
    {
        // CodeIgniter cache servisini yükleyin
        $this->cache = \Config\Services::cache();
    }

    // Whois verisini cache'den al
    public function getWhoisData($domain)
    {
        // Domain adına göre cache key oluştur
        $cacheKey = 'whois_' . md5($domain);

        //  Cache'den verial
        $data = $this->cache->get($cacheKey);

        // Cache'den veri bulunmazsa veya veri boşsa, empty döndür
        if (empty($data)) {
            return empty($data); // Boş veriyi döndürür, yani false
        }

        // Cache verisi mevcutsa, veriyi döndür
        return $data;
    }

    // Whois verisini cache'e kaydet
    public function setWhoisData($domain, $data, $ttl = 3600)
    {
        // Domain adına göre cache key oluştur
        $cacheKey = 'whois_' . md5($domain);

        // Cache'e veri kaydet 
        $this->cache->save($cacheKey, $data, $ttl);  // Default ttl 1 saat (3600 saniye)
    }
}
