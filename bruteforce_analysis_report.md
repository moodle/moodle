# Análisis del entorno Moodle

## Versión
- Moodle 4.5.2+ (Build: 20250227) según `version.php`.
- No se encontró `config.php`; se asume configuración por defecto.

## Plugins instalados
- **Autenticación**: cas, db, email, ldap, lti, manual, mnet, nologin, none, oauth2, shibboleth, webservice.
- **Inscripción**: category, cohort, database, fee (tercero), flatfile, guest, imsenterprise, ldap, lti, manual, meta, mnet, paypal, self.
- **Módulos de actividad**: assign, bigbluebuttonbn, book, chat, choice, data, feedback, folder, forum, glossary, h5pactivity, imscp, label, lesson, lti, page, quiz, resource, scorm, subsection (tercero), survey, url, wiki, workshop.
- **Bloques**: accessreview, activity_modules, activity_results, admin_bookmarks, badges, blog_menu, blog_recent, blog_tags, calendar_month, calendar_upcoming, comments, completionstatus, course_list, course_summary, feedback, globalsearch, glossary_random, html, login, lp, mentees, mnet_hosts, myoverview, myprofile, navigation, news_items, online_users, private_files, recent_activity, recentlyaccessedcourses, recentlyaccesseditems, rss_client, search_forums, section_links, selfcompletion, settings, site_main_menu, social_activities, starredcourses, tag_flickr, tag_youtube, tags, timeline.
- **Filtros**: activitynames, algebra, codehighlighter, data, displayh5p, emailprotect, emoticon, glossary, mathjaxloader, mediaplugin, multilang, tex, urltolink.
- **Temas**: boost, classic.
- **Reportes**: backups, competency, completion, configlog, courseoverview, eventlist, infectedfiles, insights, log, loglive, outline, participation, performance, progress, questioninstances, security, stats, status, themeusage, usersessions.
- **Herramientas administrativas**: admin_presets, analytics, availabilityconditions, behat, brickfield, bruteforce (tercero), capability, cohortroles, componentlibrary, customlang, dataprivacy, dbtransfer, filetypes, generator, httpsreplace, installaddon, langimport, licensemanager, log, lp, lpimportcsv, lpmigrate, messageinbound, mfa, mobile, monitor, moodlenet, multilangupgrade, oauth2, phpunit, policy, profiling, recyclebin, replace, spamcleaner, task, templatelibrary, unsuproles, uploadcourse, uploaduser, usertours, xmldb.
- **Locales**: no se detectaron plugins en `local/`.

## Mecanismos de autenticación
- Dado que `config.php` no está presente, no se puede determinar cuáles están activos.
- Los plugins disponibles permiten autenticación manual, por correo, OAuth2, LDAP, CAS, SAML/Shibboleth, LTI y web services.

## Personalizaciones de login o seguridad
- No se hallaron overrides ni hooks personalizados que alteren el flujo de login.
- Plugins de terceros detectados: `enrol_fee`, `mod_subsection` y `tool_bruteforce`; ninguno interfiere actualmente con el proceso de autenticación.

## Roles y capacidades
- Se asume la estructura estándar de roles: administrador, manager, teacher, student.
- El plugin propuesto añadirá capacidades `tool/bruteforce:manage` y `tool/bruteforce:viewreports` para su administración.

## Recomendaciones y riesgos
- Implementar un sistema de protección contra fuerza bruta para reforzar la seguridad de autenticación.
- Mantener vigilancia sobre intentos fallidos de login y bloquear IPs/usuarios sospechosos.
- Definir claramente capacidades para administrar la herramienta y roles exentos.
- Documentar integraciones con listas blanca/negra y sistemas de notificación.

## Estado actual del plugin `tool_bruteforce`

- **Funcionalidades presentes**
  - Registro de intentos de login y bloqueos básicos por usuario o IP.
  - Límite diario de intentos por IP.
  - Página de administración mínima que lista bloqueos activos.
  - Script CLI para purgar bloqueos expirados y listar bloqueos.
  - API para consultar listas blanca/negra y bloqueos.
  - Tarea programada que purga bloqueos expirados.
  - Configuración inicial con umbrales y ventanas de bloqueo.
  - Gestión básica de listas blanca/negra desde UI y CLI.

- **Brechas respecto a la especificación**
  - **Críticas** pendientes: API pública extendida, notificaciones, desbloqueo manual, protección a administradores, pruebas automatizadas integrales.
  - **Importantes**: dashboard con historiales y tendencias, CLI extendido (import/export de listas ya disponible), bloqueo extendido por abuso, soporte de roles/contextos.
  - **Opcionales**: Geo-IP, CAPTCHA, rate limiting suave, reportes PDF.

- **Deficiencias de código**
  - Verificación de roles privilegiados antes de bloquear aún limitada.
  - Falta de sanitización explícita de IPs y campos de texto en la base de datos.
  - Cobertura de pruebas limitada.

- **Riesgos de seguridad**
  - Posible bloqueo de administradores legítimos.
  - Acumulación de datos en tablas sin purga automática efectiva.
  - Bloqueo o desbloqueo manual sin auditoría.

- **Sugerencias de refactorización y rendimiento**
  - Centralizar la lógica de verificación de bloqueos en una API dedicada.
  - Añadir índices y constraints a tablas de listas para evitar duplicados.
  - Implementar clases de servicio para separar lógica de presentación.

- **Casos de prueba necesarios**
  - Conteo de intentos fallidos y activación de bloqueos por usuario/IP.
  - Respeto de whitelist y blacklist.
  - Purga automática de bloqueos expirados.
  - API pública para consultar estado de bloqueo.
