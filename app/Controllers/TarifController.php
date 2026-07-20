<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OperateurModel;

class TarifController extends BaseController
{
    protected $operateurModel;
    protected $operateurId;

    public function __construct()
    {
        $this->operateurModel = new OperateurModel();
        // Récupérer l'opérateur depuis la session, avec fallback à 1
        $session = session();
        $this->operateurId = $session->get('operateur_id') ?? 1;
    }

    public function index()
    {
        $types = $this->operateurModel->db->table('t_type_operation')
            ->whereIn('code', ['RETRAIT', 'TRANSFERT'])
            ->get()
            ->getResultArray();

        $data = [
            'types'         => $types,
            'selected_type' => $types[0]['id'] ?? null,
        ];

        return view('backoffice/tarif', $data);
    }

    public function getTarifs()
    {
        $id_type = $this->request->getGet('id_type');

        if (!$id_type) {
            return $this->response->setJSON(['error' => 'Type d\'opération manquant']);
        }

        $tarifs = $this->operateurModel->getAllTarif($id_type, $this->operateurId);
        return $this->response->setJSON($tarifs);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requête AJAX requise']);
        }

        $id_tarif = $this->request->getPost('id_tarif');
        $prix = $this->request->getPost('prix');

        if (!$id_tarif || !is_numeric($prix)) {
            return $this->response->setJSON(['error' => 'Données invalides']);
        }

        $result = $this->operateurModel->updateTarif($id_tarif, (float)$prix);

        if ($result) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['error' => 'Échec de la mise à jour']);
        }
    }
}