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

    
}
