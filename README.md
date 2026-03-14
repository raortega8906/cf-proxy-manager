<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Cloudflare Proxy Manager

## Project structure

```
app/
├── Console/
│   ├── Commands/
│   │   ├── 
│   │   └── 
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── ProxySiteController.php
│   └── ProxyScheduleController.php
├── Models/
│   ├── ProxySite.php
│   ├── ProxySchedule.php
│   └── ProxyLog.php
├── Services/
│   └── 
config/
└── cloudflare.php
database/migrations/
resources/views/
routes/
└── web.php
```

---

## Configuration `.env`

```env
CLOUDFLARE_API_TOKEN=tu_api_token_aqui
CLOUDFLARE_EMAIL=tu@email.com
CLOUDFLARE_API=https://api.cloudflare.com/client/v4
CF_SSL_DOWNTIME_MINUTES=5
CF_MATCH_PRE_MINUTES=15
CF_MATCH_POST_MINUTES=30
```

---

## Cloudflare API Token Permissions

When creating the token in Cloudflare → My Profile → API Tokens → Create Token:

| Permit | Type |
|---|---|
| Zone → DNS → Edit | ✅ Necessary |
| Zone → Zone → Read | ✅ Necessary |

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
