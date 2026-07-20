<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class CommissionModel extends Model
{
    protected $table = 't_commission';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_operateur_envoi',
        'id_operateur_receveur',
        'pourcentage',
        'valable'
    ];


    /**
     * Cherche la commission entre deux opérateurs
     */
    public function getCommission(
        int $id_operateur_envoi,
        int $id_operateur_receveur
    ) {
        return $this
            ->where('id_operateur_envoi', $id_operateur_envoi)
            ->where('id_operateur_receveur', $id_operateur_receveur)
            ->where('valable', 1)
            ->first();
    }

    public function transferer(
        int $id_client_source,
        int $id_client_cible,
        float $montant,
        ?string $date = null
    ) {

        $date = $date ?? date('Y-m-d H:i:s');


        $source = $this->clientModel->find($id_client_source);
        $cible  = $this->clientModel->find($id_client_cible);


        if (!$source) {
            throw new Exception("Client source introuvable.");
        }


        if (!$cible) {
            throw new Exception("Client destinataire introuvable.");
        }



        // Vérification solde
        $solde = $this->getSolde($id_client_source);

        if ($solde < $montant) {
            throw new Exception("Solde insuffisant.");
        }



        // Récupération du type TRANSFERT
        $operation = $this->typeOperationModel
            ->where('code', 'TRANSFERT')
            ->first();


        if (!$operation) {
            throw new Exception("Type opération TRANSFERT introuvable.");
        }



        // Vérification du tarif
        $this->checkSeuil(
            $source['id_operateur'],
            $operation['id'],
            $montant
        );



        $commissionModel = new CommissionModel();


        $commissionData = $commissionModel->getCommission(
            $source['id_operateur'],
            $cible['id_operateur']
        );



        if (!$commissionData) {

            throw new Exception(
                "Aucune commission définie entre ces opérateurs."
            );
        }


        $commission =
            $montant
            * $commissionData['pourcentage']
            / 100;

        $frais = $this->getFrais(
            $source['id_operateur'],
            $operation['id'],
            $montant
        );



        $this->insert([

            'id_client_source'  => $id_client_source,

            'id_client_cible'   => $id_client_cible,

            'id_type_operation' => $operation['id'],

            'date'              => $date,

            'montant'           => $montant,

            'frais'             => $frais,

            'commission'        => $commission

        ]);

        return true;
    }
}
