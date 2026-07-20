<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\OperateurModel;

class SoldeController extends BaseController
{
    protected $transactionModel;
    protected $operateurModel;
    protected $clientId;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->operateurModel = new OperateurModel();

        // Récupérer client_id depuis la session
        $this->clientId = session()->get('client_id');
        if (!$this->clientId) {
            return redirect()->to('/')->with('error', 'Veuillez vous connecter.');
        }
    }

    public function index()
    {
        $date = $this->request->getGet('date') ?: date('Y-m-d');
        $solde = $this->transactionModel->getSolde($this->clientId, $date);

        // Filtres
        $type = $this->request->getGet('type');
        $dateMin = $this->request->getGet('date_min');
        $dateMax = $this->request->getGet('date_max');
        $page = (int) $this->request->getGet('page') ?: 1;

        $filters = [];
        if ($type) $filters['type'] = $type;
        if ($dateMin) $filters['date_min'] = $dateMin;
        if ($dateMax) $filters['date_max'] = $dateMax;

        $perPage = 10;
        $dataTransactions = $this->transactionModel->getAllTransaction($this->clientId, $perPage, $page, $filters);

        // Types pour le filtre
        $types = $this->operateurModel->db->table('t_type_operation')
            ->whereIn('code', ['DEPOT', 'RETRAIT', 'TRANSFERT'])
            ->get()
            ->getResultArray();

        $data = [
            'solde'        => $solde,
            'date_solde'   => $date,
            'transactions' => $dataTransactions['transactions'],
            'pager'        => $dataTransactions['pager'],
            'total'        => $dataTransactions['total'],
            'types'        => $types,
            'filtre_type'  => $type,
            'filtre_date_min' => $dateMin,
            'filtre_date_max' => $dateMax,
            'client_id'    => $this->clientId,
        ];

        return view('client/solde', $data);
    }

    public function action($type)
    {
        $typeValide = in_array($type, ['depot', 'retrait', 'transfert']);
        if (!$typeValide) {
            return redirect()->to('/client/solde')->with('error', 'Action invalide.');
        }
        // Redirection vers un formulaire de transaction (à créer)
        return redirect()->to('/client/transaction/create?type=' . $type);
    }
}