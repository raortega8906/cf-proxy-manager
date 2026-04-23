<h1 align="center">☁ CF Proxy Manager</h1>

<p align="center">
  Gestión automatizada del proxy de Cloudflare para dominios afectados por los bloqueos de IP de LaLiga en España.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/versión-1.0.0-blue?style=flat-square" />
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/Cloudflare-API-F38020?style=flat-square&logo=cloudflare&logoColor=white" />
  <img src="https://img.shields.io/badge/Licencia-MIT-green?style=flat-square" />
</p>

---

## 📖 ¿Qué es esto?

Desde LaLiga, y apoyándose en una sentencia del Juzgado de lo Mercantil nº 6 de Barcelona, se impulsó una estrategia para combatir la piratería basada en solicitar a los operadores españoles el bloqueo de rangos completos de IPs asociados a Cloudflare durante los días de partido. Una medida que, aunque busca ser efectiva, puede resultar demasiado amplia en su aplicación.

El efecto es bastante claro: sitios web sin relación con el fútbol pirata —desde instituciones como la Real Academia Española hasta startups, medios locales, herramientas educativas o proyectos personales— pueden verse afectados al compartir esos rangos de IP. Como consecuencia, algunos servicios experimentan interrupciones puntuales durante los fines de semana.

En este contexto surge **CF Proxy Manager**. Esta herramienta automatiza la desactivación del proxy de Cloudflare (la conocida “nube naranja”) en los dominios potencialmente afectados antes del inicio de los partidos, reactivándolo una vez finalizan. Al exponer temporalmente la IP real del servidor, se evita el bloqueo por rango sin necesidad de intervención manual constante. Además, facilita la gestión de renovaciones de certificados SSL que requieren desactivar el proxy de forma puntual.

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
- **Gestión manual de proxy** — desactiva o activa manualmente el proxy de un sitio
- **Automatización LaLiga** — un cron diario consulta los partidos de La Liga en football-data.org y crea los schedules automáticamente
- **Automatización SSL** — desactiva el proxy para la ventana del reto HTTP-01, lo reactiva y programa la siguiente renovación
- **Logs de proxy** — registro completo de cada cambio de proxy con razón, estado y timestamp
- **Notificación por email** — envío automático cada mañana cuando se detectan partidos de LaLiga en el día, incluyendo la ventana de desactivación programada, los partidos con sus horarios y los dominios que se verán afectados
- **Exportación de logs** — descarga los logs como archivo `.xlsx` con formato `YYYYMMDD-logs-cfpm.xlsx`
- **Controles masivos** — activa o desactiva todos los proxies a la vez desde el dashboard
- **Almacenamiento cifrado** — los Zone IDs y DNS Record IDs de Cloudflare se cifran en reposo con AES-256-CBC

---

## 🏗 Estructura del proyecto ( MVC + Services + Commands )

```
app/
├── Console/Commands/
│   ├── AddAutomaticScheduleMatchCommand.php   # Consulta los partidos de LaLiga del día y crea schedules (cada día a las 00:00)
│   ├── CheckSiteSslCommand.php                # Crea los schedules con el ssl_next_renewal del sitio (cada día a las 00:05)
│   │── CheckSslRenewalsSchedulesCommand.php   # Procesa los schedules SSL (cada minuto)
│   │── ProcessProxySchedulesCommand.php       # Procesa los schedules de LaLiga (cada minuto)
│   └── SyncProxyStatusCommand.php             # Evita el N+1 actualizando el estado del proxy de los sitios (cada dos 2 minutos)
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
00:00     →  AddAutomaticScheduleMatchCommand se ejecuta
              Consulta football-data.org con los partidos de LaLiga del día
              
              Si hay un solo partido:
              Crea un ProxySchedule: disable_at = partido - 1h, enable_at = partido + 3h
              
              Si hay varios partidos:
              Crea un ProxySchedule por partido con ventanas individuales
              El último partido tiene enable_at = partido + 3h, los anteriores + 2h
              
              En ambos casos verifica que no exista ya un schedule para ese partido
              Envía email de notificación con los partidos y dominios afectados

Cada min  →  ProcessProxySchedulesCommand se ejecuta
              Filtra schedules de tipo laliga_match en estado pending o active
              
              Si disable_at <= ahora y estado pending:
              Llama a la API de Cloudflare para desactivar el proxy en los dominios afectados
              Actualiza el estado del schedule a active
              
              Si enable_at <= ahora y estado active:
              Reactiva el proxy en todos los dominios afectados
              Actualiza el estado del schedule a completed
```

