<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table = 't_operateur';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'libelle'
    ];
}