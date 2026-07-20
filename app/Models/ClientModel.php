<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table      = 't_client';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nom',
        'prenom',
        'id_operateur',
        'numero',
        'date_creation'
    ];


    public function isSigned(string $numero): bool
    {
        return $this->where('numero', $numero)
            ->countAllResults() > 0;
    }


    public function signUp(string $numero): bool
    {
        return $this->insert([
            'numero'        => $numero,
            'nom'           => null,
            'prenom'        => null,
            'id_operateur'  => null,
            'date_creation' => date('Y-m-d H:i:s')
        ]) !== false;
    }

    public function getSolde(int $id_client)
    {
        $db = \Config\Database::connect();


        $sql = "
        SELECT 
            SUM(
                CASE

                    WHEN op.code = 'DEPOT'
                    THEN t.montant


                    WHEN op.code = 'RETRAIT'
                    THEN -t.montant


                    WHEN op.code = 'TRANSFERT'
                    THEN -t.montant


                    ELSE 0

                END
            ) AS solde

        FROM t_transaction t

        JOIN t_type_operation op
        ON op.id = t.id_type_operation

        WHERE t.id_client_source = ?
    ";


        $query = $db->query(
            $sql,
            [$id_client]
        );


        $result = $query->getRowArray();


        return (float)($result['solde'] ?? 0);
    }
}
