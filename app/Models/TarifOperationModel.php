<?php

namespace App\Models;

use CodeIgniter\Model;

class TarifOperationModel extends Model
{
    protected $table = 't_tarif_operation';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_operateur',
        'id_type_operation',
        'min',
        'max',
        'prix'
    ];


    /**
     * Trouver le tarif selon le montant
     */
    public function getTarif(
        int $idOperateur,
        int $idTypeOperation,
        float $montant
    ) {
        return $this->where('id_operateur', $idOperateur)
                    ->where('id_type_operation', $idTypeOperation)
                    ->where('min <=', $montant)
                    ->where('max >=', $montant)
                    ->first();
    }
}