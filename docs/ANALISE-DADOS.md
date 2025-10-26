# Análise de Dados: Queries e Visualização

**Explorando dados do TimescaleDB com SQL e Grafana**

---

## Conectar ao TimescaleDB

```bash
# Via Docker
docker exec -it timescaledb psql -U moodleuser -d moodle_logs_tsdb

# Via psql local
psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb
```

---

## Queries Essenciais

### 1. Visão Geral dos Dados

```sql
-- Total de eventos
SELECT COUNT(*) as total_events FROM moodle_events;

-- Período coberto
SELECT
  MIN(time) as first_event,
  MAX(time) as last_event,
  AGE(MAX(time), MIN(time)) as time_span
FROM moodle_events;

-- Eventos por dia
SELECT
  time_bucket('1 day', time) AS day,
  COUNT(*) as events
FROM moodle_events
GROUP BY day
ORDER BY day DESC
LIMIT 30;
```

### 2. Top Eventos

```sql
-- Eventos mais frequentes
SELECT
  eventname,
  COUNT(*) as count,
  ROUND(100.0 * COUNT(*) / SUM(COUNT(*)) OVER (), 2) as percentage
FROM moodle_events
WHERE time > NOW() - INTERVAL '7 days'
GROUP BY eventname
ORDER BY count DESC
LIMIT 20;

-- Por component
SELECT
  component,
  action,
  COUNT(*) as count
FROM moodle_events
WHERE time > NOW() - INTERVAL '7 days'
GROUP BY component, action
ORDER BY count DESC
LIMIT 15;
```

### 3. Análise Temporal

```sql
-- Eventos por hora do dia
SELECT
  EXTRACT(HOUR FROM time) AS hour,
  COUNT(*) as events,
  COUNT(DISTINCT userid) as unique_users
FROM moodle_events
WHERE time > NOW() - INTERVAL '7 days'
GROUP BY hour
ORDER BY hour;

-- Picos de atividade
SELECT
  time_bucket('1 hour', time) AS hour,
  COUNT(*) as events,
  MAX(COUNT(*)) OVER () as max_events
FROM moodle_events
WHERE time > NOW() - INTERVAL '24 hours'
GROUP BY hour
ORDER BY events DESC
LIMIT 10;
```

### 4. Análise de Usuários

```sql
-- Usuários mais ativos
SELECT
  userid,
  COUNT(*) as actions,
  COUNT(DISTINCT DATE(time)) as active_days,
  MIN(time) as first_seen,
  MAX(time) as last_seen
FROM moodle_events
WHERE time > NOW() - INTERVAL '30 days'
GROUP BY userid
ORDER BY actions DESC
LIMIT 20;

-- Distribuição de atividade
SELECT
  activity_level,
  COUNT(*) as users
FROM (
  SELECT
    userid,
    CASE
      WHEN COUNT(*) > 1000 THEN 'Very Active'
      WHEN COUNT(*) > 100 THEN 'Active'
      WHEN COUNT(*) > 10 THEN 'Moderate'
      ELSE 'Low'
    END as activity_level
  FROM moodle_events
  WHERE time > NOW() - INTERVAL '30 days'
  GROUP BY userid
) user_activity
GROUP BY activity_level
ORDER BY
  CASE activity_level
    WHEN 'Very Active' THEN 1
    WHEN 'Active' THEN 2
    WHEN 'Moderate' THEN 3
    ELSE 4
  END;
```

### 5. Análise de Cursos

```sql
-- Cursos mais acessados
SELECT
  courseid,
  COUNT(*) as views,
  COUNT(DISTINCT userid) as unique_users,
  MAX(time) as last_activity
FROM moodle_events
WHERE courseid > 0
  AND time > NOW() - INTERVAL '7 days'
GROUP BY courseid
ORDER BY views DESC
LIMIT 15;

-- Atividade por curso ao longo do tempo
SELECT
  courseid,
  time_bucket('1 day', time) AS day,
  COUNT(*) as events
FROM moodle_events
WHERE courseid IN (SELECT courseid FROM moodle_events
                   WHERE time > NOW() - INTERVAL '7 days'
                   GROUP BY courseid
                   ORDER BY COUNT(*) DESC
                   LIMIT 5)
GROUP BY courseid, day
ORDER BY day DESC, events DESC;
```

### 6. KPIs Educacionais

