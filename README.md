<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel">
</p>

<h1 align="center">☁ CF Proxy Manager</h1>

<p align="center">
  Gestión automatizada del proxy de Cloudflare para dominios afectados por los bloqueos de IP de LaLiga en España.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/Cloudflare-API-F38020?style=flat-square&logo=cloudflare&logoColor=white" />
  <img src="https://img.shields.io/badge/Licencia-MIT-green?style=flat-square" />
</p>

---

## 📖 ¿Qué es esto?

**Javier Tebas**, presidente de LaLiga, y amparado en una sentencia del Juzgado de lo Mercantil nº 6 de Barcelona, decidió que la mejor manera de combatir la piratería era ordenar a los operadores españoles bloquear rangos enteros de IPs de Cloudflare durante los días de partido. Una solución tan elegante como tumbar todo un edificio para matar una cucaracha en el quinto piso.

El resultado es predecible: webs que no tienen absolutamente nada que ver con el fútbol pirata — desde la RAE hasta startups, medios locales, herramientas educativas y proyectos personales — caen bloqueadas cada fin de semana porque comparten rango de IP con algún dominio en la lista de Tebas. "Brillante".

**CF Proxy Manager** nació de esa realidad. Desactiva automáticamente el proxy de Cloudflare (la nube naranja) en los dominios afectados antes de que empiecen los partidos, y lo reactiva cuando terminan. Al exponer temporalmente la IP real del servidor, el dominio escapa del bloqueo por rango sin necesidad de intervención manual cada fin de semana. También gestiona las renovaciones de certificados SSL que requieren desactivar el proxy de forma puntual.

### El problema en una captura

Cuando un dominio queda atrapado en un bloqueo, los visitantes ven esto:

> *"El acceso a la presente dirección IP ha sido bloqueado en cumplimiento de lo dispuesto en la Sentencia de 18 de diciembre de 2024, dictada por el Juzgado de lo Mercantil nº 6 de Barcelona en el marco del procedimiento ordinario instado por la Liga Nacional de Fútbol Profesional..."*

CF Proxy Manager convierte ese problema en algo que se gestiona solo.

Dejando claro, que no estoy de acuerdo con estos sitios inapropiados por lo cual realmente bloquean las ips, pero tampoco, con esta solución que da Javier Tebas. Por eso, decidí implementar esta idea.

Espero que les sea de ayuda.

---

## ✨ Funcionalidades

- **Dashboard** — estado en tiempo real de todos los dominios gestionados, sincronizado directamente desde la API de Cloudflare
- **Gestión de sitios** — añade dominios por Zone ID de Cloudflare; la app descubre automáticamente el registro DNS
- **Schedules de proxy** — crea ventanas de tiempo para desactivar/reactivar el proxy, de forma manual o automática
- **Automatización LaLiga** — un cron diario consulta los partidos de La Liga en football-data.org y crea los schedules automáticamente
- **Automatización SSL** — desactiva el proxy para la ventana del reto HTTP-01, lo reactiva y programa la siguiente renovación
- **Logs de proxy** — registro completo de cada cambio de proxy con razón, estado y timestamp
- **Exportación de logs** — descarga los logs como archivo `.xlsx` con formato `YYYYMMDD-logs-cfpm.xlsx`
- **Controles masivos** — activa o desactiva todos los proxies a la vez desde el dashboard
- **Almacenamiento cifrado** — los Zone IDs y DNS Record IDs de Cloudflare se cifran en reposo con AES-256-CBC

---

## 🏗 Estructura del proyecto ( MVC + Services + Commands )

```
app/
├── Console/Commands/
│   ├── AddAutomaticScheduleMatchCommand.php   # Consulta los partidos de LaLiga del día y crea schedules
│   ├── ProcessProxySchedulesCommand.php       # Procesa los schedules de partidos (cada minuto)
│   └── CheckSslRenewalsSchedulesCommand.php   # Procesa los schedules de renovación SSL (cada minuto)
├── Exports/
│   └── ProxyLogsExport.php                    # Exportación Excel de los logs
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── ProxySiteController.php
│   ├── ProxyScheduleController.php
│   └── ProxyLogController.php
├── Http/Requests/                             # Validación de formularios (Store/Update para Sites y Schedules)
├── Mail/
│   ├── ScheduleAutomaticLaLiga.php            # Notificaciones por email con los partidos de ligas si hay
├── Models/
│   ├── ProxySite.php
│   ├── ProxySchedule.php
│   └── ProxyLog.php
└── Services/
    ├── CloudflareService.php                  # Integración con la API de Cloudflare
    ├── LaligaService.php                      # Integración con la API de football-data.org
    ├── ProxyLogService.php                    # Escritura centralizada de logs
    └── ProxyScheduleService.php               # Creación automática de schedules
config/
├── cloudflare.php
└── laliga.php
database/migrations/
routes/
├── web.php
└── console.php                                # Comandos programados
```

---

## ⚙️ Cómo funciona

### Flujo en día de partido de LaLiga

```
00:00 AM  →  AddAutomaticScheduleMatchCommand se ejecuta
             Consulta football-data.org con los partidos de La Liga del día
             Crea un ProxySchedule: disable_at = primerPartido - 1h, enable_at = últimoPartido + 3h

Cada min  →  ProcessProxySchedulesCommand se ejecuta
             Encuentra schedules pendientes donde disable_at <= ahora
             Llama a la API de Cloudflare para desactivar el proxy en los dominios afectados
             Actualiza el estado del schedule a 'active'

             Más tarde: encuentra schedules activos donde enable_at <= ahora
             Reactiva el proxy en todos los dominios afectados
             Actualiza el estado del schedule a 'completed'
```

