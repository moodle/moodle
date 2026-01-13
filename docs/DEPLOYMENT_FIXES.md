# Moodle Deployment Fixes & Production Deployment Guide

This document summarizes the fixes applied to resolve the "courses not displaying" issue and provides instructions for deploying from test to production.

---

## Summary of Issues Fixed

### 1. Nginx Host Extraction Script (Critical)
**Problem:** Nginx failed to start with `invalid number of arguments in "server_name" directive`.

**Root Cause:** The `nginx-extract-host.sh` script was mounted as a `.sh` file, which the Nginx entrypoint tried to execute (not source). Since it wasn't executable and environment variables don't persist from subprocesses, `NGINX_HOST` was empty.

**Fix:** Changed the mount extension from `.sh` to `.envsh` in `docker-compose.prod.yml`:
```yaml
# Before
- ./nginx-extract-host.sh:/docker-entrypoint.d/05-extract-host.sh

# After
- ./nginx-extract-host.sh:/docker-entrypoint.d/05-extract-host.envsh
```

### 2. Missing AMD Build Files (Critical)
**Problem:** JavaScript error: `No define call for core_course/events`

**Root Cause:** The AMD JavaScript modules in `public/course/amd/build/` were not compiled. The `.gitignore` file excluded `**/amd/build/*.min.js` files, so even after building locally, they weren't committed.

**Fix:**
1. Built AMD modules locally:
   ```powershell
   cd public
   npm install
   npx grunt amd --force
   ```
2. Force-added files to git (bypassing .gitignore):
   ```powershell
   git add -f public/course/amd/build/*.min.js
   git add -f public/course/amd/build/*.min.js.map
   git commit -m "Force add course AMD build files"
   git push origin test
   ```
3. On server, copied files directly to container (since Docker image was built before the fix):
   ```bash
   docker cp public/course/amd/build/. moodle_app:/var/www/html/public/course/amd/build/
   ```

### 3. Moodle Cron Service
**Problem:** Dashboard not updating with new courses.

**Fix:** Added a `cron` service to `docker-compose.prod.yml` that runs Moodle's cron every minute.

### 4. Course Visibility Settings
**Problem:** Newly created courses not appearing in "In Progress" list.

