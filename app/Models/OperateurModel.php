<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $db;

    protected $table = 't_operateur';
    protected $primaryKey = 'id';
    protected $allowedFields = ['libelle'];
    


    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // ================================================================
    // SECTION DASHBOARD
    // ================================================================

    public function getTotalRecuOperateur($dateMin, $dateMax, $operateurSource = null)
{
    $debut = $this->normalizeDate($dateMin, false);
    $fin   = $this->normalizeDate($dateMax, true);

    $builder = $this->db->table('t_transaction t');
    $builder->select("COALESCE(SUM(t.montant), 0) as total_montant");
    $builder->join('t_client source', 'source.id = t.id_client_source');
    $builder->join('t_client cible', 'cible.id = t.id_client_cible', 'left');
    $builder->join('t_type_operation toper', 'toper.id = t.id_type_operation');
    $builder->where('toper.code', 'TRANSFERT');
    $builder->where('t.date >=', $debut);
    $builder->where('t.date <=', $fin);
    $builder->where('source.id_operateur !=', 'cible.id_operateur', false);

    if ($operateurSource) {
        $builder->where('cible.id_operateur', $operateurSource);
    }

    $result = $builder->get()->getRow();
    return (float) ($result->total_montant ?? 0);
}

public function getRepartitionRecuOperateur($dateMin, $dateMax, $operateurCible)
{
    $debut = $this->normalizeDate($dateMin, false);
    $fin   = $this->normalizeDate($dateMax, true);

    $builder = $this->db->table('t_transaction t');
    $builder->select("
        op_source.libelle as operateur_source,
        COUNT(t.id) as nb_transactions,
        SUM(t.montant) as total_montant,
        SUM(t.frais) as total_frais,           -- AJOUTÉ
        COALESCE(SUM(t.commission), 0) as total_commission
    ");
    $builder->join('t_client source', 'source.id = t.id_client_source');
    $builder->join('t_client cible', 'cible.id = t.id_client_cible', 'left');
    $builder->join('t_type_operation toper', 'toper.id = t.id_type_operation');
    $builder->join('t_operateur op_source', 'op_source.id = source.id_operateur', 'left');
    $builder->where('toper.code', 'TRANSFERT');
    $builder->where('t.date >=', $debut);
    $builder->where('t.date <=', $fin);
    $builder->where('cible.id_operateur', $operateurCible);
    $builder->where('source.id_operateur !=', 'cible.id_operateur', false);

    $builder->groupBy('source.id_operateur, op_source.libelle');
    $builder->orderBy('total_montant', 'DESC');

    return $builder->get()->getResultArray();
}


    public function getSituationGlobale($dateMin, $dateMax, $operateur)
    {
        $debut = $this->normalizeDate($dateMin, false);
        $fin = $this->normalizeDate($dateMax, true);

        $builder = $this->db->table('t_transaction t');
        $builder->select("
            toper.code as type_code,
            SUM(t.montant) as total_montant,
            SUM(t.frais) as total_frais,
            COUNT(t.id) as nb
        ");
        $builder->join('t_client c', 'c.id = t.id_client_source', 'left');
        $builder->join('t_type_operation toper', 'toper.id = t.id_type_operation');
        $builder->where('c.id_operateur', $operateur);
        $builder->where('t.date >=', $debut);
        $builder->where('t.date <=', $fin);
        $builder->whereIn('toper.code', ['RETRAIT', 'TRANSFERT']);
        $builder->groupBy('toper.code');
        return $builder->get()->getResultArray();
    }

    public function getSituationDetail($date_debut, $date_fin, $operateur = 1)
    {
        $debut = $this->normalizeDate($date_debut, false);
        $fin = $this->normalizeDate($date_fin, true);
        if (!$debut || !$fin) {
            return ['error' => 'Format de date invalide.'];
        }

        $typeIds = $this->getTypeIds();
        if (empty($typeIds)) {
            return ['error' => 'Types d\'opération RETRAIT ou TRANSFERT introuvables.'];
        }

        $builder = $this->buildBaseQuery($operateur);
        $builder->select("
            DATE(t.date) as jour,
            toper.code as type_code,
            COUNT(t.id) as nb,
            SUM(t.montant) as total_montant,
            SUM(t.frais) as total_frais
        ");
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

   public function getRepartitionInterOperateur($dateMin, $dateMax, $operateur = null)
{
    $debut = $this->normalizeDate($dateMin, false);
    $fin   = $this->normalizeDate($dateMax, true);

    $builder = $this->db->table('t_transaction t');
    $builder->select("
        op_receveur.libelle as operateur_receveur,
        COUNT(t.id) as nb_transactions,
        SUM(t.montant) as total_montant,
        SUM(t.frais) as total_frais,
        COALESCE(SUM(t.commission), 0) as total_commission
    ");
    $builder->join('t_client source', 'source.id = t.id_client_source');
    $builder->join('t_client cible', 'cible.id = t.id_client_cible', 'left');
    $builder->join('t_type_operation toper', 'toper.id = t.id_type_operation');
    $builder->join('t_operateur op_receveur', 'op_receveur.id = cible.id_operateur', 'left');
    $builder->where('toper.code', 'TRANSFERT');
    $builder->where('t.date >=', $debut);
    $builder->where('t.date <=', $fin);
    $builder->where('source.id_operateur !=', 'cible.id_operateur', false);

    if ($operateur) {
        $builder->where('source.id_operateur', $operateur);
    }

    $builder->groupBy('cible.id_operateur, op_receveur.libelle');
    $builder->orderBy('total_montant', 'DESC');

    return $builder->get()->getResultArray();
}


    public function getMontantInterOperateur($dateMin, $dateMax, $operateur = null)
    {
        $debut = $this->normalizeDate($dateMin, false);
        $fin   = $this->normalizeDate($dateMax, true);

        $builder = $this->db->table('t_transaction t');
        $builder->select('SUM(t.montant) as total_montant');
        $builder->join('t_client source', 'source.id = t.id_client_source');
        $builder->join('t_client cible', 'cible.id = t.id_client_cible', 'left');
        $builder->join('t_type_operation toper', 'toper.id = t.id_type_operation');
        $builder->where('toper.code', 'TRANSFERT');
        $builder->where('t.date >=', $debut);
        $builder->where('t.date <=', $fin);
        $builder->where('source.id_operateur !=', 'cible.id_operateur', false); // opérateurs différents

        if ($operateur) {
            $builder->where('source.id_operateur', $operateur);
        }

        $result = $builder->get()->getRow();
        return (float) ($result->total_montant ?? 0);
    }

    public function getDashboardData($dateMin, $dateMax, $operateur = 1)
    {
        $totaux = $this->getSituationGlobale($dateMin, $dateMax, $operateur);

        $totalFrais = 0;
        $totalMontant = 0;
        $totalTransactions = 0;
        $totalRetrait = ['frais' => 0, 'nb' => 0, 'montant' => 0];
        $totalTransfert = ['frais' => 0, 'nb' => 0, 'montant' => 0];

        foreach ($totaux as $row) {
            $code = strtolower($row['type_code']);
            if ($code === 'retrait') {
                $totalRetrait = [
                    'frais'   => (float) $row['total_frais'],
                    'nb'      => (int) $row['nb'],
                    'montant' => (float) $row['total_montant'],
                ];
            } elseif ($code === 'transfert') {
                $totalTransfert = [
                    'frais'   => (float) $row['total_frais'],
                    'nb'      => (int) $row['nb'],
                    'montant' => (float) $row['total_montant'],
                ];
            }
            $totalFrais += (float) $row['total_frais'];
            $totalMontant += (float) $row['total_montant'];
            $totalTransactions += (int) $row['nb'];
        }

        $detailData = $this->getSituationDetail($dateMin, $dateMax, $operateur);
        
        $labels = $dataFrais = $dataRetrait = $dataTransfert = [];

        if (!isset($detailData['error'])) {
            foreach ($detailData['detail'] as $jour) {
                $labels[] = $jour['date'];
                $dataFrais[] = $jour['total_frais'];
                $dataRetrait[] = $jour['retrait']['frais'];
                $dataTransfert[] = $jour['transfert']['frais'];
            }
        }

        $montantInterOperateur = $this->getMontantInterOperateur($dateMin, $dateMax, $operateur);

        return [
            'date_min'       => $dateMin,
            'date_max'       => $dateMax,
            'total_frais'    => $totalFrais,
            'total_montant'  => $totalMontant,
            'total_transactions' => $totalTransactions,
            'total_retrait'  => $totalRetrait,
            'total_transfert'=> $totalTransfert,
            'montant_inter_operateur' => $montantInterOperateur,
            'labels'         => json_encode($labels),
            'data_frais'     => json_encode($dataFrais),
            'data_retrait'   => json_encode($dataRetrait),
            'data_transfert' => json_encode($dataTransfert),
            'detail'         => $detailData['detail'] ?? [],
            'error'          => $detailData['error'] ?? null,
            'repartition_inter_operateur' => $this->getRepartitionInterOperateur($dateMin, $dateMax, $operateur),
            'repartition_recu'=> $this->getRepartitionRecuOperateur($dateMin, $dateMax, $operateur),
            'montant_recu_operateur'=> $this->getTotalRecuOperateur($dateMin, $dateMax, $operateur),

        ];
    }

    // ================================================================
    // SECTION OPÉRATEURS
    // ================================================================

   

    public function getAllOperateurs()
    {
        return $this->db->table('t_operateur')->get()->getResultArray();
    }

    // ================================================================
    // SECTION TARIFS
    // ================================================================

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

    // ================================================================
    // HELPERS PRIVÉS
    // ================================================================

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
}