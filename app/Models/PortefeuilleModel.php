<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\TransactionModel;


class PortefeuilleModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getAllPortefeuille($date = null)
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

        $clients = $this->db->table('t_client')->get()->getResultArray();
        $result = [];

        $transactionModel = new TransactionModel();

        foreach ($clients as $client) {
            $solde = $transactionModel->getSolde($client['id'], $date);
            $result[] = [
                'id'          => $client['id'],
                'nom'         => $client['nom'],
                'prenom'      => $client['prenom'],
                'numero'      => $client['numero'],
                'operateur'   => $this->getOperateurLibelle($client['id_operateur']),
                'date_creation' => $client['date_creation'],
                'solde'       => $solde
            ];
        }

        usort($result, function($a, $b) {
            return $b['solde'] - $a['solde'];
        });

        return $result;
    }

    private function getOperateurLibelle($id_operateur)
    {
        $row = $this->db->table('t_operateur')->select('libelle')->where('id', $id_operateur)->get()->getRow();
        return $row ? $row->libelle : 'N/A';
    }
}