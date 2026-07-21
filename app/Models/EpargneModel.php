<?php

namespace App\Models;

use CodeIgniter\Model;

class EpargneModel extends Model
{
    protected $table      = 't_epargne';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_client',
        'frais'
    ];
    public function epargner($id_client, $frais)
    {
        return $this->insert([
            'id_client' => $id_client,
            'frais' => $frais
        ]);
    }
    public function getEpaarge($id_client)
    {
        return $this->where('id_client', $id_client)->first()['frais'];
    }
}
