# 📗 FEUILLE 2 : Exemples Avancés & Fonctionnalités

> Authentification, Import, Upload, Logs et plus

---

## 🔐 PARTIE 1 : AUTHENTIFICATION COMPLÈTE

### 1. Créer le Filtre d'Authentification

**Fichier : `app/Filters/AuthFilter.php`**

```php
<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->get('user')) {
            return redirect()->to('/login')->with('error', 'Connectez-vous');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
?>
```

### 2. Créer le Filtre de Rôle

**Fichier : `app/Filters/RoleFilter.php`**

```php
<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $user = $session->get('user');

        if (!$user || !in_array($user['role'], $arguments ?? [])) {
            return redirect()->to('/')->with('error', 'Accès refusé');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
?>
```

### 3. Enregistrer les Filtres

**Fichier : `app/Config/Filters.php`**

```php
<?php
namespace Config;
use CodeIgniter\Config\BaseConfig;
use App\Filters\AuthFilter;
use App\Filters\RoleFilter;

class Filters extends BaseConfig
{
    public $aliases = [
        'csrf' => \CodeIgniter\Filters\CSRF::class,
        'auth' => AuthFilter::class,
        'role' => RoleFilter::class,
    ];

    public $globals = [
        'before' => [
            'csrf',
        ],
    ];
}
?>
```

### 4. Contrôleur d'Authentification

**Fichier : `app/Controllers/AuthController.php`**

```php
<?php
namespace App\Controllers;
use App\Models\UserModel;

class AuthController extends BaseController
{
    // Afficher formulaire login
    public function loginForm()
    {
        if (session()->get('user')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    // Traiter login
    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $model = new UserModel();
        $user = $model->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return view('auth/login', [
                'error' => 'Email ou mot de passe incorrect',
                'email' => $email,
            ]);
        }

        // Stocker en session
        session()->set('user', [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        return redirect()->to('/dashboard');
    }

    // Afficher formulaire register
    public function registerForm()
    {
        if (session()->get('user')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/register');
    }

    // Traiter register
    public function register()
    {
        $model = new UserModel();
        $data = $this->request->getPost();
        
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['role'] = 'user';

        if (!$model->insert($data)) {
            return view('auth/register', [
                'validation' => $model->errors(),
                'data' => $this->request->getPost(),
            ]);
        }

        return redirect()->to('/login')->with('success', 'Inscription réussie');
    }

    // Déconnexion
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
?>
```

### 5. Vue Login

**Fichier : `app/Views/auth/login.php`**

```php
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-4 offset-md-4">
            <h1>Connexion</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('/login') ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?= old('email', 'test@test.com') ?>" required>
                </div>

                <div class="mb-3">
                    <label>Mot de passe</label>
                    <input type="password" name="password" class="form-control" 
                           value="password123" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Connexion</button>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>
```

### 6. Routes Auth

**Ajouter à `app/Config/Routes.php`**

```php
// Routes publiques
$routes->get('/login', 'AuthController::loginForm');
$routes->post('/login', 'AuthController::login');
$routes->get('/register', 'AuthController::registerForm');
$routes->post('/register', 'AuthController::register');

// Routes protégées (authentification obligatoire)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/dashboard', 'DashboardController::index');
    $routes->get('/logout', 'AuthController::logout');
});

// Routes admin uniquement
$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
    $routes->get('users', 'Admin\UserController::index');
    $routes->post('users/delete/(:num)', 'Admin\UserController::delete/$1');
});

// Routes admin ET librarian
$routes->group('gestion', ['filter' => 'role:admin,librarian'], function($routes) {
    $routes->get('reports', 'Gestion\ReportController::index');
});
```

### 7. BaseController Personnalisé

**Fichier : `app/Controllers/BaseController.php`**

```php
<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class BaseController extends Controller
{
    protected $helpers = ['url', 'form'];
    protected $session;
    protected $currentUser;

    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->session = \Config\Services::session();
        $this->currentUser = $this->session->get('user');
    }

    // Vérifier si connecté
    protected function isLoggedIn()
    {
        return $this->currentUser !== null;
    }

    // Exiger connexion
    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login');
        }
    }

    // Rendu avec utilisateur courant
    protected function render($view, $data = [])
    {
        $data['currentUser'] = $this->currentUser;
        return view($view, $data);
    }
}
?>
```

### 8. Layout Principal

**Fichier : `app/Views/layouts/main.php`**

