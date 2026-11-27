# Changelog

## 2.1.1

- **Se audita con `larapack-generator` 9.** La dependencia de desarrollo
  pedía `^7.0` y pasa a `^9.0`, la línea base del ecosistema. Para quien
  instala el paquete no cambia nada: `require-dev` no viaja a la aplicación.
  Aquí el generador sólo lo usa la CI para `larapack:audit`, que ahora compara
  contra la línea base de hoy y no contra la que traía la 7.10.3.

  El paquete no usa ninguna clase del generador, y el `composer.json` del
  generador es el mismo de la 7.10.3 a la 9.1.0: el resto de lo que se resuelve
  no cambia. Se fija `^9.0` en vez de ampliar a `^7.0 || ^8.0 || ^9.0` porque en
  desarrollo no hay consumidor al que dejarle la 7, y el rango ancho sólo
  serviría para que la CI resolviera un generador viejo.

## 2.1.0

Correcciones para instalarlo en una aplicación Laravel 13 nueva, con un SPA en
Vue o en React.

- **Las rutas sin nombre ya no salen en el JSON.** Caían todas bajo la clave
  `""` y se pisaban entre sí; desde el frontend solo se pide una ruta por su
  nombre.
- **Una ruta de salida relativa se resuelve contra la raíz del proyecto**
  (`base_path()`), no contra el directorio de trabajo:
  `JSON_ROUTES_FILE=resources/react/assets/json/routes.json` funciona también
  desde `Artisan::call` en una petición o un job.
- **Un `JSON_ROUTES_FILE=` vacío usa la ruta por defecto**,
  `resources/vue/assets/json/routes.json`, la misma que declara la
  configuración. El comando tenía otra (`resources/json/routes.json`) y con la
  cadena vacía fallaba al escribir.
- **`File` se importa desde `Illuminate\Support\Facades\File`** en lugar de
  depender del alias global.
- **README corregido.** El comando es `route:json` (no `routes:json`) y el tag de
  publicación es `config` (no `routes-to-json-config`). Documenta la ruta de
  salida y cómo apuntarla a un build de React.
- **Tests de comportamiento** en `tests/Feature`: las rutas con nombre salen con
  su URI, las que no tienen nombre se saltan, se respeta la ruta configurada,
  relativa o absoluta, y el directorio se crea si no existe.

### Contrato

Sin cambios en el comando `route:json`, en la clave `routes-to-json.path`, en la
variable `JSON_ROUTES_FILE` ni en el tag `config`. El único cambio visible en el
JSON es que desaparece la clave `""`.
