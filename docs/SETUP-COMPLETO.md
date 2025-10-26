# Setup Completo: Moodle + TimescaleDB + Plugin TSDB

**Guia passo-a-passo para configurar todo o ambiente do TCC**

---

## Índice

1. [Pré-requisitos](#pré-requisitos)
2. [Instalação do Moodle](#instalação-do-moodle)
3. [Setup TimescaleDB](#setup-timescaledb)
4. [Instalação do Plugin logstore_tsdb](#instalação-do-plugin)
5. [Configuração de Web Services](#configuração-de-web-services)
6. [Scripts de Simulação](#scripts-de-simulação)
7. [Verificação Final](#verificação-final)
8. [Troubleshooting](#troubleshooting)

---

## Pré-requisitos

### Sistema

- Ubuntu 20.04+ ou Debian 11+
- 4GB RAM mínimo (8GB recomendado)
- 20GB espaço em disco
- Docker instalado e funcionando
- Conexão com internet

### Verificação Rápida

```bash
# Versões necessárias
php -v          # 8.2+
psql --version  # 14+
docker --version
docker-compose --version
node -v         # 22.11+
composer --version
```

---

## Instalação do Moodle

### Passo 1: Instalar Dependências do Sistema

```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar PHP 8.2 e extensões
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install -y \
    php8.2 \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-curl \
    php8.2-gd \
    php8.2-intl \
    php8.2-mbstring \
    php8.2-pgsql \
    php8.2-xml \
    php8.2-zip \
    php8.2-soap \
    php8.2-xmlrpc \
    php8.2-opcache \
    php8.2-readline

# Instalar PostgreSQL 14
sudo apt install -y postgresql-14 postgresql-contrib-14

# Instalar Node.js 22
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Git
sudo apt install -y git
```

✅ **Checkpoint:** Verifique todas as versões com os comandos acima.

### Passo 2: Configurar PostgreSQL

```bash
# Iniciar PostgreSQL
sudo systemctl start postgresql
sudo systemctl enable postgresql

# Acessar PostgreSQL
sudo -u postgres psql

# No prompt psql, execute:
CREATE DATABASE moodle WITH ENCODING 'UTF8' LC_COLLATE='en_US.UTF-8' LC_CTYPE='en_US.UTF-8' TEMPLATE=template0;
CREATE USER moodleuser WITH PASSWORD 'Moodle@2025!Strong';
GRANT ALL PRIVILEGES ON DATABASE moodle TO moodleuser;

-- Para testes PHPUnit
CREATE DATABASE moodle_test WITH ENCODING 'UTF8' LC_COLLATE='en_US.UTF-8' LC_CTYPE='en_US.UTF-8' TEMPLATE=template0;
GRANT ALL PRIVILEGES ON DATABASE moodle_test TO moodleuser;

-- Sair
\q
```

✅ **Checkpoint:** Teste a conexão:
```bash
psql -h localhost -U moodleuser -d moodle -c "SELECT version();"
# Deve pedir senha e mostrar a versão do PostgreSQL
```

### Passo 3: Preparar Diretórios

```bash
# Ir para o diretório do projeto
cd ~/Documentos/Estudos/moodle-plugin-rework

# Criar diretório de dados Moodle
mkdir -p ~/moodledata
chmod 0777 ~/moodledata  # Apenas para dev local!

# Criar diretórios de teste
mkdir -p ~/moodledata_phpunit
chmod 0777 ~/moodledata_phpunit
```

### Passo 4: Instalar Dependências do Projeto

```bash
# Instalar dependências PHP via Composer
composer install

# Instalar dependências Node.js
npm install
```

✅ **Checkpoint:** Verifique que `vendor/` e `node_modules/` foram criados.

### Passo 5: Configurar config.php

```bash
# Copiar template
cp config-dist.php config.php

# Editar config.php
nano config.php
```

Configuração completa do `config.php`:

```php
<?php
unset($CFG);
global $CFG;
$CFG = new stdClass();

// ========================================
// DATABASE CONFIGURATION
// ========================================
$CFG->dbtype    = 'pgsql';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'moodleuser';
$CFG->dbpass    = 'Moodle@2025!Strong';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = [
    'dbpersist' => 0,
    'dbport' => '5432',
    'dbsocket' => '',
];

// ========================================
// WEB ADDRESS
// ========================================
$CFG->wwwroot   = 'http://localhost:8000';

// ========================================
// DATA DIRECTORIES
// ========================================
$CFG->dataroot  = '/home/SEU_USUARIO/moodledata';  // AJUSTAR!
$CFG->admin     = 'admin';

// ========================================
// DIRECTORY ROOTS
// ========================================
$CFG->dirroot   = '/home/SEU_USUARIO/Documentos/Estudos/moodle-plugin-rework/public';  // AJUSTAR!

// ========================================
// PHPUNIT CONFIGURATION
// ========================================
$CFG->phpunit_prefix = 'phpu_';
$CFG->phpunit_dataroot = '/home/SEU_USUARIO/moodledata_phpunit';  // AJUSTAR!
$CFG->phpunit_dbtype = 'pgsql';
$CFG->phpunit_dbhost = 'localhost';
$CFG->phpunit_dbname = 'moodle_test';
$CFG->phpunit_dbuser = 'moodleuser';
$CFG->phpunit_dbpass = 'Moodle@2025!Strong';

// ========================================
// DEBUGGING - APENAS DESENVOLVIMENTO
// ========================================
$CFG->debug = (E_ALL | E_STRICT);
$CFG->debugdisplay = 1;
$CFG->debugpageinfo = 1;
$CFG->perfdebug = 15;
$CFG->perfinfo = 1;
$CFG->cachejs = false;
$CFG->cachetemplates = false;
$CFG->langstringcache = false;
$CFG->themedesignermode = true;

// ========================================
// DISABLE OPCACHE
// ========================================
ini_set('opcache.enable', '0');

require_once(__DIR__ . '/lib/setup.php');
```

⚠️ **IMPORTANTE:** Substitua `SEU_USUARIO` pelos caminhos corretos!

### Passo 6: Instalar Moodle

```bash
# Via CLI (recomendado)
php admin/cli/install.php \
    --lang=en \
    --wwwroot=http://localhost:8000 \
    --dataroot=/home/SEU_USUARIO/moodledata \
    --dbtype=pgsql \
    --dbhost=localhost \
    --dbname=moodle \
    --dbuser=moodleuser \
    --dbpass='Moodle@2025!Strong' \
    --prefix=mdl_ \
    --fullname="Moodle TCC" \
    --shortname="tcc" \
    --adminuser=admin \
    --adminpass='Admin@2025!TCC' \
    --adminemail=admin@example.com \
    --agree-license \
    --non-interactive
```

✅ **Checkpoint:** Instalação deve completar sem erros.

### Passo 7: Iniciar Servidor de Desenvolvimento

```bash
# Terminal 1: Servidor PHP
cd public
php -S localhost:8000

# Deixe rodando e abra novo terminal
```

Acesse: **http://localhost:8000**
- Login: `admin`
- Senha: `Admin@2025!TCC`

✅ **Checkpoint:** Você deve conseguir fazer login no Moodle!

---

## Setup TimescaleDB

### Passo 1: Criar docker-compose.yml

No diretório raiz do projeto, já existe o arquivo `scripts/docker-compose.yml` com TimescaleDB configurado.

### Passo 2: Iniciar TimescaleDB

```bash
# Do diretório raiz do projeto
cd ~/Documentos/Estudos/moodle-plugin-rework

# Iniciar container
docker-compose -f scripts/docker-compose.yml up -d

# Verificar que está rodando
docker-compose -f scripts/docker-compose.yml ps
```

Você deve ver:
```
NAME                COMMAND                  SERVICE             STATUS
timescaledb         "docker-entrypoint.s…"   timescaledb         Up
```

✅ **Checkpoint:** Container rodando!

### Passo 3: Criar Schema do Banco

```bash
# Conectar ao TimescaleDB
docker exec -it timescaledb psql -U moodleuser -d moodle_logs_tsdb

# OU via psql local
psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb
```

Execute o script SQL (copie o conteúdo de `public/admin/tool/log/store/tsdb/db/timescaledb_schema.sql`):

```sql
-- Criar tabela de logs
CREATE TABLE IF NOT EXISTS moodle_events (
    time TIMESTAMPTZ NOT NULL,
    eventname TEXT NOT NULL,
    component TEXT,
    action TEXT,
    target TEXT,
    crud CHAR(1),
    edulevel SMALLINT,
    courseid INTEGER,
    userid INTEGER,
    contextid INTEGER,
    contextlevel SMALLINT,
    contextinstanceid INTEGER,
    objectid INTEGER,
    objecttable TEXT,
    relateduserid INTEGER,
    anonymous SMALLINT DEFAULT 0,
    ip INET,
    origin TEXT,
    realuserid INTEGER
);

-- Converter para hypertable (feature do TimescaleDB)
SELECT create_hypertable('moodle_events', 'time',
    chunk_time_interval => INTERVAL '1 day',
    if_not_exists => TRUE
);

-- Criar índices otimizados
CREATE INDEX IF NOT EXISTS idx_eventname ON moodle_events (time DESC, eventname);
CREATE INDEX IF NOT EXISTS idx_component ON moodle_events (time DESC, component);
CREATE INDEX IF NOT EXISTS idx_courseid ON moodle_events (time DESC, courseid);
CREATE INDEX IF NOT EXISTS idx_userid ON moodle_events (time DESC, userid);
CREATE INDEX IF NOT EXISTS idx_action ON moodle_events (time DESC, action);

-- Criar política de compressão (dados > 7 dias)
ALTER TABLE moodle_events SET (
    timescaledb.compress,
    timescaledb.compress_segmentby = 'component, action',
    timescaledb.compress_orderby = 'time DESC'
);

SELECT add_compression_policy('moodle_events', INTERVAL '7 days');

-- Criar política de retenção (manter 1 ano)
SELECT add_retention_policy('moodle_events', INTERVAL '1 year');

-- Verificar
SELECT * FROM timescaledb_information.hypertables WHERE hypertable_name = 'moodle_events';
```

✅ **Checkpoint:** Deve mostrar informações da hypertable criada.

### Passo 4: Testar Inserção Manual

```sql
-- Inserir evento de teste
INSERT INTO moodle_events (
    time, eventname, component, action, target, crud,
    edulevel, courseid, userid, contextid
) VALUES (
    NOW(),
    '\core\event\user_loggedin',
    'core',
    'loggedin',
    'user',
    'r',
    0,
    0,
    2,
    1
);

-- Verificar
SELECT * FROM moodle_events ORDER BY time DESC LIMIT 5;
```

✅ **Checkpoint:** Deve mostrar o evento inserido!

---

## Instalação do Plugin logstore_tsdb

### Passo 1: Verificar Arquivos do Plugin

O plugin já está em: `public/admin/tool/log/store/tsdb/`

Verifique estrutura:
```bash
cd public/admin/tool/log/store/tsdb
ls -la
```

Você deve ver:
- `version.php`
- `settings.php`
- `lang/en/logstore_tsdb.php`
- `classes/log/store.php`
- `classes/client/timescaledb_client.php`
- `db/timescaledb_schema.sql`
- `README.md`

### Passo 2: Instalar via Interface Web

1. Acesse: **http://localhost:8000**
2. Login como admin
3. Navegue para: **Site administration → Notifications**
4. Moodle detectará o novo plugin
5. Clique em **"Upgrade Moodle database now"**

✅ **Checkpoint:** Plugin instalado com sucesso!

### Passo 3: Configurar o Plugin

1. Vá para: **Site administration → Plugins → Logging → Manage log stores**
2. Localize "Time Series Database Log Store"
3. **Habilite** o plugin (ícone de olho)
4. Clique no ícone de **Settings** (engrenagem)

Configure:
- **TSDB Type**: TimescaleDB
- **Host**: localhost
- **Port**: 5433
- **Database/Bucket**: moodle_logs_tsdb
- **Username**: moodleuser
- **Password**: Moodle@TSDB2025!
- **Write Mode**: Asynchronous (recommended)
- **Buffer Size**: 1000
- **Flush Interval**: 60

5. **Save changes**

✅ **Checkpoint:** Configurações salvas!

### Passo 4: Testar Plugin

```bash
# Gerar alguns eventos
# Faça login/logout algumas vezes
# Navegue pelo Moodle
# Visualize alguns cursos

# Verificar no TimescaleDB
psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb -c \
  "SELECT time, eventname, action, userid FROM moodle_events ORDER BY time DESC LIMIT 10;"
```

✅ **Checkpoint:** Você deve ver eventos sendo capturados!

---

## Configuração de Web Services

### Passo 1: Habilitar Web Services

1. **Site administration → Advanced features**
2. Marcar: ☑ **Enable web services**
3. Save changes

### Passo 2: Criar Usuário de API

1. **Site administration → Users → Accounts → Add a new user**
2. Criar usuário:
   - Username: `apiuser`
   - Password: `ApiUser@2025!`
   - First name: API
   - Last name: User
   - Email: apiuser@example.com
3. Save changes

### Passo 3: Atribuir Role

1. **Site administration → Users → Permissions → Assign system roles**
2. Clicar em **Manager**
3. Adicionar `apiuser`

### Passo 4: Habilitar Protocolos

1. **Site administration → Plugins → Web services → Manage protocols**
2. Habilitar: **REST protocol**

### Passo 5: Criar Serviço Externo

1. **Site administration → Plugins → Web services → External services**
2. Clicar em **Add**
3. Configurar:
   - Name: `Moodle TCC API`
   - Short name: `tcc_api`
   - Enabled: Yes
   - Authorized users only: Yes
4. Save changes

### Passo 6: Adicionar Funções ao Serviço

1. Clicar em **Add functions** no serviço criado
2. Adicionar as funções necessárias:
   - `core_user_create_users`
   - `core_user_get_users`
   - `core_course_get_courses`
   - `core_course_create_courses`
   - `core_enrol_get_enrolled_users`
   - `core_enrol_enrol_users`
   - `mod_quiz_get_quizzes_by_courses`
   - `mod_forum_get_forums_by_courses`
   - `core_webservice_get_site_info`
3. Save

### Passo 7: Criar Token

1. **Site administration → Plugins → Web services → Manage tokens**
2. Clicar em **Add**
3. Configurar:
   - User: `apiuser`
   - Service: `Moodle TCC API`
4. Save changes
5. **COPIAR O TOKEN GERADO** (algo como `1234567890abcdef1234567890abcdef`)

### Passo 8: Testar API

```bash
# Substituir YOUR_TOKEN pelo token copiado
TOKEN="YOUR_TOKEN"

# Testar conexão
curl -X POST "http://localhost:8000/webservice/rest/server.php" \
  -d "wstoken=$TOKEN" \
  -d "wsfunction=core_webservice_get_site_info" \
  -d "moodlewsrestformat=json"
```

✅ **Checkpoint:** Deve retornar JSON com informações do site!

---

## Scripts de Simulação

### Passo 1: Instalar Python e Dependências

```bash
# Instalar Python 3.10+
sudo apt install -y python3 python3-pip python3-venv

# Criar ambiente virtual
cd ~/Documentos/Estudos/moodle-plugin-rework/scripts/simulation
python3 -m venv venv
source venv/bin/activate

# Instalar dependências
pip install -r requirements.txt
```

### Passo 2: Configurar Token

```bash
# Editar config.json
nano config.json
```

Ajustar:
```json
{
  "moodle_url": "http://localhost:8000",
  "ws_token": "SEU_TOKEN_AQUI",
  ...
}
```

### Passo 3: Executar Simulação de Teste

```bash
# Rodar script de teste (poucos eventos)
python generate_load.py --mode test --duration 60

# Acompanhar logs
tail -f simulation.log
```

✅ **Checkpoint:** Script deve criar usuários, cursos e gerar eventos!

### Passo 4: Simulação Completa

```bash
# Simulação realista (1 hora, 100 usuários)
python generate_load.py --mode realistic --duration 3600 --users 100

# Verificar no TimescaleDB quantos eventos foram gerados
psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb -c \
  "SELECT COUNT(*), MIN(time), MAX(time) FROM moodle_events;"
```

---

## Verificação Final

### Checklist Completo

Execute este checklist para garantir que tudo está funcionando:

```bash
# 1. Moodle acessível
curl -s http://localhost:8000 | grep -o "<title>.*</title>"

# 2. PostgreSQL Moodle funcionando
psql -h localhost -U moodleuser -d moodle -c "SELECT COUNT(*) FROM mdl_user;"

# 3. TimescaleDB rodando
docker-compose -f scripts/docker-compose.yml ps | grep timescaledb

# 4. TimescaleDB recebendo logs
psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb -c \
  "SELECT COUNT(*) FROM moodle_events;"

# 5. Plugin habilitado
psql -h localhost -U moodleuser -d moodle -c \
  "SELECT value FROM mdl_config_plugins WHERE plugin='tool_log' AND name='enabled_stores';"
# Deve conter 'logstore_tsdb'

# 6. API funcionando
curl -X POST "http://localhost:8000/webservice/rest/server.php" \
  -d "wstoken=$TOKEN" \
  -d "wsfunction=core_webservice_get_site_info" \
  -d "moodlewsrestformat=json" | grep -o "sitename"

# 7. Scripts Python funcionando
cd scripts/simulation && python -c "import requests; print('OK')"
```

Se todos os comandos funcionarem: **✅ AMBIENTE 100% FUNCIONAL!**

---

## Troubleshooting

### Problema: "Failed to connect to database"

**Solução:**
```bash
# Verificar PostgreSQL rodando
sudo systemctl status postgresql

# Verificar porta
sudo netstat -tlnp | grep 5432

# Testar conexão
psql -h localhost -U moodleuser -d moodle
```

### Problema: "Permission denied for directory"

**Solução:**
```bash
# Ajustar permissões moodledata
chmod -R 0777 ~/moodledata
chown -R $USER:$USER ~/moodledata
```

### Problema: TimescaleDB container não inicia

**Solução:**
```bash
# Ver logs
docker-compose -f scripts/docker-compose.yml logs timescaledb

# Recriar container
docker-compose -f scripts/docker-compose.yml down
docker-compose -f scripts/docker-compose.yml up -d
```

### Problema: Plugin não aparece

**Solução:**
```bash
# Limpar cache
php admin/cli/purge_caches.php

# Verificar permissões
ls -la public/admin/tool/log/store/tsdb/version.php

# Re-executar upgrade
php admin/cli/upgrade.php --non-interactive
```

### Problema: API retorna "Invalid token"

**Solução:**
1. Verificar que Web Services estão habilitados
2. Gerar novo token via interface web
3. Verificar que usuário tem permissões
4. Testar com curl

### Problema: Scripts Python não encontram módulos

**Solução:**
```bash
# Ativar virtual environment
source scripts/simulation/venv/bin/activate

# Reinstalar dependências
pip install -r scripts/simulation/requirements.txt
```

---

## Próximos Passos

Após completar este setup:

1. 📖 Ler `docs/PLUGIN-TIMESCALEDB.md` - Entender o código do plugin
2. 📖 Ler `docs/SIMULACAO-CARGA.md` - Usar os scripts avançados
3. 📖 Ler `docs/ANALISE-DADOS.md` - Analisar os dados gerados
4. 🔬 Experimentar diferentes cenários de carga
5. 📊 Conectar Grafana para visualizações
6. 📝 Documentar resultados para o TCC

---

## Suporte

**Documentação:**
- [docs/TCC-LOGGING-GUIDE.md](TCC-LOGGING-GUIDE.md) - Arquitetura de logging
- [docs/API-MOODLE.md](API-MOODLE.md) - Uso da API
- [docs/PLUGIN-TIMESCALEDB.md](PLUGIN-TIMESCALEDB.md) - Detalhes do plugin

**Logs para Debug:**
- Moodle: Verifique `~/moodledata/` e debugging no navegador
- TimescaleDB: `docker logs timescaledb`
- Scripts: `scripts/simulation/simulation.log`

**Comandos Úteis:**
```bash
# Reiniciar tudo
docker-compose -f scripts/docker-compose.yml restart
php admin/cli/purge_caches.php

# Ver eventos em tempo real
watch -n 2 "psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb -c 'SELECT COUNT(*) FROM moodle_events;'"
```

---

**Última atualização**: 2025-01-25
**Versão**: 1.0.0
**Status**: ✅ Testado e funcional
