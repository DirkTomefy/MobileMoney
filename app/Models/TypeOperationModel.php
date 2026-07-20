<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table = 't_type_operation';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'code'
    ];


    /**
     * Vérifie si un code opération existe
     */
    public function existsCode(string $code): bool
    {
        return $this->where('code', strtoupper($code))
                    ->countAllResults() > 0;
    }
}