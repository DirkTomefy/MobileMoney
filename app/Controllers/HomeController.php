<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixModel;
use Exception;

class HomeController extends BaseController
{
    protected ClientModel $clientModel;
    protected PrefixModel $prefixModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->prefixModel = new PrefixModel();
    }
    public function index()
    {
        return view('login');
    }

    public function connect()
    {
        $numero = $this->request->getPost('phone');

        if (!$numero) {
            return redirect()->back()
                ->with('error', 'Le numéro est obligatoire.');
        }

        $client = $this->clientModel
            ->where('numero', $numero)
            ->first();

        if ($client) {

            session()->set([
                'client_id' => $client['id'],
                'numero'    => $client['numero'],
                'connecte'  => true
            ]);

            return redirect()->to('/client/home');
        }

        if (!$this->prefixModel->isValid($numero)) {
            return redirect()->back()
                ->with('error', 'Numéro invalide.');
        }

        $operateur = $this->prefixModel
            ->getOperateurByNumero($numero);

        if (!$operateur) {

            return redirect()->back()
                ->with('error', 'Opérateur introuvable.');
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
}
