# Guide de test pour la Plateforme Centrale

Ce document explique comment tester la plateforme centrale qui reçoit les leads des différents sites.

## Prérequis

1. La plateforme centrale doit être installée et configurée :

    ```bash
    cd hackathon-platform-centrale
    composer install
    php artisan migrate
    ```

2. Au moins un site doit être créé dans la base de données avec un token API valide.

## Tests automatisés

Nous avons créé plusieurs tests automatisés pour vérifier le bon fonctionnement de la plateforme centrale.

### Exécuter tous les tests

```bash
cd hackathon-platform-centrale
php artisan test
```

### Exécuter les tests spécifiques à l'API

```bash
cd hackathon-platform-centrale
php artisan test --filter=LeadApi
```

### Exécuter les tests de la plateforme centrale

```bash
cd hackathon-platform-centrale
php artisan test --filter=CentralPlatform
```

### Exécuter les tests unitaires du modèle CentralizedLead

```bash
cd hackathon-platform-centrale
php artisan test --filter=CentralizedLead
```

## Test manuel via le script

Nous avons créé un script de test manuel qui vérifie que la plateforme centrale fonctionne correctement :

```bash
cd hackathon-platform-centrale
php tests/manual_central_platform_test.php
```

Ce script effectue plusieurs opérations :

1. Vérifie la connexion à la base de données
2. Crée ou récupère un site de test avec un token API
3. Teste la création d'un lead via l'API
4. Vérifie que le lead existe dans la base de données
5. Teste la mise à jour d'un lead
6. Teste la récupération des leads via l'API

## Test manuel via Postman ou des requêtes HTTP

Vous pouvez également tester manuellement l'API avec des outils comme Postman ou cURL.

### Exemple de création de lead avec cURL

```bash
curl -X POST http://localhost:8000/api/v1/leads/sync \
  -H "Authorization: Bearer VOTRE_TOKEN_API" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "original_id": 123,
    "first_name": "Jean",
    "last_name": "Dupont",
    "email": "jean.dupont@example.com",
    "phone": "0601020304",
    "energy_type": "pompe_a_chaleur",
    "property_type": "maison_individuelle",
    "is_owner": true,
    "has_project": true
  }'
```

### Exemple de récupération des leads avec cURL

```bash
curl -X GET http://localhost:8000/api/v1/leads \
  -H "Authorization: Bearer VOTRE_TOKEN_API" \
  -H "Accept: application/json"
```

## Vérification manuelle en base de données

Vous pouvez vérifier directement dans la base de données que les leads sont bien reçus :

```bash
cd hackathon-platform-centrale
php artisan tinker
```

Puis dans tinker :

```php
App\Models\CentralizedLead::all();
```

## Tests d'intégration avec les sites externes

Pour tester l'intégration complète :

1. Configurez un site externe (par exemple, hackathon-LF) avec les variables d'environnement suivantes :

    ```
    CENTRAL_PLATFORM_URL=http://localhost:8000
    CENTRAL_PLATFORM_API_TOKEN=votre_token_api
    ```

2. Créez un lead sur le site externe.

3. Vérifiez que le lead est bien synchronisé avec la plateforme centrale :
    ```bash
    cd hackathon-platform-centrale
    php artisan tinker
    > App\Models\CentralizedLead::latest()->first();
    ```

## Résolution des problèmes courants

1. **Erreur d'authentification (401)**

    - Vérifiez que le token API est correctement configuré
    - Vérifiez que le site est actif dans la base de données

2. **Erreur de validation (422)**

    - Vérifiez que tous les champs requis sont présents
    - Vérifiez le format des données envoyées

3. **Erreur de serveur (500)**
    - Vérifiez les logs d'erreur Laravel : `storage/logs/laravel.log`
    - Vérifiez que la configuration de la base de données est correcte
