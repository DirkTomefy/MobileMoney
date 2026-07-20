<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\ClientModel;
use Exception;


class TransactionController extends BaseController
{

    protected TransactionModel $transactionModel;
    protected ClientModel $clientModel;



    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->clientModel = new ClientModel();
    }



    private function checkSession()
    {
        if (!session()->get('connecte')) {
            return redirect()->to('/');
        }

        return null;
    }



    public function index()
    {
        if ($redirect = $this->checkSession()) {
            return $redirect;
        }


        $idClient = session()->get('client_id');


        if (!$idClient) {
            return redirect()->to('/');
        }


        $solde = $this->clientModel->getSolde($idClient);


        return view('client/transaction', [
            'solde' => $solde
        ]);
    }



    public function saveDeposer()
    {

        if ($redirect = $this->checkSession()) {
            return $redirect;
        }


        try {

            $idClient = session()->get('client_id');

            $montant = (float)$this->request->getPost('montant');


            $this->transactionModel->deposer(
                $idClient,
                $montant
            );


            return redirect()
                ->to('/client/transaction')
                ->with(
                    'success',
                    'Dépôt effectué avec succès.'
                );
        } catch (Exception $e) {

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }







    public function saveRetirer()
    {

        if ($redirect = $this->checkSession()) {
            return $redirect;
        }


        try {

            $idClient = session()->get('client_id');

            $montant = (float)$this->request->getPost('montant');


            $this->transactionModel->retirer(
                $idClient,
                $montant
            );


            return redirect()
                ->to('/client/transaction')
                ->with(
                    'success',
                    'Retrait effectué avec succès.'
                );
        } catch (Exception $e) {

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }







    public function saveTransferer()
    {

        if ($redirect = $this->checkSession()) {
            return $redirect;
        }


        try {


            $idSource = session()->get('client_id');


            $numero = $this->request->getPost('numero');


            $montant = (float)$this->request->getPost('montant');



            $cible = $this->clientModel
                ->where('numero', $numero)
                ->first();



            if (!$cible) {

                throw new Exception(
                    "Client destinataire introuvable."
                );
            }



            $this->transactionModel->transferer(
                $idSource,
                $cible['id'],
                $montant
            );



            return redirect()
                ->to('/client/transaction')
                ->with(
                    'success',
                    'Transfert effectué avec succès.'
                );
        } catch (Exception $e) {


            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function getInfoNumero()
    {
        $numero = $this->request->getGet('numero');


        $clientModel = new \App\Models\ClientModel();


        $client = $clientModel->findByNumero($numero);


        if (!$client) {

            return $this->response->setJSON([
                'success' => false
            ]);
        }


        return $this->response->setJSON([

            'success' => true,

            'id_client' => $client['id'],

            'id_operateur' => $client['id_operateur']

        ]);
    }
    public function getCommission()
    {
        $idOperateurEnvoi = $this->request->getGet('id_operateur_envoi');
        $idOperateurReceveur = $this->request->getGet('id_operateur_receveur');


        $commissionModel = new \App\Models\CommissionModel();


        $commission = $commissionModel->getCommission(
            $idOperateurEnvoi,
            $idOperateurReceveur
        );


        if (!$commission) {

            return $this->response->setJSON([
                'success' => false,
                'pourcentage' => 0
            ]);
        }


        return $this->response->setJSON([

            'success' => true,

            'pourcentage' => (float)$commission['pourcentage']

        ]);
    }
}
