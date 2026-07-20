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
                // doit contenir exactement 7 chiffres
                if (preg_match('/^[0-9]{7}$/', $reste)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getOperateurByNumero(string $numero)
    {
        return $this->select('t_operateur.*')
            ->join(
                't_operateur',
                't_operateur.id = t_prefix.id_operateur'
            )
            ->like('t_prefix.libelle', $numero, 'after')
            ->first();
    }
}
