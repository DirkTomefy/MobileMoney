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
}
