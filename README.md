```mdx
# Guide d'installation et configuration d'un projet Laravel avec Inertia.js et Vue.js

## Pré-requis

Avant tout, assurez-vous que votre machine locale dispose des outils suivants :

- **PHP** (au moins version 8.3)
- **Composer** (gestionnaire de dépendances PHP)
- **Node.js** et **npm** (ou Bun) pour compiler les assets du frontend

---

## Installation de PHP et Composer

### Linux et macOS

Installez PHP 8.3 avec la commande suivante :

```bash
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.3)"
```

### Windows (PowerShell)

Pour installer PHP 8.3 sur Windows, utilisez cette commande PowerShell :

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.3'))
```

---

## Création du projet Laravel

Créez un nouveau projet Laravel avec Composer :

```bash
composer create-project laravel/laravel nomProjet
```

---

## Installation et configuration d'Inertia.js

### Étape 1 : Installer le package Laravel d'Inertia.js

Ajoutez le package Inertia.js :

```bash
composer require inertiajs/inertia-laravel
```

### Étape 2 : Configurer le fichier principal `app.blade.php`

Modifiez le fichier `resources/views/app.blade.php` pour inclure les scripts nécessaires :

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite('resources/js/app.js')
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
```

### Étape 3 : Publier le middleware d'Inertia.js

Publiez le middleware `HandleInertiaRequests` avec la commande suivante :

```bash
php artisan inertia:middleware
```

### Étape 4 : Ajouter le middleware dans le groupe web

Ajoutez `HandleInertiaRequests` au groupe web dans `bootstrap/app.php` pour :

- Gérer les réponses Inertia et transmettre les props nécessaires
- Partager des données globales (ex. : informations utilisateur ou paramètres de session)
- Définir un point d'entrée unique pour le layout principal

---

## Configuration du frontend

### Étape 1 : Installer les dépendances Vue.js

Ajoutez les dépendances nécessaires pour Inertia.js avec Vue.js :

```bash
npm install @inertiajs/vue3
```

### Étape 2 : Initialiser le frontend dans `resources/js/app.js`

Créez ou modifiez le fichier `resources/js/app.js` comme suit :

```javascript
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
```

### Étape 3 : Installer le plugin Vue.js pour Vite

Ajoutez le plugin Vite pour Vue.js :

```bash
npm install @vitejs/plugin-vue
```

### Étape 4 : Configurer `vite.config.js`

Modifiez le fichier `vite.config.js` pour inclure Vue.js :

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

---

## Démarrage du projet Laravel avec Inertia.js

Après avoir configuré le projet, suivez ces étapes pour lancer l'application :

### 1. Lancer le serveur de développement Laravel

Dans le répertoire du projet, exécutez la commande suivante pour démarrer le serveur local Laravel :

```bash
php artisan serve
```

### 2. Compiler les assets avec Vite

Ouvrez un second terminal, puis lancez le serveur de développement Vite pour compiler et surveiller les fichiers front-end :

```bash
npm run dev
```

### 3. Accéder à l'application

Votre application est maintenant accessible via le navigateur à l'adresse suivante :

[http://127.0.0.1:8000](http://127.0.0.1:8000)
```

Ce fichier est bien structuré pour une utilisation dans un projet MDX. Souhaitez-vous que j'y ajoute des styles ou d'autres détails spécifiques ? 😊
