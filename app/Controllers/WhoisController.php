<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BizParser;
use App\Models\CcParser;
use App\Models\NetParser;
use App\Models\TrParser;
use CodeIgniter\Database\Query;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\WhoisModel;
use App\Models\ComParser;
use App\Controllers\CacheWhois;
use App\Models\OrgParser;
use App\Models\XyzParser;
use App\Models\GenericParser;
use App\Models\InfoParser;

class WhoisController extends BaseController
{
    public function index()
    {
        return $this->response->setJSON([
            'status'=>'ok',
            'msg'=>'Sistem aktif olarak çalışıyor...'
        ]);

    }

    public function query($domain)
    {
        // Domain uzantısını al
        $extension = pathinfo($domain, PATHINFO_EXTENSION);

        // Doğru parser sınıfını seç
        $parser = $this->getParserByExtension($extension);

        $cache = new CacheWhois;
        $cache_control = $cache->getWhoisData($domain);; 
        if (empty($cache_control))
        {
            $whoisModel = $parser->parse($domain);
            $cache->setWhoisData($domain,$whoisModel,1);
            
        }else{
            $whoisModel = $cache->getWhoisData($domain);
            

        }
            
        
        
        // JSON formatında çıktıyı döner
        return $this->response->setJSON([
            "data"=>$whoisModel->toArray(),
        ]);
        
    }

    private function getParserByExtension($extension)
    {
        // Uzantıya Özel Regex Gerektiren Domainler Burada Bulunur
        switch ($extension) {
            case 'tr':
                return new TrParser();
            // Generic Parse // Genel Yapıya Uyan Regex Domain Uzantıları İçindir.
            default:
                return new GenericParser();
        }
    }
}
