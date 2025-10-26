# Simulação de Carga: Guia Prático

**Como usar os scripts Python para gerar carga realista e testar o logstore_tsdb**

---

## Quick Start

```bash
# 1. Instalar dependências
cd scripts/simulation
pip install -r requirements.txt

# 2. Configurar Moodle Web Services (ver API-MOODLE.md)
# Obter token e colocar em config.json

# 3. Rodar simulação básica
python generate_load.py

# 4. Verificar logs no TimescaleDB
psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb -c "
SELECT COUNT(*) FROM moodle_events WHERE time > NOW() - INTERVAL '1 hour';"
```

---

## Cenários de Teste

### 1. Teste de Stress (Burst)

**Objetivo**: Testar picos de carga

```bash
python generate_load.py --mode burst

# Output esperado:
# - 100 eventos em 10 segundos
# - ~10 eventos/segundo
# - Testar capacidade de buffer do plugin
```

**Análise**:
```sql
-- Ver distribuição de eventos
SELECT
  time_bucket('1 second', time) AS second,
  COUNT(*) as events
FROM moodle_events
WHERE time > NOW() - INTERVAL '1 minute'
GROUP BY second
ORDER BY second;
```

### 2. Teste de Sustentação (Steady)

**Objetivo**: Carga constante prolongada

```bash
python generate_load.py --mode steady --duration 3600

# Output esperado:
# - 10 eventos/segundo
# - 36.000 eventos em 1 hora
# - Testar estabilidade e flush contínuo
```

**Análise**:
```sql
-- Performance ao longo do tempo
SELECT
  time_bucket('5 minutes', time) AS interval,
  COUNT(*) as events,
  AVG(EXTRACT(EPOCH FROM (time - LAG(time) OVER (ORDER BY time)))) as avg_interval
FROM moodle_events
WHERE time > NOW() - INTERVAL '1 hour'
GROUP BY interval
ORDER BY interval;
```

### 3. Teste Realista (Realistic)

**Objetivo**: Simular padrões reais de uso

```bash
python generate_load.py --mode realistic --duration 7200

# Output esperado:
# - Picos nos horários 9-11h, 14-16h, 19-21h
# - Baixa atividade 0-6h, 23h
# - Distribuição natural de atividades
```

**Análise**:
```sql
-- Ver padrão de uso por hora
SELECT
  EXTRACT(HOUR FROM time) AS hour,
  COUNT(*) as events,
  COUNT(DISTINCT userid) as active_users
FROM moodle_events
WHERE time > NOW() - INTERVAL '24 hours'
GROUP BY hour
ORDER BY hour;
```

---

## Configuração de Cenários

### config.json - Ajustes Principais

```json
{
  "simulation": {
    "num_users": 100,          // Ajuste para seu teste
    "num_courses": 10,
    "duration_seconds": 3600,
    "mode": "realistic"
  },

  "activities": {
    "weights": {
      "course_view": 30,       // % de visualizações
      "quiz_attempt": 15,      // % de quizzes
      "forum_post": 10         // % de posts
    }
  }
}
```

### Cenários Customizados

**Alta Intensidade**:
```json
{
  "simulation": {
    "num_users": 500,
    "events_per_second": 50,
    "mode": "steady"
  }
}
```

**Longevidade**:
```json
{
  "simulation": {
    "duration_seconds": 86400,  // 24 horas
    "mode": "realistic"
  }
}
```

**Teste de Recuperação**:
```bash
# Simular falha e recuperação
python generate_load.py --mode burst &
# Parar TimescaleDB
docker stop timescaledb
sleep 30
# Reiniciar TimescaleDB
docker start timescaledb
# Verificar se eventos foram bufferizados e escritos
```

---

## Métricas de Avaliação

### 1. Performance do Plugin

```sql
-- Taxa de escrita
SELECT
  time_bucket('1 minute', time) AS minute,
  COUNT(*) as events_written,
  COUNT(*) / 60.0 as events_per_second
FROM moodle_events
WHERE time > NOW() - INTERVAL '10 minutes'
GROUP BY minute
ORDER BY minute DESC;
```

### 2. Latência

```sql
-- Delay entre evento real e timestamp de escrita
-- (requer campo adicional created_at)
SELECT
  AVG(EXTRACT(EPOCH FROM (created_at - time))) as avg_delay_seconds
FROM moodle_events
WHERE time > NOW() - INTERVAL '1 hour';
```

