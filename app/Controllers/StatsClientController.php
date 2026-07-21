<?php

namespace App\Controllers;

use App\Models\OperateurModel;

class StatsClientController extends BaseController
{
    protected $operateurModel;
    protected $operateurId;

    public function __construct()
    {
        $this->operateurModel = new OperateurModel();
        $this->operateurId = session()->get('operateur_id') ?? 1;
    }

    public function topClients()
    {
        $dateMin = $this->request->getGet('date_min') ?: date('Y-m-01');
        $dateMax = $this->request->getGet('date_max') ?: date('Y-m-d');
        $orderBy = $this->request->getGet('order') ?? 'montant'; // 'montant' ou 'nb'

        if ($dateMin > $dateMax) {
            $tmp = $dateMin;
            $dateMin = $dateMax;
            $dateMax = $tmp;
        }

        $topClients = $this->operateurModel->getTopClients($dateMin, $dateMax, $this->operateurId, 10, $orderBy);

        return view('backoffice/top_clients', [
            'topClients' => $topClients,
            'date_min'   => $dateMin,
            'date_max'   => $dateMax,
            'orderBy'    => $orderBy
        ]);
    }
}