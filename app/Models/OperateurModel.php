<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    //! Branche DashBoard.php
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getSituationGlobal($date = null, $operateur = 1)
    {
        $date = $this->normalizeDate($date, true);
        $typeIds = $this->getTypeIds();

        if (empty($typeIds)) {
            return $this->emptySituation($date, $operateur);
        }

        $builder = $this->buildBaseQuery($operateur);
        $builder->select('toper.code as type_code, COUNT(t.id) as nb, SUM(t.montant) as total_montant, SUM(t.frais) as total_frais');
        $builder->where('t.date <=', $date);
        $builder->whereIn('t.id_type_operation', $typeIds);
        $builder->groupBy('toper.code');

        $results = $builder->get()->getResultArray();
        $data = $this->aggregateGlobal($results);
        $data['date'] = $date;
        $data['operateur'] = $operateur;

        return $data;
    }

    public function getSituationDetail($date_debut, $date_fin, $operateur = 1)
    {
        $debut = $this->normalizeDate($date_debut, false);
        $fin = $this->normalizeDate($date_fin, true);
        if (!$debut || !$fin) {
            return ['error' => 'Format de date invalide. Utilisez Y-m-d.'];
        }

        $typeIds = $this->getTypeIds();
        if (empty($typeIds)) {
            return ['error' => 'Types d\'opération RETRAIT ou TRANSFERT introuvables.'];
        }

        $builder = $this->buildBaseQuery($operateur);
        $builder->select("DATE(t.date) as jour, toper.code as type_code, COUNT(t.id) as nb, SUM(t.montant) as total_montant, SUM(t.frais) as total_frais");
        $builder->where('t.date >=', $debut);
        $builder->where('t.date <=', $fin);
        $builder->whereIn('t.id_type_operation', $typeIds);
        $builder->groupBy('DATE(t.date), toper.code');
        $builder->orderBy('jour', 'ASC');

        $results = $builder->get()->getResultArray();
        $detail = $this->aggregateDetail($results);

        return [
            'periode' => ['debut' => $debut, 'fin' => $fin],
            'operateur' => $operateur,
            'detail' => $detail
        ];
    }

    private function normalizeDate($date, $endOfDay)
    {
        if ($date === null) {
            return date('Y-m-d 23:59:59');
        }
        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        if (!$dt) {
            $dt = \DateTime::createFromFormat('Y-m-d', $date);
            if ($dt) {
                $dt->setTime($endOfDay ? 23 : 0, $endOfDay ? 59 : 0, $endOfDay ? 59 : 0);
            }
        }
        return $dt ? $dt->format('Y-m-d H:i:s') : date('Y-m-d 23:59:59');
    }

    private function getTypeIds()
    {
        $types = $this->db->table('t_type_operation')
            ->whereIn('code', ['RETRAIT', 'TRANSFERT'])
            ->get()
            ->getResultArray();
        return array_column($types, 'id');
    }

    private function buildBaseQuery($operateur)
    {
        return $this->db->table('t_transaction t')
            ->join('t_client c', 'c.id = t.id_client_source', 'left')
            ->join('t_type_operation toper', 'toper.id = t.id_type_operation')
            ->where('c.id_operateur', $operateur);
    }

    private function emptySituation($date, $operateur)
    {
        return [
            'date' => $date,
            'operateur' => $operateur,
            'retrait' => ['frais' => 0, 'nb' => 0, 'montant' => 0],
            'transfert' => ['frais' => 0, 'nb' => 0, 'montant' => 0],
            'total_frais' => 0,
            'total_transactions' => 0,
        ];
    }

    private function aggregateGlobal($results)
    {
        $data = [
            'retrait' => ['frais' => 0, 'nb' => 0, 'montant' => 0],
            'transfert' => ['frais' => 0, 'nb' => 0, 'montant' => 0],
            'total_frais' => 0,
            'total_transactions' => 0,
        ];
        foreach ($results as $row) {
            $code = strtolower($row['type_code']);
            if (isset($data[$code])) {
                $data[$code] = [
                    'frais' => (float) $row['total_frais'],
                    'nb' => (int) $row['nb'],
                    'montant' => (float) $row['total_montant'],
                ];
            }
        }
        $data['total_frais'] = $data['retrait']['frais'] + $data['transfert']['frais'];
        $data['total_transactions'] = $data['retrait']['nb'] + $data['transfert']['nb'];
        return $data;
    }

    private function aggregateDetail($results)
    {
        $dates = [];
        foreach ($results as $row) {
            $jour = $row['jour'];
            if (!isset($dates[$jour])) {
                $dates[$jour] = [
                    'date' => $jour,
                    'retrait' => ['frais' => 0, 'nb' => 0, 'montant' => 0],
                    'transfert' => ['frais' => 0, 'nb' => 0, 'montant' => 0],
                    'total_frais' => 0,
                    'total_transactions' => 0,
                ];
            }
            $code = strtolower($row['type_code']);
            if (isset($dates[$jour][$code])) {
                $dates[$jour][$code] = [
                    'frais' => (float) $row['total_frais'],
                    'nb' => (int) $row['nb'],
                    'montant' => (float) $row['total_montant'],
                ];
            }
            $dates[$jour]['total_frais'] += (float) $row['total_frais'];
            $dates[$jour]['total_transactions'] += (int) $row['nb'];
        }
        return array_values($dates);
    }

    //! Branche Tarif
    public function getAllTarif($id_type_operation, $operateur = 1)
{
    return $this->db->table('t_tarif_operation')
        ->where('id_operateur', $operateur)
        ->where('id_type_operation', $id_type_operation)
        ->orderBy('min', 'ASC')
        ->get()
        ->getResultArray();
}

public function updateTarif($id_tarif, $prix, $date = null)
{
    if ($date === null) {
        $date = date('Y-m-d H:i:s');
    }

    $db = $this->db;

    $old = $db->table('t_tarif_operation')->select('prix')->where('id', $id_tarif)->get()->getRow();
    if (!$old) {
        return false;
    }
    $ancien_prix = $old->prix;

    $db->table('t_tarif_operation')->where('id', $id_tarif)->update(['prix' => $prix]);

    $currentHist = $db->table('t_historique_tarif')
        ->where('id_tarif_operation', $id_tarif)
        ->where('date_changement', null)
        ->get()
        ->getRow();

    if ($currentHist) {
        $db->table('t_historique_tarif')
            ->where('id', $currentHist->id)
            ->update(['date_changement' => $date]);
    }

    $db->table('t_historique_tarif')->insert([
        'id_tarif_operation' => $id_tarif,
        'prix' => $prix,
        'date_changement' => null,
    ]);

    return true;
}

}


