<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PortefeuilleModel;

class PortefeuilleController extends BaseController
{
    protected $portefeuilleModel;

    public function __construct()
    {
        $this->portefeuilleModel = new PortefeuilleModel();
    }

    public function index()
    {
        $date = $this->request->getGet('date') ?: date('Y-m-d');
        $portefeuille = $this->portefeuilleModel->getAllPortefeuille($date);

        $data = [
            'date'         => $date,
            'portefeuille' => $portefeuille
        ];

        return view('backoffice/portefeuille', $data);
    }
}