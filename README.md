Avant tout, s'assurer que votre machine locale a PHP et Composer installé. On devrait aussi installer Node et NPM oou Bun pour compiler les assets du frontend:
Nous devons d'abord installer php et composer.

-  Linux et MacOs
```
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.3)"
```

-  WIndows Powershell
```
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.3'))
```

-  Créer l'application

```
composer create-project laravel/laravel nomProjet
```

-  Installer Inertiajs

```
composer require inertiajs/inertia-laravel
```

-  Configurer le ficher principal `app.blade.php`

```
<!DOCTYPE html>  
<html>  
<head>  
    <meta charset="utf-8" />  
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />  
    @vite('resources/js/app.js')  
    @inertiaHead</head>  
<body>  
@inertia  
</body>  
</html>
```

-  Publication du middleware `HandleInertiaRequests` d'Inertia

```
php artisan inertia:middleware
```

-  Ajouter le `HandleInertiaRequests` middleware au groupe de middleware  `web` dans le fichier `bootstrap/app.php` .
##### Pourquoi l'ajouter à `web` dans `app.php` :

1. **Gestion des réponses Inertia :**  
   Inertia.js permet de rendre des vues côté client tout en gardant le serveur Laravel pour la logique. Lorsqu'une requête est effectuée, le middleware `HandleInertiaRequests` va automatiquement gérer la réponse Inertia, en ajoutant les props nécessaires pour rendre les composants Vue.js ou React, tout en respectant la structure de l'application Laravel.

2. **Ajouter des données partagées globalement :**  
   Ce middleware permet aussi de transmettre des données globales partagées à toutes les vues Inertia, telles que les informations de l'utilisateur connecté, les paramètres de la session, etc. Cela est réalisé par l'injection de ces données dans toutes les réponses Inertia, ce qui permet à vos composants front-end de les utiliser.

3. **Définir un point d'entrée de l'application (comme le layout principal) :**  
   Lorsque vous ajoutez ce middleware dans le groupe `web`, cela garantit que chaque requête traitée par votre application Laravel et envoyée au front-end via Inertia aura un contexte de réponse complet. Ce contexte peut inclure des informations essentielles comme les éléments du layout ou les métadonnées du document, comme le titre de la page, le modèle de layout à utiliser, etc.


-  Installation des dépendances d'inertia pour la couche frontend

```
npm install @inertiajs/vue3
```

-  Initialisation de l'app frontend `resources/js/app.js`

```javascript
import {createApp, h} from 'vue'  
import {createInertiaApp} from '@inertiajs/vue3'  
  
createInertiaApp({  
    resolve: name => {  
        const pages = import.meta.glob('./Pages/**/*.vue', {eager: true})  
        return pages[`./Pages/${name}.vue`]  
    },  
    setup({el, App, props, plugin}) {  
        createApp({render: () => h(App, props)})  
            .use(plugin)  
            .mount(el)  
    },  
})
```

La fonction `createInertiaApp` fait partie de la configuration d'Inertia.js dans un projet Vue.js (ou un autre framework frontend). Elle est utilisée pour initialiser l'application avec Inertia.js, résoudre les composants des pages et les rendre avec les bonnes props. Voici une explication détaillée de chaque partie de ce code :

### 1. **La fonction `createInertiaApp` :**

```javascript
createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', {eager: true})
        return pages[`./Pages/${name}.vue`]
    },
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .mount(el)
    },
})
```

Cette fonction initialise l'application Inertia.js, résout les pages dynamiquement et les rend dans l'application Vue.js avec les bonnes props.

### 2. **La fonction `resolve` :**

```javascript
resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', {eager: true})
    return pages[`./Pages/${name}.vue`]
}
```

