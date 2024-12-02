<?php

namespace App\Models;

use CodeIgniter\Model;
use PHPUnit\Util\Json;

class WhoisModel
{
    public $registrar;
    public $creationDate;
    public $expirationDate;
    public $nameServers = [];
    public $raw_data;
    public $abuse_mail;
    public $abuse_phone;
    public $status;
    public $dns_sec;

    public function __construct($data)
    {
        $this->registrar = $data['registrar'] ?? null;
        $this->creationDate = $data['creation_date'] ?? null;
        $this->expirationDate = $data['expiration_date'] ?? null;
        $this->nameServers = $data['name_servers'] ?? [];
        $this->raw_data = $data["raw_data"] ?? null;
        $this->abuse_mail = $data["abuse_mail"] ?? null;
        $this->abuse_phone = $data["abuse_phone"] ?? null;
        $this->status = $data["status"] ?? null;
        $this->dns_sec = $data["dns_sec"] ?? null;
    }

    public function toArray():array
    {
        return [
            'registrar' => $this->registrar,
            'creationDate' => $this->creationDate,
            'expirationDate' => $this->expirationDate,
            'status'=> $this->status,
            'dns_sec'=> $this->dns_sec,
            'name_servers' => $this->nameServers,
            'abuse_mail'=> $this->abuse_mail,
            'abuse_phone'=>$this->abuse_phone,
            'raw_data' => $this->raw_data,

        ];
    }
}
