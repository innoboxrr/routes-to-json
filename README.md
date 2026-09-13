# Routes to JSON

**Stop hardcoding URLs in your frontend.**

Your Laravel routes already have names. This package exports them to a JSON file your JavaScript can read, so the frontend resolves URLs the same way Blade does — by name, not by string.

```bash
php artisan route:json
```

```json
{
    "user.profile": "users/{id}/profile",
    "invoice.show": "invoices/{invoice}"
}
```

Only named routes are exported: a route without a name can't be requested by name, so it is skipped.

Then resolve them in the browser with the companion package, [route-resolver](https://github.com/innoboxrr/route-resolver):

```js
import route, { setRoutes } from 'innoboxrr-route-resolver';
import routes from './routes.json';

setRoutes(routes);

route('user.profile', { id: 42 });   // → //your-host/users/42/profile
```

## Why it matters

Hardcoded URLs are silent breakage. You rename a route, the backend tests pass, and a button in the SPA quietly 404s in production. Naming the route on both sides makes the rename a compile-time problem instead of a support ticket.

## Install

```bash
composer require innoboxrr/routes-to-json
```

The service provider is auto-discovered. Publishing the config is optional; its tag is `config`, and passing `--provider` keeps other packages that use the same tag from publishing their files too:

```bash
php artisan vendor:publish --provider="Innoboxrr\RoutesToJson\Providers\RoutesToJsonServiceProvider" --tag=config
```

## Output path

By default the file is written to `resources/vue/assets/json/routes.json`. The config key is `routes-to-json.path`:

```php
'path' => env('JSON_ROUTES_FILE', resource_path('vue/assets/json/routes.json')),
```

- The directory is created if it doesn't exist.
- A relative path is resolved against the project root (`base_path()`), not the current working directory.
- An empty `JSON_ROUTES_FILE=` falls back to the default path.

### React build

A React app doesn't live under `resources/vue`. Point the file to where your React code imports it from, in `.env`:

```dotenv
JSON_ROUTES_FILE=resources/react/assets/json/routes.json
```

and import that file from the React entry point (the exact import path depends on your Vite aliases). If the configuration is cached, run `php artisan config:clear` after changing the variable.

## Keep it in sync

Regenerate the file whenever routes change, before building the frontend:

```json
{
    "scripts": {
        "build": "php artisan route:json && vite build"
    }
}
```

---

Part of [Innobox R&R](https://github.com/innoboxrr) — 52 open-source packages extracted from production work. **[innobox.systems](https://innobox.systems)**