### Flujo de renovación SSL
```
00:05     →  CheckSiteSslCommand se ejecuta
              Busca sitios con ssl_auto_renewal = true y ssl_next_renewal = hoy
              Agrupa todos los sitios que renuevan hoy en un único schedule
              Actualiza ssl_next_renewal +3 meses en cada sitio afectado
              Verifica que no exista ya un schedule SSL para hoy antes de crear
              Crea un ProxySchedule: disable_at = 02:00, enable_at = 08:00

Cada min  →  CheckSslRenewalsSchedulesCommand se ejecuta
              Filtra schedules de tipo ssl_renewal en estado pending o active
              
              Si disable_at <= ahora y estado pending:
              Desactiva el proxy → el reto HTTP-01 de ACME puede llegar al servidor
              Actualiza el estado del schedule a active
              
              Si enable_at <= ahora y estado active:
              Reactiva el proxy en todos los dominios afectados
              Actualiza el estado del schedule a completed
```

### Sincronización de estado
```
Cada 2 min →  SyncProxyStatusCommand se ejecuta
               Consulta la API de Cloudflare para cada dominio registrado
               Actualiza proxy_enabled en base de datos con el estado real
               Mantiene el dashboard coherente sin bloquear al usuario
```

### Resumen de comandos programados

| Comando | Frecuencia | Propósito |
|---|---|---|
| `app:add-automatic-schedule-match-command` | Diario 00:00 | Consulta partidos y crea schedules LaLiga |
| `app:check-site-ssl-command` | Diario 00:05 | Detecta renovaciones SSL del día y crea schedules |
| `app:process-proxy-schedules-command` | Cada minuto | Ejecuta desactivaciones y reactivaciones LaLiga |
| `app:check-ssl-renewals-schedules-command` | Cada minuto | Ejecuta desactivaciones y reactivaciones SSL |
| `app:sync-proxy-status-command` | Cada 2 minutos | Sincroniza estado real de Cloudflare con la BD |

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

- Todas las rutas privadas requieren autenticación (Laravel Breeze)
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

### Requisitos (PHP recomendado para actualizar a laravel 13)

- PHP 8.3+
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

| Campo | Valor                |
|---|----------------------|
| Email | `manager@cfproxy.es` |
| Contraseña | `laravel2026`        |

### Scheduler

**Desarrollo local** — ejecuta este comando en la terminal para simular el cron:
```bash
php artisan schedule:work
```

---

## 🗺 Próximas versiones

CF Proxy Manager es un MVP funcional. Estas son las funcionalidades planificadas para versiones futuras:

### Notificaciones y alertas
- **Slack** — notificaciones en tiempo real cuando un proxy se desactiva o reactiva, integrables con Slack
- **Alertas de fallo** — email automático cuando un schedule falla, cuando Cloudflare devuelve error o cuando un dominio no responde tras reactivar el proxy

### Gestión multi-cuenta y equipos
- **Multi-cuenta Cloudflare** — gestionar dominios de distintas cuentas Cloudflare desde un único panel, ideal para agencias con múltiples clientes
- **Grupos de dominios** — agrupar sitios por cliente o proyecto y aplicar acciones masivas, schedules y configuraciones por grupo
- **Multi-usuario con roles** — sistema de permisos con roles admin, editor y solo lectura para equipos de agencia

### Analítica y visibilidad
- **Histórico de disponibilidad** — gráfico temporal por dominio mostrando cuándo estuvo activo o desactivado el proxy, con correlación de eventos LaLiga y SSL

### Integraciones y extensibilidad
- **Soporte multi-proveedor DNS** — extensión del sistema a otros proveedores DNS más allá de Cloudflare
- **API REST propia** — endpoints documentados para integrar CF Proxy Manager con sistemas externos, pipelines CI/CD o automatizaciones propias

---

## 📌 Historial de versiones

| Versión   | Descripción                                                                  |
|-----------|------------------------------------------------------------------------------|
| `v1.0.0`✅ | MVP inicial — LaLiga, SSL, logs, dashboard |
| `v1.1.0`  | Alertas por email de fallos + notificaciones Slack                           |
| `v1.2.0`  | Multi-cuenta Cloudflare                                                      |
| `v1.3.0`  | Grupos de dominios + roles de usuario                                        |
| `v2.0.0`  | API REST + soporte multi-proveedor DNS                                 |

> El proyecto sigue **Semantic Versioning**. El número mayor cambia con cambios arquitectónicos, el del medio con nuevas funcionalidades y el menor con fixes y mejoras.

---

## 📄 Licencia

Este proyecto es software de código abierto licenciado bajo la licencia MIT.

© 2026 Rafael A. Ortega
