<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OperateurModel;

class DashboardController extends BaseController
{
    protected $operateurModel;
    protected $operateurId;

    public function __construct()
    {
        $this->operateurModel = new OperateurModel();
        $this->operateurId = session()->get('operateur_id') ?? 1;
    }

    public function index()
    {
        $dateMin = $this->request->getGet('date_min') ?: date('Y-m-01');
        $dateMax = $this->request->getGet('date_max') ?: date('Y-m-d');

        if ($dateMin > $dateMax) {
            $tmp = $dateMin;
            $dateMin = $dateMax;
            $dateMax = $tmp;
        }

        $data = $this->operateurModel->getDashboardData($dateMin, $dateMax, $this->operateurId);
        $data['var'] = 'Les données sont chargées.';

        return view('backoffice/dashboard', $data);
    }
}