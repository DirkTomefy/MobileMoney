<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getSolde($id_client, $date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d H:i:s');
        } else {
            $dt = \DateTime::createFromFormat('Y-m-d', $date);
            if ($dt) {
                $dt->setTime(23, 59, 59);
                $date = $dt->format('Y-m-d H:i:s');
            }
        }

        $builderSource = $this->db->table('t_transaction t');
        $builderSource->select("SUM(CASE 
            WHEN toper.code = 'DEPOT' THEN t.montant
            WHEN toper.code IN ('RETRAIT', 'TRANSFERT') THEN - (t.montant + t.frais)
            ELSE 0
        END) as total_source");
        $builderSource->join('t_type_operation toper', 'toper.id = t.id_type_operation');
        $builderSource->where('t.id_client_source', $id_client);
        $builderSource->where('t.date <=', $date);
        $querySource = $builderSource->get();
        $totalSource = (float) $querySource->getRow()->total_source ?? 0;

        $builderCible = $this->db->table('t_transaction t');
        $builderCible->select('SUM(t.montant) as total_cible');
        $builderCible->join('t_type_operation toper', 'toper.id = t.id_type_operation');
        $builderCible->where('t.id_client_cible', $id_client);
        $builderCible->where('toper.code', 'TRANSFERT');
        $builderCible->where('t.date <=', $date);
        $queryCible = $builderCible->get();
        $totalCible = (float) $queryCible->getRow()->total_cible ?? 0;

        return $totalSource + $totalCible;
    }

    public function getAllTransaction($id_client, $perPage = 10, $page = 1, $filters = [])
    {
        $builder = $this->db->table('t_transaction t');
        $builder->select('
            t.id,
            t.date,
            t.montant,
            t.frais,
            toper.code as type_code,
            source.prenom as source_prenom,
            source.nom as source_nom,
            source.numero as source_numero,
            cible.prenom as cible_prenom,
            cible.nom as cible_nom,
            cible.numero as cible_numero
        ');
        $builder->join('t_type_operation toper', 'toper.id = t.id_type_operation', 'left');
        $builder->join('t_client source', 'source.id = t.id_client_source', 'left');
        $builder->join('t_client cible', 'cible.id = t.id_client_cible', 'left');

        $builder->where('(t.id_client_source = ' . $this->db->escape($id_client) . ' OR t.id_client_cible = ' . $this->db->escape($id_client) . ')');

        if (!empty($filters['type'])) {
            $builder->where('t.id_type_operation', $filters['type']);
        }
        if (!empty($filters['date_min'])) {
            $builder->where('t.date >=', $filters['date_min'] . ' 00:00:00');
        }
        if (!empty($filters['date_max'])) {
            $builder->where('t.date <=', $filters['date_max'] . ' 23:59:59');
        }

        $builder->orderBy('t.date', 'DESC');

        $total = $builder->countAllResults(false);
        $builder->limit($perPage, ($page - 1) * $perPage);

        $result = $builder->get()->getResultArray();

        $pager = \Config\Services::pager();
        $pager->makeLinks($page, $perPage, $total);

        return [
            'transactions' => $result,
            'pager' => $pager,
            'total' => $total
        ];
    }
}