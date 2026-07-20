<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class TransactionModel extends Model
{
    protected $table = 't_transaction';
    protected $primaryKey = 'id';


    protected $allowedFields = [
        'id_client_source',
        'id_client_cible',
        'id_type_operation',
        'date',
        'montant',
        'frais'
    ];


    protected ClientModel $clientModel;
    protected TypeOperationModel $typeOperationModel;
    protected TarifOperationModel $tarifModel;


    public function __construct()
    {
        $this->clientModel = new ClientModel();

        $this->typeOperationModel = new TypeOperationModel();
        $this->tarifModel = new TarifOperationModel();
    }


    public function retirer(
        int $id_client,
        float $montant,
        ?string $date = null
    ) {

        $date = $date ?? date('Y-m-d H:i:s');

        $client = $this->clientModel->find($id_client);

        if (!$client) {
            throw new Exception("Client introuvable.");
        }

        $solde = $this->clientModel->getSolde($id_client);

        if ($solde < $montant) {
            throw new Exception("Solde insuffisant.");
        }

        $operation = $this->typeOperationModel
            ->where('code', 'RETRAIT')
            ->first();

        if (!$operation) {
            throw new Exception("Type opération RETRAIT introuvable.");
        }

        $this->checkSeuil(
            $client['id_operateur'],
            $operation['id'],
            $montant
        );


        $this->clientModel->update(
            $id_client,
            [
                'solde' => $solde - $montant
            ]
        );

        return $this->transactionModel->insert([
            'id_client_source'  => $id_client,
            'id_client_cible'   => null,
            'id_type_operation' => $operation['id'],
            'date'              => $date,
            'montant'           => $montant,
            'frais'             => 0
        ]);
    }

    public function deposer(
        int $id_client,
        float $montant,
        ?string $date = null
    ) {

        $date = $date ?? date('Y-m-d H:i:s');


        $client = $this->clientModel->find($id_client);

        if (!$client) {
            throw new Exception("Client introuvable.");
        }

        $solde = $this->clientModel->getSolde($id_client);


        $operation = $this->typeOperationModel
            ->where('code', 'DEPOT')
            ->first();


        if (!$operation) {
            throw new Exception("Type opération DEPOT introuvable.");
        }


        // Vérification seuil
        $this->checkSeuil(
            $client['id_operateur'],
            $operation['id'],
            $montant
        );

        $this->clientModel->update(
            $id_client,
            [
                'solde' => $solde + $montant
            ]
        );

        return $this->transactionModel->insert([
            'id_client_source'  => $id_client,
            'id_client_cible'   => null,
            'id_type_operation' => $operation['id'],
            'date'              => $date,
            'montant'           => $montant,
            'frais'             => 0
        ]);
    }

    public function transferer(
        int $id_client_source,
        int $id_client_cible,
        float $montant,
        ?string $date = null
    ) {

        $date = $date ?? date('Y-m-d H:i:s');

        $source = $this->clientModel->find($id_client_source);
        $cible = $this->clientModel->find($id_client_cible);

        if (!$source || !$cible) {
            throw new Exception("Client introuvable.");
        }

        $soldeSource = $this->clientModel->getSolde($id_client_source);

        if ($soldeSource < $montant) {
            throw new Exception("Solde insuffisant.");
        }

        $operation = $this->typeOperationModel
            ->where('code', 'TRANSFERT')
            ->first();

        if (!$operation) {
            throw new Exception("Type opération TRANSFERT introuvable.");
        }

        $this->checkSeuil(
            $source['id_operateur'],
            $operation['id'],
            $montant
        );


        $soldeCible = $this->clientModel->getSolde($id_client_cible);

        $this->clientModel->update(
            $id_client_source,
            [
                'solde' => $soldeSource - $montant
            ]
        );
        $this->clientModel->update(
            $id_client_cible,
            [
                'solde' => $soldeCible + $montant
            ]
        );
        return $this->transactionModel->insert([
            'id_client_source'  => $id_client_source,
            'id_client_cible'   => $id_client_cible,
            'id_type_operation' => $operation['id'],
            'date'              => $date,
            'montant'           => $montant,
            'frais'             => 0
        ]);
    }





    private function checkSeuil(
        int $idOperateur,
        int $idTypeOperation,
        float $montant
    ) {
        $tarif = $this->tarifModel->getTarif(
            $idOperateur,
            $idTypeOperation,
            $montant
        );
        if (!$tarif) {

            throw new Exception(
                "Montant hors limite autorisée."
            );
        }


        return true;
    }
}
