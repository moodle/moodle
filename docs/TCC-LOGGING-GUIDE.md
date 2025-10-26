# Guia Técnico: Sistema de Logging do Moodle

**Objetivo**: Documentação técnica para desenvolvimento do plugin logstore_tsdb (Time Series Database integration)

**Autor**: Documentação para TCC - Integração de TSDB no Moodle

---

## Índice

1. [Visão Geral da Arquitetura](#visão-geral-da-arquitetura)
2. [Events API - Sistema de Eventos](#events-api---sistema-de-eventos)
3. [Log Manager - Gerenciador de Logs](#log-manager---gerenciador-de-logs)
4. [Logstore Plugins](#logstore-plugins)
5. [Desenvolvimento de Logstore Customizado](#desenvolvimento-de-logstore-customizado)
6. [Interfaces e Contratos](#interfaces-e-contratos)
7. [Event Data Structure](#event-data-structure)
8. [Exemplos Práticos](#exemplos-práticos)
9. [Testing](#testing)
10. [Performance e Otimizações](#performance-e-otimizações)

---

## Visão Geral da Arquitetura

### Fluxo Completo de Dados

```
┌─────────────────────────────────────────────────────────────┐
│                    CAMADA DE APLICAÇÃO                       │
│  (Módulos: Quiz, Assignment, Forum, Course, User, etc.)     │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│                      EVENTS API                              │
│  - Cria objetos de evento estruturados                      │
│  - Valida dados obrigatórios                                │
│  - Adiciona contexto automático                             │
└────────────────────────┬────────────────────────────────────┘
                         ↓
                   $event->trigger()
                         ↓
┌─────────────────────────────────────────────────────────────┐
│                   EVENT DISPATCHER                           │
│  - Aguarda commit da transação do banco                     │
│  - Notifica todos os observers registrados                  │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│                     LOG MANAGER                              │
│  - Recebe evento via observer pattern                       │
│  - Distribui para todos logstores habilitados               │
│  - Trata erros sem interromper o fluxo                      │
└─────┬───────────────────┬──────────────────┬────────────────┘
      ↓                   ↓                  ↓
┌──────────┐      ┌──────────────┐    ┌─────────────┐
│ Logstore │      │  Logstore    │    │  Logstore   │
│ Standard │      │  Database    │    │   TSDB      │
│          │      │  (External)  │    │  (Custom)   │
└────┬─────┘      └──────┬───────┘    └──────┬──────┘
     ↓                   ↓                    ↓
┌──────────┐      ┌──────────────┐    ┌─────────────┐
│  MySQL/  │      │   External   │    │  InfluxDB/  │
│PostgreSQL│      │   Database   │    │ TimescaleDB │
└──────────┘      └──────────────┘    └─────────────┘
```

### Componentes Principais

| Componente | Localização | Responsabilidade |
|------------|-------------|------------------|
| **Event Base Class** | `public/lib/classes/event/base.php` | Classe abstrata base para todos os eventos |
| **Event Examples** | `public/lib/classes/event/*.php` | 254+ eventos core (user_loggedin, course_viewed, etc.) |
| **Log Manager Interface** | `public/lib/classes/log/manager.php` | Interface que define contrato do gerenciador |
| **Log Manager Implementation** | `public/admin/tool/log/classes/log/manager.php` | Implementação concreta do gerenciador |
| **Writer Interface** | `public/admin/tool/log/classes/log/writer.php` | Interface para plugins que escrevem logs |
| **Reader Interface** | `public/lib/classes/log/reader.php` | Interface para plugins que leem logs |
| **SQL Reader Interface** | `public/lib/classes/log/sql_reader.php` | Interface estendida para readers SQL |
| **Logstore Standard** | `public/admin/tool/log/store/standard/` | Implementação padrão (banco relacional) |
| **Logstore Database** | `public/admin/tool/log/store/database/` | Implementação para banco externo |

---

## Events API - Sistema de Eventos

### Anatomia de um Evento

Todo evento no Moodle herda de `\core\event\base` e possui estrutura bem definida:

```php
<?php
namespace core\event;

abstract class base {
    protected $data = [
        // Identificação do evento
        'eventname' => '',      // Nome completo da classe (ex: \core\event\user_loggedin)
        'component' => '',      // Plugin que disparou (ex: core, mod_quiz, block_navigation)

        // O que aconteceu
        'action' => '',         // viewed, created, updated, deleted, submitted, etc.
        'target' => '',         // user, course, course_module, discussion, etc.
        'crud' => '',           // c=create, r=read, u=update, d=delete

        // Contexto
        'contextid' => 0,       // ID do contexto onde ocorreu
        'contextlevel' => 0,    // CONTEXT_SYSTEM, CONTEXT_COURSE, CONTEXT_MODULE, etc.
        'contextinstanceid' => 0,

        // Atores e relacionamentos
        'userid' => 0,          // Quem fez a ação
        'relateduserid' => 0,   // Usuário relacionado (opcional)
        'courseid' => 0,        // Curso onde ocorreu (0 se sistema)

        // Objeto afetado
        'objecttable' => '',    // Tabela do banco (ex: user, course, quiz_attempts)
        'objectid' => 0,        // ID do registro afetado

        // Metadados
        'edulevel' => 0,        // LEVEL_TEACHING, LEVEL_PARTICIPATING, LEVEL_OTHER
        'anonymous' => 0,       // Se deve ser ocultado em relatórios
        'other' => null,        // Array com dados adicionais específicos
        'timecreated' => 0,     // Unix timestamp
    ];
}
```

### Níveis Educacionais (edulevel)

```php
const LEVEL_OTHER = 0;          // Eventos administrativos/sistema
const LEVEL_TEACHING = 1;       // Ações de ensino (professor cria atividade)
const LEVEL_PARTICIPATING = 2;  // Ações de aprendizado (aluno submete tarefa)
```

### CRUD Indicator

| Letra | Significado | Exemplo |
|-------|-------------|---------|
| `c` | Create | Novo curso criado, novo usuário registrado |
| `r` | Read | Visualização de página, download de arquivo |
| `u` | Update | Edição de perfil, atualização de nota |
| `d` | Delete | Exclusão de post, remoção de usuário |

### Criando um Evento Customizado

**Localização**: `public/mod/yourplugin/classes/event/something_happened.php`

```php
<?php
namespace mod_yourplugin\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event disparado quando algo importante acontece.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int itemid: ID do item processado
 *      - string action: Tipo de ação realizada
 * }
 */
class something_happened extends \core\event\base {

    /**
     * Inicializa dados estáticos do evento.
     */
    protected function init() {
        $this->data['crud'] = 'c';                              // Create
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;    // Ação de estudante
        $this->data['objecttable'] = 'yourplugin_items';        // Tabela afetada
    }

    /**
     * Retorna nome legível do evento.
     */
    public static function get_name() {
        return get_string('eventsomethinghappened', 'mod_yourplugin');
    }

    /**
     * Retorna descrição do evento para logs.
     */
    public function get_description() {
        return "The user with id '$this->userid' performed action on item '{$this->objectid}' " .
               "in the yourplugin activity with course module id '$this->contextinstanceid'.";
    }

    /**
     * Retorna URL relacionada ao evento.
     */
    public function get_url() {
        return new \moodle_url('/mod/yourplugin/view.php', [
            'id' => $this->contextinstanceid
        ]);
    }

    /**
     * Validação customizada.
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['itemid'])) {
            throw new \coding_exception('The \'itemid\' value must be set in other.');
        }
    }
}
```

### Disparando o Evento

```php
<?php
// No código do seu plugin
$event = \mod_yourplugin\event\something_happened::create([
    'objectid' => $item->id,
    'context' => context_module::instance($cm->id),
    'other' => [
        'itemid' => $item->id,
        'action' => 'submit',
    ]
]);
$event->trigger();
```

---

## Log Manager - Gerenciador de Logs

### Implementação Atual

**Arquivo**: `public/admin/tool/log/classes/log/manager.php`

```php
<?php
namespace tool_log\log;

class manager implements \core\log\manager {

    /** @var \tool_log\log\store[] Lista de todos os logstores habilitados */
    protected $stores;

    /** @var \tool_log\log\writer[] Lista de logstores que escrevem */
    protected $writers;

    /** @var \core\log\reader[] Lista de logstores que leem */
    protected $readers;

    /**
     * Inicializa logstores habilitados.
     */
    protected function init() {
        $this->stores = [];
        $this->readers = [];
        $this->writers = [];

        // Registra shutdown handler para cleanup
        \core_shutdown_manager::register_function([$this, 'dispose']);

        // Carrega configuração de logstores habilitados
        $plugins = get_config('tool_log', 'enabled_stores');
        if (empty($plugins)) {
            return;
        }

        // Instancia cada logstore habilitado
        $plugins = explode(',', $plugins);
        foreach ($plugins as $plugin) {
            $classname = "\\$plugin\\log\\store";
            if (class_exists($classname)) {
                $store = new $classname($this);
                $this->stores[$plugin] = $store;

                // Registra como writer se implementar a interface
                if ($store instanceof \tool_log\log\writer) {
                    $this->writers[$plugin] = $store;
                }

                // Registra como reader se implementar a interface
                if ($store instanceof \core\log\reader) {
                    $this->readers[$plugin] = $store;
                }
            }
        }
    }

    /**
     * Processa evento recebido do Event Dispatcher.
     *
     * Chamado automaticamente após commit da transação.
     */
    public function process(\core\event\base $event) {
        $this->init();

        // Distribui para todos os writers
        foreach ($this->writers as $plugin => $writer) {
            try {
                $writer->write($event, $this);
            } catch (\Exception $e) {
                // Log do erro mas não interrompe outros logstores
                debugging(
                    'Exception detected when logging event ' . $event->eventname .
                    ' in ' . $plugin . ': ' . $e->getMessage(),
                    DEBUG_NORMAL,
                    $e->getTrace()
                );
            }
        }
    }

    /**
     * Retorna readers disponíveis para relatórios.
     *
     * @param string $interface Filtrar por interface específica
     * @return \core\log\reader[]
     */
    public function get_readers($interface = null) {
        $this->init();

        $return = [];
        foreach ($this->readers as $plugin => $reader) {
            if (empty($interface) || ($reader instanceof $interface)) {
                $return[$plugin] = $reader;
            }
        }

        return $return;
    }

    /**
     * Cleanup ao final da requisição.
     */
    public function dispose() {
        foreach ($this->stores as $store) {
            if (method_exists($store, 'dispose')) {
                $store->dispose();
            }
        }
    }
}
```

### Registro do Observer

O Log Manager é registrado como observer de TODOS os eventos via `public/admin/tool/log/db/events.php`:

```php
<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '*',  // Observa TODOS os eventos
        'callback' => '\tool_log\observer::log_event',
        'internal' => false,  // Chamado APÓS commit da transação
        'priority' => 9999,   // Executa por último
    ],
];
```

**Observer Implementation** (`public/admin/tool/log/classes/observer.php`):

```php
<?php
namespace tool_log;

class observer {
    public static function log_event(\core\event\base $event) {
        $manager = get_log_manager();
        $manager->process($event);
    }
}
```

---

## Logstore Plugins

### Estrutura de Diretórios Completa

```
public/admin/tool/log/store/yourstore/
├── classes/
│   ├── log/
│   │   └── store.php                    # Classe principal (OBRIGATÓRIO)
│   ├── privacy/
│   │   └── provider.php                 # Compliance GDPR (OBRIGATÓRIO)
│   └── task/
│       ├── cleanup_task.php             # Tarefa de limpeza (opcional)
│       └── buffer_flush.php             # Flush assíncrono (opcional)
├── db/
│   ├── install.xml                      # Schema do banco (se usar DB local)
│   ├── upgrade.php                      # Migrations
│   ├── tasks.php                        # Registro de tarefas agendadas
│   └── access.php                       # Capabilities
├── lang/
│   └── en/
│       └── logstore_yourstore.php       # Strings de idioma (OBRIGATÓRIO)
├── tests/
│   ├── store_test.php                   # Testes unitários
│   └── privacy/
│       └── provider_test.php            # Testes de privacy
├── settings.php                          # Configurações admin (OBRIGATÓRIO)
├── version.php                           # Metadados do plugin (OBRIGATÓRIO)
└── README.md                             # Documentação
```

### Arquivos Obrigatórios Detalhados

#### 1. version.php

```php
<?php
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'logstore_yourstore';
$plugin->version   = 2025012500;        // YYYYMMDDXX
$plugin->requires  = 2024042200;        // Moodle 4.4+
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = 'v1.0.0';
```

#### 2. lang/en/logstore_yourstore.php

```php
<?php
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Your Custom Log Store';
$string['pluginname_desc'] = 'A custom log store that saves events to [your storage]';
$string['privacy:metadata'] = 'The Your Store log plugin does not store any personal data.';

// Settings strings
$string['setting_host'] = 'Host';
$string['setting_host_desc'] = 'Hostname of the storage server';
$string['setting_port'] = 'Port';
$string['setting_port_desc'] = 'Port number';
```

#### 3. settings.php

```php
<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings->add(new admin_setting_configtext(
        'logstore_yourstore/host',
        get_string('setting_host', 'logstore_yourstore'),
        get_string('setting_host_desc', 'logstore_yourstore'),
        'localhost',
        PARAM_HOST
    ));

    $settings->add(new admin_setting_configtext(
        'logstore_yourstore/port',
        get_string('setting_port', 'logstore_yourstore'),
        get_string('setting_port_desc', 'logstore_yourstore'),
        '8086',
        PARAM_INT
    ));

    $settings->add(new admin_setting_configselect(
        'logstore_yourstore/writemode',
        'Write Mode',
        'Synchronous (blocking) or Asynchronous (buffered)',
        'async',
        [
            'sync' => 'Synchronous',
            'async' => 'Asynchronous (recommended)',
        ]
    ));
}
```

---

## Interfaces e Contratos

### 1. \tool_log\log\writer

Interface para logstores que ESCREVEM eventos:

```php
<?php
namespace tool_log\log;

interface writer extends store {
    /**
     * Escreve um evento no armazenamento.
     *
     * @param \core\event\base $event Evento a ser armazenado
     * @param \tool_log\log\manager $manager Manager instance
     */
    public function write(\core\event\base $event, \tool_log\log\manager $manager);
}
```

### 2. \core\log\reader

Interface para logstores que LEEM eventos:

```php
<?php
namespace core\log;

interface reader {
    /**
     * Retorna eventos baseado em critérios.
     *
     * @param string $selectwhere SQL WHERE clause
     * @param array $params Parâmetros nomeados
     * @param string $sort SQL ORDER BY
     * @param int $limitfrom Offset
     * @param int $limitnum Limit
     * @return \core\event\base[] Array de eventos
     */
    public function get_events_select($selectwhere, array $params, $sort, $limitfrom, $limitnum);
}
```

### 3. \core\log\sql_reader

Interface estendida com funcionalidades SQL:

```php
<?php
namespace core\log;

interface sql_reader extends reader {
    /**
     * Conta eventos baseado em critérios.
     */
    public function get_events_select_count($selectwhere, array $params);

    /**
     * Retorna iterator para grandes conjuntos.
     */
    public function get_events_select_iterator($selectwhere, array $params, $sort, $limitfrom, $limitnum);
}
```

---

## Desenvolvimento de Logstore Customizado

### Implementação Mínima Funcional

**Arquivo**: `public/admin/tool/log/store/tsdb/classes/log/store.php`

```php
<?php
namespace logstore_tsdb\log;

defined('MOODLE_INTERNAL') || die();

class store implements \tool_log\log\writer {

    /** @var \tool_log\log\manager */
    protected $manager;

    /** @var array Configurações do plugin */
    protected $config;

    /** @var mixed Cliente de conexão com TSDB */
    protected $client;

    /**
     * Construtor - chamado pelo Log Manager.
     *
     * @param \tool_log\log\manager $manager
     */
    public function __construct(\tool_log\log\manager $manager) {
        $this->manager = $manager;
        $this->load_config();
        $this->init_client();
    }

    /**
     * Carrega configurações do plugin.
     */
    protected function load_config() {
        $this->config = [
            'host' => get_config('logstore_tsdb', 'host'),
            'port' => get_config('logstore_tsdb', 'port'),
            'database' => get_config('logstore_tsdb', 'database'),
            'writemode' => get_config('logstore_tsdb', 'writemode'),
        ];
    }

    /**
     * Inicializa cliente de conexão.
     */
    protected function init_client() {
        // Implementar lógica de conexão com InfluxDB/TimescaleDB
        // Exemplo placeholder:
        // $this->client = new \InfluxDB\Client($this->config['host'], $this->config['port']);
    }

    /**
     * Escreve evento no TSDB.
     *
     * @param \core\event\base $event
     * @param \tool_log\log\manager $manager
     */
    public function write(\core\event\base $event, \tool_log\log\manager $manager) {
        // Ignora eventos anônimos
        if ($event->anonymous) {
            return;
        }

        // Transforma evento em formato TSDB
        $datapoint = $this->transform_event($event);

        // Escreve no TSDB
        $this->write_datapoint($datapoint);
    }

    /**
     * Transforma evento Moodle em formato TSDB.
     *
     * @param \core\event\base $event
     * @return array Datapoint formatado
     */
    protected function transform_event(\core\event\base $event) {
        return [
            'measurement' => 'moodle_events',
            'tags' => [
                'eventname' => $event->eventname,
                'component' => $event->component,
                'action' => $event->action,
                'target' => $event->target,
                'crud' => $event->crud,
                'courseid' => $event->courseid,
            ],
            'fields' => [
                'userid' => $event->userid,
                'contextid' => $event->contextid,
                'contextlevel' => $event->contextlevel,
                'objectid' => $event->objectid,
                'relateduserid' => $event->relateduserid,
                'edulevel' => $event->edulevel,
            ],
            'timestamp' => $event->timecreated,
        ];
    }

    /**
     * Escreve datapoint no TSDB.
     *
     * @param array $datapoint
     */
    protected function write_datapoint($datapoint) {
        try {
            // Implementar escrita real
            // Exemplo InfluxDB:
            // $this->client->writePoints([$datapoint], \InfluxDB\Database::PRECISION_SECONDS);

        } catch (\Exception $e) {
            debugging('Error writing to TSDB: ' . $e->getMessage(), DEBUG_NORMAL);
        }
    }

    /**
     * Cleanup ao final da requisição.
     */
    public function dispose() {
        // Fechar conexões, flush de buffers, etc.
        if ($this->client) {
            // $this->client->close();
        }
    }

    /**
     * Helper para acessar configurações.
     *
     * @param string $name Nome da configuração
     * @param mixed $default Valor padrão
     * @return mixed
     */
    protected function get_config($name, $default = null) {
        return get_config('logstore_tsdb', $name) ?: $default;
    }
}
```

---

## Event Data Structure

### Campos Disponíveis em Todos os Eventos

```php
// Propriedades acessíveis via $event->propertyname
$event->eventname;           // string: '\core\event\user_loggedin'
$event->component;           // string: 'core' | 'mod_quiz' | etc.
$event->action;              // string: 'viewed', 'created', 'submitted', etc.
$event->target;              // string: 'user', 'course', 'course_module', etc.
$event->objecttable;         // string: 'user', 'course', 'quiz_attempts', etc.
$event->objectid;            // int: ID do objeto afetado
$event->crud;                // string: 'c' | 'r' | 'u' | 'd'
$event->edulevel;            // int: 0 (OTHER) | 1 (TEACHING) | 2 (PARTICIPATING)
$event->contextid;           // int: ID do contexto
$event->contextlevel;        // int: 10 (SYSTEM) | 50 (COURSE) | 70 (MODULE)
$event->contextinstanceid;   // int: ID da instância do contexto
$event->userid;              // int: Quem fez a ação
$event->courseid;            // int: Curso relacionado (0 se sistema)
$event->relateduserid;       // int: Usuário secundário (opcional)
$event->anonymous;           // int: 0 | 1
$event->other;               // mixed: Dados adicionais específicos do evento
$event->timecreated;         // int: Unix timestamp
```

### Dados Adicionais via Métodos

```php
// Dados derivados do contexto
$event->get_context();       // object: Objeto de contexto completo
$event->get_url();           // moodle_url: URL relacionada ao evento
$event->get_name();          // string: Nome legível do evento
$event->get_description();   // string: Descrição detalhada

// Dados de logging
$event->get_logextra();      // array: Dados extras para logging
```

### Campo 'other' - Exemplos por Tipo de Evento

```php
// user_loggedin
$event->other = [
    'username' => 'john.doe',
];

// course_module_viewed
$event->other = [
    'cmid' => 42,
    'instanceid' => 15,
];

// quiz_attempt_submitted
$event->other = [
    'quizid' => 10,
    'attemptid' => 150,
    'submitterid' => 42,
];
```

---

## Exemplos Práticos

### Exemplo 1: Logstore com Buffer Assíncrono

```php
<?php
namespace logstore_tsdb\log;

class store implements \tool_log\log\writer {

    protected $buffer = [];
    protected $buffer_size = 1000;
    protected $last_flush = 0;
    protected $flush_interval = 60; // segundos

    public function write(\core\event\base $event, \tool_log\log\manager $manager) {
        // Adiciona ao buffer
        $this->buffer[] = $this->transform_event($event);

        // Flush se atingiu tamanho ou tempo
        if (count($this->buffer) >= $this->buffer_size ||
            (time() - $this->last_flush) >= $this->flush_interval) {
            $this->flush_buffer();
        }
    }

    protected function flush_buffer() {
        if (empty($this->buffer)) {
            return;
        }

        try {
            // Escreve em lote
            $this->client->writePoints($this->buffer);

            // Limpa buffer
            $this->buffer = [];
            $this->last_flush = time();

        } catch (\Exception $e) {
            debugging('Error flushing buffer: ' . $e->getMessage());
        }
    }

    public function dispose() {
        // Garante flush final
        $this->flush_buffer();
    }
}
```

### Exemplo 2: Logstore com Filtros de Eventos

```php
<?php
namespace logstore_tsdb\log;

class store implements \tool_log\log\writer {

    protected $ignored_events = [
        '\core\event\user_viewed',  // Muito frequente
        '\core\event\dashboard_viewed',
    ];

    protected $min_edulevel = 1;  // Apenas TEACHING e PARTICIPATING

    public function write(\core\event\base $event, \tool_log\log\manager $manager) {
        // Filtro 1: Eventos ignorados
        if (in_array($event->eventname, $this->ignored_events)) {
            return;
        }

        // Filtro 2: Nível educacional mínimo
        if ($event->edulevel < $this->min_edulevel) {
            return;
        }

        // Filtro 3: Apenas eventos de cursos (não sistema)
        if ($event->courseid == 0) {
            return;
        }

        // Processa evento
        $this->write_datapoint($this->transform_event($event));
    }
}
```

### Exemplo 3: Logstore com Retry Logic

```php
<?php
namespace logstore_tsdb\log;

class store implements \tool_log\log\writer {

    protected $max_retries = 3;
    protected $retry_delay = 1000; // ms

    protected function write_datapoint($datapoint) {
        $retries = 0;

        while ($retries < $this->max_retries) {
            try {
                $this->client->writePoints([$datapoint]);
                return; // Sucesso

            } catch (\InfluxDB\Exception\ConnectionException $e) {
                $retries++;

                if ($retries >= $this->max_retries) {
                    // Falha definitiva - pode guardar em arquivo local
                    $this->fallback_to_file($datapoint);
                    debugging('Failed to write to TSDB after retries: ' . $e->getMessage());
                    return;
                }

                // Aguarda antes de retry
                usleep($this->retry_delay * 1000);

            } catch (\Exception $e) {
                // Erro não relacionado a conexão - não retry
                debugging('Error writing to TSDB: ' . $e->getMessage());
                return;
            }
        }
    }

    protected function fallback_to_file($datapoint) {
        global $CFG;
        $file = $CFG->dataroot . '/tsdb_fallback.log';
        file_put_contents($file, json_encode($datapoint) . "\n", FILE_APPEND);
    }
}
```

---

## Testing

### Estrutura de Testes

**Arquivo**: `public/admin/tool/log/store/tsdb/tests/store_test.php`

```php
<?php
namespace logstore_tsdb;

defined('MOODLE_INTERNAL') || die();

/**
 * Testes do logstore TSDB.
 *
 * @package    logstore_tsdb
 * @covers     \logstore_tsdb\log\store
 */
class store_test extends \advanced_testcase {

    /** @var \logstore_tsdb\log\store */
    protected $store;

    /**
     * Setup inicial de cada teste.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        // Habilita logstore para testes
        set_config('enabled_stores', 'logstore_tsdb', 'tool_log');

        // Configura mock do TSDB
        set_config('host', 'localhost', 'logstore_tsdb');
        set_config('port', '8086', 'logstore_tsdb');

        $manager = get_log_manager(true); // Force reload
        $this->store = $manager->get_stores()['logstore_tsdb'];
    }

    /**
     * Testa escrita de evento básico.
     */
    public function test_write_basic_event() {
        // Cria usuário e curso de teste
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        // Ativa captura de eventos
        $sink = $this->redirectEvents();

        // Dispara evento
        $event = \core\event\course_viewed::create([
            'objectid' => $course->id,
            'context' => \context_course::instance($course->id),
            'userid' => $user->id,
        ]);
        $event->trigger();

        // Verifica que evento foi capturado
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\course_viewed', $events[0]);

        // Verifica que store processou evento
        // (Aqui você verificaria no TSDB mock se os dados foram escritos)
    }

    /**
     * Testa filtro de eventos anônimos.
     */
    public function test_anonymous_events_ignored() {
        $sink = $this->redirectEvents();

        // Cria evento anônimo (não deve ser logado)
        $event = \core\event\unittest_executed::create([
            'other' => ['sample' => 1, 'xx' => 10],
        ]);
        $event->trigger();

        // Verifica processamento
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $this->assertEquals(1, $events[0]->anonymous);
    }

    /**
     * Testa transformação de evento para formato TSDB.
     */
    public function test_event_transformation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        $event = \core\event\course_viewed::create([
            'objectid' => $course->id,
            'context' => \context_course::instance($course->id),
            'userid' => $user->id,
        ]);

        // Usa reflection para acessar método protected
        $method = new \ReflectionMethod($this->store, 'transform_event');
        $method->setAccessible(true);
        $datapoint = $method->invoke($this->store, $event);

        // Verifica estrutura
        $this->assertArrayHasKey('measurement', $datapoint);
        $this->assertArrayHasKey('tags', $datapoint);
        $this->assertArrayHasKey('fields', $datapoint);
        $this->assertArrayHasKey('timestamp', $datapoint);

        // Verifica valores
        $this->assertEquals('moodle_events', $datapoint['measurement']);
        $this->assertEquals('\core\event\course_viewed', $datapoint['tags']['eventname']);
        $this->assertEquals($user->id, $datapoint['fields']['userid']);
    }
}
```

### Executando Testes

```bash
# Inicializa ambiente PHPUnit
php public/admin/tool/phpunit/cli/init.php

# Roda todos os testes do plugin
vendor/bin/phpunit public/admin/tool/log/store/tsdb/tests/

# Roda teste específico
vendor/bin/phpunit public/admin/tool/log/store/tsdb/tests/store_test.php

# Roda método específico
vendor/bin/phpunit --filter test_write_basic_event public/admin/tool/log/store/tsdb/tests/store_test.php
```

---

## Performance e Otimizações

### 1. Buffering Assíncrono

**Problema**: Escrever cada evento individualmente adiciona latência.

**Solução**: Buffer em memória + flush em lote.

```php
class store implements \tool_log\log\writer {
    use \tool_log\helper\buffered_writer;  // Trait fornecido pelo Moodle

    protected function insert_event_entries($events) {
        // Escreve múltiplos eventos de uma vez
        $this->client->writePoints($events);
    }
}
```

### 2. Scheduled Task para Flush

**Arquivo**: `db/tasks.php`

```php
<?php
defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => '\logstore_tsdb\task\buffer_flush',
        'blocking' => 0,
        'minute' => '*/5',  // A cada 5 minutos
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
];
```

**Arquivo**: `classes/task/buffer_flush.php`

```php
<?php
namespace logstore_tsdb\task;

class buffer_flush extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('taskbufferflush', 'logstore_tsdb');
    }

    public function execute() {
        $manager = get_log_manager();
        $stores = $manager->get_stores();

        if (isset($stores['logstore_tsdb'])) {
            $stores['logstore_tsdb']->flush_buffer();
        }
    }
}
```

### 3. Connection Pooling

```php
class store implements \tool_log\log\writer {

    protected static $connection_pool = [];

    protected function get_connection() {
        $key = $this->config['host'] . ':' . $this->config['port'];

        if (!isset(self::$connection_pool[$key])) {
            self::$connection_pool[$key] = new \InfluxDB\Client(
                $this->config['host'],
                $this->config['port']
            );
        }

        return self::$connection_pool[$key];
    }
}
```

### 4. Seletividade de Eventos

Não logue tudo - seja seletivo:

```php
protected function should_log_event(\core\event\base $event) {
    // Apenas eventos de cursos
    if ($event->courseid == 0) {
        return false;
    }

    // Apenas ações significativas
    $important_actions = ['submitted', 'created', 'graded', 'completed'];
    if (!in_array($event->action, $important_actions)) {
        return false;
    }

    // Apenas níveis educacionais relevantes
    if ($event->edulevel == \core\event\base::LEVEL_OTHER) {
        return false;
    }

    return true;
}
```

---

## Próximos Passos

1. ✅ Entender arquitetura de eventos
2. ✅ Conhecer interfaces necessárias
3. 🔄 Implementar classe básica do logstore
4. 🔄 Integrar com InfluxDB/TimescaleDB
5. 🔄 Adicionar buffering assíncrono
6. 🔄 Implementar interface de configuração
7. 🔄 Criar testes unitários
8. 🔄 Documentar API de integração

---

## Referências

- [Moodle Events API](https://moodledev.io/docs/apis/core/event)
- [Moodle Logging API](https://moodledev.io/docs/apis/subsystems/logging)
- [Plugin Development](https://moodledev.io/docs/guides/plugins)
- [InfluxDB Line Protocol](https://docs.influxdata.com/influxdb/v2.0/reference/syntax/line-protocol/)
- [TimescaleDB Hypertables](https://docs.timescale.com/use-timescale/latest/hypertables/)

---

**Última atualização**: 2025-01-25
