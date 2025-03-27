# Plateforme Centrale - Gestion des Leads

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.4-blue" alt="PHP Version">
  <img src="https://img.shields.io/badge/License-MIT-green" alt="License">
</p>

## 📋 À propos

La Plateforme Centrale est une application web développée pour centraliser et gérer efficacement les leads provenant de différents sites partenaires. Elle offre une interface d'administration complète, des statistiques avancées, et une API sécurisée permettant aux sites d'envoyer automatiquement leurs leads.

Ce projet a été développé dans le cadre d'un hackathon pour démontrer la capacité à créer rapidement une solution de gestion de leads performante et scalable.

## ✨ Fonctionnalités

-   **Tableau de Bord Analytique**

    -   Statistiques en temps réel
    -   Graphiques d'évolution des leads
    -   Visualisation de la distribution par heure
    -   Taux de conversion et autres KPIs

-   **Gestion des Leads**

    -   Liste complète avec filtres avancés
    -   Édition et mise à jour des statuts
    -   Export CSV des données
    -   Suivi de conversion

-   **Gestion des Sites Partenaires**

    -   Génération de tokens d'API sécurisés
    -   Suivi des performances par site
    -   Configuration des paramètres

-   **API RESTful**

    -   Authentification via tokens
    -   Endpoints documentés
    -   Validation des données

-   **Sécurité**
    -   Authentification utilisateur
    -   Protection CSRF
    -   Validation des entrées

## 🚀 Installation

### Prérequis

-   PHP 8.4+
-   Composer
-   MySQL ou SQLite
-   Node.js & NPM

### Étapes d'installation

1. **Cloner le dépôt**

    ```bash
    git clone https://github.com/TheKyyn/hackathon-platform-centrale.git
    cd hackathon-platform-centrale
    ```

2. **Installer les dépendances**

    ```bash
    composer install
    npm install
    ```

3. **Configurer l'environnement**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Configurer la base de données**
   Modifiez le fichier `.env` avec vos informations de connexion à la base de données.

5. **Migrer la base de données**

    ```bash
    php artisan migrate
    ```

6. **Créer un utilisateur administrateur** (optionnel)

    ```bash
    php artisan db:seed --class=CreateAdminUserSeeder
    ```

7. **Compiler les assets**

    ```bash
    npm run dev
    ```

8. **Démarrer le serveur**
    ```bash
    php artisan serve
    ```

## 🌐 Utilisation de l'API

### Authentification

Toutes les requêtes API doivent inclure un token d'authentification dans l'en-tête :

```
Authorization: Bearer {TOKEN}
```

### Endpoints principaux

-   `POST /api/leads` : Ajouter un nouveau lead
-   `GET /api/leads` : Récupérer la liste des leads (pour le site associé au token)
-   `GET /api/sites/{site}/leads` : Récupérer les leads d'un site spécifique (admin uniquement)

Pour plus de détails, consultez la [documentation de l'API](docs/API.md).

## 📊 Tableau de Bord

Le tableau de bord offre une vue d'ensemble des performances :

-   Nombre total de leads
-   Taux de conversion
-   Leads du jour/semaine/mois
-   Graphique d'évolution sur 30 jours
-   Distribution horaire
-   Répartition par type d'énergie
-   Répartition par site

## 🔧 Personnalisation

La plateforme est hautement personnalisable :

-   Modification des statuts de leads
-   Ajout de champs personnalisés
-   Configuration des exports
-   Personnalisation des filtres

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## ✏️ Auteurs

-   **TheKyyn** - [GitHub](https://github.com/TheKyyn)

---

<p align="center">Développé avec ❤️ pour le Hackathon 2025</p>
