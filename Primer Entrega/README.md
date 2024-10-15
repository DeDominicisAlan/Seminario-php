Seminario de PHP, React, y API Rest
===================================

Librerias Externas:

JimTools - JWT-Auth: Middleware de autenticaciones para tokens JSON

Tuve problemas con la compatibilidad del autenticador jwt de tuupola, debido a que actualice las librerias y tuupola esta descontinuado.
Por ende utilice JimTools, que esta actualizada.

FireBase - JWT: Biblioteca para codificar y decodificar tokens JSON.

===================================
## Configuración inicial

1. Crear archivo `.env` a partir de `.env.dist`

```bash
cp .env.dist .env
```

2. Crear volumen para la base de datos

```bash
docker volume create seminariophp
```

donde *seminariophp* es el valor de la variable `DB_VOLUME`

## Iniciar servicios

```bash
docker compose up -d
```

## Terminar servicios

```bash
docker compose down -v
```

## Eliminar base de datos

```bash
docker volume rm seminariophp
```
