<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OperateurModel;

class DashboardController extends BaseController
{
    protected $operateurModel;

    public function __construct()
    {
        $this->operateurModel = new OperateurModel();
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

        $detailData = $this->operateurModel->getSituationDetail($dateMin, $dateMax);

        $totalFrais = 0;
        $totalMontant = 0;
        $totalTransactions = 0;
        $totalRetrait = ['frais' => 0, 'nb' => 0, 'montant' => 0];
        $totalTransfert = ['frais' => 0, 'nb' => 0, 'montant' => 0];
        $labels = $dataFrais = $dataRetrait = $dataTransfert = [];

        if (!isset($detailData['error'])) {
            foreach ($detailData['detail'] as $jour) {
                $labels[] = $jour['date'];
                $dataFrais[] = $jour['total_frais'];
                $dataRetrait[] = $jour['retrait']['frais'];
                $dataTransfert[] = $jour['transfert']['frais'];

                $totalFrais += $jour['total_frais'];
                $totalMontant += $jour['retrait']['montant'] + $jour['transfert']['montant'];
                $totalTransactions += $jour['total_transactions'];
                $totalRetrait['frais'] += $jour['retrait']['frais'];
                $totalRetrait['nb'] += $jour['retrait']['nb'];
                $totalRetrait['montant'] += $jour['retrait']['montant'];
                $totalTransfert['frais'] += $jour['transfert']['frais'];
                $totalTransfert['nb'] += $jour['transfert']['nb'];
                $totalTransfert['montant'] += $jour['transfert']['montant'];
            }
        }

        // Variable de débogage (à supprimer en production)
        $debugInfo = 'Les données sont chargées.';

        $data = [
            'date_min'       => $dateMin,
            'date_max'       => $dateMax,
            'total_frais'    => $totalFrais,
            'total_montant'  => $totalMontant,
            'total_transactions' => $totalTransactions,
            'total_retrait'  => $totalRetrait,
            'total_transfert'=> $totalTransfert,
            'labels'         => json_encode($labels),
            'data_frais'     => json_encode($dataFrais),
            'data_retrait'   => json_encode($dataRetrait),
            'data_transfert' => json_encode($dataTransfert),
            'detail'         => $detailData['detail'] ?? [],
            'error'          => $detailData['error'] ?? null,
            'var'            => $debugInfo, // 👈 variable ajoutée
        ];

        return view('backoffice/dashboard', $data);
    }
}