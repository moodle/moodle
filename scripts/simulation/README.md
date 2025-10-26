# Moodle Load Simulation

Scripts Python para simular carga realista na plataforma Moodle e gerar eventos de log para teste do plugin `logstore_tsdb`.

## Visão Geral

Este conjunto de scripts permite:

- ✅ Criar centenas de usuários fake
- ✅ Criar cursos e inscrever usuários
- ✅ Simular interações realistas (views, quizzes, fóruns, etc.)
- ✅ Gerar milhares de eventos de log
- ✅ Testar performance do logstore TSDB

## Instalação

### 1. Instalar Dependências Python

```bash
cd scripts/simulation
pip install -r requirements.txt
```

### 2. Configurar Moodle Web Services

**No Moodle:**

1. Site Administration → Plugins → Web services → Overview
2. Habilitar Web Services
3. Criar um novo Service: "Load Simulation Service"
4. Adicionar funções necessárias:
   - `core_user_create_users`
   - `core_user_get_users`
   - `core_course_create_courses`
   - `core_course_get_courses`
   - `core_course_view_course`
   - `enrol_manual_enrol_users`
   - `core_webservice_get_site_info`
5. Criar um usuário de API ou usar admin
6. Gerar token: Site Administration → Plugins → Web services → Manage tokens

### 3. Configurar config.json

Edite `config.json` e atualize:

```json
{
  "moodle": {
    "base_url": "http://localhost/moodle-plugin-rework/public",
    "wstoken": "SEU_TOKEN_AQUI"
  }
}
```

## Uso

### Modo Básico

```bash
python generate_load.py
```

Usa configurações padrão do `config.json`.

### Modos de Simulação

**1. Modo Realista (padrão)**

Simula padrões realistas baseados na hora do dia:

```bash
python generate_load.py --mode realistic --duration 3600
```

- Picos de atividade: 9-11h, 14-16h, 19-21h
- Baixa atividade: 0-6h, 23h
- Duração: 1 hora (3600 segundos)

**2. Modo Steady (carga constante)**

Taxa constante de eventos:

```bash
python generate_load.py --mode steady --duration 1800
```

- Taxa constante de 10 eventos/segundo
- Útil para testes de performance

**3. Modo Burst (rajadas)**

Rajadas intensas de atividade:

```bash
python generate_load.py --mode burst
```

- 100 eventos em 10 segundos
- Testa picos de carga

### Opções Avançadas

**Pular setup inicial**

Se usuários e cursos já existem:

```bash
python generate_load.py --skip-setup
```

**Arquivo de configuração customizado**

```bash
python generate_load.py --config my_config.json
```

## Estrutura de Arquivos

```
simulation/
├── README.md               # Este arquivo
├── requirements.txt        # Dependências Python
├── config.json            # Configurações
├── generate_load.py       # Script principal
├── modules/
│   ├── __init__.py
│   ├── moodle_api.py      # Cliente API Moodle
│   ├── users.py           # Gerenciamento de usuários
│   ├── courses.py         # Gerenciamento de cursos
│   ├── activities.py      # Atividades (quiz, assignment, etc.)
│   └── interactions.py    # Simulador de interações
└── data/
    └── (dados gerados)
```

## Configuração Detalhada

### Parâmetros de Simulação

```json
{
  "simulation": {
    "num_users": 100,              // Número de usuários a criar
    "num_courses": 10,             // Número de cursos
    "num_activities_per_course": 5, // Atividades por curso
    "duration_seconds": 3600,      // Duração da simulação
    "mode": "realistic",           // Modo: realistic/steady/burst
    "events_per_second": 10        // Taxa de eventos (steady mode)
  }
}
```

### Distribuição de Atividades

```json
{
  "activities": {
    "weights": {
      "course_view": 30,           // 30% - visualizações de curso
      "quiz_attempt": 15,          // 15% - tentativas de quiz
      "assignment_submit": 10,     // 10% - submissões de tarefa
      "forum_post": 10,            // 10% - posts em fórum
      "resource_download": 20,     // 20% - downloads
      "user_profile_view": 10,     // 10% - visualizações de perfil
      "dashboard_view": 5          // 5% - dashboard
    }
  }
}
```

## Exemplos de Uso

### Teste de Performance Básico

```bash
# 1. Criar ambiente
python generate_load.py --mode burst --duration 10

# 2. Gerar carga contínua
python generate_load.py --mode steady --duration 600
```

### Simulação Realista de Um Dia

```bash
# Simular 24 horas em 1 hora (acelerado 24x)
python generate_load.py --mode realistic --duration 3600
```

### Teste de Stress

```bash
# Múltiplas rajadas
for i in {1..10}; do
  python generate_load.py --mode burst --skip-setup
  sleep 5
done
```

## Verificação de Resultados

### 1. Verificar Logs do Moodle

```bash
tail -f moodledata/logs/moodle.log
```

### 2. Verificar Eventos no TimescaleDB

```sql
-- Conectar ao TimescaleDB
psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb

-- Ver eventos recentes
SELECT time, eventname, component, action, userid, courseid
FROM moodle_events
ORDER BY time DESC
LIMIT 20;

-- Contar eventos por hora
SELECT
  time_bucket('1 hour', time) AS hour,
  COUNT(*) as events
FROM moodle_events
WHERE time > NOW() - INTERVAL '24 hours'
GROUP BY hour
ORDER BY hour DESC;

-- Eventos por tipo
SELECT eventname, COUNT(*) as count
FROM moodle_events
WHERE time > NOW() - INTERVAL '1 hour'
GROUP BY eventname
ORDER BY count DESC;
```

### 3. Verificar Performance

```bash
# Ver estatísticas em tempo real
watch -n 5 'psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb -c "
SELECT COUNT(*) as total_events,
       MAX(time) as latest_event
FROM moodle_events"'
```

## Solução de Problemas

### Erro: "Web service token not configured"

- Verifique se `wstoken` está configurado no `config.json`
- Verifique se o token é válido no Moodle

### Erro: "Access control exception"

- Verifique se todas as funções necessárias estão habilitadas no Service
- Verifique capabilities do usuário de API

### Erro: "Connection refused"

- Verifique se o Moodle está rodando
- Verifique a URL em `base_url`

### Poucos eventos sendo gerados

- Aumente `events_per_second` no config.json
- Use modo `burst` para testes rápidos
- Verifique se há erros no log

## Métricas e KPIs

A simulação gera as seguintes métricas:

- **Total de eventos**: Quantidade total gerada
- **Taxa de sucesso**: % de eventos bem-sucedidos
- **Eventos/segundo**: Throughput médio
- **Duração**: Tempo total de execução
- **Distribuição por tipo**: Breakdown por tipo de evento

## Logs

Logs são salvos em:

- `simulation.log` - Log completo da simulação
- Console - Output em tempo real

Nível de log configurável em `config.json`:

```json
{
  "logging": {
    "level": "INFO",  // DEBUG, INFO, WARNING, ERROR
    "file": "simulation.log",
    "console": true
  }
}
```

## Contribuindo

Para adicionar novos tipos de interação:

1. Adicione a função em `modules/moodle_api.py`
2. Implemente simulação em `modules/interactions.py`
3. Atualize pesos em `config.json`

## Licença

GNU GPL v3 or later
