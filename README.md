# Hackat'Innov - Gestion de hackathons

Application web Symfony de gestion de hackathons, réalisée dans le cadre du BTS SIO option SLAM (projet fil rouge Hackat'Innov).

Elle permet de consulter les hackathons, leurs inscrits et leurs équipes, d'affecter les participants à une équipe via un formulaire, et expose une API JSON.

---

## Sommaire

1. [Fonctionnalités](#fonctionnalités)
2. [Stack technique](#stack-technique)
3. [Installation](#installation)
4. [Lancer l'application](#lancer-lapplication)
5. [Routes](#routes)
6. [Modèle de données](#modèle-de-données)
7. [Architecture du code](#architecture-du-code)
8. [Choix techniques](#choix-techniques)
9. [Jeu d'essai](#jeu-dessai)
10. [Commandes utiles](#commandes-utiles)
11. [Historique des TP](#historique-des-tp)

---

## Fonctionnalités

### Interface web (Twig)

| Page | Description |
|---|---|
| Liste des hackathons | Tableau trié par date de début : thème (cliquable), ville, lieu, dates, nombre de projets |
| Détail d'un hackathon | Informations générales et liste des inscrits avec leur date d'inscription, leur compétence et leur équipe |
| Équipes d'un hackathon | Chaque équipe avec ses membres, son responsable, son taux de remplissage (`x / 5`) et un indicateur « Complète », ainsi que la liste des inscrits sans équipe |
| Affectation | Formulaire pour affecter une inscription à une équipe du même hackathon |

### Règles métier

- Une inscription ne peut être affectée qu'à une équipe **du même hackathon** (une équipe est rattachée à un hackathon par son projet).
- Une équipe compte au maximum **5 membres** (`Equipe::NB_MAX_MEMBRES`). Une équipe complète n'est plus proposée dans le formulaire d'affectation.
- Ces deux règles sont aussi vérifiées côté serveur : un choix hors liste envoyé en modifiant le HTML est rejeté par la validation du formulaire.

### API JSON

Lecture et création de hackathons au format JSON (sérialisation par groupe `hackathon:read`).

---

## Stack technique

| Élément | Version / outil |
|---|---|
| Langage | PHP 8.2 minimum (développé avec PHP 8.4) |
| Framework | Symfony 7.4 |
| ORM | Doctrine ORM 3, Doctrine Migrations, Doctrine Fixtures |
| Base de données | MySQL 8 |
| Vues | Twig |
| Formulaires | Symfony Form + Validator, protection CSRF |
| Front | AssetMapper, Stimulus, Turbo, CSS natif |
| API | Symfony Serializer |

---

## Installation

### Prérequis

- PHP 8.2 ou plus, avec les extensions `pdo_mysql`, `intl`, `ctype`, `iconv`
- Composer
- Un serveur MySQL 8 accessible (local ou conteneur Docker)
- Optionnel : la [Symfony CLI](https://symfony.com/download)

### Étapes

```bash
# 1. Récupérer le projet
git clone https://github.com/Flowz5/hackathon.git
cd hackathon

# 2. Installer les dépendances
composer install
```

3. Créer un fichier `.env.local` à la racine (il n'est pas versionné) avec la connexion à la base :

```dotenv
DATABASE_URL="mysql://UTILISATEUR:MOT_DE_PASSE@127.0.0.1:3306/Hackathon?serverVersion=8.0.32-mysql&charset=utf8mb4"
```

Si MySQL tourne dans un conteneur Docker, l'utilisateur doit être créé avec l'hôte `'%'` (et non `'localhost'`) pour accepter les connexions venant de la machine hôte :

```sql
CREATE USER IF NOT EXISTS 'UTILISATEUR'@'%' IDENTIFIED BY 'MOT_DE_PASSE';
GRANT ALL PRIVILEGES ON Hackathon.* TO 'UTILISATEUR'@'%';
FLUSH PRIVILEGES;
```

```bash
# 4. Créer la base et le schéma
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate -n

# 5. Charger le jeu d'essai
php bin/console doctrine:fixtures:load -n
```

> Le fichier `compose.yaml` est celui généré par défaut par Symfony (PostgreSQL). Il n'est pas utilisé : le projet fonctionne avec MySQL configuré dans `.env.local`.

---

## Lancer l'application

```bash
symfony serve
# ou, sans la Symfony CLI :
php -S 127.0.0.1:8000 -t public
```

Puis ouvrir `http://127.0.0.1:8000/hackathons`.

Les identifiants des hackathons changent à chaque rechargement des fixtures. Pour les connaître :

```bash
php bin/console dbal:run-sql "SELECT id, theme FROM hackathon"
```

---

## Routes

### Interface web

| Nom | Méthode | Chemin | Rôle |
|---|---|---|---|
| `app_hackathon_index_html` | GET | `/hackathons` | Liste des hackathons |
| `app_hackathon_show` | GET | `/hackathons/{id}` | Détail d'un hackathon et de ses inscrits |
| `app_hackathon_equipes` | GET | `/hackathons/{id}/equipes` | Équipes du hackathon et leurs membres |
| `app_hackathon_affectation` | GET, POST | `/hackathons/{id}/affectation` | Formulaire d'affectation d'une inscription à une équipe |

### API JSON

| Nom | Méthode | Chemin | Rôle |
|---|---|---|---|
| `app_hackathon_index_json` | GET | `/hackathons/api` | Liste de tous les hackathons |
| `app_hackathon_show_json` | GET | `/hackathons/api/{id}` | Un hackathon (404 s'il n'existe pas) |
| `app_hackathon_create_json` | POST | `/hackathons/api` | Création d'un hackathon, réponse `201 Created` |

Exemple de création :

```bash
curl -X POST http://127.0.0.1:8000/hackathons/api \
     -H "Content-Type: application/json" \
     -d '{
           "theme": "Numérique responsable",
           "lieu": "Lycée Livet",
           "ville": "Nantes",
           "dateHeureDebut": "2027-04-09T09:00:00",
           "dateHeureFin": "2027-04-10T18:00:00",
           "objectifs": "Concevoir des services numériques sobres."
         }'
```

Toutes les routes `{id}` n'acceptent qu'un entier (`requirements: ['id' => '\d+']`) et renvoient une erreur 404 si le hackathon n'existe pas.

---

## Modèle de données

### Schéma relationnel (extrait)

```
Hackathon   (id, theme, lieu, ville, dateHeureDebut, dateHeureFin, affiche, objectifs)
Projet      (id, description, retenu, #idHackathon)
Equipe      (id, nom, lienPrototype, #idProjet, #idResponsable)
Membre      (id, nom, prenom, email, telephone)
Participant (#id, dateNaissance, lienPortefolio)
Inscription (#idHackathon, num, date, competence, #idParticipant, #idEquipe)
            clé primaire : (idHackathon, num)
```

### Entités

| Entité | Attributs | Relations |
|---|---|---|
| `Hackathon` | `theme`, `lieu`, `ville`, `dateHeureDebut`, `dateHeureFin`, `affiche`, `objectifs` | ManyToMany `Organisateur`, ManyToMany `MembreJury`, OneToMany `Projet`, OneToMany `Inscription` |
| `Projet` | `description`, `retenu` | ManyToOne `Hackathon`, OneToMany `Equipe` |
| `Equipe` | `nom`, `lienPrototype` | ManyToOne `Projet`, ManyToOne `Participant` (responsable), OneToMany `Inscription`, OneToMany `Noter` |
| `Inscription` | `num`, `date`, `competence` | ManyToOne `Hackathon` (fait partie de la clé), ManyToOne `Participant`, ManyToOne `Equipe` (facultative) |
| `Membre` (abstraite) | `nom`, `prenom`, `email`, `telephone` | Héritage `JOINED` |
| `Participant` (hérite de `Membre`) | `dateNaissance`, `lienPortefolio` | OneToMany `Inscription`, OneToMany `Equipe` (responsable) |
| `MembreJury` (hérite de `Membre`) | | ManyToMany `Hackathon`, OneToMany `Noter` |
| `Organisateur` | `nom`, `statut`, `siteWeb`, `email` | ManyToMany `Hackathon` |
| `Noter` (classe d'association) | `note` | ManyToOne `MembreJury`, ManyToOne `Equipe` |

### Points particuliers

- **Inscription identifiée relativement au hackathon** : sa clé primaire est le couple `(hackathon, num)`, où `num` repart de 1 pour chaque hackathon.
- **Équipe et hackathon** : `Equipe` n'a pas de lien direct vers `Hackathon`. On passe par son projet : `equipe.projet.hackathon`.

---

## Architecture du code

```
src/
├── Controller/
│   ├── HackathonController.php      Pages web : liste, détail, équipes, affectation
│   └── HackathonApiController.php   API JSON
├── Entity/                          9 entités Doctrine
├── Repository/
│   ├── HackathonRepository.php      findWithInscriptions()
│   └── EquipeRepository.php         findByHackathon(), findDisponiblesByHackathon()
└── DataFixtures/
    └── AppFixtures.php              Jeu d'essai

templates/
├── base.html.twig
└── hackathon/
    ├── index.html.twig              Liste
    ├── show.html.twig               Détail et inscrits
    ├── equipes.html.twig            Équipes
    └── affectation.html.twig        Formulaire d'affectation

assets/styles/app.css                Feuille de style (tableaux, formulaire, messages)
migrations/                          Migrations Doctrine
```

### Méthodes de repository

| Méthode | Rôle |
|---|---|
| `HackathonRepository::findWithInscriptions(int $id)` | Charge un hackathon avec ses inscriptions, participants et équipes en une seule requête |
| `EquipeRepository::findByHackathon(Hackathon $h)` | Équipes d'un hackathon avec leurs membres et leur responsable, en une seule requête |
| `EquipeRepository::findDisponiblesByHackathon(Hackathon $h, int $nbMax)` | Équipes du hackathon ayant moins de `$nbMax` membres (`GROUP BY` / `HAVING COUNT`) |

---

## Choix techniques

### Problème du N+1

Doctrine charge les relations à la demande (lazy loading). Afficher 50 inscrits avec leur participant et leur équipe déclencherait plus de 100 requêtes. Les méthodes de repository utilisent donc `leftJoin(...)->addSelect(...)` pour tout charger en une seule requête.

| Page | Requêtes SQL |
|---|---|
| Détail d'un hackathon | 1 |
| Équipes d'un hackathon | 2 |

### Formulaire d'affectation

- Construit avec `createFormBuilder()` dans le contrôleur, sans classe de formulaire dédiée.
- Champ `inscription` : `ChoiceType` avec `choice_value` sur `num`, car `Inscription` a une clé composée.
- Champ `equipe` : `EntityType` dont l'option `choices` est limitée aux équipes disponibles du hackathon. Cette liste sert aussi de validation côté serveur.
- Contraintes `NotNull` sur les deux champs.
- Pattern Post/Redirect/Get avec message flash après l'enregistrement.

### Sécurité

- **CSRF** : chaque formulaire contient un champ caché `_token`. La protection est en mode stateless (`config/packages/csrf.yaml`), et le jeton est géré par le contrôleur Stimulus `csrf_protection_controller.js`. Une soumission sans jeton valide est rejetée.
- **Validation serveur** : une équipe d'un autre hackathon ou une équipe complète, envoyée en modifiant le HTML, est refusée.
- **Paramètres de route** : les `{id}` sont limités aux entiers.
- **Secrets** : les identifiants de base de données restent dans `.env.local`, qui n'est pas versionné.

---

## Jeu d'essai

`php bin/console doctrine:fixtures:load -n` génère notamment :

| Hackathon | Ville | Équipes (membres) | Sans équipe |
|---|---|---|---|
| IA et santé | Paris | 3 équipes (4, 3, 5) | 2 |
| Ville durable | Lyon | 3 équipes (3, 4, 4) | 1 |
| Cybersécurité | Nantes | 2 équipes (5, 3) | 0 |
| Accessibilité numérique | Lille | 4 équipes (4, 4, 3, 3) | 3 |
| Agriculture connectée | Bordeaux | aucune | 5 |
| Éducation et numérique | Marseille | aucune | aucun inscrit |

Ces données couvrent les cas à tester : équipes complètes, inscrits sans équipe, hackathon sans équipe et hackathon sans inscrit. Les fixtures créent aussi les organisateurs, les membres du jury, les projets et les notes.

---

## Commandes utiles

```bash
# Routes de l'application
php bin/console debug:router

# Vérifier le mapping et la synchronisation du schéma
php bin/console doctrine:schema:validate

# État des migrations
php bin/console doctrine:migrations:status

# Vérifier la syntaxe des templates
php bin/console lint:twig templates

# Requête SQL rapide
php bin/console dbal:run-sql "SELECT id, theme, ville FROM hackathon"

# Tester une requête DQL
php bin/console doctrine:query:dql "SELECT e.nom FROM App\Entity\Equipe e"

# Vider le cache
php bin/console cache:clear
```

En environnement `dev`, la barre de debug Symfony (onglet Doctrine) affiche le nombre de requêtes exécutées par page.

---

## Historique des TP

| TP | Contenu |
|---|---|
| Back - TP1 | Mise en place de Doctrine : entités, migrations, fixtures |
| Back - TP2 | Contrôleur HTTP : versions HTML (Twig) et JSON (API) |
| Front - TP1 | Premières vues Twig |
| Front - TP2 | Vues dynamiques : détail d'un hackathon, inscrits, répartition par équipe, résolution du N+1 |
| Front - TP3 | Formulaires : affectation d'une inscription à une équipe, page des équipes, limite de membres par équipe |

---

Projet pédagogique réalisé dans le cadre du BTS SIO SLAM.