- **But** : La fonction `resolve` est responsable de l'importation dynamique du composant Vue correspondant à la page demandée.
- **`import.meta.glob`** est une fonctionnalité de Vite (ou d'autres outils de bundling comme Webpack) qui permet d'importer plusieurs fichiers de manière dynamique. Ici, elle est utilisée pour importer automatiquement tous les fichiers Vue présents dans le répertoire `./Pages` et ses sous-répertoires.
    - **`import.meta.glob`** scanne tous les fichiers du répertoire spécifié (ici, `./Pages/**/*.vue`), et les importe de manière dynamique.
    - **`{eager: true}`** garantit que tous les fichiers sont importés immédiatement (de manière « eager »), ce qui les rend disponibles en mémoire et évite un chargement paresseux (lazy loading).
- **Comment ça marche** : Lorsque Inertia.js tente de charger une page (par exemple, `/home`), il appelle la fonction `resolve` avec le nom de la page (par exemple, `'Home'`). Cette fonction retourne alors le composant Vue correspondant dans l'objet `pages` qui a été importé par `import.meta.glob`.
- Par exemple, si l'utilisateur navigue vers la route `/home`, la fonction `resolve` retournera le composant Vue situé dans `./Pages/Home.vue`.

### 3. **La fonction `setup` :**

```javascript
setup({el, App, props, plugin}) {
    createApp({render: () => h(App, props)})
        .use(plugin)
        .mount(el)
}
```

- **But** : La fonction `setup` initialise et monte l'application Vue avec les props et le plugin Inertia.js.
- **`{el, App, props, plugin}`** : Ce sont les paramètres passés à la fonction `setup` par Inertia.js :
    - **`el`** : L'élément DOM (généralement un élément avec `id="app"`) où l'application Vue sera montée.
    - **`App`** : Le composant racine de votre application, qui est normalement le layout principal.
    - **`props`** : Les données (props) transmises par Inertia à votre composant racine. Ce sont les données associées à la page courante.
    - **`plugin`** : Le plugin Inertia.js à utiliser dans votre application Vue, permettant à Vue de gérer les transitions de pages.
- **`createApp({render: () => h(App, props)})`** :
    - Cela crée une nouvelle instance de l'application Vue en utilisant `createApp`.
    - `render: () => h(App, props)` indique à Vue de rendre le composant `App` et de lui passer les `props` (données passées par Inertia).
    - **`h`** est la fonction de rendu de Vue qui crée des nœuds du DOM virtuel.
- **`.use(plugin)`** : Cela installe le plugin Inertia.js dans l'application Vue, permettant à Inertia de gérer les transitions de page et la gestion des routes.
- **`.mount(el)`** : Cela monte l'application Vue sur l'élément DOM spécifié (l'élément `el`). Ce processus rend la page en utilisant les `props` envoyées par Inertia.

### Flux global de l'application :

1. **Résolution de la page :**

    - Lorsque l'utilisateur navigue vers une route, Inertia.js appelle la fonction `resolve`, qui charge dynamiquement le composant Vue correspondant à la page demandée.
    - Le composant de la page est ensuite passé au composant racine (`App`), avec les `props` nécessaires.
2. **Initialisation de l'application :**

    - La fonction `setup` est exécutée pour initialiser l'application Vue.
    - L'application est créée avec `createApp` et est configurée avec le plugin Inertia.
    - L'application est ensuite montée sur l'élément DOM, ce qui déclenche le rendu de la page avec les données appropriées.

### En résumé :

La fonction `createInertiaApp` configure l'application Inertia.js en résolvant dynamiquement les composants Vue depuis le dossier `Pages`, et en initialisant l'application Vue avec le bon plugin Inertia.js pour gérer les transitions de page. La fonction `resolve` est responsable de la recherche de la page correcte, et la fonction `setup` initialise et monte l'application Vue avec les données appropriées. Cela permet une intégration fluide entre Laravel (backend) et Vue.js (frontend) tout en permettant à Inertia.js de gérer les transitions de page côté client.


-  Installation de `@vitejs/plugin-vue`

```
npm install @vitejs/plugin-vue
```

-  Configuration de `vite.config.js`

```javascript
import {defineConfig} from 'vite';  
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
Ici `vue()`  va permettre à Vite de gérer les fichiers Vue (`.vue`), ce qui signifie que vous pouvez écrire des composants Vue dans vos fichiers.

-  Création de la première page

```
php artisan make:HomeController
```


-  Configuration du `tsconfig.json`

```json
{  
    "compilerOptions": {  
        "target": "ES6",  
        "moduleResolution": "Node",  
        "module": "ESNext",  
        "baseUrl": "./",  
        "paths": {  
            "@/*": [  
                "resources/js/*"  
            ]  
        }  
    },  
    "include": [  
        "resources/js/**/*.js",  
        "resources/js/**/*.ts",  
        "resources/js/**/*.vue"  
    ]  
}

```

### **Résumé des options**

|**Option**|**Description**|
|---|---|
|`target: "ES6"`|Génère du code JavaScript compatible avec ES6.|
|`moduleResolution: "Node"`|Résout les imports en suivant les règles de Node.js (utile pour les projets modernes).|
|`module: "ESNext"`|Utilise la syntaxe moderne des modules JavaScript dans le code généré.|
|`baseUrl: "./"`|Définit la base pour résoudre les chemins relatifs (ici, la racine du projet).|
|`paths`|Permet de définir des alias pour simplifier les imports, comme `@/` pour `resources/js/`.|
|`include`|Indique quels fichiers TypeScript doit analyser (`.js`, `.ts`, `.vue` dans ce cas).|

---

### **À quoi sert ce fichier dans votre projet ?**

1. **Simplifier les imports** : Les alias définis avec `paths` permettent d'éviter des chemins complexes (`../../../`) et rendent le code plus lisible.
2. **Définir une sortie moderne** : Avec `target` et `module`, le projet utilise des fonctionnalités modernes de JavaScript tout en restant compatible avec les outils modernes comme Webpack, Vite ou ESBuild.
3. **Inclure les fichiers nécessaires** : TypeScript ne traite que les fichiers spécifiés dans `include`, ce qui accélère la compilation.
4. **Faciliter l'intégration avec Vue.js** : En incluant les fichiers `.vue`, TypeScript peut comprendre les types dans vos composants Vue.


-  Installation de `shadcn-vue`

``` 
npx shadcn-vue@latest init
```

#Tips
Install ts en dev si ça bug
