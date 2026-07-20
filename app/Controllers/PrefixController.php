<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PrefixModel;
use App\Models\OperateurModel;

class PrefixController extends BaseController
{
    protected $prefixModel;
    protected $operateurModel;

    public function __construct()
    {
        $this->prefixModel = new PrefixModel();
        $this->operateurModel = new OperateurModel();
    }

    public function index()
    {
        $data = [
            'prefixes'   => $this->prefixModel->getAllPrefixes(),
            'operateurs' => $this->operateurModel->getAllOperateurs(),
        ];
        return view('backoffice/prefix/index', $data);
    }

    public function create()
    {
        $data['operateurs'] = $this->operateurModel->getAllOperateurs();
        return view('backoffice/prefix/form', $data);
    }

    public function store()
    {
        $libelle = trim($this->request->getPost('libelle'));
        $operateurId = $this->request->getPost('id_operateur');

        if (!$libelle || !$operateurId) {
            return redirect()->back()->withInput()->with('error', 'Tous les champs sont obligatoires.');
        }

        if ($this->prefixModel->createPrefix($operateurId, $libelle)) {
            return redirect()->to('/backoffice/prefix')->with('success', 'Préfixe ajouté.');
        }
        return redirect()->back()->withInput()->with('error', 'Erreur lors de l\'ajout.');
    }

    public function edit($id)
    {
        $prefix = $this->prefixModel->getPrefix($id);
        if (!$prefix) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Préfixe introuvable.');
        }
        $data = [
            'prefix'     => $prefix,
            'operateurs' => $this->operateurModel->getAllOperateurs(),
        ];
        return view('backoffice/prefix/form', $data);
    }

    public function update($id)
    {
        $libelle = trim($this->request->getPost('libelle'));
        $operateurId = $this->request->getPost('id_operateur');

        if (!$libelle || !$operateurId) {
            return redirect()->back()->withInput()->with('error', 'Tous les champs sont obligatoires.');
        }

        if ($this->prefixModel->updatePrefix($id, $libelle, $operateurId)) {
            return redirect()->to('/backoffice/prefix')->with('success', 'Préfixe mis à jour.');
        }
        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour.');
    }

    public function delete($id)
    {
        if ($this->prefixModel->deletePrefix($id)) {
            return redirect()->to('/backoffice/prefix')->with('success', 'Préfixe supprimé.');
        }
        return redirect()->back()->with('error', 'Erreur lors de la suppression.');
    }
}