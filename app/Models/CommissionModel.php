<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionModel extends Model
{
    protected $table            = 't_commission';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = ['id_operateur_envoi', 'id_operateur_receveur', 'pourcentage', 'valable'];

    protected $validationRules = [
        'id_operateur_envoi'    => 'required|integer',
        'id_operateur_receveur' => 'required|integer',
        'pourcentage'           => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
        'valable'               => 'permit_empty|integer|in_list[0,1]',
    ];

    protected $validationMessages = [
        'pourcentage' => [
            'greater_than' => 'Le pourcentage doit être supérieur à 0.',
            'less_than_equal_to' => 'Le pourcentage ne peut pas dépasser 100%.',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

   
    public function getCommissions()
    {
        $builder = $this->db->table('t_commission c');
        $builder->select('c.*, 
            e.libelle as operateur_envoi, 
            r.libelle as operateur_receveur');
        $builder->join('t_operateur e', 'e.id = c.id_operateur_envoi', 'left');
        $builder->join('t_operateur r', 'r.id = c.id_operateur_receveur', 'left');
        $builder->orderBy('e.libelle', 'ASC');
        return $builder->get()->getResultArray();
    }

    

public function getCommission(int $id_operateur_envoi, int $id_operateur_receveur)
{
    return $this->where('id_operateur_envoi', $id_operateur_envoi)
                ->where('id_operateur_receveur', $id_operateur_receveur)
                ->where('valable', 1)
                ->first(); // retourne null si non trouvée
}


       public function isUnique($envoi, $receveur, $excludeId = null)
    {
        $builder = $this->where('id_operateur_envoi', $envoi)
                        ->where('id_operateur_receveur', $receveur);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() == 0;
    }

    
    public function areDifferent($envoi, $receveur)
    {
        return $envoi != $receveur;
    }

    
    public function createCommission($data)
    {
        $this->db->transStart();

        $data['valable'] = 1;
        $this->insert($data);
        $id = $this->getInsertID();

        $this->db->table('t_historique_commission')->insert([
            'id_commission' => $id,
            'pourcentage'   => $data['pourcentage'],
            'date_modif'    => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();
        return $this->db->transStatus();
    }

   
    public function updateCommission($id, $data)
    {
        $old = $this->find($id);
        if (!$old) {
            return false;
        }

        $this->db->transStart();

        if ($old['pourcentage'] != $data['pourcentage']) {
            $this->db->table('t_historique_commission')->insert([
                'id_commission' => $id,
                'pourcentage'   => $old['pourcentage'],
                'date_modif'    => date('Y-m-d H:i:s'),
            ]);
        }

        $this->update($id, $data);

        $this->db->transComplete();
        return $this->db->transStatus();
    }

  
    public function deleteCommission($id)
    {
        return $this->delete($id);
    }
}
