# Mini-blog CodeIgniter 4 + SQLite — Projet à auditer

Ce mini-projet (gestion d'utilisateurs et d'articles) a été **généré par une IA**
à partir d'une consigne courte. Il **fonctionne** : on peut s'inscrire, se
connecter, créer et supprimer des articles, et accéder à un espace admin.

Votre mission n'est **pas** de le développer, mais de le **relire en professionnel** :
trouver ce qui ne va pas, et le corriger. Voir `GRILLE-AUDIT-etudiant.md`.

---

## Pré-requis

- PHP 8.1+
- Composer
- L'extension `php-sqlite3` activée

## Installation

Ces fichiers sont les fichiers **applicatifs** : ils se déposent dans une
installation neuve de CodeIgniter 4.

```bash
# 1. Intégrer ce fichier dans 1 repertoire CI4
cd blog-audit

# 2. Copier le contenu du dossier app/ fourni ICI par-dessus le app/ du projet
#    (Config/Database.php, Config/Routes.php, Config/Filters.php,
#     Controllers/, Models/, Database/, Views/)

# 3. Créer le dossier de la base SQLite
mkdir -p writable/db

# 4. Créer les tables et les données de démonstration
php spark migrate
php spark db:seed DemoSeeder

# 5. Lancer le serveur
php spark serve
```

Ouvrir ensuite http://localhost:8080

## Comptes de démonstration

| Email           | Mot de passe | Rôle   |
|-----------------|--------------|--------|
| rojo@itu.mg     | secret123    | admin  |
| sitraka@itu.mg  | azerty       | membre |

## Règle du jeu

1. Lancez l'application, naviguez, constatez : **ça marche**.
2. Ouvrez le code et auditez-le (grille fournie).
3. Pour chaque problème : où ? pourquoi c'est un problème ? comment le démontrer ?
   comment le corriger ?

> Indice de méthode : si vous deviez mettre cette application **en production
> demain**, qu'est-ce qui vous empêcherait de dormir ?
