<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixModel;
use App\Models\OperateurModel;

class HomeController extends BaseController
{
    protected ClientModel $clientModel;
    protected PrefixModel $prefixModel;
    protected OperateurModel $operateurModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->prefixModel = new PrefixModel();
        $this->operateurModel = new OperateurModel();
    }

    public function index()
    {
        // Récupérer la liste des opérateurs pour le select
        $operateurs = $this->operateurModel->getAllOperateurs();
        return view('login', ['operateurs' => $operateurs]);
    }

    public function connect()
    {
        $numero = $this->request->getPost('phone');

        if (!$numero) {
            return redirect()->back()->with('error', 'Le numéro est obligatoire.');
        }

        $client = $this->clientModel
            ->where('numero', $numero)
            ->first();
        $client = $this->clientModel->where('numero', $numero)->first();

        if ($client) {
            session()->set([
                'client_id' => $client['id'],
                'numero'    => $client['numero'],
                'connecte'  => true
            ]);
            return redirect()->to('/client/home');
        }

        if (!$this->prefixModel->isValid($numero)) {
            return redirect()->back()->with('error', 'Numéro invalide.');
        }

        $operateur = $this->prefixModel
            ->getOperateurByNumero($numero);
        $operateur = $this->prefixModel->getOperateurByNumero($numero);

        if (!$operateur) {
            return redirect()->back()->with('error', 'Opérateur introuvable.');
        }

        $data = [
            'numero'        => $numero,
            'id_operateur'  => $operateur['id'],
            'nom'           => null,
            'prenom'        => null,
            'date_creation' => date('Y-m-d H:i:s')
        ];

        if (!$this->clientModel->insert($data)) {
            dd($this->clientModel->errors());
        }

        $clientId = $this->clientModel->getInsertID();

        session()->set([
            'client_id' => $clientId,
            'numero'    => $numero,
            'connecte'  => true
        ]);

        return redirect()->to('/client/home');
    }

    public function connectOperateur()
    {
        $operateurId = $this->request->getPost('operateur_id');

        if (!$operateurId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un opérateur.');
        }

        $operateur = $this->operateurModel->find($operateurId);
        if (!$operateur) {
            return redirect()->back()->with('error', 'Opérateur invalide.');
        }

        session()->set([
            'operateur_id' => $operateurId,
            'operateur_connecte' => true,
            'connecte' => true 
        ]);

        return redirect()->to('/backoffice/dashboard');
    }
}