# Projet Symfony

Bienvenue dans notre projet Symfony ! 🚀

## Prérequis

- PHP >= 8.1
- Composer
- SQLite

## Installation

1. **Cloner le dépôt :**

   ```bash
   git clone https://github.com/votre-utilisateur/votre-projet.git
   cd votre-projet
   ```

2. **Installer les dépendances avec Composer :**

   ```bash
   composer install
   ```

3. **Configurer les variables d'environnement :**

   Copiez le fichier `.env.example` en `.env` et ajustez les variables si nécessaire :

   ```bash
   cp .env.example .env
   ```

4. **Exécuter les migrations de la base de données :**

   ```bash
   php bin/console doctrine:migrations:migrate
   ```

5. **Importer les fichiers CSV :**

   Placez vos fichiers CSV dans le dossier `csv` ou utilisez les fichiers existants. Ensuite, exécutez la commande suivante, elle vous demandera le chemin du fichier CSV à importer :

   ```bash
   php bin/console ugo:orders:import
   ```

6. **Lancer le serveur Symfony :**

   ```bash
   symfony server:start
   ```

## Utilisation

Une fois le serveur démarré, vous pouvez accéder à votre application à l'adresse suivante : [ http://127.0.0.1:8000](http://127.0.0.1:8000)