<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CommissionModel;
use App\Models\OperateurModel;

class CommissionController extends BaseController
{
    protected $commissionModel;
    protected $operateurModel;

    public function __construct()
    {
        $this->commissionModel = new CommissionModel();
        $this->operateurModel = new OperateurModel();
    }

    public function index()
    {
        $data['commissions'] = $this->commissionModel->getCommissions();
        return view('backoffice/commission/index', $data);
    }

    public function create()
    {
        $data['operateurs'] = $this->operateurModel->getAllOperateurs();
        return view('backoffice/commission/form', $data);
    }

    public function store()
    {
        $envoi     = $this->request->getPost('id_operateur_envoi');
        $receveur  = $this->request->getPost('id_operateur_receveur');
        $pourcentage = $this->request->getPost('pourcentage');

        if (!$envoi || !$receveur || !$pourcentage) {
            return redirect()->back()->withInput()->with('error', 'Tous les champs sont obligatoires.');
        }

        if ($envoi == $receveur) {
            return redirect()->back()->withInput()->with('error', 'Les deux opérateurs doivent être différents.');
        }

        if (!$this->commissionModel->isUnique($envoi, $receveur)) {
            return redirect()->back()->withInput()->with('error', 'Cette paire d\'opérateurs existe déjà.');
        }

        $data = [
            'id_operateur_envoi'    => $envoi,
            'id_operateur_receveur' => $receveur,
            'pourcentage'           => $pourcentage,
        ];

        if ($this->commissionModel->createCommission($data)) {
            return redirect()->to('/backoffice/commission')->with('success', 'Commission ajoutée.');
        }
        return redirect()->back()->withInput()->with('error', 'Erreur lors de l\'ajout.');
    }

    public function edit($id)
    {
        $commission = $this->commissionModel->getCommission($id);
        if (!$commission) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Commission introuvable.');
        }
        $data = [
            'commission' => $commission,
            'operateurs' => $this->operateurModel->getAllOperateurs(),
        ];
        return view('backoffice/commission/form', $data);
    }

    public function update($id)
    {
        $envoi     = $this->request->getPost('id_operateur_envoi');
        $receveur  = $this->request->getPost('id_operateur_receveur');
        $pourcentage = $this->request->getPost('pourcentage');

        if (!$envoi || !$receveur || !$pourcentage) {
            return redirect()->back()->withInput()->with('error', 'Tous les champs sont obligatoires.');
        }

        if ($envoi == $receveur) {
            return redirect()->back()->withInput()->with('error', 'Les deux opérateurs doivent être différents.');
        }

        if (!$this->commissionModel->isUnique($envoi, $receveur, $id)) {
            return redirect()->back()->withInput()->with('error', 'Cette paire d\'opérateurs existe déjà.');
        }

        $data = [
            'id_operateur_envoi'    => $envoi,
            'id_operateur_receveur' => $receveur,
            'pourcentage'           => $pourcentage,
        ];

        if ($this->commissionModel->updateCommission($id, $data)) {
            return redirect()->to('/backoffice/commission')->with('success', 'Commission mise à jour.');
        }
        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour.');
    }

    public function delete($id)
    {
        if ($this->commissionModel->deleteCommission($id)) {
            return redirect()->to('/backoffice/commission')->with('success', 'Commission supprimée.');
        }
        return redirect()->back()->with('error', 'Erreur lors de la suppression.');
    }
}