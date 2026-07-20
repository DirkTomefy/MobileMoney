# 📘 FEUILLE 1 : Utilitaires + Exemples de Base

> Tout ce qu'il faut pour démarrer un projet CodeIgniter 4

---

## 🔧 PARTIE 1 : UTILITAIRES ESSENTIELS

### URLs

```php
// base_url() - URL de base
<a href="<?= base_url('/users') ?>">Liste</a>
<img src="<?= base_url('images/logo.png') ?>">

// site_url() - URLs relatives
<form action="<?= site_url('users/store') ?>" method="post">

// current_url() - URL actuelle
$current = current_url();          // Avec query string
$current = current_url(false);     // Sans query string

// url_title() - Créer un slug
$slug = url_title("Mon Article", '-', true);
// "mon-article"

// anchor() - Créer des liens
echo anchor('users/5', 'Voir');
// <a href="http://localhost:8080/users/5">Voir</a>

// urlencode() / urldecode()
$encoded = urlencode("Jean Dupont");  // "Jean+Dupont"
$decoded = urldecode("Jean+Dupont");  // "Jean Dupont"
```

### Sécurité (TRÈS IMPORTANT !)

```php
// esc() - Échapper HTML (OBLIGATOIRE partout)
// ❌ Jamais : <?= $user['name'] ?>
// ✅ Toujours : <?= esc($user['name']) ?>

// htmlspecialchars() - Pareil que esc()
$safe = htmlspecialchars("<b>Gras</b>");
// "&lt;b&gt;Gras&lt;/b&gt;"

// password_hash() - Hasher mot de passe
$hashed = password_hash("mon_mot_de_passe", PASSWORD_BCRYPT);

// password_verify() - Vérifier mot de passe
if (password_verify("mon_mot_de_passe", $hashed)) {
    echo "Correct";
}

// md5() / sha1() - Hasher (seulement pour non-sensible)
$unique = md5(time() . rand());

// hash() - Hasher avancé
$hash = hash('sha256', 'ma donnée');
$hash = hash_hmac('sha256', 'ma donnée', 'secret_key');
```

### Strings

```php
// Cas
strtoupper("jean");              // "JEAN"
strtolower("JEAN");              // "jean"
ucfirst("jean dupont");          // "Jean dupont"
ucwords("jean dupont");          // "Jean Dupont"

// Espaces
trim("  Jean  ");                // "Jean"
ltrim("  Jean  ");               // "Jean  "
rtrim("  Jean  ");               // "  Jean"

// Extraire partie
substr("Jean Dupont", 0, 4);     // "Jean"
substr("Jean Dupont", 5);        // "Dupont"
substr("Jean Dupont", -6);       // "Dupont"

// Remplacer
str_replace("Jean", "Marie", "Bonjour Jean");
// "Bonjour Marie"

// Vérifier si contient
if (str_contains("jean@gmail.com", "@")) {
    echo "Email valide";
}

// Longueur
strlen("password123");           // 11

// Diviser / Joindre
explode(",", "Jean,Marie,Pierre");           // ['Jean', 'Marie', 'Pierre']
implode(", ", ['Jean', 'Marie', 'Pierre']);  // "Jean, Marie, Pierre"
```

### Fichiers

```php
// Vérifier existence
if (file_exists('public/logo.png')) {
    echo "Fichier existe";
}

// Types
is_file('logo.png');     // true = fichier
is_dir('images');        // true = dossier
is_readable('file.txt'); // true = lisible
is_writable('file.txt'); // true = modifiable

// Lire
$content = file_get_contents('data.txt');
$data = json_decode(file_get_contents('data.json'), true);

// Écrire
file_put_contents('hello.txt', 'Bonjour');

// Ajouter à la fin
file_put_contents('log.txt', 'Nouvelle ligne\n', FILE_APPEND);

// Supprimer
if (file_exists('old.txt')) {
    unlink('old.txt');
}

// Copier
copy('source.txt', 'destination.txt');

// Infos fichier
$info = pathinfo('document.pdf');
$info['extension'];  // "pdf"
$info['filename'];   // "document"
```

### Dates & Heures

```php
// date() - Formater
date('Y-m-d');           // "2026-07-17"
date('d/m/Y');           // "17/07/2026"
date('Y-m-d H:i:s');     // "2026-07-17 14:30:45"

// time() - Timestamp
$now = time();  // 1721240000

// strtotime() - Texte → timestamp
strtotime('2026-07-17');
strtotime('tomorrow');
strtotime('+1 day');

// DateTime - Plus puissant
$date = new DateTime('2026-07-17');
$date->format('Y-m-d');
$date->add(new DateInterval('P1D'));  // +1 jour
```

### Nombres

