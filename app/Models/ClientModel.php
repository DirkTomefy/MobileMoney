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
}