# Plugin logstore_tsdb: Arquitetura e Funcionamento

**Documentação técnica completa do plugin de integração TimescaleDB**

---

## Índice

1. [Vis

ão Geral](#visão-geral)
2. [Arquitetura](#arquitetura)
3. [Componentes](#componentes)
4. [Fluxo de Dados](#fluxo-de-dados)
5. [Schema do Banco](#schema-do-banco)
6. [Configuração](#configuração)
7. [Performance e Otimização](#performance-e-otimização)
8. [Troubleshooting](#troubleshooting)

---

## Visão Geral

O `logstore_tsdb` é um plugin de armazenamento de logs (logstore) para o Moodle que redireciona eventos do sistema para um banco de dados Time Series (TimescaleDB) ao invés do banco relacional tradicional.

### Objetivos

- ✅ Escalabilidade massiva para milhões de eventos
- ✅ Performance superior em queries temporais
- ✅ Compressão automática de dados antigos (90%+ de economia)
- ✅ Políticas de retenção automatizadas
- ✅ Zero impacto na performance do Moodle (modo async)

### Comparação: Standard vs TSDB

| Aspecto | logstore_standard | logstore_tsdb |
|---------|------------------|---------------|
| **Armazenamento** | MySQL/PostgreSQL relacional | TimescaleDB (hypertable) |
| **Tamanho típico** | 100GB+ sem compressão | 10-20GB com compressão |
| **Query "últimas 24h"** | 15-60 segundos | 100-500ms |
| **Escritas/segundo** | ~1000 (limitado) | 10.000+ (batch) |
| **Latência do usuário** | 5-10ms por evento | <0.1ms (buffer assíncrono) |
| **Retenção automática** | ❌ Manual | ✅ Automática |

---

## Arquitetura

### Integração com Moodle

```
┌─────────────────────────────────────────────────────────┐
│                   MOODLE CORE                            │
├─────────────────────────────────────────────────────────┤
│  Módulos (Quiz, Assignment, Forum, etc.)                │
│               ↓                                          │
│  Events API (\core\event\base)                          │
│               ↓                                          │
│  Log Manager (\tool_log\log\manager)                    │
│         ↓                           ↓                    │
│  logstore_standard           logstore_tsdb              │
│  (MySQL table)               (TimescaleDB)              │
└─────────────────────────────────────────────────────────┘
```

### Componentes do Plugin

```
logstore_tsdb/
├── classes/
│   ├── log/
│   │   └── store.php           # Classe principal do logstore
│   └── client/
│       └── timescaledb_client.php  # Cliente TimescaleDB
├── db/
│   └── timescaledb_schema.sql  # Schema da hypertable
├── lang/
│   └── en/
│       └── logstore_tsdb.php   # Strings de idioma
├── settings.php                 # Interface administrativa
├── version.php                  # Metadados do plugin
└── tests/
    └── store_test.php          # Testes PHPUnit
```

---

## Componentes

### 1. store.php - Classe Principal

**Localização**: `classes/log/store.php`

**Responsabilidades**:

- Recebe eventos do Log Manager
- Transforma eventos Moodle → formato TSDB
- Gerencia buffer de escrita assíncrona
- Coordena flush de dados para TimescaleDB

**Interfaces Implementadas**:

```php
class store implements \tool_log\log\writer {
    public function write(\core\event\base $event, \tool_log\log\manager $manager);
    public function dispose();
}
```

**Métodos Principais**:

```php
// Inicialização
protected function load_config();
protected function init_client();

// Processamento de eventos
public function write(\core\event\base $event, ...);
protected function transform_event(\core\event\base $event);

// Gerenciamento de buffer
protected function buffer_event($datapoint);
protected function flush_buffer();

// Escrita
protected function write_datapoint($datapoint);

// Limpeza
public function dispose();
```

### 2. timescaledb_client.php - Cliente TimescaleDB

**Localização**: `classes/client/timescaledb_client.php`

**Responsabilidades**:

- Gerencia conexão PostgreSQL
- Implementa retry logic
- Executa batch inserts otimizados
- Fornece queries helper

**Métodos Principais**:

```php
// Conexão
protected function connect();
public function is_connected();

// Escrita
public function write_point(array $datapoint);
public function write_points(array $datapoints);

// Queries
public function query($sql, array $params = []);
public function count_events($where = '', array $params = []);
public function get_events(...);
public function get_statistics($starttime, $endtime);

// Utilitários
public function get_version();
public function get_timescaledb_version();
public function close();
```

---

## Fluxo de Dados

### 1. Captura do Evento

```php
// Usuário submete quiz
$event = \mod_quiz\event\attempt_submitted::create([
    'objectid' => $attemptid,
    'context' => $context,
]);
$event->trigger();
```

### 2. Recebimento pelo Log Manager

```php
// Log Manager observa evento (após commit do DB)
foreach ($this->writers as $writer) {
    $writer->write($event, $this);
}
```

### 3. Transformação do Evento

```php
protected function transform_event(\core\event\base $event) {
    return [
        'measurement' => 'moodle_events',
        'tags' => [
            'eventname' => $event->eventname,
            'component' => $event->component,
            'action' => $event->action,
            'target' => $event->target,
            'crud' => $event->crud,
            'edulevel' => (string)$event->edulevel,
            'courseid' => (string)$event->courseid,
        ],
        'fields' => [
            'userid' => $event->userid,
            'contextid' => $event->contextid,
            'objectid' => $event->objectid ?? null,
            'ip' => getremoteaddr(),
            'other' => json_encode($event->other),
            // ... outros campos
        ],
        'timestamp' => $event->timecreated,
    ];
}
```

### 4. Buffering (Modo Async)

```php
protected function buffer_event($datapoint) {
    $this->buffer[] = $datapoint;

    // Flush se buffer cheio OU intervalo atingido
    if (count($this->buffer) >= $this->config['buffersize'] ||
        (time() - $this->lastflush) >= $this->config['flushinterval']) {
        $this->flush_buffer();
    }
}
```

### 5. Escrita no TimescaleDB

```php
public function write_points(array $datapoints) {
    pg_query($this->connection, 'BEGIN');

    $sql = "INSERT INTO moodle_events (time, eventname, component, ...)
            VALUES ($1, $2, $3, ...)";

    foreach ($datapoints as $dp) {
        pg_query_params($this->connection, $sql, $params);
    }

    pg_query($this->connection, 'COMMIT');
}
```

---

## Schema do Banco

### Tabela Principal: moodle_events

```sql
CREATE TABLE moodle_events (
    time TIMESTAMPTZ NOT NULL,
    eventname TEXT NOT NULL,
    component TEXT,
    action TEXT,
    target TEXT,
    crud CHAR(1),
    edulevel SMALLINT,
    anonymous SMALLINT DEFAULT 0,
    courseid INTEGER,
    contextid INTEGER,
    contextlevel INTEGER,
    contextinstanceid INTEGER,
    userid INTEGER,
    relateduserid INTEGER,
    realuserid INTEGER,
    objectid INTEGER,
    objecttable TEXT,
    ip INET,
    origin TEXT DEFAULT 'web',
    other JSONB
);
```

### Conversão para Hypertable

```sql
SELECT create_hypertable('moodle_events', 'time',
    chunk_time_interval => INTERVAL '1 day'
);
```

**Benefícios**:
- Dados particionados automaticamente por dia
- Queries temporais otimizadas
- Compressão por chunk

### Índices Otimizados

```sql
-- Por nome de evento
CREATE INDEX idx_moodle_events_eventname
ON moodle_events (time DESC, eventname);

-- Por curso
CREATE INDEX idx_moodle_events_course
ON moodle_events (time DESC, courseid)
WHERE courseid > 0;

-- Por usuário
CREATE INDEX idx_moodle_events_user
ON moodle_events (time DESC, userid);

-- Por component + action
CREATE INDEX idx_moodle_events_component
ON moodle_events (time DESC, component, action);
```

### Políticas de Compressão

```sql
ALTER TABLE moodle_events SET (
  timescaledb.compress,
  timescaledb.compress_segmentby = 'eventname, component',
  timescaledb.compress_orderby = 'time DESC'
);

-- Comprimir chunks > 7 dias
SELECT add_compression_policy('moodle_events', INTERVAL '7 days');
```

### Políticas de Retenção

```sql
-- Deletar dados > 365 dias
SELECT add_retention_policy('moodle_events', INTERVAL '365 days');
```

---

## Configuração

### Configurações Administrativas

**Localização**: Site Administration → Plugins → Logging → TSDB Log Store

| Setting | Default | Descrição |
|---------|---------|-----------|
| `tsdb_type` | timescaledb | Tipo de TSDB (futuro: influxdb) |
| `host` | localhost | Host do TimescaleDB |
| `port` | 5433 | Porta |
| `database` | moodle_logs_tsdb | Nome do database |
| `username` | moodleuser | Usuário |
| `password` | - | Senha |
| `writemode` | async | sync ou async |
| `buffersize` | 1000 | Tamanho do buffer (async) |
| `flushinterval` | 60 | Intervalo de flush em segundos |

### Configuração via config.php

```php
// Opcional: sobrescrever configurações
$CFG->logstore_tsdb_host = 'timescaledb.example.com';
$CFG->logstore_tsdb_port = '5432';
$CFG->logstore_tsdb_writemode = 'async';
```

---

## Performance e Otimização

### Modo Síncrono vs Assíncrono

**Modo Síncrono** (`writemode = 'sync'`):

- ✅ Dados disponíveis imediatamente
- ❌ Adiciona 5-10ms de latência por requisição
- 📊 Uso: Debugging, ambientes pequenos

**Modo Assíncrono** (`writemode = 'async'`):

- ✅ ~0.1ms de latência (apenas buffer)
- ✅ Batch writes (muito mais eficiente)
- ⏰ Delay de até 60 segundos nos dados
- 📊 Uso: Produção, alto volume

### Tuning de Performance

**Buffer Size**:
```
Pequeno (100):  Flush frequente, menor uso de memória
Médio (1000):   Balanceado (recomendado)
Grande (5000):  Menos flushes, maior uso de memória
```

**Flush Interval**:
```
10s:  Dados quase em tempo real
60s:  Padrão, boa performance
300s: Máxima performance, delay aceitável
```

### Métricas de Performance Típicas

**Escrita**:
- Modo sync: ~1.000 eventos/segundo
- Modo async: ~10.000 eventos/segundo

**Leitura**:
- Query "últimas 24h": 100-500ms
- Agregação por hora (7 dias): 200-800ms
- Full table scan (evitar): segundos/minutos

---

## Troubleshooting

### Plugin não aparece nas configurações

**Problema**: Logstore TSDB não listado em Site Administration

**Solução**:
1. Verificar instalação: `ls -la public/admin/tool/log/store/tsdb/`
2. Rodar upgrade: `php admin/cli/upgrade.php`
3. Purgar caches: `php admin/cli/purge_caches.php`

### Erro de conexão com TimescaleDB

**Problema**: "Error connecting to TimescaleDB"

**Solução**:
```bash
# 1. Verificar se TimescaleDB está rodando
docker ps | grep timescaledb

# 2. Testar conexão manual
psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb

# 3. Verificar configurações no Moodle
# Site Administration → Plugins → Logging → TSDB Log Store

# 4. Ver logs de debug
tail -f moodledata/debug.log
```

### Eventos não estão sendo escritos

**Diagnóstico**:

```php
// Habilitar debugging em config.php
$CFG->debug = (E_ALL | E_STRICT);
$CFG->debugdisplay = 1;

// Verificar se plugin está habilitado
SELECT * FROM mdl_config WHERE name LIKE '%logstore%';

// Verificar logs
tail -f moodledata/logs/moodle.log
```

**Checklist**:
- [ ] Plugin está instalado e habilitado
- [ ] TimescaleDB está acessível
- [ ] Credenciais corretas
- [ ] Hypertable foi criada
- [ ] Não há erros PHP

### Buffer não está fazendo flush

**Problema**: Eventos ficam em buffer mas não são escritos

**Causas Comuns**:
1. `buffersize` muito grande
2. `flushinterval` muito longo
3. Erro de conexão (eventos descartados)

**Solução**:
```php
// Forçar flush manual para debug
$manager = get_log_manager();
foreach ($manager->get_readers() as $reader) {
    if ($reader instanceof \logstore_tsdb\log\store) {
        $reader->dispose(); // Força flush
    }
}
```

### Performance degradada

**Problema**: Queries lentas no TimescaleDB

**Diagnóstico**:
```sql
-- Ver tamanho da tabela
SELECT pg_size_pretty(pg_total_relation_size('moodle_events'));

-- Ver chunks
SELECT show_chunks('moodle_events');

-- Ver índices
SELECT * FROM pg_indexes WHERE tablename = 'moodle_events';
```

**Otimizações**:
1. Comprimir chunks antigos manualmente
2. Atualizar estatísticas: `ANALYZE moodle_events;`
3. Reindexar se necessário
4. Ajustar `work_mem` do PostgreSQL

---

## Como Adicionar Features

### Adicionar novo tipo de TSDB (ex: InfluxDB)

1. Criar `classes/client/influxdb_client.php`
2. Implementar mesma interface que `timescaledb_client`
3. Atualizar `init_client()` em `store.php`:

```php
protected function init_client() {
    if ($this->config['tsdb_type'] === 'influxdb') {
        $this->client = new \logstore_tsdb\client\influxdb_client($this->config);
    } else if ($this->config['tsdb_type'] === 'timescaledb') {
        // ...
    }
}
```

### Adicionar campos customizados ao evento

Editar `transform_event()`:

```php
protected function transform_event(\core\event\base $event) {
    return [
        // ... campos existentes
        'fields' => [
            // ... campos existentes
            'custom_field' => $event->other['mycustomfield'] ?? null,
        ],
    ];
}
```

Atualizar schema SQL:

```sql
ALTER TABLE moodle_events ADD COLUMN custom_field TEXT;
```

### Criar continuous aggregates (views materializadas)

```sql
CREATE MATERIALIZED VIEW events_by_hour
WITH (timescaledb.continuous) AS
SELECT
  time_bucket('1 hour', time) AS hour,
  eventname,
  COUNT(*) as count
FROM moodle_events
GROUP BY hour, eventname;

-- Refresh automático
SELECT add_continuous_aggregate_policy('events_by_hour',
  start_offset => INTERVAL '1 month',
  end_offset => INTERVAL '1 hour',
  schedule_interval => INTERVAL '1 hour');
```

---

## Referências

- [TimescaleDB Documentation](https://docs.timescale.com/)
- [Moodle Logging API](https://moodledev.io/docs/apis/core/log)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [TCC: Plugin TSDB - Análise Completa](./TCC-LOGGING-GUIDE.md)