```php
// Formater
number_format(1234.5678, 2);           // "1,234.57"
number_format(1234.5678, 2, ',', ' '); // "1 234,57"

// Arrondir
round(3.7);     // 4
ceil(3.7);      // 4
floor(3.7);     // 3
abs(-10);       // 10
```

### Arrays

```php
// Fusionner
array_merge(['a' => 1], ['b' => 2]);  // ['a' => 1, 'b' => 2]

// Clés / Valeurs
array_keys(['name' => 'Jean', 'age' => 30]);      // ['name', 'age']
array_values(['name' => 'Jean', 'age' => 30]);    // ['Jean', 30]

// Chercher
in_array('admin', ['admin', 'user']);  // true
array_search('user', ['admin', 'user']); // 1

// Compter
count(['Jean', 'Marie', 'Pierre']);  // 3

// Filtrer
array_filter([1,2,3,4,5], fn($n) => $n % 2 === 0);  // [2,4]

// Transformer
array_map(fn($n) => $n * 2, [1,2,3]);  // [2,4,6]
```

### JSON

```php
// Convertir en JSON
$json = json_encode(['name' => 'Jean', 'age' => 30]);
// {"name":"Jean","age":30}

// Convertir depuis JSON
$data = json_decode($json, true);  // Array
$obj = json_decode($json);         // Object
```

### Sessions

```php
// Stocker
session()->set('user', ['id' => 1, 'name' => 'Jean']);
session()->set('message', 'Bienvenue');

// Récupérer
$user = session()->get('user');
$role = session()->get('role', 'guest');  // Avec défaut

// Vérifier
if (session()->has('user')) {
    echo "Connecté";
}

// Supprimer
session()->remove('message');

// Détruire
session()->destroy();  // Déconnexion
```

### Request & Response

```php
// Récupérer POST
$all = $this->request->getPost();
$name = $this->request->getPost('name');
$role = $this->request->getPost('role', 'user');  // Avec défaut

// Récupérer GET
$search = $this->request->getGet('search');

// Récupérer fichier
$file = $this->request->getFile('avatar');

// Redirection
return redirect()->to('/users');
return redirect()->to('/users')->with('success', 'Créé !');
return redirect()->back();

// Afficher une vue
return view('users/index');
return view('users/index', ['users' => $users, 'title' => 'Liste']);
```

### Logging

```php
$logger = \Config\Services::logger();

$logger->debug('Debug message');
$logger->info('Info message');
$logger->warning('Warning');
$logger->error('Error');
$logger->critical('Critical');

// Voir les logs : writable/logs/log-YYYY-MM-DD.log
```

---

## 🛠️ PARTIE 2 : EXEMPLES DE BASE

### 1. Connecter à la Base de Données

**Fichier : `.env`**

```env
database.default.hostname = localhost
database.default.database = ma_base
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.port = 3306
```

**Vérifier la connexion :**

```php
<?php
$db = \Config\Database::connect();
if ($db->connect()) {
    echo "Connecté !";
}
?>
```

### 2. Créer une Table MySQL

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 3. Créer un Model

**Fichier : `app/Models/UserModel.php`**

```php
<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['name', 'email', 'password', 'role'];
    protected $useTimestamps = true;

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]',
        'role' => 'in_list[admin,user]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Le nom est obligatoire',
            'min_length' => 'Minimum 3 caractères',
        ],
        'email' => [
            'required' => 'Email obligatoire',
            'valid_email' => 'Email invalide',
            'is_unique' => 'Cet email existe déjà',
        ],
    ];

    // Méthodes personnalisées
    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function search($keyword)
    {
        return $this->like('name', $keyword)
                    ->orLike('email', $keyword)
                    ->findAll();
    }

    public function getByRole($role)
    {
        return $this->where('role', $role)->findAll();
    }
}
?>
```

### 4. Créer les Routes

**Fichier : `app/Config/Routes.php`**

```php
<?php
namespace Config;
use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

// Accueil
$routes->get('/', 'Home::index');

// CRUD pour users
$routes->get('/users', 'UserController::index');                  // Liste
$routes->get('/users/create', 'UserController::create');          // Formulaire
$routes->post('/users', 'UserController::store');                 // Créer
$routes->get('/users/(:num)', 'UserController::show/$1');         // Détail
$routes->get('/users/(:num)/edit', 'UserController::edit/$1');    // Formulaire edit
$routes->post('/users/(:num)', 'UserController::update/$1');      // Mettre à jour
$routes->post('/users/(:num)/delete', 'UserController::delete/$1'); // Supprimer

// Autres...
$routes->setAutoRoute(true);
?>
```

### 5. Créer un Controller CRUD