### 3. Taxa de Sucesso

```python
# No output do script
stats = {
    'total_events': 10000,
    'successful': 9950,
    'failed': 50,
    'success_rate': 99.5%
}
```

---

## Comparação: Standard vs TSDB

### Setup Paralelo

```bash
# Habilitar ambos os logstores
# Site Admin → Plugins → Logging → Manage log stores

# 1. logstore_standard: Enabled
# 2. logstore_tsdb: Enabled
```

### Rodar Teste Comparativo

```bash
# Gerar 10.000 eventos
python generate_load.py --mode steady --duration 1000

# Comparar tamanhos
```

**logstore_standard**:
```sql
SELECT
  pg_size_pretty(pg_total_relation_size('mdl_logstore_standard_log')) as size,
  COUNT(*) as events
FROM mdl_logstore_standard_log;
```

**logstore_tsdb**:
```sql
SELECT
  pg_size_pretty(pg_total_relation_size('moodle_events')) as size,
  COUNT(*) as events
FROM moodle_events;
```

### Comparar Queries

```sql
-- Standard (MySQL)
SELECT COUNT(*) FROM mdl_logstore_standard_log
WHERE timecreated > UNIX_TIMESTAMP(NOW() - INTERVAL 24 HOUR);
-- Tempo: ~15-30 segundos (em tabela grande)

-- TSDB (TimescaleDB)
SELECT COUNT(*) FROM moodle_events
WHERE time > NOW() - INTERVAL '24 hours';
-- Tempo: ~100-500ms
```

---

## Troubleshooting

### Poucos Eventos Gerados

**Problema**: Script roda mas poucos eventos aparecem no TSDB

**Debug**:
1. Verificar logs do Moodle: `tail -f moodledata/debug.log`
2. Verificar logs do script: `cat simulation.log`
3. Verificar buffer do plugin não está travado

**Solução**:
```php
// Forçar flush do buffer
php -r "
require_once('config.php');
\$manager = get_log_manager();
// Trigger dispose on all stores
"
```

### Erros de API

**Problema**: `Access control exception`

**Solução**: Verificar funções habilitadas no Web Service (ver API-MOODLE.md)

### Performance Degradada

**Problema**: Simulação fica lenta após muitos eventos

**Causa**: Buffer do plugin cheio, TimescaleDB lento

**Debug**:
```sql
-- Ver chunks do TimescaleDB
SELECT show_chunks('moodle_events');

-- Ver tamanho
SELECT pg_size_pretty(pg_total_relation_size('moodle_events'));
```

**Solução**:
- Comprimir chunks antigos
- Aumentar `buffersize` no plugin
- Otimizar configurações do PostgreSQL

---

## Best Practices

### 1. Testes Progressivos

```bash
# Começar pequeno
python generate_load.py --mode burst  # 100 eventos

# Escalar gradualmente
python generate_load.py --mode steady --duration 600  # 6.000 eventos

# Teste completo
python generate_load.py --mode realistic --duration 3600  # 36.000+ eventos
```

### 2. Monitoramento Durante Teste

Terminal 1:
```bash
python generate_load.py --mode steady --duration 3600
```

Terminal 2:
```bash
# Watch de eventos em tempo real
watch -n 5 'psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb -c "
SELECT COUNT(*) as total, MAX(time) as latest FROM moodle_events"'
```

Terminal 3:
```bash
# Monitor de recursos
docker stats timescaledb
```

### 3. Documentar Resultados

Criar `results.md`:
```markdown
# Teste de Carga - 2025-01-15

## Configuração
- Modo: steady
- Duração: 1 hora
- Taxa: 10 eventos/segundo

## Resultados
- Eventos gerados: 36.000
- Taxa de sucesso: 99.8%
- Tamanho final: 15MB
- Compressão: 85%
- Query "24h": 150ms

## Observações
- Buffer flush a cada 60s funcionou bem
- Sem degradação de performance
- Latência média: <1ms
```

---

## Próximos Passos

Após simulação bem-sucedida:

1. **Analisar Dados** → Ver [ANALISE-DADOS.md](./ANALISE-DADOS.md)
2. **Comparar Performance** → Métricas Standard vs TSDB
3. **Otimizar Configurações** → Tuning baseado em resultados
4. **Documentar para TCC** → Gráficos e conclusões