```sql
-- Taxa de engajamento (últimos 7 dias)
SELECT
  COUNT(DISTINCT userid) as active_users,
  COUNT(*) as total_interactions,
  ROUND(COUNT(*) / NULLIF(COUNT(DISTINCT userid), 0), 2) as avg_interactions_per_user
FROM moodle_events
WHERE time > NOW() - INTERVAL '7 days';

-- Retenção de usuários
WITH daily_users AS (
  SELECT
    DATE(time) as day,
    userid
  FROM moodle_events
  WHERE time > NOW() - INTERVAL '30 days'
  GROUP BY DATE(time), userid
)
SELECT
  day,
  COUNT(DISTINCT userid) as daily_active_users
FROM daily_users
GROUP BY day
ORDER BY day;

-- Taxa de conclusão de atividades
SELECT
  component,
  COUNT(*) FILTER (WHERE action = 'submitted') as submissions,
  COUNT(*) FILTER (WHERE action = 'viewed') as views,
  ROUND(100.0 * COUNT(*) FILTER (WHERE action = 'submitted') /
        NULLIF(COUNT(*) FILTER (WHERE action = 'viewed'), 0), 2) as completion_rate
FROM moodle_events
WHERE component LIKE 'mod_%'
  AND time > NOW() - INTERVAL '30 days'
GROUP BY component
HAVING COUNT(*) FILTER (WHERE action = 'viewed') > 0
ORDER BY completion_rate DESC;
```

### 7. Performance do Sistema

```sql
-- Taxa de eventos por segundo
SELECT
  time_bucket('1 minute', time) AS minute,
  COUNT(*) as events,
  ROUND(COUNT(*) / 60.0, 2) as events_per_second
FROM moodle_events
WHERE time > NOW() - INTERVAL '1 hour'
GROUP BY minute
ORDER BY minute DESC;

-- Distribuição de tipos de eventos (CRUD)
SELECT
  crud,
  CASE crud
    WHEN 'c' THEN 'Create'
    WHEN 'r' THEN 'Read'
    WHEN 'u' THEN 'Update'
    WHEN 'd' THEN 'Delete'
  END as operation,
  COUNT(*) as count,
  ROUND(100.0 * COUNT(*) / SUM(COUNT(*)) OVER (), 2) as percentage
FROM moodle_events
WHERE time > NOW() - INTERVAL '7 days'
GROUP BY crud
ORDER BY count DESC;
```

---

## Continuous Aggregates (Views Materializadas)

### Criar Aggregate para Dashboard

```sql
-- Agregar eventos por hora
CREATE MATERIALIZED VIEW events_hourly
WITH (timescaledb.continuous) AS
SELECT
  time_bucket('1 hour', time) AS hour,
  component,
  action,
  COUNT(*) as event_count,
  COUNT(DISTINCT userid) as unique_users
FROM moodle_events
GROUP BY hour, component, action;

-- Policy de refresh automático
SELECT add_continuous_aggregate_policy('events_hourly',
  start_offset => INTERVAL '1 week',
  end_offset => INTERVAL '1 hour',
  schedule_interval => INTERVAL '1 hour');
```

### Usar Aggregate

```sql
-- Query rápida com dados pré-agregados
SELECT
  hour,
  SUM(event_count) as total_events,
  SUM(unique_users) as total_users
FROM events_hourly
WHERE hour > NOW() - INTERVAL '7 days'
GROUP BY hour
ORDER BY hour DESC;
-- ⚡ Muito mais rápido que query original!
```

---

## Configurar Grafana

### 1. Instalar Grafana

```bash
# Via Docker
docker run -d \
  --name=grafana \
  -p 3000:3000 \
  --network=host \
  grafana/grafana-oss
```

Acessar: `http://localhost:3000`
- User: admin
- Pass: admin

### 2. Adicionar Data Source

1. Configuration → Data Sources → Add data source
2. Selecionar: **PostgreSQL**
3. Configurar:
   - **Host**: `localhost:5433`
   - **Database**: `moodle_logs_tsdb`
   - **User**: `moodleuser`
   - **Password**: (sua senha)
   - **TLS/SSL Mode**: disable (para local)
   - **TimescaleDB**: ✅ Enable

4. Test & Save

### 3. Dashboards Pré-Configurados

#### Dashboard: Visão Geral

**Panel 1: Total de Eventos (Stat)**
```sql
SELECT COUNT(*) FROM moodle_events
WHERE time > NOW() - INTERVAL '24 hours'
```

**Panel 2: Eventos por Hora (Time Series)**
```sql
SELECT
  time_bucket('1 hour', time) AS time,
  COUNT(*) as value
FROM moodle_events
WHERE time > $__timeFrom() AND time < $__timeTo()
GROUP BY time
ORDER BY time
```