**Fichier : `app/Controllers/UserController.php`**

```php
<?php
namespace App\Controllers;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    // Liste
    public function index()
    {
        $search = $this->request->getGet('search') ?? '';

        if ($search) {
            $users = $this->model->search($search);
        } else {
            $users = $this->model->paginate(10);
        }

        return view('users/index', [
            'users' => $users,
            'pager' => $this->model->pager,
            'search' => $search,
        ]);
    }

    // Détail
    public function show($id)
    {
        $user = $this->model->find($id);
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('users/show', ['user' => $user]);
    }

    // Formulaire création
    public function create()
    {
        return view('users/form');
    }

    // Enregistrer
    public function store()
    {
        $data = $this->request->getPost();
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        if (!$this->model->insert($data)) {
            return view('users/form', [
                'validation' => $this->model->errors(),
                'data' => $this->request->getPost(),
            ]);
        }

        return redirect()->to('/users')->with('success', 'Utilisateur créé');
    }

    // Formulaire modification
    public function edit($id)
    {
        $user = $this->model->find($id);
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('users/form', ['user' => $user]);
    }

    // Mettre à jour
    public function update($id)
    {
        $user = $this->model->find($id);
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->request->getPost();
        
        // Ne pas mettre à jour si mot de passe vide
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (!$this->model->update($id, $data)) {
            return view('users/form', [
                'validation' => $this->model->errors(),
                'user' => $user,
            ]);
        }

        return redirect()->to('/users')->with('success', 'Utilisateur modifié');
    }

    // Supprimer
    public function delete($id)
    {
        $user = $this->model->find($id);
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $this->model->delete($id);
        return redirect()->to('/users')->with('success', 'Utilisateur supprimé');
    }
}
?>
```

### 6. Validation dans les Vues

**Fichier : `app/Views/users/form.php`**

```php
<form method="post" action="<?= base_url('/users') ?>">
    <?= csrf_field() ?>

    <!-- Nom -->
    <div>
        <label for="name">Nom</label>
        <input type="text" id="name" name="name" 
               value="<?= old('name', $user['name'] ?? '') ?>">
        <?php if (isset($validation['name'])): ?>
            <span class="error"><?= $validation['name'] ?></span>
        <?php endif; ?>
    </div>

    <!-- Email -->
    <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" 
               value="<?= old('email', $user['email'] ?? '') ?>">
        <?php if (isset($validation['email'])): ?>
            <span class="error"><?= $validation['email'] ?></span>
        <?php endif; ?>
    </div>

    <!-- Mot de passe -->
    <div>
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password">
        <?php if (isset($validation['password'])): ?>
            <span class="error"><?= $validation['password'] ?></span>
        <?php endif; ?>
    </div>

    <button type="submit">Enregistrer</button>
</form>
```

### 7. Query Builder (Pas de SQL direct)

```php
<?php
$db = \Config\Database::connect();

// SELECT avec WHERE
$users = $db->table('users')
    ->select('name, email')
    ->where('role', 'admin')
    ->get()
    ->getResultArray();

// WHERE avec opérateurs
$users = $db->table('users')
    ->where('age >', 18)
    ->where('city', 'Paris')
    ->get()
    ->getResultArray();

// LIKE
$users = $db->table('users')
    ->like('name', 'jean')
    ->get()
    ->getResultArray();

// ORDER BY
$users = $db->table('users')
    ->orderBy('name', 'ASC')
    ->get()
    ->getResultArray();

// LIMIT
$users = $db->table('users')
    ->limit(10)
    ->offset(20)
    ->get()
    ->getResultArray();

// COUNT
$count = $db->table('users')->countAllResults();

// INSERT
$db->table('users')->insert([
    'name' => 'Jean',
    'email' => 'jean@test.com'
]);

// UPDATE
$db->table('users')
    ->where('id', 1)
    ->update(['name' => 'John']);

// DELETE
$db->table('users')->where('id', 1)->delete();

// JOIN
$db->table('users')
    ->select('users.name, posts.title')
    ->join('posts', 'posts.user_id = users.id')
    ->get()
    ->getResultArray();
?>
```

---

## ✅ Checklist : Fichiers à Créer

- [ ] `.env` (avec BD)
- [ ] `app/Models/UserModel.php`
- [ ] `app/Controllers/UserController.php`
- [ ] `app/Controllers/BaseController.php` (personnalisé)
- [ ] `app/Config/Routes.php`
- [ ] `app/Views/layouts/main.php`
- [ ] `app/Views/users/index.php`
- [ ] `app/Views/users/form.php`
- [ ] `app/Views/users/show.php`

---

**Feuille 1 complète ! Voir FEUILLE 2 pour Auth, Import, Upload, etc.**
