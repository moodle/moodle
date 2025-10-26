# Guia Completo: Moodle Web Services API

**Como habilitar, configurar e usar a API REST do Moodle para simulação de carga**

---

## Índice

1. [Visão Geral](#visão-geral)
2. [Habilitação de Web Services](#habilitação-de-web-services)
3. [Autenticação e Tokens](#autenticação-e-tokens)
4. [Endpoints Disponíveis](#endpoints-disponíveis)
5. [Exemplos de Uso](#exemplos-de-uso)
6. [Segurança e Best Practices](#segurança-e-best-practices)
7. [Troubleshooting](#troubleshooting)

---

## Visão Geral

A API Web Services do Moodle permite acesso programático a todas as funcionalidades da plataforma via REST, SOAP ou XML-RPC.

### Casos de Uso

- ✅ Integração com sistemas externos
- ✅ Automação de tarefas administrativas
- ✅ Aplicativos móveis
- ✅ **Simulação de carga** (nosso caso)

### Endpoint Base

```
https://seu-moodle.com/webservice/rest/server.php
```

---

## Habilitação de Web Services

### Passo 1: Habilitar Web Services Globalmente

**Navegação**: Site Administration → Advanced Features

1. Marcar: **Enable web services**
2. Salvar mudanças

### Passo 2: Habilitar Protocolos

**Navegação**: Site Administration → Plugins → Web services → Manage protocols

Habilitar:
- ✅ **REST protocol** (recomendado)
- ⚠️ SOAP protocol (opcional)
- ⚠️ XML-RPC protocol (legado)

### Passo 3: Criar um External Service

**Navegação**: Site Administration → Plugins → Web services → External services

1. Clicar em **Add**
2. Preencher:
   - **Name**: `Load Simulation Service`
   - **Short name**: `load_sim`
   - **Enabled**: ✅
   - **Authorized users only**: ✅
3. Salvar

### Passo 4: Adicionar Funções ao Service

Clicar em **Add functions** e adicionar:

#### Funções Essenciais

**Gerenciamento de Usuários**:
- `core_user_create_users`
- `core_user_get_users`
- `core_user_get_users_by_field`
- `core_user_update_users`

**Gerenciamento de Cursos**:
- `core_course_create_courses`
- `core_course_get_courses`
- `core_course_get_course_module`
- `core_course_get_contents`
- `core_course_view_course`

**Matrículas**:
- `enrol_manual_enrol_users`
- `enrol_manual_unenrol_users`
- `core_enrol_get_enrolled_users`

**Atividades - Quiz**:
- `mod_quiz_get_quizzes_by_courses`
- `mod_quiz_start_attempt`
- `mod_quiz_get_attempt_data`
- `mod_quiz_save_attempt`

**Atividades - Assignment**:
- `mod_assign_get_assignments`
- `mod_assign_get_submissions`
- `mod_assign_save_submission`

**Atividades - Forum**:
- `mod_forum_get_forums_by_courses`
- `mod_forum_get_forum_discussions`
- `mod_forum_add_discussion`
- `mod_forum_add_discussion_post`

**Informações do Sistema**:
- `core_webservice_get_site_info`
- `core_dashboard_view_dashboard`

### Passo 5: Criar Usuário de API (Opcional)

**Navegação**: Site Administration → Users → Accounts → Add a new user

Criar usuário específico para API:
- Username: `api_user`
- Email: `api@example.com`
- Role: Administrator (ou custom role com capabilities necessárias)

---

## Autenticação e Tokens

### Método 1: Token Manual (Recomendado para Desenvolvimento)

**Navegação**: Site Administration → Plugins → Web services → Manage tokens

1. Clicar em **Add**
2. Selecionar:
   - **User**: admin (ou api_user)
   - **Service**: Load Simulation Service
3. Salvar

O token será exibido. **Copie e guarde em local seguro!**

Exemplo:
```
a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
```

### Método 2: Token via Login (Produção)

```bash
curl -X POST "https://seu-moodle.com/login/token.php" \
  -d "username=admin" \
  -d "password=SuaSenha" \
  -d "service=moodle_mobile_app"
```

Resposta:
```json
{
  "token": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6",
  "privatetoken": "..."
}
```

### Usar o Token

Toda requisição deve incluir:
```
wstoken=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
```

---

## Endpoints Disponíveis

### Estrutura da Requisição

```http
POST /webservice/rest/server.php
Content-Type: application/x-www-form-urlencoded

wstoken=SEU_TOKEN&
wsfunction=NOME_DA_FUNCAO&
moodlewsrestformat=json&
param1=valor1&
param2=valor2
```

### Funções Principais

#### 1. core_webservice_get_site_info

**Descrição**: Informações do site Moodle

**Parâmetros**: Nenhum

**Exemplo**:
```bash
curl -X POST "http://localhost/moodle/webservice/rest/server.php" \
  -d "wstoken=SEU_TOKEN" \
  -d "wsfunction=core_webservice_get_site_info" \
  -d "moodlewsrestformat=json"
```

**Resposta**:
```json
{
  "sitename": "Minha Plataforma Moodle",
  "username": "admin",
  "firstname": "Admin",
  "lastname": "User",
  "userid": 2,
  "siteurl": "http://localhost/moodle",
  "release": "4.5+ (Build: 20250101)",
  "version": "2025010100.00"
}
```

#### 2. core_user_create_users

**Descrição**: Criar múltiplos usuários

**Parâmetros**:
```
users[0][username]=student001
users[0][password]=Password123!
users[0][firstname]=João
users[0][lastname]=Silva
users[0][email]=joao@example.com
users[1][username]=student002
...
```

**Exemplo Python**:
```python
import requests

data = {
    'wstoken': 'SEU_TOKEN',
    'wsfunction': 'core_user_create_users',
    'moodlewsrestformat': 'json',
    'users[0][username]': 'student001',
    'users[0][password]': 'Password123!',
    'users[0][firstname]': 'João',
    'users[0][lastname]': 'Silva',
    'users[0][email]': 'joao@example.com',
}

response = requests.post(
    'http://localhost/moodle/webservice/rest/server.php',
    data=data
)
print(response.json())
```

**Resposta**:
```json
[
  {
    "id": 10,
    "username": "student001"
  }
]
```

#### 3. core_course_create_courses

**Descrição**: Criar cursos

**Parâmetros**:
```
courses[0][fullname]=Introdução à Programação
courses[0][shortname]=PROG101
courses[0][categoryid]=1
courses[0][summary]=Curso básico de programação
```

**Resposta**:
```json
[
  {
    "id": 5,
    "shortname": "PROG101"
  }
]
```

#### 4. enrol_manual_enrol_users

**Descrição**: Matricular usuários em cursos

**Parâmetros**:
```
enrolments[0][roleid]=5        # 5 = student
enrolments[0][userid]=10
enrolments[0][courseid]=5
```

#### 5. core_course_view_course

**Descrição**: Registrar visualização de curso (gera evento de log)

**Parâmetros**:
```
courseid=5
```

**Importante**: Esta função **gera um evento** que será capturado pelo logstore_tsdb!

---

## Exemplos de Uso

### Exemplo 1: Criar Usuário e Matricular

```python
import requests

BASE_URL = 'http://localhost/moodle/webservice/rest/server.php'
TOKEN = 'seu_token_aqui'

def call_api(function, **params):
    data = {
        'wstoken': TOKEN,
        'wsfunction': function,
        'moodlewsrestformat': 'json',
    }
    data.update(params)
    response = requests.post(BASE_URL, data=data)
    return response.json()

# 1. Criar usuário
users = call_api('core_user_create_users',
    **{
        'users[0][username]': 'aluno001',
        'users[0][password]': 'Senha123!',
        'users[0][firstname]': 'Aluno',
        'users[0][lastname]': 'Teste',
        'users[0][email]': 'aluno001@example.com',
    }
)
userid = users[0]['id']
print(f"Usuário criado: ID {userid}")

# 2. Matricular em curso
call_api('enrol_manual_enrol_users',
    **{
        'enrolments[0][roleid]': 5,
        'enrolments[0][userid]': userid,
        'enrolments[0][courseid]': 2,
    }
)
print(f"Usuário matriculado no curso 2")

# 3. Simular acesso ao curso (gera log!)
call_api('core_course_view_course', courseid=2)
print("Visualização de curso registrada → evento gerado!")
```

### Exemplo 2: Listar e Visualizar Cursos

```python
# Obter todos os cursos
courses = call_api('core_course_get_courses')

for course in courses:
    if course['id'] > 1:  # Pular site course
        print(f"Curso: {course['fullname']} (ID: {course['id']})")

        # Simular visualização
        call_api('core_course_view_course', courseid=course['id'])
```

### Exemplo 3: Simular Tentativa de Quiz

```python
# 1. Obter quizzes do curso
quizzes = call_api('mod_quiz_get_quizzes_by_courses',
    **{'courseids[0]': 2}
)

if quizzes['quizzes']:
    quiz = quizzes['quizzes'][0]
    print(f"Quiz encontrado: {quiz['name']}")

    # 2. Iniciar tentativa
    attempt = call_api('mod_quiz_start_attempt', quizid=quiz['id'])
    print(f"Tentativa iniciada: {attempt}")
```

### Exemplo 4: Criar Post em Fórum

```python
# 1. Obter fóruns do curso
forums = call_api('mod_forum_get_forums_by_courses',
    **{'courseids[0]': 2}
)

if forums:
    forum = forums[0]

    # 2. Criar discussão
    discussion = call_api('mod_forum_add_discussion',
        forumid=forum['id'],
        subject='Dúvida sobre a aula',
        message='Alguém pode me ajudar com o exercício 3?'
    )
    print(f"Discussão criada: {discussion}")
```

---

## Segurança e Best Practices

### Segurança do Token

**❌ NUNCA**:
- Commitar tokens no Git
- Expor tokens em logs públicos
- Usar o mesmo token para múltiplos ambientes

**✅ SEMPRE**:
- Usar variáveis de ambiente
- Rotacionar tokens periodicamente
- Usar HTTPS em produção
- Limitar capabilities do usuário de API

### Exemplo com .env

```python
# .env
MOODLE_TOKEN=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
MOODLE_URL=http://localhost/moodle

# script.py
from dotenv import load_dotenv
import os

load_dotenv()

TOKEN = os.getenv('MOODLE_TOKEN')
BASE_URL = os.getenv('MOODLE_URL')
```

### Rate Limiting

Configure rate limiting para evitar sobrecarga:

```php
// config.php
$CFG->webserviceratelimit = [
    'enabled' => true,
    'limit' => 100,      // Requests por minuto
    'period' => 60,
];
```

### Capabilities Necessárias

Para um usuário de API, criar role customizado com:

```
webservice/rest:use
moodle/user:create
moodle/user:update
moodle/course:create
moodle/course:view
```

---

## Troubleshooting

### Erro: "Web services are not enabled"

```json
{
  "exception": "moodle_exception",
  "errorcode": "enablewebservices"
}
```

**Solução**: Habilitar em Site Administration → Advanced Features

### Erro: "Access control exception"

```json
{
  "exception": "webservice_access_exception",
  "errorcode": "accessexception"
}
```

**Causas**:
- Token inválido
- Função não habilitada no service
- Usuário sem capability

**Debug**:
```bash
# Verificar se token é válido
SELECT * FROM mdl_external_tokens WHERE token = 'SEU_TOKEN';

# Verificar funções do service
SELECT * FROM mdl_external_services_functions WHERE externalserviceid = X;
```

### Erro: "Invalid parameter value detected"

Parâmetros mal formatados. Verificar:
- Nomenclatura exata dos parâmetros
- Arrays devem usar índice: `param[0][key]`
- Tipos corretos (int vs string)

### Erro: "Function not found"

```json
{
  "exception": "invalid_parameter_exception",
  "errorcode": "invalidparameter"
}
```

Função não existe ou nome incorreto. Ver lista completa:

```bash
# Via API
curl "http://localhost/moodle/webservice/rest/server.php?wstoken=TOKEN&wsfunction=core_webservice_get_site_info&moodlewsrestformat=json"

# Ver campo 'functions'
```

### Debug Mode

Habilitar debug para ver erros detalhados:

```php
// config.php
$CFG->debug = (E_ALL | E_STRICT);
$CFG->debugdisplay = 1;
```

---

## Ferramentas Úteis

### Postman Collection

Importar collection pronta:
```
https://www.postman.com/moodlehq/workspace/moodle-web-services
```

### MoodlePy (Python Library)

```bash
pip install moodlepy
```

```python
from moodlepy import Moodle

moodle = Moodle('http://localhost/moodle', 'SEU_TOKEN')
site_info = moodle.core.webservice.get_site_info()
print(site_info)
```

### API Explorer (Plugin)

Instalar plugin `local_wstemplate` para explorar API visualmente no Moodle.

---

## Referências

- [Moodle Web Services Documentation](https://docs.moodle.org/dev/Web_services)
- [Web Services API Reference](https://docs.moodle.org/dev/Web_service_API_functions)
- [Authentication](https://docs.moodle.org/dev/Creating_a_web_service_client)
- [Script de Simulação](../scripts/simulation/README.md)
