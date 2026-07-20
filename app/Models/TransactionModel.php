<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class TransactionModel extends Model
{
    protected $table = 't_transaction';
    protected $primaryKey = 'id';
    protected $db;

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
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->clientModel = new ClientModel();
        $this->typeOperationModel = new TypeOperationModel();
        $this->tarifModel = new TarifOperationModel();
    }

    /**
     * Effectue un retrait pour un client
     */
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

        $solde = $this->getSolde($id_client);

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

        return $this->insert([
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

        $operation = $this->typeOperationModel
            ->where('code', 'DEPOT')
            ->first();

        if (!$operation) {
            throw new Exception("Type opération DEPOT introuvable.");
        }

        $this->checkSeuil(
            $client['id_operateur'],
            $operation['id'],
            $montant
        );

        return $this->insert([
            'id_client_source'  => $id_client,
            'id_client_cible'   => null,
            'id_type_operation' => $operation['id'],
            'date'              => $date,
            'montant'           => $montant,
            'frais'             => 0
        ]);
    }
    /**
     * Effectue un dépôt pour un client
     */
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

        $solde = $this->getSolde($id_client_source);

        if ($solde < $montant) {
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

        // Enregistrer le transfert (débit du client source)
        $this->insert([
            'id_client_source'  => $id_client_source,
            'id_client_cible'   => $id_client_cible,
            'id_type_operation' => $operation['id'],
            'date'              => $date,
            'montant'           => $montant,
            'frais'             => 0
        ]);

        return true;
    }
    /**
     * Récupère le solde d'un client à une date donnée
     */
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

        // Calcul des transactions en tant que source
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
        $totalSource = (float) ($querySource->getRow()->total_source ?? 0);

        // Calcul des transferts reçus (en tant que cible)
        $builderCible = $this->db->table('t_transaction t');
        $builderCible->select('SUM(t.montant) as total_cible');
        $builderCible->join('t_type_operation toper', 'toper.id = t.id_type_operation');
        $builderCible->where('t.id_client_cible', $id_client);
        $builderCible->where('toper.code', 'TRANSFERT');
        $builderCible->where('t.date <=', $date);
        $queryCible = $builderCible->get();
        $totalCible = (float) ($queryCible->getRow()->total_cible ?? 0);

        return $totalSource + $totalCible;
    }

    /**
     * Récupère toutes les transactions d'un client avec pagination et filtres
     */
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

        // Filtres optionnels
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

        // Récupère le total avant la pagination
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

    /**
     * Vérifie si le montant respecte les seuils autorisés
     */
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

        if (!$tarif && $idTypeOperation !== 1) { // 1 = DEPOT
            throw new Exception(
                "Montant hors limite autorisée."
            );
        }

        return true;
    }
}
