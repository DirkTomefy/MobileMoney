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


        /*
     * Transactions où le client est source
     */
        $sqlSource = "
        SELECT 
            COALESCE(SUM(
                CASE

                    WHEN op.code = 'DEPOT'
                    THEN t.montant


                    WHEN op.code = 'RETRAIT'
                    THEN -(t.montant + t.frais)


                    WHEN op.code = 'TRANSFERT'
                    THEN -(t.montant + t.frais + t.commission)


                    ELSE 0

                END
            ),0) AS solde

        FROM t_transaction t

        JOIN t_type_operation op
        ON op.id = t.id_type_operation

        WHERE t.id_client_source = ?
    ";


        $querySource = $db->query(
            $sqlSource,
            [$id_client]
        );


        $soldeSource = (float)
        $querySource->getRowArray()['solde'];



        /*
     * Transferts reçus par le client
     */
        $sqlCible = "
        SELECT 
            COALESCE(SUM(t.montant),0) AS solde

        FROM t_transaction t

        JOIN t_type_operation op
        ON op.id = t.id_type_operation

        WHERE t.id_client_cible = ?

        AND op.code = 'TRANSFERT'
    ";


        $queryCible = $db->query(
            $sqlCible,
            [$id_client]
        );


        $soldeCible = (float)
        $queryCible->getRowArray()['solde'];



        return $soldeSource + $soldeCible;
    }
    

}
