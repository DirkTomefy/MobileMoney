<?php

namespace App\Controllers;

use App\Models\OperateurModel;

class AlertesController extends BaseController
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

        // Seuil paramétrable
        $seuil = $this->request->getGet('seuil') ?: 1000000;

        if ($dateMin > $dateMax) {
            $tmp = $dateMin;
            $dateMin = $dateMax;
            $dateMax = $tmp;
        }

        $data = [
            'date_min' => $dateMin,
            'date_max' => $dateMax,
            'seuil'    => $seuil,
            'alertes'  => $this->operateurModel->getAlertesTransferts($dateMin, $dateMax, $this->operateurId, $seuil),
            'statistiques' => $this->operateurModel->getStatsAlertes($dateMin, $dateMax, $this->operateurId, $seuil),
        ];

        return view('backoffice/alertes', $data);
    }
}