<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return $this->response->setJSON([
            'status'=> 'ok',
            'msg'=> 'Whois API v0.1'
        ]);
    }
}
