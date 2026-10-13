# TP1 - Hackathon : Mise en place de l'ORM Doctrine

Projet développé avec Symfony 6.4 / 7 et Doctrine ORM dans le cadre du projet Hackat'Innov (BTS SIO SLAM).

## 1. Procédure d'installation

```bash
# 1. Cloner le dépôt et installer les dépendances
git clone <URL_DU_DEPOT_GITHUB>
cd hackathon
composer install

# 2. Configurer les variables d'environnement locales
# Créer le fichier .env.local avec vos identifiants MySQL :
# DATABASE_URL="mysql://usr_hack:PassHackathon2026!@127.0.0.1:3306/Hackathon?serverVersion=8.0.32-mysql&charset=utf8mb4"

# 3. Créer la base de données (si non existante) et exécuter les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n

# 4. Charger le jeu d'essai (Fixtures)
php bin/console doctrine:fixtures:load -n
```

## 2. Liste des entités et relations

Le modèle implémente le Diagramme de Classes V1.0 :

* **Organisateur** : `nom`, `statut`, `siteWeb`, `email`
    * Relation : *ManyToMany* avec `Hackathon`
* **Membre** *(Classe abstraite mère)* : `nom`, `prenom`, `email`, `telephone` (Héritage `JOINED`)
* **Participant** *(Hérite de Membre)* : `dateNaissance`, `lienPortefolio`
    * Relations : *OneToMany* vers `Inscription`, *OneToMany* vers `Equipe` (responsable)
* **MembreJury** *(Hérite de Membre)*
    * Relations : *ManyToMany* avec `Hackathon`, *OneToMany* vers `Noter`
* **Hackathon** : `theme`, `lieu`, `ville`, `dateHeureDebut`, `dateHeureFin`, `affiche`, `objectifs`
    * Relations : *ManyToMany* avec `Organisateur`, *ManyToMany* avec `MembreJury`, *OneToMany* vers `Projet`, *OneToMany* vers `Inscription`
* **Projet** : `description`, `retenu`
    * Relations : *ManyToOne* vers `Hackathon`, *OneToMany* vers `Equipe`
* **Equipe** : `nom`, `lienPrototype`
    * Relations : *ManyToOne* vers `Projet`, *ManyToOne* vers `Participant` (responsable), *OneToMany* vers `Inscription`, *OneToMany* vers `Noter`
* **Inscription** : `date`, `competence`
    * Relations : *ManyToOne* vers `Hackathon`, *ManyToOne* vers `Participant`, *ManyToOne* vers `Equipe`
* **Noter** *(Classe d'association)* : `note` (float)
    * Relations : *ManyToOne* vers `MembreJury`, *ManyToOne* vers `Equipe`

## 3. Test d'interrogation de la base (DBAL)

Commande exécutée :
```bash
php bin/console dbal:run-sql "SELECT id, theme, ville, date_heure_debut FROM hackathon LIMIT 5;"
```

Résultat :
```text
 ---- ----------------------------- -------- --------------------- 
  id   theme                         ville    date_heure_debut      
 ---- ----------------------------- -------- --------------------- 
  1    L'IA au service de la Santé   Nantes   2026-11-15 09:00:00   
 ---- ----------------------------- -------- --------------------- 
```