```php
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mon App' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">Mon App</a>
            <div class="ms-auto">
                <?php if ($currentUser): ?>
                    <span class="text-white me-3">Bienvenue <?= esc($currentUser['name']) ?></span>
                    <a href="<?= base_url('/logout') ?>" class="btn btn-sm btn-danger">Déconnexion</a>
                <?php else: ?>
                    <a href="<?= base_url('/login') ?>" class="btn btn-sm btn-primary">Connexion</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Contenu -->
    <div class="container mt-4">
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## 📤 PARTIE 2 : IMPORT CSV COMPLET

### 1. Vue Formulaire d'Import

**Fichier : `app/Views/import/form.php`**

```php
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <h1>Importer des utilisateurs</h1>

    <form method="post" action="<?= base_url('/import/upload') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="csv_file">Fichier CSV</label>
            <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
            <small class="text-muted">Format : nom;email;password;role (séparé par ;)</small>
        </div>

        <button type="submit" class="btn btn-primary">Importer</button>
    </form>

    <!-- Résultats -->
    <?php if (isset($results)): ?>
        <hr>
        <h3>Résultats de l'import</h3>
        <p><strong>✅ <?= $results['success'] ?></strong> utilisateurs importés</p>
        
        <?php if (!empty($results['errors'])): ?>
            <h4>Erreurs :</h4>
            <ul>
                <?php foreach ($results['errors'] as $error): ?>
                    <li>Ligne <?= $error['ligne'] ?>: <?= $error['message'] ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
<?= $this->endSection() ?>
```

### 2. Ajouter Méthode d'Import au Model

**Ajouter à `app/Models/UserModel.php`**

```php
public function importCSV($file)
{
    // Ouvrir le fichier
    $handle = fopen($file->getTempName(), 'r');
    
    // Lire l'en-tête
    $headers = fgetcsv($handle, 0, ';');
    
    $success = 0;
    $errors = [];
    $lineNumber = 2;

    // Lire chaque ligne
    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        
        // Créer un tableau associatif
        $data = array_combine($headers, $row);

        // Valider email
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = ['ligne' => $lineNumber, 'message' => 'Email invalide'];
            $lineNumber++;
            continue;
        }

        // Vérifier si email existe
        if ($this->findByEmail($data['email'])) {
            $errors[] = ['ligne' => $lineNumber, 'message' => 'Email déjà existant'];
            $lineNumber++;
            continue;
        }

        // Hasher le mot de passe
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        
        if (empty($data['role'])) {
            $data['role'] = 'user';
        }

        // Insérer
        if ($this->insert($data)) {
            $success++;
        } else {
            $errors[] = ['ligne' => $lineNumber, 'message' => 'Erreur insertion'];
        }

        $lineNumber++;
    }

    fclose($handle);

    return [
        'success' => $success,
        'errors' => $errors,
    ];
}
```

### 3. Contrôleur d'Import

**Fichier : `app/Controllers/ImportController.php`**

```php
<?php
namespace App\Controllers;
use App\Models\UserModel;

class ImportController extends BaseController
{
    // Afficher le formulaire
    public function form()
    {
        return $this->render('import/form');
    }

    // Traiter l'import
    public function upload()
    {
        $file = $this->request->getFile('csv_file');

        // Vérifier fichier
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Fichier invalide');
        }

        // Vérifier extension
        if ($file->getExtension() !== 'csv') {
            return redirect()->back()->with('error', 'Le fichier doit être un CSV');
        }

        // Importer
        $model = new UserModel();
        $results = $model->importCSV($file);

        return $this->render('import/form', ['results' => $results]);
    }
}
?>
```

### 4. Routes Import

**Ajouter à `app/Config/Routes.php`**

```php
// Routes d'import (protégées)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/import', 'ImportController::form');
    $routes->post('/import/upload', 'ImportController::upload');
});
```

### 5. Format Fichier CSV

```
nom;email;password;role
Jean Dupont;jean@test.com;password123;user
Marie Martin;marie@test.com;password123;admin
Pierre Dupuis;pierre@test.com;password123;user
```

---

## 📤 PARTIE 3 : UPLOAD FICHIER SÉCURISÉ

### 1. Vue Upload

**Fichier : `app/Views/profile/upload.php`**

```php
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <h1>Mettre à jour mon avatar</h1>

    <form method="post" action="<?= base_url('/profile/upload-avatar') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="avatar">Avatar (Max 2 Mo)</label>
            <input type="file" id="avatar" name="avatar" accept="image/*" required>
            <small class="text-muted">Formats acceptés : JPG, PNG, WEBP</small>
        </div>

        <button type="submit" class="btn btn-primary">Télécharger</button>
    </form>
<?= $this->endSection() ?>
```

### 2. Contrôleur Upload

**Fichier : `app/Controllers/ProfileController.php`**

```php
<?php
namespace App\Controllers;
use App\Models\UserModel;

class ProfileController extends BaseController
{
    // Afficher formulaire upload
    public function uploadForm()
    {
        return $this->render('profile/upload');
    }

    // Traiter upload
    public function uploadAvatar()
    {
        $file = $this->request->getFile('avatar');

        // Vérifier validité
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Fichier invalide');
        }

