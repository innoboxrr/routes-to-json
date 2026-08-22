# Routes to JSON

**Stop hardcoding URLs in your frontend.**

Your Laravel routes already have names. This package exports them to a JSON file your JavaScript can read, so the frontend resolves URLs the same way Blade does — by name, not by string.

```bash
php artisan routes:json
```

```json
{
  "user.profile": "/users/{id}/profile",
  "invoice.show": "/invoices/{invoice}"
}
```

Then resolve them in the browser with the companion package, [route-resolver](https://github.com/innoboxrr/route-resolver):

```js
route('user.profile', { id: 42 });   // → /users/42/profile
```

## Why it matters

Hardcoded URLs are silent breakage. You rename a route, the backend tests pass, and a button in the SPA quietly 404s in production. Naming the route on both sides makes the rename a compile-time problem instead of a support ticket.

## Install

```bash
composer require innoboxrr/routes-to-json
php artisan vendor:publish --tag=routes-to-json-config
```

Configure the output path and which route groups to include, then run the command as part of your build.

---

Part of [Innobox R&R](https://github.com/innoboxrr) — 52 open-source packages extracted from production work. **[innobox.systems](https://innobox.systems)**
