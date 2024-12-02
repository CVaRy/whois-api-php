<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Controllers\WhoisQuery;
class TrParser extends Model
{
    protected $whoisServer = 'whois.trabis.gov.tr';
    protected $patterns = [
        'registrar' => '/Registrar:\s*(.+)/i',
        'creation_date' => '/Created on..............:\s*(.+)/i',
        'status'=> '/Domain Status:\s*(.+)\s*Frozen Status:\s*(.+)\s*Transfer Status:\s*(.+)/i',
        'expiration_date' => '/Expires on..............:\s*(.+)/i',
        'abuse_mail'=> '/Registrar Abuse Contact Email:\s*(.+)/i',
        'abuse_phone'=>'/Phone\t\t\t:\s*(.+)/i',
        'name_servers' => '/Domain Servers?:\s*(.*)/i',
        'dns_sec'=> '/DNSSEC:\s*(.+)/i'
    ];

    public function parse($domain)
    {
        // Whois sunucusuna istek gönder
        $request_data = new WhoisQuery();
        
        $rawData = $request_data->index($this->whoisServer,$domain);
        // Regex desenlerine göre veriyi işle
        $data = [];

        foreach ($this->patterns as $key => $pattern) {
            if ($key === 'name_servers') {
                // Domain Servers başlığını bul ve sonrasındaki satırlarda isim sunucularını al
                preg_match($pattern, $rawData, $matches);
                
                if (isset($matches[1])) {
                    // Başlık sonrası gelen 4 satırı almak için satır sonu karakterine göre böl
                    $serversBlock = substr($rawData, strpos($rawData, $matches[1]));
                    
                    // İlk satırı geç ve sonraki 4 satırı al
                    $nameServers = explode("\n", $serversBlock);
                    $nameServers = array_map('trim', array_slice($nameServers, 0, 4)); // İlk satırı atla ve sonraki 4 satırı al
                    
                    $data[$key] = $nameServers;
                }
            } else if ($key === 'status') {
                // Status için alt alta olacak şekilde veriyi al
                preg_match($pattern, $rawData, $matches);
                if (isset($matches[1]) && isset($matches[2]) && isset($matches[3])) {
                    $data[$key] = [
                        'domain_status' => trim($matches[1]),
                        'frozen_status' => trim($matches[2]),
                        'transfer_status' => trim($matches[3]),
                    ];
                } else {
                    $data[$key] = null; // Eğer eşleşme bulunamazsa null ekle
                }
            } else {
                preg_match($pattern, $rawData, $matches);
                $data[$key] = $matches[1] ?? null;
            }
            $data["raw_data"] = $rawData;
        }
        

        return new WhoisModel($data);
    }
}