        // Vérifier le type MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return redirect()->back()->with('error', 'Type d\'image non autorisé');
        }

        // Vérifier la taille (2 Mo max)
        $maxSize = 2 * 1024 * 1024;  // 2 Mo
        if ($file->getSize() > $maxSize) {
            return redirect()->back()->with('error', 'Fichier trop volumineux (max 2 Mo)');
        }

        // Générer un nom unique
        $newName = $file->getRandomName();

        // Déplacer le fichier
        $file->move(WRITEPATH . 'uploads', $newName);

        // Enregistrer en BD
        $userId = session('user')['id'];
        $model = new UserModel();
        $model->update($userId, ['avatar' => $newName]);

        // Mettre à jour la session
        $user = session('user');
        $user['avatar'] = $newName;
        session()->set('user', $user);

        return redirect()->back()->with('success', 'Avatar mis à jour');
    }
}
?>
```

### 3. Routes Upload

**Ajouter à `app/Config/Routes.php`**

```php
// Routes profil (protégées)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/profile/upload', 'ProfileController::uploadForm');
    $routes->post('/profile/upload-avatar', 'ProfileController::uploadAvatar');
});
```

---

## 📊 PARTIE 4 : PAGINATION

### 1. Dans le Contrôleur

```php
<?php
$model = new UserModel();

// Paginer 10 par page
$users = $model->paginate(10);

return $this->render('users/index', [
    'users' => $users,
    'pager' => $model->pager,
]);
?>
```

### 2. Dans la Vue

```php
<?php
// Afficher les résultats
<?php foreach ($users as $user): ?>
    <p><?= esc($user['name']) ?></p>
<?php endforeach; ?>

// Afficher les liens de pagination
<?= $pager->links() ?>
?>
```

---

## 📝 PARTIE 5 : LOGGING (Déboguer)

### 1. Enregistrer des logs

```php
<?php
$logger = \Config\Services::logger();

// Différents niveaux
$logger->debug('Message de debug');
$logger->info('Information');
$logger->warning('Attention');
$logger->error('Une erreur');
$logger->critical('Critique');

// Exemple : logger une erreur d'import
$logger->error('Erreur import CSV', [
    'file' => $file->getName(),
    'errors' => $errors,
]);
?>
```

### 2. Voir les logs

```
writable/logs/log-2026-07-17.log
```

### 3. Exemple avec JSON

```php
<?php
$logger->error('Import failed', ['data' => json_encode($errors)]);
?>
```

---

## 🔒 PARTIE 6 : CSRF TOKEN

### Activation

**Fichier : `app/Config/Filters.php`**

```php
public $globals = [
    'before' => [
        'csrf',  // ← CSRF activé globalement
    ],
];
```

### Utilisation dans les formulaires

```php
<form method="post" action="<?= base_url('/users') ?>">
    <?= csrf_field() ?>  <!-- ← TOKEN CSRF (OBLIGATOIRE) -->
    
    <input type="text" name="name" required>
    <button type="submit">Envoyer</button>
</form>
```

### Vérification automatique

CodeIgniter vérifie automatiquement le token CSRF dans tous les POST. ✅

---

## 📋 Vue Liste Complète

**Fichier : `app/Views/users/index.php`**

```php
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Liste des utilisateurs</h1>
        <a href="<?= base_url('/users/create') ?>" class="btn btn-primary">+ Ajouter</a>
    </div>

    <!-- Recherche -->
    <form method="get" class="mb-4">
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" 
                       placeholder="Chercher..." value="<?= esc($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Chercher</button>
            </div>
        </div>
    </form>

    <!-- Tableau -->
    <table class="table table-hover">
        <thead class="table-dark">
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= esc($user['name']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td><?= esc($user['role']) ?></td>
                    <td>
                        <a href="<?= base_url('/users/' . $user['id']) ?>" class="btn btn-sm btn-info">Voir</a>
                        <a href="<?= base_url('/users/' . $user['id'] . '/edit') ?>" class="btn btn-sm btn-warning">Éditer</a>
                        <form method="post" action="<?= base_url('/users/' . $user['id'] . '/delete') ?>" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Êtes-vous sûr ?')">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?= $pager->links() ?>
<?= $this->endSection() ?>
```

---

## ✅ Checklist : Fichiers à Créer

- [ ] `app/Filters/AuthFilter.php`
- [ ] `app/Filters/RoleFilter.php`
- [ ] `app/Config/Filters.php` (personnalisé)
- [ ] `app/Controllers/AuthController.php`
- [ ] `app/Controllers/ImportController.php`
- [ ] `app/Controllers/ProfileController.php`
- [ ] `app/Views/auth/login.php`
- [ ] `app/Views/auth/register.php`
- [ ] `app/Views/import/form.php`
- [ ] `app/Views/profile/upload.php`
- [ ] `app/Views/users/index.php` (complète)
- [ ] `app/Views/layouts/main.php` (avec Bootstrap)

---

## 🎯 Résumé des Routes

```php
// Publiques
GET    /login              → Formulaire login
POST   /login              → Traiter login
GET    /register           → Formulaire register
POST   /register           → Traiter register

// Protégées (auth)
GET    /dashboard          → Accueil
GET    /logout             → Déconnexion
GET    /import             → Formulaire import
POST   /import/upload      → Traiter import
GET    /profile/upload     → Formulaire upload avatar
POST   /profile/upload-avatar → Traiter upload

// Admin
GET    /admin/users        → Liste users (admin)
POST   /admin/users/delete/:id → Supprimer (admin)
```

---

**FEUILLE 2 complète ! Avec Feuille 1 : vous avez TOUT ! 🚀**
