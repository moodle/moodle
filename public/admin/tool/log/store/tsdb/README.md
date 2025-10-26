# Logstore TSDB - Time Series Database Log Store

Plugin de armazenamento de logs do Moodle para Time Series Databases (InfluxDB/TimescaleDB).

## Descrição

Este plugin logstore permite armazenar eventos do Moodle em um banco de dados de séries temporais (TSDB) ao invés do banco de dados relacional tradicional. Isso oferece:

- **Performance superior** para queries temporais
- **Compressão agressiva** reduzindo armazenamento em 90%+
- **Agregações nativas** otimizadas para análises
- **Políticas de retenção automáticas** para gerenciamento de ciclo de vida dos dados
- **Escalabilidade horizontal** para grandes volumes

## Status do Desenvolvimento

🚧 **EM DESENVOLVIMENTO - ALPHA** 🚧

Este é um plugin em desenvolvimento ativo para TCC. Funcionalidades atualmente implementadas:

- ✅ Estrutura básica do plugin
- ✅ Interface de configuração administrativa
- ✅ Transformação de eventos Moodle → formato TSDB
- ✅ Suporte a escrita síncrona e assíncrona (buffering)
- ⏳ Integração InfluxDB (em desenvolvimento)
- ⏳ Integração TimescaleDB (planejado)
- ⏳ Interface de leitura para relatórios (planejado)
- ⏳ Testes unitários e de integração (planejado)

## Requisitos

- Moodle 4.4+
- PHP 8.2+
- Um dos seguintes TSDBs:
  - InfluxDB 2.x
  - TimescaleDB (PostgreSQL 14+ com extensão TimescaleDB)

## Instalação

### 1. Clone/Copie o Plugin

```bash
cd public/admin/tool/log/store/
# Plugin já está aqui se você está no repositório do TCC
```

### 2. Instale via Interface Admin

1. Acesse: **Site Administration → Notifications**
2. O Moodle detectará o novo plugin
3. Clique em "Upgrade Moodle database now"

### 3. Configure o Plugin

1. Acesse: **Site Administration → Plugins → Logging → Manage log stores**
2. Habilite "Time Series Database Log Store"
3. Clique no ícone de configurações (engrenagem)
4. Configure:
   - TSDB Type: InfluxDB ou TimescaleDB
   - Host: localhost (ou IP do servidor TSDB)
   - Port: 8086 (InfluxDB) ou 5432 (TimescaleDB)
   - Database/Bucket: moodle_logs
   - Username/Password: credenciais do TSDB
   - Write Mode: Asynchronous (recomendado)

### 4. Configure TSDB Backend

#### InfluxDB via Docker

```bash
docker run -d \
  --name influxdb \
  -p 8086:8086 \
  -v influxdb-data:/var/lib/influxdb2 \
  influxdb:2.7

# Setup inicial em http://localhost:8086
```

#### TimescaleDB via Docker

```bash
docker run -d \
  --name timescaledb \
  -p 5432:5432 \
  -e POSTGRES_PASSWORD=moodle_tsdb_password \
  timescale/timescaledb:latest-pg14
```

## Uso

Após configurado, o plugin automaticamente:

1. Captura todos os eventos do Moodle
2. Transforma em formato TSDB otimizado
3. Escreve no TSDB (síncrona ou assincronamente)

### Modos de Escrita

**Síncrono:**
- Eventos escritos imediatamente
- Adiciona pequena latência às requisições
- Ideal para: debugging, desenvolvimento

**Assíncrono (recomendado):**
- Eventos bufferizados em memória
- Escritas em lote periódicas
- Zero impacto na experiência do usuário
- Ideal para: produção, alto volume

## Estrutura de Dados

### Formato InfluxDB (Line Protocol)

```
moodle_events,eventname=\core\event\user_loggedin,component=core,action=loggedin,courseid=0 userid=42i,contextid=1i 1706198400
```

### Formato TimescaleDB (SQL)

```sql
INSERT INTO moodle_logs VALUES (
    '2025-01-25 14:30:00',           -- timestamp
    '\core\event\user_loggedin',     -- eventname
    'core',                          -- component
    'loggedin',                      -- action
    42,                              -- userid
    ...
);
```

## Desenvolvimento

### Executar Testes

```bash
# Testes unitários (quando implementados)
vendor/bin/phpunit public/admin/tool/log/store/tsdb/tests/

# Verificar padrões de código
vendor/bin/phpcs --standard=moodle public/admin/tool/log/store/tsdb/
```

### Adicionar Nova Funcionalidade

1. Implemente em `classes/log/store.php`
2. Adicione strings em `lang/en/logstore_tsdb.php`
3. Adicione settings em `settings.php` se necessário
4. Documente no README

### Debugging

Habilite debugging no Moodle (`config.php`):

```php
$CFG->debug = (E_ALL | E_STRICT);
$CFG->debugdisplay = 1;
```

Verifique logs de debugging para mensagens do plugin.

## Roadmap

### Fase 1: Core Implementation (atual)
- [x] Estrutura básica
- [x] Interface de configuração
- [ ] Cliente InfluxDB funcional
- [ ] Testes básicos

### Fase 2: Otimizações
- [ ] Retry logic com exponential backoff
- [ ] Fallback para arquivo local em caso de falha
- [ ] Métricas de monitoramento
- [ ] Scheduled task para flush garantido

### Fase 3: Advanced Features
- [ ] Cliente TimescaleDB
- [ ] Interface de leitura (reader)
- [ ] Filtros configuráveis de eventos
- [ ] Downsampling automático

### Fase 4: Analytics
- [ ] Dashboards Grafana pré-configurados
- [ ] Queries de exemplo
- [ ] Documentação de análise de dados

## Arquitetura

```
┌─────────────┐
│   Moodle    │
│   Events    │
└──────┬──────┘
       ↓
┌─────────────┐
│ Log Manager │
└──────┬──────┘
       ↓
┌─────────────┐      ┌──────────────┐
│  Logstore   │─────→│    Buffer    │
│    TSDB     │      │  (optional)  │
└──────┬──────┘      └──────┬───────┘
       ↓                    ↓
┌─────────────┐      ┌──────────────┐
│   Client    │      │ Batch Write  │
│  InfluxDB/  │←─────│              │
│ TimescaleDB │      └──────────────┘
└─────────────┘
```

## Documentação Adicional

- [Guia Técnico de Logging](../../../../../../../docs/TCC-LOGGING-GUIDE.md)
- [Setup do Ambiente](../../../../../../../docs/DEV-ENVIRONMENT.md)
- [Moodle Logging API](https://moodledev.io/docs/apis/subsystems/logging)

## Licença

GPL v3 ou posterior. Veja [LICENSE](https://www.gnu.org/licenses/gpl-3.0.html).

## Autor

Desenvolvido como parte de TCC sobre integração de Time Series Databases em sistemas de LMS.

---

**Última atualização**: 2025-01-25
