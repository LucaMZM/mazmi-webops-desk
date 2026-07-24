# Mazmi WebOps Desk

Panel de gestión de mantenimiento web para agencias, freelancers técnicos y pequeñas empresas.

Mazmi WebOps Desk centraliza clientes, webs, tickets, tareas de mantenimiento, vencimientos de dominio/hosting y reportes mensuales en una aplicación full-stack construida con Laravel, Vue 3 e Inertia.

## Qué problema resuelve

En muchos equipos pequeños, el mantenimiento web acaba repartido entre emails, hojas de cálculo, chats y notas sueltas. Eso complica saber qué webs necesitan atención, qué tickets siguen abiertos, quién tiene asignada cada tarea o qué se ha hecho durante el mes.

Esta aplicación organiza ese flujo de trabajo en un único panel. No almacena contraseñas, tokens ni credenciales reales de webs; esos datos deben gestionarse siempre en herramientas específicas para secretos.

## Estado del proyecto

Aplicación de demostración técnica con CRUDs completos, roles, seeders, dashboard, validaciones, documentación y tests. No es un producto comercial terminado.

## Funcionalidades

- Autenticación con Laravel Breeze.
- Roles de administrador, técnico y cliente.
- Dashboard con métricas operativas y datos reales de la base de datos.
- Gestión de clientes.
- Inventario de webs, tecnologías, planes y vencimientos.
- Tickets con prioridad, estado, cliente, web y técnico asignado.
- Tareas de mantenimiento programadas, atrasadas y completadas.
- Reportes mensuales con resumen, métricas y recomendaciones.
- Filtros mediante query string.
- Validación con Form Requests.
- Policies para aislar datos por rol y cliente.
- Seeders con datos ficticios reproducibles.
- Interfaz responsive con Tailwind CSS.

## Stack técnico

| Área | Tecnología |
|---|---|
| Backend | PHP 8.3+, Laravel 13 |
| Frontend | JavaScript, Vue 3 |
| Navegación | Inertia.js |
| Estilos | Tailwind CSS |
| Base de datos | MySQL 8 |
| Assets | Vite |
| Tests | PHPUnit |

SQLite está soportado para demo local rápida y CI, pero MySQL es la base de datos principal recomendada.

## Instalación

### Requisitos

- PHP 8.3 o superior.
- Composer 2.
- Node.js 20 o superior.
- npm.
- MySQL 8 recomendado.

### 1. Clonar el repositorio

```bash
git clone https://github.com/LucaMZM/mazmi-webops-desk.git
cd mazmi-webops-desk
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Preparar entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar base de datos

Para MySQL:

```sql
CREATE DATABASE mazmi_webops_desk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Variables principales en `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mazmi_webops_desk
DB_USERNAME=root
DB_PASSWORD=
```

Para una demo rápida con SQLite:

```bash
touch database/database.sqlite
```

Y en `.env`:

```dotenv
DB_CONNECTION=sqlite
```

### 5. Migrar y cargar datos demo

```bash
php artisan migrate:fresh --seed
```

### 6. Ejecutar la aplicación

En una terminal:

```bash
php artisan serve
```

En otra terminal:

```bash
npm run dev
```

Abre `http://127.0.0.1:8000`.

Para generar assets de producción:

```bash
npm run build
```

## Usuarios demo

Contraseña común: `password`

| Rol | Email | Alcance |
|---|---|---|
| Administrador | `admin@webops.test` | Gestión completa |
| Técnico | `tech1@webops.test` | Tickets y tareas asignadas |
| Técnico | `tech2@webops.test` | Tickets y tareas asignadas |
| Cliente | `cliente1@webops.test` | Datos de su empresa |
| Cliente | `cliente2@webops.test` | Datos de su empresa |
| Cliente | `cliente3@webops.test` | Datos de su empresa |

Los datos del seeder son ficticios.

## Estructura

```text
app/Http/Controllers     Controladores por módulo
app/Http/Requests        Validaciones
app/Models               Modelos Eloquent
app/Policies             Autorización por recurso
database/migrations      Esquema de base de datos
database/seeders         Datos demo
resources/js/Layouts     Layouts Inertia
resources/js/Pages       Pantallas Vue
resources/js/Components  Componentes reutilizables
docs                     Documentación técnica
tests                    Pruebas automatizadas
```

## Tests

```bash
php artisan test
npm run build
```

El workflow de GitHub Actions ejecuta instalación, migraciones con SQLite, tests y build frontend.

## Documentación

- [Esquema de base de datos](docs/database-schema.md)
- [Funcionalidades y permisos](docs/features.md)

## Próximas mejoras

- Exportación PDF de reportes.
- Notificaciones por email.
- Adjuntos en tickets.
- Calendario visual de mantenimiento.
- Más tests de interfaz y accesibilidad.
- API REST opcional.
- Demo online controlada.

## Autor

**Luca Mazmishvili**  
Desarrollador web  
Valencia, España
