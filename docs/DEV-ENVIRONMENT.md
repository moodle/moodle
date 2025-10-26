# Guia de Configuração do Ambiente de Desenvolvimento Moodle

Este guia detalha como configurar um ambiente de desenvolvimento local do Moodle para trabalhar no plugin logstore_tsdb.

---

## Índice

1. [Pré-requisitos](#pré-requisitos)
2. [Instalação do Moodle](#instalação-do-moodle)
3. [Configuração do Banco de Dados](#configuração-do-banco-de-dados)
4. [Configuração do PHP](#configuração-do-php)
5. [Instalação de Dependências](#instalação-de-dependências)
6. [Configuração de Debugging](#configuração-de-debugging)
7. [PHPUnit Setup](#phpunit-setup)
8. [Behat Setup](#behat-setup)
9. [InfluxDB/TimescaleDB Setup](#influxdbtimescaledb-setup)
10. [Ferramentas de Desenvolvimento](#ferramentas-de-desenvolvimento)
11. [Troubleshooting](#troubleshooting)

---

## Pré-requisitos

### Sistema Operacional

- **Linux** (Ubuntu 22.04+ recomendado)
- **macOS** (12.0+)
- **Windows** (via WSL2)

### Software Obrigatório

```bash
# PHP 8.2 ou superior
php -v

# Composer (gerenciador de dependências PHP)
composer --version

# Node.js 22.11.0+
node -v
npm -v

# Git
git --version

# Banco de Dados (escolha um):
# - MySQL 8.0+
# - PostgreSQL 14+
# - MariaDB 10.6+
```

---

## Instalação do Moodle

### 1. Clone do Repositório

```bash
cd ~/Documentos/Estudos
git clone https://github.com/moodle/moodle.git moodle-plugin-rework
cd moodle-plugin-rework

# Ou se já clonou, faça pull
git pull origin main
```

### 2. Verifique a Estrutura

Este repositório usa estrutura com `public/` subdirectory:

```bash
ls -la
# Deve ver:
# - public/          (código Moodle principal)
# - admin/cli/       (scripts CLI)
# - composer.json
# - package.json
```

### 3. Instale Dependências PHP

```bash
# No diretório raiz do projeto
composer install --no-dev

# Para desenvolvimento (inclui PHPUnit, Behat):
composer install
```

### 4. Instale Dependências Node.js

```bash
npm install
```

---

## Configuração do Banco de Dados

### Opção 1: MySQL/MariaDB

#### Instalação

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install mysql-server

# macOS (via Homebrew)
brew install mysql
brew services start mysql
```

#### Criação do Banco

```bash
# Acesse o MySQL
sudo mysql -u root

# No prompt MySQL:
CREATE DATABASE moodle DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'moodleuser'@'localhost' IDENTIFIED BY 'moodlepassword';
GRANT ALL PRIVILEGES ON moodle.* TO 'moodleuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Opção 2: PostgreSQL

#### Instalação

```bash
# Ubuntu/Debian
sudo apt install postgresql postgresql-contrib

# macOS
brew install postgresql@14
brew services start postgresql@14
```

#### Criação do Banco

```bash
# Acesse o PostgreSQL
sudo -u postgres psql

# No prompt psql:
CREATE DATABASE moodle WITH ENCODING 'UTF8';
CREATE USER moodleuser WITH PASSWORD 'moodlepassword';
GRANT ALL PRIVILEGES ON DATABASE moodle TO moodleuser;
\q
```

---

## Configuração do PHP

### 1. Verificar Extensões Necessárias

```bash
php -m | grep -E '(iconv|mbstring|curl|openssl|ctype|zip|gd|simplexml|spl|pcre|dom|xml|intl|json|hash|fileinfo|sodium)'
```

Se alguma estiver faltando:

```bash
# Ubuntu/Debian
sudo apt install php8.2-{iconv,mbstring,curl,zip,gd,xml,intl,mysql,pgsql,soap}

# macOS (via Homebrew)
brew install php@8.2
brew link php@8.2
```

### 2. Configurar php.ini

Localize seu php.ini:

```bash
php --ini | grep "Loaded Configuration File"
```

Edite e ajuste:

```ini
; Limites de memória e upload
memory_limit = 256M
post_max_size = 512M
upload_max_filesize = 512M
max_execution_time = 300

; Debugging
display_errors = On
error_reporting = E_ALL
log_errors = On

; Opcache (desabilite durante desenvolvimento)
opcache.enable = 0

; Outras
max_input_vars = 5000
```

Reinicie o servidor PHP:

```bash
# Se usando PHP-FPM
sudo systemctl restart php8.2-fpm

# Se usando Apache
sudo systemctl restart apache2
```

---

## Instalação de Dependências

### 1. Criar Diretório de Dados

```bash
# Crie fora do diretório web (segurança)
mkdir -p ~/moodledata
chmod 0777 ~/moodledata  # Apenas para desenvolvimento local!
```

### 2. Configurar config.php

Copie o template de configuração:

```bash
cp config-dist.php config.php
```

Edite `config.php`:

```php
<?php
unset($CFG);
global $CFG;
$CFG = new stdClass();

// Banco de Dados
$CFG->dbtype    = 'mysqli';           // 'mysqli' para MySQL, 'pgsql' para PostgreSQL
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'moodleuser';
$CFG->dbpass    = 'moodlepassword';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = [
    'dbpersist' => 0,
    'dbport' => 3306,              // 3306 para MySQL, 5432 para PostgreSQL
    'dbsocket' => '',
    'dbcollation' => 'utf8mb4_unicode_ci',
];

// Paths
$CFG->wwwroot   = 'http://localhost:8000';  // Ajuste para seu servidor
$CFG->dataroot  = '/home/seu_usuario/moodledata';  // Path absoluto!
$CFG->admin     = 'admin';

// Diretórios (estrutura com public/)
$CFG->dirroot   = '/home/seu_usuario/Documentos/Estudos/moodle-plugin-rework/public';

require_once(__DIR__ . '/lib/setup.php');
```

### 3. Instalar Moodle via CLI

```bash
# Execute o instalador
php admin/cli/install.php \
    --lang=en \
    --wwwroot=http://localhost:8000 \
    --dataroot=/home/seu_usuario/moodledata \
    --dbtype=mysqli \
    --dbhost=localhost \
    --dbname=moodle \
    --dbuser=moodleuser \
    --dbpass=moodlepassword \
    --prefix=mdl_ \
    --fullname="Moodle Dev" \
    --shortname="mdev" \
    --adminuser=admin \
    --adminpass=Admin@123 \
    --adminemail=admin@example.com \
    --agree-license \
    --non-interactive
```

---

## Configuração de Debugging

Adicione ao seu `config.php` (ANTES do `require_once`):

```php
// ========================================
// DEBUGGING - APENAS DESENVOLVIMENTO
// ========================================

// Debugging máximo
$CFG->debug = (E_ALL | E_STRICT);           // Todos os erros
$CFG->debugdisplay = 1;                     // Exibe na tela
$CFG->debugpageinfo = 1;                    // Info de página

// Performance info
$CFG->perfdebug = 15;                       // Mostra métricas
$CFG->perfinfo = 1;

// Cache
$CFG->cachejs = false;                      // Não cache JS
$CFG->cachetemplates = false;               // Não cache templates
$CFG->langstringcache = false;              // Não cache strings

// Outros
$CFG->themedesignermode = true;             // Modo designer de temas
$CFG->yuislowmo = true;                     // YUI debug mode

// ========================================
```

**⚠️ IMPORTANTE**: Remova estas configurações em produção!

---

## PHPUnit Setup

### 1. Criar Banco de Dados de Teste

```bash
# MySQL
sudo mysql -u root
CREATE DATABASE moodle_test DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON moodle_test.* TO 'moodleuser'@'localhost';
EXIT;

# PostgreSQL
sudo -u postgres psql
CREATE DATABASE moodle_test WITH ENCODING 'UTF8';
GRANT ALL PRIVILEGES ON DATABASE moodle_test TO moodleuser;
\q
```

### 2. Configurar PHPUnit em config.php

Adicione ao `config.php`:

```php
// PHPUnit Configuration
$CFG->phpunit_prefix = 'phpu_';
$CFG->phpunit_dataroot = '/home/seu_usuario/moodledata_phpunit';
$CFG->phpunit_dbtype = 'mysqli';
$CFG->phpunit_dbhost = 'localhost';
$CFG->phpunit_dbname = 'moodle_test';
$CFG->phpunit_dbuser = 'moodleuser';
$CFG->phpunit_dbpass = 'moodlepassword';
```

### 3. Criar Diretório de Dados de Teste

```bash
mkdir -p ~/moodledata_phpunit
chmod 0777 ~/moodledata_phpunit
```

### 4. Inicializar PHPUnit

```bash
php public/admin/tool/phpunit/cli/init.php
```

Saída esperada:
```
Purging dataroot
... lots of output ...
PHPUnit initialization completed
```

### 5. Executar Testes

```bash
# Todos os testes (demora muito!)
vendor/bin/phpunit

# Teste específico
vendor/bin/phpunit public/lib/tests/event_test.php

# Suite específica
vendor/bin/phpunit --testsuite core_event_testsuite

# Com coverage (requer xdebug)
vendor/bin/phpunit --coverage-html coverage/
```

---

## Behat Setup

### 1. Instalar Selenium (para testes de navegador)

```bash
# Via Docker (recomendado)
docker run -d -p 4444:4444 -p 7900:7900 --shm-size="2g" selenium/standalone-chrome:latest

# Ou baixar manualmente
# https://www.selenium.dev/downloads/
```

### 2. Configurar Behat em config.php

```php
// Behat Configuration
$CFG->behat_prefix = 'bht_';
$CFG->behat_dataroot = '/home/seu_usuario/moodledata_behat';
$CFG->behat_wwwroot = 'http://localhost:8001';  // Porta diferente!
$CFG->behat_dbtype = 'mysqli';
$CFG->behat_dbhost = 'localhost';
$CFG->behat_dbname = 'moodle_behat';
$CFG->behat_dbuser = 'moodleuser';
$CFG->behat_dbpass = 'moodlepassword';

// Selenium
$CFG->behat_config = [
    'default' => [
        'extensions' => [
            'Behat\MinkExtension' => [
                'selenium2' => [
                    'wd_host' => 'http://localhost:4444/wd/hub',
                ],
            ],
        ],
    ],
];
```

### 3. Criar Banco e Diretórios

```bash
# Banco
sudo mysql -u root
CREATE DATABASE moodle_behat DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON moodle_behat.* TO 'moodleuser'@'localhost';
EXIT;

# Diretório
mkdir -p ~/moodledata_behat
chmod 0777 ~/moodledata_behat
```

### 4. Inicializar Behat

```bash
php admin/cli/behat.php --init
```

### 5. Executar Testes Behat

```bash
# Todos os testes
vendor/bin/behat --config public/behat.yml.dist

# Feature específica
vendor/bin/behat --config public/behat.yml.dist public/mod/forum/tests/behat/forum_post.feature

# Tag específica
vendor/bin/behat --config public/behat.yml.dist --tags=@javascript
```

---

## InfluxDB/TimescaleDB Setup

### Opção 1: InfluxDB (Time Series puro)

#### Via Docker (recomendado)

```bash
# InfluxDB 2.x
docker run -d \
  --name influxdb \
  -p 8086:8086 \
  -v influxdb-data:/var/lib/influxdb2 \
  influxdb:2.7

# Acesse http://localhost:8086 para setup inicial
# Username: admin
# Password: adminadmin
# Organization: moodle
# Bucket: moodle_logs
```

#### Instalação Nativa

```bash
# Ubuntu
wget https://dl.influxdata.com/influxdb/releases/influxdb2-2.7.1-amd64.deb
sudo dpkg -i influxdb2-2.7.1-amd64.deb
sudo systemctl start influxdb

# macOS
brew install influxdb
brew services start influxdb
```

#### Cliente PHP

```bash
composer require influxdata/influxdb-client-php
```

### Opção 2: TimescaleDB (PostgreSQL extension)

#### Via Docker

```bash
docker run -d \
  --name timescaledb \
  -p 5432:5432 \
  -e POSTGRES_PASSWORD=password \
  timescale/timescaledb:latest-pg14
```

#### Instalação Nativa

```bash
# Ubuntu
sudo apt install gnupg
echo "deb https://packagecloud.io/timescale/timescaledb/ubuntu/ $(lsb_release -c -s) main" | \
  sudo tee /etc/apt/sources.list.d/timescaledb.list
wget --quiet -O - https://packagecloud.io/timescale/timescaledb/gpgkey | sudo apt-key add -
sudo apt update
sudo apt install timescaledb-2-postgresql-14

# Ativar extensão
sudo -u postgres psql
CREATE EXTENSION IF NOT EXISTS timescaledb;
\q
```

#### Cliente PHP

Usa driver PostgreSQL nativo:

```bash
# Já incluído no PHP
php -m | grep pgsql
```

---

## Ferramentas de Desenvolvimento

### 1. IDE/Editor

**VS Code** (recomendado):

```bash
# Instale extensões
code --install-extension bmewburn.vscode-intelephense-client
code --install-extension xdebug.php-debug
code --install-extension EditorConfig.EditorConfig
```

**PhpStorm**:
- Suporte nativo ao Moodle
- Debugging integrado
- Refactoring avançado

### 2. Xdebug (Debugging)

```bash
# Instalar
sudo apt install php8.2-xdebug

# Configurar em php.ini
zend_extension=xdebug.so
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.client_port=9003
```

### 3. Git Hooks

Crie `.git/hooks/pre-commit`:

```bash
#!/bin/bash
# Executa code sniffer em arquivos modificados

FILES=$(git diff --cached --name-only --diff-filter=ACM | grep '\.php$')

if [ -n "$FILES" ]; then
    echo "Running PHP CodeSniffer..."
    vendor/bin/phpcs --standard=moodle $FILES
    if [ $? -ne 0 ]; then
        echo "Fix coding standards before committing."
        exit 1
    fi
fi
```

Torne executável:

```bash
chmod +x .git/hooks/pre-commit
```

### 4. Aliases Úteis

Adicione ao `~/.bashrc` ou `~/.zshrc`:

```bash
# Moodle aliases
alias moodle-purge='php admin/cli/purge_caches.php'
alias moodle-upgrade='php admin/cli/upgrade.php --non-interactive'
alias moodle-phpunit='vendor/bin/phpunit'
alias moodle-behat='vendor/bin/behat --config public/behat.yml.dist'
alias moodle-cron='php admin/cli/cron.php'
alias moodle-test-init='php public/admin/tool/phpunit/cli/init.php'
```

---

## Servidor de Desenvolvimento

### Opção 1: PHP Built-in Server

```bash
# Inicie servidor na porta 8000
cd public
php -S localhost:8000

# Acesse: http://localhost:8000
```

### Opção 2: Apache

```bash
# Instalar
sudo apt install apache2 libapache2-mod-php

# Criar VirtualHost
sudo nano /etc/apache2/sites-available/moodle.conf
```

Conteúdo:

```apache
<VirtualHost *:80>
    ServerName moodle.local
    DocumentRoot /home/seu_usuario/Documentos/Estudos/moodle-plugin-rework/public

    <Directory /home/seu_usuario/Documentos/Estudos/moodle-plugin-rework/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/moodle_error.log
    CustomLog ${APACHE_LOG_DIR}/moodle_access.log combined
</VirtualHost>
```

Ativar:

```bash
sudo a2ensite moodle.conf
sudo a2enmod rewrite
sudo systemctl restart apache2

# Adicione ao /etc/hosts
echo "127.0.0.1 moodle.local" | sudo tee -a /etc/hosts
```

### Opção 3: Docker Compose

Crie `docker-compose.yml`:

```yaml
version: '3.8'

services:
  moodle:
    image: moodlehq/moodle-php-apache:8.2
    ports:
      - "8000:80"
    volumes:
      - ./public:/var/www/html
      - ~/moodledata:/var/www/moodledata
    environment:
      - MOODLE_DOCKER_DBTYPE=mysqli
      - MOODLE_DOCKER_DBHOST=db
      - MOODLE_DOCKER_DBNAME=moodle
      - MOODLE_DOCKER_DBUSER=root
      - MOODLE_DOCKER_DBPASS=rootpass

  db:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=rootpass
      - MYSQL_DATABASE=moodle
    volumes:
      - mysql-data:/var/lib/mysql

volumes:
  mysql-data:
```

Execute:

```bash
docker-compose up -d
```

---

## Troubleshooting

### Problema: "Fatal error: Maximum execution time"

**Solução**:
```php
// config.php
$CFG->cron_lock_timeout = 600;
set_time_limit(300);
```

### Problema: "Cannot write to dataroot"

**Solução**:
```bash
sudo chown -R www-data:www-data ~/moodledata
# Ou para desenvolvimento:
chmod -R 0777 ~/moodledata
```

### Problema: PHPUnit database not found

**Solução**:
```bash
# Re-inicialize
php public/admin/tool/phpunit/cli/init.php --force
```

### Problema: Behat fails to start

**Solução**:
```bash
# Verifique Selenium
curl http://localhost:4444/status

# Re-inicialize Behat
php admin/cli/behat.php --init --force
```

### Problema: InfluxDB connection refused

**Solução**:
```bash
# Verifique se está rodando
docker ps | grep influxdb

# Teste conexão
curl http://localhost:8086/health
```

### Problema: Opcache issues

**Solução**:
```bash
# Limpe opcache
sudo systemctl restart php8.2-fpm

# Ou desabilite no php.ini durante dev
opcache.enable=0
```

---

## Verificação Final

Execute este checklist:

```bash
# ✅ PHP versão correta
php -v  # Deve ser 8.2+

# ✅ Extensões PHP
php -m | grep -E '(mbstring|curl|zip|gd|xml|intl|mysqli)'

# ✅ Composer instalado
composer --version

# ✅ Node.js instalado
node -v  # Deve ser 22.11.0+

# ✅ Moodle acessível
curl http://localhost:8000

# ✅ Banco de dados funcionando
php admin/cli/check_database_schema.php

# ✅ PHPUnit funcional
vendor/bin/phpunit --version

# ✅ Cache limpo
php admin/cli/purge_caches.php

# ✅ Cron rodando
php admin/cli/cron.php
```

---

## Próximos Passos

Após configurar o ambiente:

1. ✅ Testar instalação: `http://localhost:8000`
2. ✅ Login com admin / Admin@123
3. ✅ Explorar admin interface
4. 📖 Ler `docs/TCC-LOGGING-GUIDE.md`
5. 🔨 Começar desenvolvimento do plugin logstore_tsdb

---

**Dúvidas?** Consulte:
- [Moodle Developer Docs](https://moodledev.io)
- [Moodle Forums](https://moodle.org/forums)
- [Stack Overflow - Moodle Tag](https://stackoverflow.com/questions/tagged/moodle)
