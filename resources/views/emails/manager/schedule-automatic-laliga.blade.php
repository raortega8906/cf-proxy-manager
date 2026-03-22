<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Schedule LaLiga Automático</title>
  <style type="text/css">
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
    body { margin: 0 !important; padding: 0 !important; background-color: #f0f4f8; }
    @media only screen and (max-width: 620px) {
      .wrapper { width: 100% !important; }
      .content-block { padding: 24px 20px !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;">

<!-- WRAPPER -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f0f4f8;">
  <tr>
    <td align="center" style="padding:32px 16px;">

      <!-- CONTAINER -->
      <table class="wrapper" border="0" cellpadding="0" cellspacing="0" width="600" style="max-width:600px;">

        <!-- HEADER -->
        <tr>
          <td align="center" style="background-color:#060910;border-radius:14px 14px 0 0;padding:32px 36px;border-bottom:2px solid #00d4ff;">

            <!-- LOGO -->
            <table border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center" style="padding-bottom:16px;">
                  <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="background:linear-gradient(135deg,#00d4ff,#0066ff);border-radius:8px;width:36px;height:36px;text-align:center;vertical-align:middle;font-size:18px;line-height:36px;">
                        ☁
                      </td>
                      <td style="padding-left:10px;font-family:Arial,sans-serif;font-size:15px;font-weight:bold;color:#ffffff;letter-spacing:0.05em;">
                        CF Proxy Manager
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- BADGE -->
            <table border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center" style="padding-bottom:14px;">
                  <span style="display:inline-block;background-color:#1a0a05;border:1px solid rgba(255,107,53,0.4);color:#ff6b35;font-family:Arial,sans-serif;font-size:11px;letter-spacing:0.1em;text-transform:uppercase;padding:5px 16px;border-radius:100px;">
                    ⚽ Schedule LaLiga automático
                  </span>
                </td>
              </tr>
            </table>

            <!-- TITLE -->
            <table border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center">
                  <p style="font-family:Arial,sans-serif;font-size:22px;font-weight:bold;color:#ffffff;margin:0;line-height:1.3;">
                    Schedule creado para<br/>
                    <span style="color:#00d4ff;">{{ isset($laLiga[0]) ? \Carbon\Carbon::parse($laLiga[0]['datetime'])->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                  </p>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <!-- BODY -->
        <tr>
          <td class="content-block" style="background-color:#ffffff;padding:32px 36px;">

            <!-- INTRO -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td style="padding-bottom:28px;">
                  <p style="font-family:Arial,sans-serif;font-size:13px;color:#4a6285;line-height:1.8;margin:0;">
                    El sistema ha detectado <strong style="color:#1a2a3a;">{{ count($laLiga) }} partido(s) de LaLiga</strong> hoy y ha creado automáticamente un schedule de desactivación de proxy para los dominios afectados.
                  </p>
                </td>
              </tr>
            </table>

            <!-- SECTION TITLE: SCHEDULE -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td style="padding-bottom:10px;">
                  <p style="font-family:Arial,sans-serif;font-size:10px;font-weight:bold;color:#1a2a3a;letter-spacing:0.15em;text-transform:uppercase;margin:0;">
                    ⏱ Ventana de desactivación programada
                  </p>
                </td>
              </tr>
            </table>

            <!-- SCHEDULE BOX -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:24px;">
              <tr>
                <td style="background-color:#060910;border-radius:12px;padding:24px 28px;border:1px solid #1e3a5f;">

                  <p style="font-family:Arial,sans-serif;font-size:10px;color:#4a6285;letter-spacing:0.12em;text-transform:uppercase;margin:0 0 14px 0;">Detalles del schedule</p>

                  <!-- PROXY OFF -->
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:10px;">
                    <tr>
                      <td width="90" style="font-family:Arial,sans-serif;font-size:10px;color:#4a6285;letter-spacing:0.08em;text-transform:uppercase;vertical-align:middle;">
                        Proxy OFF
                      </td>
                      <td style="font-family:'Courier New',monospace;font-size:13px;color:#ff6b35;vertical-align:middle;">
                        {{ \Carbon\Carbon::parse($laLiga[0]['datetime'])->subHour()->format('d/m/Y H:i') }} h
                      </td>
                    </tr>
                  </table>

                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:10px;">
                    <tr><td style="background-color:#1a2a3a;height:1px;font-size:1px;line-height:1px;">&nbsp;</td></tr>
                  </table>

                  <!-- PROXY ON -->
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:10px;">
                    <tr>
                      <td width="90" style="font-family:Arial,sans-serif;font-size:10px;color:#4a6285;letter-spacing:0.08em;text-transform:uppercase;vertical-align:middle;">
                        Proxy ON
                      </td>
                      <td style="font-family:'Courier New',monospace;font-size:13px;color:#00d4ff;vertical-align:middle;">
                        {{ \Carbon\Carbon::parse(end($laLiga)['datetime'])->addHours(3)->format('d/m/Y H:i') }} h
                      </td>
                    </tr>
                  </table>

                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:10px;">
                    <tr><td style="background-color:#1a2a3a;height:1px;font-size:1px;line-height:1px;">&nbsp;</td></tr>
                  </table>

                  <!-- ESTADO Y DOMINIOS -->
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:8px;">
                    <tr>
                      <td width="90" style="font-family:Arial,sans-serif;font-size:10px;color:#4a6285;letter-spacing:0.08em;text-transform:uppercase;vertical-align:middle;">
                        Estado
                      </td>
                      <td style="font-family:Arial,sans-serif;font-size:13px;color:#ffd60a;vertical-align:middle;">
                        Pendiente
                      </td>
                    </tr>
                  </table>
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td width="90" style="font-family:Arial,sans-serif;font-size:10px;color:#4a6285;letter-spacing:0.08em;text-transform:uppercase;vertical-align:middle;">
                        Dominios
                      </td>
                      <td style="font-family:Arial,sans-serif;font-size:13px;color:#e2e8f0;vertical-align:middle;">
                        {{ count($domains) }} afectados
                      </td>
                    </tr>
                  </table>

                </td>
              </tr>
            </table>

            <!-- SECTION TITLE: PARTIDOS -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td style="padding-bottom:10px;">
                  <p style="font-family:Arial,sans-serif;font-size:10px;font-weight:bold;color:#1a2a3a;letter-spacing:0.15em;text-transform:uppercase;margin:0;">
                    📋 Partidos del día
                  </p>
                </td>
              </tr>
            </table>

            <!-- MATCH LIST -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:24px;">
              @foreach($laLiga as $match)
              <tr>
                <td style="padding-bottom:8px;">
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 16px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                          <tr>
                            <td style="font-family:Arial,sans-serif;font-size:13px;font-weight:bold;color:#1a2a3a;">
                              {{ $match['home'] }}
                              <span style="font-weight:normal;color:#4a6285;font-size:11px;"> vs </span>
                              {{ $match['away'] }}
                            </td>
                            <td align="right" style="white-space:nowrap;">
                              <span style="display:inline-block;background-color:#060910;color:#e2e8f0;font-family:'Courier New',monospace;font-size:11px;padding:3px 10px;border-radius:6px;">
                                {{ \Carbon\Carbon::parse($match['datetime'])->format('H:i') }} h
                              </span>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              @endforeach
            </table>

            <!-- SECTION TITLE: DOMINIOS -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td style="padding-bottom:10px;">
                  <p style="font-family:Arial,sans-serif;font-size:10px;font-weight:bold;color:#1a2a3a;letter-spacing:0.15em;text-transform:uppercase;margin:0;">
                    🌐 Dominios afectados
                  </p>
                </td>
              </tr>
            </table>

            <!-- DOMAIN LIST -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:24px;">
              @foreach($domains as $domain)
              <tr>
                <td style="padding-bottom:6px;">
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px 14px;">
                        <table border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td width="14" style="vertical-align:middle;">
                              <div style="width:7px;height:7px;border-radius:50%;background-color:#ff6b35;font-size:1px;line-height:1px;">&nbsp;</div>
                            </td>
                            <td style="padding-left:8px;font-family:'Courier New',monospace;font-size:12px;color:#1a2a3a;vertical-align:middle;">
                              {{ $domain }}
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              @endforeach
            </table>

            <!-- ALERT DETECCIÓN -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:28px;">
              <tr>
                <td style="background-color:#fffbeb;border:1px solid #fcd34d;border-radius:10px;padding:16px 20px;">
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td width="30" style="vertical-align:top;font-size:18px;padding-top:2px;">
                        🛡
                      </td>
                      <td style="padding-left:10px;font-family:Arial,sans-serif;font-size:12px;color:#78350f;line-height:1.7;vertical-align:top;">
                        <strong style="color:#451a03;">Detección inteligente activa.</strong> Antes de desactivar cada dominio, el sistema comprobará si realmente está siendo bloqueado. Si un dominio responde con normalidad, el proxy permanecerá activo y la omisión quedará registrada en los logs.
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- DIVIDER -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:28px;">
              <tr><td style="background-color:#e2e8f0;height:1px;font-size:1px;line-height:1px;">&nbsp;</td></tr>
            </table>

            <!-- CTA -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:24px;">
              <tr>
                <td align="center">
                  <a href="{{ config('app.url') }}/dashboard" style="display:inline-block;background-color:#00d4ff;color:#060910;font-family:Arial,sans-serif;font-size:12px;font-weight:bold;letter-spacing:0.08em;text-transform:uppercase;padding:13px 32px;border-radius:8px;text-decoration:none;">
                    Ver en el dashboard →
                  </a>
                </td>
              </tr>
            </table>

            <!-- NOTE -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td align="center">
                  <p style="font-family:Arial,sans-serif;font-size:11px;color:#94a3b8;line-height:1.7;margin:0;">
                    Si necesitas modificar este schedule puedes hacerlo desde el panel antes de que se ejecute.<br/>
                    Los logs de cada acción quedan registrados en tiempo real.
                  </p>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td align="center" style="background-color:#060910;border-radius:0 0 14px 14px;padding:20px 36px;border-top:1px solid #1a2a3a;">
            <p style="font-family:Arial,sans-serif;font-size:11px;color:#4a6285;line-height:1.7;margin:0;">
              Este email fue generado automáticamente por
              <a href="{{ config('app.url') }}" style="color:#00d4ff;text-decoration:none;">CF Proxy Manager</a>.<br/>
              Enviado a {{ $email }} · {{ now()->format('d/m/Y H:i') }}
            </p>
          </td>
        </tr>

      </table>
      <!-- END CONTAINER -->

    </td>
  </tr>
</table>
<!-- END WRAPPER -->

</body>
</html>