**Panel 3: Top Eventos (Bar Chart)**
```sql
SELECT
  eventname as metric,
  COUNT(*) as value
FROM moodle_events
WHERE time > $__timeFrom() AND time < $__timeTo()
GROUP BY eventname
ORDER BY value DESC
LIMIT 10
```

**Panel 4: Usuários Ativos (Stat)**
```sql
SELECT COUNT(DISTINCT userid)
FROM moodle_events
WHERE time > NOW() - INTERVAL '24 hours'
```

#### Dashboard: Performance

**Panel 1: Taxa de Eventos/Segundo**
```sql
SELECT
  time_bucket('1 minute', time) AS time,
  COUNT(*) / 60.0 as "Events/sec"
FROM moodle_events
WHERE time > $__timeFrom() AND time < $__timeTo()
GROUP BY time
ORDER BY time
```

**Panel 2: Latência de Escrita**
```sql
SELECT
  time_bucket('5 minutes', time) AS time,
  AVG(EXTRACT(EPOCH FROM (NOW() - time))) as "Avg Delay (s)"
FROM moodle_events
WHERE time > $__timeFrom() AND time < $__timeTo()
GROUP BY time
ORDER BY time
```

---

## Exportar Dados

### CSV

```bash
# Exportar últimos 7 dias
psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb -c "
COPY (
  SELECT * FROM moodle_events
  WHERE time > NOW() - INTERVAL '7 days'
) TO STDOUT WITH CSV HEADER" > events_7days.csv
```

### JSON

```bash
# Exportar agregados por dia
psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb -t -c "
SELECT json_agg(row_to_json(t))
FROM (
  SELECT
    DATE(time) as day,
    COUNT(*) as events,
    COUNT(DISTINCT userid) as users
  FROM moodle_events
  WHERE time > NOW() - INTERVAL '30 days'
  GROUP BY DATE(time)
  ORDER BY day
) t" > events_30days.json
```

---

## Comparativo: Standard vs TSDB

### Query Performance

```sql
-- TSDB
EXPLAIN ANALYZE
SELECT COUNT(*) FROM moodle_events
WHERE time > NOW() - INTERVAL '7 days';

-- Standard (no Moodle)
EXPLAIN ANALYZE
SELECT COUNT(*) FROM mdl_logstore_standard_log
WHERE timecreated > UNIX_TIMESTAMP(NOW() - INTERVAL 7 DAY);
```

### Storage Efficiency

```sql
-- TSDB
SELECT
  pg_size_pretty(pg_total_relation_size('moodle_events')) as size,
  COUNT(*) as events,
  pg_size_pretty(pg_total_relation_size('moodle_events') / COUNT(*)) as size_per_event
FROM moodle_events;

-- Standard
SELECT
  pg_size_pretty(SUM(data_length + index_length)) as size,
  COUNT(*) as events
FROM information_schema.tables
WHERE table_name = 'mdl_logstore_standard_log';
```

---

## Análise para o TCC

### Métricas a Documentar

1. **Performance**:
   - Tempo de query: Standard vs TSDB
   - Taxa de escrita (eventos/segundo)
   - Latência média

2. **Storage**:
   - Tamanho total (GB)
   - Tamanho por evento
   - Taxa de compressão

3. **Escalabilidade**:
   - Degradação com aumento de volume
   - Comportamento com milhões de eventos

### Gerar Gráficos

```python
import psycopg2
import matplotlib.pyplot as plt
import pandas as pd

# Conectar
conn = psycopg2.connect(
    host='localhost',
    port=5433,
    database='moodle_logs_tsdb',
    user='moodleuser'
)

# Query
df = pd.read_sql("""
    SELECT
        time_bucket('1 hour', time) AS hour,
        COUNT(*) as events
    FROM moodle_events
    WHERE time > NOW() - INTERVAL '7 days'
    GROUP BY hour
    ORDER BY hour
""", conn)

# Plot
plt.figure(figsize=(12, 6))
plt.plot(df['hour'], df['events'])
plt.title('Eventos por Hora (Últimos 7 Dias)')
plt.xlabel('Hora')
plt.ylabel('Número de Eventos')
plt.xticks(rotation=45)
plt.tight_layout()
plt.savefig('eventos_7_dias.png', dpi=300)
```

---

## Próximos Passos

1. Criar dashboards customizados no Grafana
2. Configurar alertas para picos de atividade
3. Documentar queries para o TCC
4. Comparar métricas com logstore_standard
5. Gerar relatórios visuais para apresentação
