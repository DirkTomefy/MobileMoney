<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EpargneModel;
use Exception;

class ClientController extends BaseController
{
    protected $epargneModel;

    public function __construct()
    {
        $this->epargneModel = new EpargneModel();
    }

    public function epargner()
    {
        $id_client = session()->get('client_id');
        $pourcentage   = $this->request->getPost('pourcentage');

        if (!$id_client) {
            throw new Exception("veuiller vous reiscrit");
        }
        $this->epargneModel->epargner($id_client,$pourcentage);
        return view('client/epargne');
    }
    public function formEpargne()
    {
        return view('client/epargne');
    }
    
}
