<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixModel extends Model
{
    protected $table = 't_prefix';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_operateur',
        'libelle'
    ];


    public function isValid(string $numero): bool
    {
        $prefixes = $this->findAll();
        foreach ($prefixes as $prefix) {
            $libelle = $prefix['libelle'];
            if (str_starts_with($numero, $libelle)) {
                // récupérer la partie après le préfixe
                $reste = substr($numero, strlen($libelle));
                // doit contenir exactement 7 chiffre
                if (preg_match('/^[0-9]{7}$/', $reste)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getOperateurByNumero(string $numero)
    {

        $prefixes = $this->findAll();

        foreach ($prefixes as $prefix) {

            $libelle = trim($prefix['libelle']);

            if (str_starts_with($numero, $libelle)) {

                return $this->db->table('t_operateur')
                    ->where('id', $prefix['id_operateur'])
                    ->get()
                    ->getRowArray();
            }
        }

        return null;
    }

    public function getAllPrefixes()
    {
        return $this->orderBy('libelle', 'ASC')->findAll();
    }

    public function getPrefix($id)
    {
        return $this->find($id);
    }

    public function getPrefixesByOperateur($operateurId)
    {
        return $this->where('id_operateur', $operateurId)
                    ->orderBy('libelle', 'ASC')
                    ->findAll();
    }

    public function createPrefix($operateurId, $libelle)
    {
        return $this->insert([
            'id_operateur' => $operateurId,
            'libelle' => $libelle
        ]);
    }

    public function updatePrefix($id, $libelle, $operateurId = null)
    {
        $data = ['libelle' => $libelle];
        if ($operateurId !== null) {
            $data['id_operateur'] = $operateurId;
        }
        return $this->update($id, $data);
    }

    public function deletePrefix($id)
    {
        return $this->delete($id);
    }

}