### Flujo de renovación SSL

```
Manual    →  Crea un schedule ssl_renewal con la ventana deseada

Cada min  →  CheckSslRenewalsSchedulesCommand se ejecuta
             Desactiva el proxy → el reto HTTP-01 puede llegar al servidor
             Reactiva el proxy tras la ventana
             Actualiza ssl_next_renewal (+3 meses)
             Crea automáticamente el siguiente schedule ssl_renewal
```

---

## 🚀 Instalación

### Requisitos

- PHP 8.2+
- Composer
- MySQL 8.0+ o compatible
- Cuenta de Cloudflare con token de API
- Clave de API de football-data.org (plan gratuito: 100 req/día)

### Puesta en marcha

```bash
# Clonar el repositorio
git clone https://github.com/raortega8906/cf-proxy-manager.git
cd cf-proxy-manager

# Instalar dependencias
composer install

# Copiar el archivo de entorno
cp .env.example .env

# Generar la clave de aplicación
php artisan key:generate

# Ejecutar las migraciones
php artisan migrate

# Arrancar el servidor de desarrollo
php artisan serve
```

### Usuario de acceso

El proyecto incluye un seeder con un usuario administrador por defecto. Ejecútalo después de las migraciones:
```bash
php artisan db:seed
```

Credenciales de acceso:

| Campo | Valor |
|---|---|
| Email | `manager@cfproxy.es` |
| Contraseña | `laravel2026` |

### Scheduler

**Desarrollo local** — ejecuta este comando en la terminal para simular el cron:
```bash
php artisan schedule:work
```

**Producción** — añade esta única entrada al crontab del servidor. Laravel se encarga de gestionar internamente el resto de comandos programados:
```bash
* * * * * cd /ruta-del-proyecto && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📬 Notificaciones por email

CF Proxy Manager envía un email automático cada vez que se crea un schedule automático de LaLiga obtenidos de la API. El email incluye la ventana de desactivación programada, los partidos del día con sus horarios y los dominios que se verán afectados.

---

## 🔧 Configuración

### Variables de entorno

```env
# Aplicación
APP_KEY=                          # Generada por php artisan key:generate
APP_URL=https://tu-dominio.com

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cf_proxy_manager
DB_USERNAME=tu_usuario_db
DB_PASSWORD=tu_password_db

# Email
MAIL_MAILER=smtp
MAIL_HOST=tu_smtp_host
MAIL_PORT=587
MAIL_USERNAME=tu@email.com
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu@email.com
MAIL_FROM_NAME="${APP_NAME}"

# Cloudflare
CLOUDFLARE_API_TOKEN=             # Tu token de API de Cloudflare
CLOUDFLARE_API=https://api.cloudflare.com/client/v4

# API de LaLiga (football-data.org)
LALIGA_API_TOKEN=                 # Tu clave de API de football-data.org
LALIGA_API=https://api.football-data.org/v4/competitions/PD/matches
```

### Permisos del token de API de Cloudflare

Crea tu token en: **Cloudflare → My Profile → API Tokens → Create Token**

| Permiso | Requerido |
|---|---|
| Zone → DNS → Edit | ✅ |
| Zone → Zone → Read | ✅ |

Importante: También en la configuración del token se puede restringir para una única IP.

---

## 🗄 Esquema de base de datos

| Tabla | Propósito |
|---|---|
| `proxy_sites` | Dominios gestionados con Zone ID y Record ID de Cloudflare (cifrados) |
| `proxy_schedules` | Ventanas de tiempo para las operaciones de desactivar/reactivar proxy |
| `proxy_logs` | Registro de auditoría de cada cambio de proxy |

### Tipos de schedule

| Tipo | Creado por |
|---|---|
| `laliga_match` | Automáticamente por `AddAutomaticScheduleMatchCommand` o de forma manual |
| `ssl_renewal` | Manualmente, luego renovado automáticamente por `CheckSslRenewalsSchedulesCommand` |
| `manual` | Creado manualmente para operaciones puntuales |

### Estados de schedule

| Estado | Significado |
|---|---|
| `pending` | Esperando a que llegue `disable_at` |
| `active` | El proxy está desactivado, esperando a `enable_at` |
| `completed` | El proxy ha sido reactivado |
| `failed` | Se produjo un error |

---

## 🔒 Seguridad

- Todas las rutas requieren autenticación (Laravel Breeze)
- Los Zone IDs y DNS Record IDs de Cloudflare se cifran en reposo con AES-256-CBC nativo de Laravel (vinculado a `APP_KEY`)
- Los tokens de API se almacenan exclusivamente en `.env`, nunca en la base de datos
- Protección CSRF en todos los formularios
- En la configuración del token de Cloudflare se puede restringir para una única IP.

---

## 📦 Dependencias instaladas

| Paquete | Propósito |
|---|---|
| `laravel/breeze` | Scaffolding de autenticación |
| `maatwebsite/excel` | Exportación de logs a XLSX |

---

## 📄 Licencia

Este proyecto es software de código abierto licenciado bajo la [licencia MIT](https://opensource.org/licenses/MIT).