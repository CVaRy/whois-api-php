<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Controllers\WhoisQuery;
use Exception;
class GenericParser extends Model
{
    protected $whois_server_finder = 'whois.iana.org';
    protected $whoisServer;
    protected $patterns = [
        'registrar' => '/Registrar:\s*(.+)/i',
        'creation_date' => '/Creation Date:\s*(.+)/i',
        'status'=> '/Domain Status:s*(.+)/i',
        'expiration_date' => '/Registry Expiry Date:\s*(.+)/i',
        'abuse_mail'=> '/Registrar Abuse Contact Email:\s*(.+)/i',
        'abuse_phone'=>'/Registrar Abuse Contact Phone:\s*(.+)/i',
        'name_servers' => '/Name Server:\s*(.+)/i',
        'dns_sec'=> '/DNSSEC:\s*(.+)/i'
    ];

    protected $pattern_whois_server = [
        'whois_server' => '/whois:\s+([^\s]+)/'
    ];



    public function parse($domain){
        // IANA Whois sorgusu
        $req_whois_iana = new WhoisQuery;
        $raw_iana = $req_whois_iana->index($this->whois_server_finder, $domain);

        // IANA'dan whois sunucusu bulma
        $data0 = [];
        foreach ($this->pattern_whois_server as $key => $whois_parse) {
            preg_match($whois_parse, $raw_iana, $matches);
            $data0[$key] = $matches[1] ?? null;
        }

        $this->whoisServer = $data0['whois_server'] ?? null;

        if (!$this->whoisServer) {
            throw new Exception("Whois sunucusu bulunamadı.");
        }

        // Whois sunucusuna sorgu
        $request_data = new WhoisQuery();
        $rawData = $request_data->index($this->whoisServer, $domain);

        if (!$rawData) {
            throw new Exception("Whois sorgusu başarısız.");
        }
            

        // Whois sunucusuna istek gönder
        $request_data = new WhoisQuery();
        
        $rawData = $request_data->index($this->whoisServer,$domain);
        // Regex desenlerine göre veriyi işle
        $data = [];

        foreach ($this->patterns as $key => $pattern) {
            if ($key === 'name_servers') {
                preg_match_all($pattern, $rawData, $matches);
                $data[$key] = array_map('trim', $matches[1]);
            } else if ($key === 'status') {
                // Status için tek bir eşleşme
                preg_match_all($pattern, $rawData, $matches);
                $data[$key] = array_map('trim', $matches[1]);
            }else{
                preg_match($pattern, $rawData, $matches);
                $data[$key] = $matches[1] ?? null;
            }
            $data["raw_data"] = $rawData;
        }
        

        return new WhoisModel($data);
    }
}