**Fix:** Updated `master_builder.py` to set:
- `startdate` = yesterday (so courses appear immediately)
- `enddate` = 0 (disabled, so courses don't expire)
- `visible` = 1

### 5. Quiz AMD Build Files
**Problem:** JavaScript error: `No define call for mod_quiz/preflight_check`

**Root Cause:** Same as issue #2 - quiz AMD modules were not compiled and committed.

**Fix:**
1. Built AMD modules locally: `npx grunt amd --force`
2. Force-added quiz files to git:
   ```powershell
   git add -f public/mod/quiz/amd/build/*.min.js
   git add -f public/mod/quiz/amd/build/*.min.js.map
   git commit -m "Add quiz AMD build files"
   git push origin test
   ```
3. On server, copied files:
   ```bash
   docker cp public/mod/quiz/amd/build/. moodle_app:/var/www/html/public/mod/quiz/amd/build/
   ```

### 6. Local Plugin Database Tables
**Problem:** Error: `Table "local_quiz_password_verify" does not exist`

**Root Cause:** The local plugin files existed in two locations (`/var/www/html/local/` and `/var/www/html/public/local/`), but the correct location (`/var/www/html/local/`) was missing the `db` folder with `install.xml`. Moodle's upgrade script didn't detect the plugin as needing installation.

**Fix:**
1. Copy complete plugin from public to local:
   ```bash
   docker exec moodle_app cp -r /var/www/html/public/local/quiz_password_verify/. /var/www/html/local/quiz_password_verify/
   ```
2. Manually create the database table using a PHP script:
   ```bash
   cat > /tmp/create_table.php << 'EOF'
   <?php
   define('CLI_SCRIPT', true);
   require('/var/www/html/config.php');
   
   $dbman = $DB->get_manager();
   $table = new xmldb_table('local_quiz_password_verify');
   $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
   $table->add_field('quizid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
   $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
   $table->add_field('attemptid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
   $table->add_field('timeverified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
   $table->add_field('ipaddress', XMLDB_TYPE_CHAR, '45', null, null, null, null);
   $table->add_field('useragent', XMLDB_TYPE_TEXT, null, null, null, null, null);
   $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
   
   if (!$dbman->table_exists($table)) {
       $dbman->create_table($table);
       echo "Table created successfully!\n";
   } else {
       echo "Table already exists.\n";
   }
   EOF
   
   docker cp /tmp/create_table.php moodle_app:/tmp/create_table.php
   docker exec moodle_app php /tmp/create_table.php
   ```

### 7. Plugin Language Strings Not Loading
**Problem:** Modal dialogs show raw string identifiers like `[[verifyyouridentity,local_quiz_password_verify]]` instead of translated text.

**Root Cause:** The plugin is not registered in Moodle's `config_plugins` table, so Moodle doesn't recognize it as installed. The `lib.php` hook that loads strings via `strings_for_js()` is never called.

**Diagnosis:**
```bash
docker exec moodle_app php -r "
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
\$version = \$DB->get_field('config_plugins', 'value', ['plugin' => 'local_quiz_password_verify', 'name' => 'version']);
echo 'Plugin version: ' . (\$version ?: 'NOT FOUND') . PHP_EOL;
"
```

**Fix:** Manually register the plugin in the database:
```bash
docker exec moodle_app php -r "
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
\$record = new stdClass();
\$record->plugin = 'local_quiz_password_verify';
\$record->name = 'version';
\$record->value = '2025120102';
\$DB->insert_record('config_plugins', \$record);
echo 'Plugin registered.' . PHP_EOL;
"
docker exec moodle_app php /var/www/html/admin/cli/purge_caches.php
```

---

## Deploying from Test to Production

### Pre-Deployment Checklist
- [ ] All fixes tested on test environment
- [ ] AMD build files committed with `git add -f`
- [ ] `docker-compose.prod.yml` has the `.envsh` extension fix
- [ ] Cron service is configured

### Step-by-Step Deployment

#### 1. Merge Test Branch to Main/Prod
```bash
# On local machine
git checkout main
git pull origin main
git merge test
git push origin main
```

#### 2. SSH to Production Server
```bash
ssh ubuntu@<PROD_SERVER_IP>
cd ~/moodle
```

#### 3. Pull Latest Code
```bash
git fetch origin
git reset --hard origin/main
```

#### 4. Reset Docker Volume (Important!)
The Docker image may not have the latest AMD files. Reset the code volume:
```bash
docker compose -f docker-compose.prod.yml down
docker volume rm moodle_moodle_code
docker compose -f docker-compose.prod.yml up -d
```

#### 5. Copy AMD Build Files (If Still Missing)
If the JavaScript error persists, manually copy the files:
```bash
docker cp public/course/amd/build/. moodle_app:/var/www/html/public/course/amd/build/
```

#### 6. Verify Deployment
```bash
# Check containers are running
docker ps

# Check for errors
docker logs moodle_web
docker logs moodle_app

# Verify AMD files exist
docker exec moodle_app ls -la /var/www/html/public/course/amd/build/
```

#### 7. Purge Moodle Caches
In the Moodle admin UI: **Site Administration → Development → Purge all caches**

---

## Permanent Fix: Rebuild Docker Image

To avoid needing `docker cp` on every deployment, trigger a new Docker image build:

1. Make a small change to force rebuild (or manually trigger GitHub Actions)
2. The new image will include the AMD build files
3. Future deployments won't need the manual copy step

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Nginx won't start | Check `docker logs moodle_web`, verify `.envsh` mount |
| JS errors in console | Run `docker cp` for AMD files, purge caches |
| Courses not on dashboard | Wait for cron (1 min) or check course dates |
| 404 on logo | Configure logo in Site Administration → Appearance → Logos |
| `No define call for mod_quiz/...` | Copy quiz AMD files: `docker cp public/mod/quiz/amd/build/. moodle_app:/var/www/html/public/mod/quiz/amd/build/` |
| `Table "local_..." does not exist` | Copy plugin files and run create_table.php script (see section 6) |

---

## Files Changed

| File | Change |
|------|--------|
| `docker-compose.prod.yml` | Changed `.sh` to `.envsh`, added cron service, added `moodle_code` volume |
| `public/course/amd/build/*.min.js` | Force-added course AMD build files |
| `public/mod/quiz/amd/build/*.min.js` | Force-added quiz AMD build files |
| `public/local/quiz_password_verify/` | Complete plugin with db folder |
| `master_builder.py` | Fixed course date settings |

