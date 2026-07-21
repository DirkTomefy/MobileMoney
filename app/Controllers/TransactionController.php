<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\ClientModel;
use App\Models\EpargneModel;
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
        if ($redirect = $this->checkSession()) return $redirect;

        $idClient = session()->get('client_id');
        if (!$idClient) return redirect()->to('/');

        $solde = $this->clientModel->getSolde($idClient);
        $eparge=$this->clientModel->getEparge($idClient);

        $client = $this->clientModel->find($idClient);

        return view('client/transaction', [
            'solde'  => $solde,
            'client' => $client,
            'epargne' => $eparge,
            'en_compte'=>$solde-$eparge,
        ]);
    }

    public function saveDeposer()
    {
        if ($redirect = $this->checkSession()) return $redirect;
        try {
            $idClient = session()->get('client_id');
            $montant = (float)$this->request->getPost('montant');
            $this->transactionModel->deposer($idClient, $montant);
            return redirect()->to('/client/transaction')->with('success', 'Dépôt effectué.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function saveRetirer()
    {
        if ($redirect = $this->checkSession()) return $redirect;
        try {
            $idClient = session()->get('client_id');
            $montant = (float)$this->request->getPost('montant');
            $this->transactionModel->retirer($idClient, $montant);
            return redirect()->to('/client/transaction')->with('success', 'Retrait effectué.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function saveTransferer()
    {
        if ($redirect = $this->checkSession()) return $redirect;

        $idSource = session()->get('client_id');
        $numerosStr = $this->request->getPost('numeros');
        $montantTotal = (float)$this->request->getPost('montant_total');
        $addFees = $this->request->getPost('addFees') ? true : false;

        // Nettoyer les numéros
        $numeros = array_map('trim', explode(',', $numerosStr));
        $numeros = array_filter($numeros, fn($n) => $n !== '');
        if (empty($numeros)) {
            return redirect()->back()->with('error', 'Veuillez saisir au moins un numéro.');
        }

        // Vérifier que tous les numéros existent
        $clientsCibles = [];
        foreach ($numeros as $num) {
            $client = $this->clientModel->where('numero', $num)->first();
            if (!$client) {
                return redirect()->back()->with('error', "Le numéro $num n'existe pas.");
            }
            $clientsCibles[] = $client;
        }

        // Montant par bénéficiaire (arrondi à 2 décimales)
        $nbBenef = count($clientsCibles);
        $montantParBenef = round($montantTotal / $nbBenef, 2);

        if ($montantParBenef < 1) {
            return redirect()->back()->with('error', 'Le montant par bénéficiaire est trop faible.');
        }

        // Démarrer la transaction
        $this->transactionModel->db->transStart();

        try {
            foreach ($clientsCibles as $cible) {
                $this->transactionModel->transferer(
                    $idSource,
                    $cible['id'],
                    $montantParBenef,
                    null, // date
                    $addFees
                );
            }

            // Valider la transaction
            $this->transactionModel->db->transComplete();

            if ($this->transactionModel->db->transStatus() === false) {
                throw new Exception('Erreur lors de l\'enregistrement des transferts.');
            }

            return redirect()->to('/client/transaction')->with('success', 'Transferts effectués avec succès.');
        } catch (Exception $e) {
            $this->transactionModel->db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // API : informations d'un numéro
    public function getInfoNumero()
    {
        $numero = $this->request->getGet('numero');
        $client = $this->clientModel->where('numero', $numero)->first();
        if (!$client) {
            return $this->response->setJSON(['success' => false]);
        }
        return $this->response->setJSON([
            'success' => true,
            'id_client'    => $client['id'],
            'id_operateur' => $client['id_operateur'],
        ]);
    }

    // API : commission entre opérateurs
    public function getCommission()
    {
        $idEnvoi = $this->request->getGet('id_operateur_envoi');
        $idReceveur = $this->request->getGet('id_operateur_receveur');

        $commissionModel = new \App\Models\CommissionModel();
        $commission = $commissionModel->getCommission($idEnvoi, $idReceveur);

        if (!$commission) {
            return $this->response->setJSON(['success' => false, 'pourcentage' => 0]);
        }
        return $this->response->setJSON([
            'success' => true,
            'pourcentage' => (float)$commission['pourcentage'],
        ]);
    }

    // API : frais de transfert (pour un montant donné)
    public function getFraisTransfert()
    {
        $montant = (float)$this->request->getGet('montant');
        $idSource = session()->get('client_id');
        $client = $this->clientModel->find($idSource);
        if (!$client) {
            return $this->response->setJSON(['frais' => 0]);
        }

        $tarifModel = new \App\Models\TarifOperationModel();
        $typeModel = new \App\Models\TypeOperationModel();
        $type = $typeModel->where('code', 'TRANSFERT')->first();
        if (!$type) {
            return $this->response->setJSON(['frais' => 0]);
        }

        $tarif = $tarifModel->getTarif($client['id_operateur'], $type['id'], $montant);
        if (!$tarif) {
            return $this->response->setJSON(['frais' => 0]);
        }
        return $this->response->setJSON(['frais' => (float)$tarif['prix']]);
    }
}