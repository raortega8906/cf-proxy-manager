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

## 🚀 Instalación local

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

---

## ☁️ Despliegue en producción — CubePath + Coolify

CF Proxy Manager está desplegado en producción sobre infraestructura de **CubePath**, usando un VPS nano ubicado en Barcelona, España.

### Infraestructura

| Recurso | Detalle |
|---|---|
| Proveedor | CubePath |
| Servidor | `starlight-dragon-407` — Barcelona, Spain |
| Especificaciones | 1 vCPU · 2 GB RAM · 40 GB SSD |
| Coste estimado | ~$0.17 / día |
| URL producción | [https://cfproxy.caelix.es](https://cfproxy.caelix.es) |

### Coolify como plataforma de despliegue

Sobre el VPS se instaló **Coolify**, una plataforma self-hosted de tipo PaaS que gestiona el ciclo completo de despliegue sin necesidad de configurar nginx, Docker ni servicios manualmente. Coolify actúa como un Heroku propio dentro del servidor.

La aplicación está configurada con **Build Pack: Dockerfile**, lo que permite un control total sobre el entorno de ejecución. El proyecto usa la imagen `serversideup/php:8.3-fpm-nginx`, que incluye PHP-FPM y nginx preconfigurados específicamente para Laravel.

El dominio `https://cfproxy.caelix.es` apunta al servidor mediante un registro DNS tipo A. Coolify gestiona el certificado SSL automáticamente a través de Let's Encrypt vía Traefik, que actúa como proxy inverso entre el dominio y el contenedor.

La base de datos MySQL corre también en el mismo servidor como un contenedor gestionado por Coolify, dentro de la misma red Docker interna, lo que permite la conexión entre app y base de datos sin exponer puertos al exterior.

### Deploy automático desde GitHub

Coolify está conectado al repositorio de GitHub. Cada vez que se hace un push a la rama `main`, Coolify detecta el cambio automáticamente, construye la nueva imagen Docker y realiza un **rolling update** sin downtime: levanta el nuevo contenedor, comprueba que está healthy, y solo entonces elimina el anterior. El comando de post-deploy ejecuta `php artisan migrate --force` automáticamente en cada despliegue.
```
Push a main
    → Coolify detecta el cambio
    → Build de la imagen Docker
    → Rolling update sin downtime
    → Post-deploy: php artisan migrate --force
    → Aplicación actualizada en producción
```

### Scheduler en producción

El scheduler de Laravel está configurado como una **Scheduled Task** dentro de Coolify, con frecuencia `* * * * *` (cada minuto). Coolify gestiona la ejecución del cron directamente sobre el contenedor, registra cada ejecución con timestamp de inicio y fin, y permite descargar los logs de cada pasada. No es necesario configurar nada en el crontab del sistema operativo.

### Dockerfile de producción

El despliegue completo se consigue con un Dockerfile de menos de 15 líneas. La imagen base `serversideup/php` incluye nginx y PHP-FPM preconfigurados para Laravel, lo que elimina toda la configuración manual de servidor:
```dockerfile
FROM serversideup/php:8.3-fpm-nginx

USER root

RUN install-php-extensions gd

COPY --chown=www-data:www-data . /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache
```

### Rollback instantáneo

Coolify mantiene un histórico de imágenes Docker construidas anteriormente. Ante cualquier problema en producción, es posible revertir a cualquier versión anterior con un solo clic sin necesidad de hacer ningún push ni intervención en el código. Cada imagen queda identificada por su SHA de commit y timestamp exacto de construcción.

### Valoración del stack de despliegue

**Ventajas**

- **Deploy automático desde Git** — cada push a `main` desencadena el pipeline completo sin intervención manual. En menos de 3 minutos el cambio está en producción. (Se puede escoger que rama desplegar)
- **Rolling update sin downtime** — Coolify levanta el nuevo contenedor, verifica que está healthy y solo entonces elimina el anterior. La aplicación nunca deja de responder durante un despliegue.
- **Rollback con un clic** — las imágenes anteriores se conservan localmente. Revertir a una versión previa es instantáneo, sin necesidad de recompilar ni hacer git revert.
- **SSL gestionado automáticamente** — Traefik obtiene y renueva el certificado Let's Encrypt sin configuración adicional en cuanto el dominio apunta al servidor.
- **Scheduler integrado** — el cron de Laravel se configura desde el panel de Coolify como una Scheduled Task, con logs de cada ejecución y sin tocar el crontab del sistema operativo.
- **Dockerfile mínimo** — gracias a `serversideup/php`, toda la configuración de nginx, PHP-FPM y extensiones se resuelve en pocas líneas. No hay que mantener ficheros de configuración de servidor.
- **Todo en un solo VPS** — app, base de datos MySQL y scheduler corren en el mismo servidor dentro de la red Docker interna de Coolify. Sin latencia entre servicios, sin costes adicionales.
- **Coste real** — el VPS nano de CubePath con 1 vCPU, 2 GB RAM y 40 GB SSD tiene un coste de aproximadamente $0.17 al día, suficiente para un MVP en producción con tráfico moderado.

**Consideraciones**

- Un único VPS sin réplica es un punto único de fallo. Para un entorno más crítico lo natural sería añadir un segundo servidor o un balanceador de carga, ambos disponibles en CubePath.
- El plan de 2 GB de RAM es suficiente para este caso de uso, pero proyectos con mayor carga de trabajo deberían dimensionar el servidor en consecuencia antes de ir a producción.

---

## 📄 Licencia

Este proyecto es software de código abierto licenciado bajo la [licencia MIT](https://opensource.org/licenses/MIT).