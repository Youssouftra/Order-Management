# Brasil Burger - Order Management System

Application Symfony de gestion des commandes pour Brasil Burger.

## 🚀 Déploiement sur Render

### Prérequis
- Compte GitHub
- Compte Render (gratuit)
- Base de données PostgreSQL Neon (déjà configurée)

### Informations du projet

**Stack technique:**
- PHP 8.1+
- Symfony 6.4
- PostgreSQL 16
- Doctrine ORM
- Twig

**Base de données:**
- Type: PostgreSQL (Neon)
- Host: ep-plain-dream-af7ysrga-pooler.c-2.us-west-2.aws.neon.tech
- Database: neondb
- User: neondb_owner

**Services externes:**
- Cloudinary (stockage images)
  - Cloud Name: di38pznie
  - API Key: 124532626687588

### Étapes de déploiement

#### 1. Préparer le repository
```bash
git add render.yaml Procfile build.sh README.md
git commit -m "Add Render deployment configuration"
git push origin Symfony
```

#### 2. Créer le service sur Render

**Option A: Déploiement automatique (Blueprint)**
1. Aller sur https://dashboard.render.com
2. Cliquer sur "New +" → "Blueprint"
3. Connecter votre repository GitHub
4. Sélectionner la branche `Symfony`
5. Render détectera automatiquement le fichier `render.yaml`

**Option B: Déploiement manuel**
1. Aller sur https://dashboard.render.com
2. Cliquer sur "New +" → "Web Service"
3. Connecter votre repository: `https://github.com/Youssouftra/Order-Management.git`
4. Configurer:
   - **Name**: brasil-burger-symfony
   - **Region**: Frankfurt (ou autre)
   - **Branch**: Symfony
   - **Runtime**: PHP
   - **Build Command**: `bash build.sh`
   - **Start Command**: `php -S 0.0.0.0:$PORT -t public`

#### 3. Configurer les variables d'environnement

Dans Render Dashboard → Service → Environment:

```env
APP_ENV=prod
APP_SECRET=<générer_une_clé_secrète_32_caractères>
DATABASE_URL=postgresql://neondb_owner:npg_JYGXfQKkT73C@ep-plain-dream-af7ysrga-pooler.c-2.us-west-2.aws.neon.tech/neondb?sslmode=require&serverVersion=16&charset=utf8
CLOUDINARY_CLOUD_NAME=di38pznie
CLOUDINARY_API_KEY=124532626687588
CLOUDINARY_API_SECRET=RpxMyOXnJ94EYd8AY5NixPAqNoY
```

#### 4. Déployer

Cliquer sur "Create Web Service" ou "Deploy latest commit"

### 📋 Checklist de déploiement

- [ ] Repository GitHub poussé sur branche Symfony
- [ ] Fichiers de configuration créés (render.yaml, Procfile, build.sh)
- [ ] Service Render créé
- [ ] Variables d'environnement configurées
- [ ] Base de données Neon accessible
- [ ] Premier déploiement réussi
- [ ] Application accessible via URL Render

### 🔧 Commandes utiles

**Générer APP_SECRET:**
```bash
php -r "echo bin2hex(random_bytes(16));"
```

**Tester localement:**
```bash
composer install
php bin/console cache:clear
symfony server:start
```

**Vérifier la connexion DB:**
```bash
php bin/console doctrine:query:sql "SELECT 1"
```

### 🌐 URLs

- **Repository**: https://github.com/Youssouftra/Order-Management.git
- **Branche**: Symfony
- **Render Dashboard**: https://dashboard.render.com
- **URL de production**: https://brasil-burger-symfony.onrender.com (après déploiement)

### 📦 Structure du projet

```
brasil-burger-complete(you)/
├── config/              # Configuration Symfony
├── public/              # Point d'entrée web
│   ├── index.php
│   └── css/
├── src/
│   ├── Controller/      # Contrôleurs
│   ├── Entity/          # Entités Doctrine
│   ├── Form/            # Formulaires
│   └── Repository/      # Repositories
├── templates/           # Templates Twig
├── .env                 # Variables d'environnement (local)
├── composer.json        # Dépendances PHP
├── render.yaml          # Configuration Render
├── Procfile             # Commande de démarrage
└── build.sh             # Script de build
```

### ⚠️ Notes importantes

1. **Base de données**: Utilise PostgreSQL Neon (déjà existante, partagée avec C#)
2. **Pas de migrations**: Ne pas exécuter de migrations, le schéma existe déjà
3. **Images**: Stockées sur Cloudinary
4. **Plan gratuit Render**: Le service s'endort après 15 min d'inactivité
5. **Fichiers sensibles**: `.env` et `.env.dev` ne sont pas commités

### 🐛 Dépannage

**Erreur de connexion DB:**
- Vérifier DATABASE_URL dans les variables d'environnement
- Vérifier que l'IP de Render est autorisée sur Neon

**Erreur 500:**
- Vérifier les logs Render
- Vérifier APP_ENV=prod
- Vérifier cache:clear a été exécuté

**Performance lente:**
- Activer le cache OPcache
- Optimiser les requêtes Doctrine
- Utiliser un plan payant Render

### 📞 Support

Pour toute question, consulter:
- Documentation Symfony: https://symfony.com/doc
- Documentation Render: https://render.com/docs
- Documentation Neon: https://neon.tech/docs
