# 🏛️ Sistema de Gestão de Igrejas

## Visão Geral do Sistema

Sistema SaaS multi-igreja para administração eclesiástica. Permite que múltiplas igrejas utilizem a mesma plataforma com isolamento completo de dados, cada uma gerenciando seus membros, finanças, eventos e departamentos de forma independente.

## Objetivo do Produto

Fornecer uma plataforma unificada para igrejas gerenciarem suas operações diárias — desde o cadastro de membros até o controle financeiro — com uma interface simples, moderna e acessível para usuários não técnicos.

## Stack Utilizada

### Obrigatória (em uso)

| Tecnologia | Versão | Função |
|------------|--------|--------|
| Laravel | 11 (13.x) | Framework backend |
| PHP | 8.4 | Linguagem |
| Blade | — | Template engine |
| Livewire | 3 | Componentes reativos |
| Volt | 1 | Componentes funcionais Livewire |
| Tailwind CSS | 4 | Estilização |
| Alpine.js | 3 | Interatividade no frontend |
| Vite | 8 | Bundler frontend |
| Spatie Permission | 8 | Roles e permissões |
| MySQL | 8.0 | Banco de dados |
| Docker | — | Containerização |

### Suporte (em uso)

| Tecnologia | Função |
|------------|--------|
| Laravel Breeze | Scaffold de autenticação |
| phpMyAdmin | Admin do banco (dev) |

## Arquitetura Multi-Igreja

O sistema segue o modelo **multi-tenant por linha (row-level isolation)** usando `church_id` como chave estrangeira em todas as tabelas de negócio.

### Regras de Isolamento

- **Master** visualiza todas as igrejas e seus dados.
- **Admin** e demais perfis visualizam **apenas dados da própria igreja**.
- Nenhum usuário pode acessar dados de outra igreja.

### Implementação Atual

- Tabela `users` possui campo `church_id` (nullable — Master pode ser nulo).
- Tabela `churches` é a entidade raiz do tenant.
- O isolamento é aplicado **nos queries** dos módulos (sem Global Scope no model User para não afetar autenticação).
- Helper `isMaster()` e `currentChurch()` disponíveis globalmente.

## Regras de Negócio

1. **Usuário Master** pode ter `church_id = null` e não está vinculado a nenhuma igreja específica.
2. **Usuário Admin** deve estar vinculado a uma igreja.
3. Apenas **Master** pode criar/editar/ativar/inativar igrejas.
4. **Master** gerencia usuários de qualquer igreja.
5. **Admin** gerencia apenas usuários da própria igreja.
6. Cadastro público de novos usuários está desabilitado.
7. Senha padrão `12345678` — apenas para ambiente de desenvolvimento local.

## Perfis e Permissões

### Master

Acesso irrestrito ao sistema.

| Permissão | Descrição |
|-----------|-----------|
| `church.view` | Visualizar igrejas |
| `church.create` | Criar igrejas |
| `church.edit` | Editar igrejas |
| `church.delete` | Excluir igrejas |
| `user.view` | Visualizar usuários |
| `user.create` | Criar usuários |
| `user.edit` | Editar usuários |
| `user.delete` | Excluir usuários |

### Admin

Gerencia a própria igreja.

| Permissão | Descrição |
|-----------|-----------|
| `church.view` | Visualizar igrejas (apenas leitura) |
| `user.view` | Visualizar usuários da própria igreja |
| `user.create` | Criar usuários na própria igreja |
| `user.edit` | Editar usuários da própria igreja |
| `user.delete` | Excluir usuários da própria igreja |

### Tesoureiro (reservado)

| Permissão | Descrição | Status |
|-----------|-----------|--------|
| Acesso financeiro | — | 🔜 Sprint 3 |

### Secretaria (reservado)

| Permissão | Descrição | Status |
|-----------|-----------|--------|
| Acesso membros | — | 🔜 Sprint 2 |

## Entidades Atuais

### Church (Igreja)

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigIncrements | PK |
| name | string | Nome da igreja |
| cnpj | string(18) | CNPJ (único, nullable) |
| phone | string(20) | Telefone (nullable) |
| email | string | Email (nullable) |
| pastor_name | string | Nome do pastor (nullable) |
| address | text | Endereço completo (nullable) |
| city | string | Cidade (nullable) |
| state | string(2) | Estado (UF, nullable) |
| active | boolean | Se a igreja está ativa |
| created_at | timestamp | |
| updated_at | timestamp | |

### User (Usuário)

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigIncrements | PK |
| church_id | bigInteger (FK) | FK para churches (nullable) |
| name | string | Nome do usuário |
| email | string | Email (único) |
| password | string | Hash da senha |
| active | boolean | Se o usuário está ativo |
| email_verified_at | timestamp | (não utilizado) |
| remember_token | string | Token de sessão |
| created_at | timestamp | |
| updated_at | timestamp | |

### Relacionamentos

- `User` **pertence a** `Church` (belongsTo, nullable)
- `Church` **tem muitos** `User` (hasMany)

## Sprint 1 — Concluída ✅

### Período
Junho 2026

### Escopo
Fundação do sistema — infraestrutura, autenticação, multi-igreja, usuários, roles e permissões.

### Entregues

| Funcionalidade | Status |
|----------------|--------|
| Docker (app + mysql + phpmyadmin) | ✅ Concluído |
| Laravel 11 + PHP 8.4 CLI | ✅ Concluído |
| Autenticação (login, logout, recuperação de senha) | ✅ Concluído |
| Breeze Blade + Livewire + Volt | ✅ Concluído |
| Spatie Permission (4 roles, 8 permissões) | ✅ Concluído |
| Migrations (churches, users) | ✅ Concluído |
| Models (Church, User c/ HasRoles) | ✅ Concluído |
| Multi-igreja com church_id | ✅ Concluído |
| Layout sidebar + navbar responsivo | ✅ Concluído |
| Dashboard (Livewire) — Master e Admin | ✅ Concluído |
| CRUD Igrejas (Volt) — acesso Master | ✅ Concluído |
| CRUD Usuários (Volt) — Master e Admin | ✅ Concluído |
| Seeders (Master, Admin, igreja exemplo) | ✅ Concluído |
| Frontend (Tailwind + Alpine + Vite) | ✅ Concluído |
| Isolamento por church_id nos queries | ✅ Concluído |
| README com instruções | ✅ Concluído |

### Fora do Escopo da Sprint 1

- ❌ Membros
- ❌ Financeiro
- ❌ Relatórios
- ❌ Eventos
- ❌ Departamentos
- ❌ Patrimônio
- ❌ Congregações
- ❌ PIX
- ❌ Área do membro
- ❌ Aplicativo mobile

## Credenciais de Desenvolvimento

| Nome | Email | Senha | Perfil | Igreja |
|------|-------|-------|--------|--------|
| Administrador Master | admin@system.local | 12345678 | Master | N/A |
| Administrador da Igreja | admin@igreja.local | 12345678 | Admin | Igreja Batista Exemplo |

> ⚠️ A senha `12345678` é utilizada **exclusivamente** em ambiente de desenvolvimento local.

## Estrutura do Projeto

```
/
├── Dockerfile                     # Imagem PHP 8.4 CLI
├── docker-compose.yml             # app + mysql + phpmyadmin
├── .env                           # Configuração local (gitignored)
├── .env.example                   # Template de configuração
├── .gitignore                     # Arquivos ignorados
├── README.md                      # Instruções de uso
├── PLANO_PROJETO_IGREJA.md        # Este documento
│
├── app/
│   ├── helpers.php                # Funções globais (isMaster, currentChurch)
│   ├── Livewire/
│   │   ├── Dashboard.php          # Componente Dashboard
│   │   ├── Actions/Logout.php     # Ação de logout
│   │   └── Forms/LoginForm.php    # Formulário de login
│   ├── Models/
│   │   ├── Church.php             # Model Igreja
│   │   └── User.php               # Model Usuário (c/ HasRoles)
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── VoltServiceProvider.php
│
├── database/
│   ├── factories/
│   │   ├── ChurchFactory.php
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_06_16_023236_create_permission_tables.php
│   │   ├── 2026_06_16_023239_create_churches_table.php
│   │   └── 2026_06_16_023643_add_church_foreign_key_to_users.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RolePermissionSeeder.php
│       ├── ChurchSeeder.php
│       └── UserSeeder.php
│
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php          # Layout autenticado (sidebar + navbar)
│   │   └── guest.blade.php        # Layout público (login)
│   ├── components/
│   │   ├── sidebar.blade.php      # Sidebar de navegação
│   │   ├── sidebar-link.blade.php # Link da sidebar
│   │   └── ... (Breeze components)
│   └── livewire/
│       ├── dashboard.blade.php    # View do Dashboard
│       ├── churches/
│       │   └── index.blade.php    # CRUD Igrejas (Volt)
│       ├── users/
│       │   └── index.blade.php    # CRUD Usuários (Volt)
│       ├── layout/
│       │   └── navigation.blade.php   # Navbar superior
│       ├── pages/auth/            # Páginas de autenticação
│       └── profile/               # Gerenciamento de perfil
│
├── routes/
│   ├── web.php                    # Rotas web (dashboard, churches, users)
│   └── auth.php                   # Rotas de autenticação
│
├── config/                        # Configurações Laravel
├── public/                        # Frontend compilado
├── tests/                         # Testes
├── storage/                       # Cache, logs, sessions
│
├── composer.json
├── package.json
├── vite.config.js
├── tailwind.config.js
└── postcss.config.js
```

## Roadmap das Próximas Sprints

### 🔜 Sprint 2 — Membros

**Previsão:** Próxima sprint

Funcionalidades:
- CRUD completo de membros
- Campos: nome, data de nascimento, telefone, email, endereço, foto, data de batismo, data de conversão, cargos (diácono, pastor, etc.)
- Vínculo do membro com igreja (`church_id`)
- Importação/exportação de membros
- Perfil do membro (área do membro — futuramente)
- Permissões específicas para Secretaria

### 🔜 Sprint 3 — Financeiro

**Previsão:** Pós Sprint 2

Funcionalidades:
- Controle de ofertas e dízimos
- Despesas
- Fluxo de caixa
- Relatórios financeiros
- Integração PIX (futuro)
- Permissões específicas para Tesoureiro

### 🔜 Sprint 4 — Auditoria e Segurança

**Previsão:** Pós Sprint 3

Funcionalidades:
- Log de auditoria (Spatie Activitylog)
- Histórico de alterações em entidades críticas
- Geração de relatórios
- Melhorias de segurança
- Backup automático

## Decisões Técnicas Adotadas

### Por que PHP CLI + artisan serve em vez de Nginx/FPM?

Ambiente de desenvolvimento simples e funcional. O servidor embutido do Laravel atende perfeitamente ao desenvolvimento local. Para produção, recomenda-se Nginx + PHP-FPM ou FrankenPHP.

### Por que Volt em vez de Livewire Components completos?

Volt é mais direto para CRUDs e telas simples, reduzindo a quantidade de arquivos. Componentes completos são usados quando a lógica é mais complexa (ex: Dashboard).

### Por que não Global Scope no User?

Para não interferir na autenticação e não bloquear o Master de visualizar todas as igrejas. O isolamento é aplicado manualmente nos queries de cada módulo.

### Por que Master tem church_id = null?

Para indicar que não está vinculado a nenhuma igreja específica, tendo acesso global ao sistema.

## Tecnologias que Não Serão Utilizadas

| Tecnologia | Motivo |
|------------|--------|
| React | Stack definida como Blade + Livewire |
| Vue | Stack definida como Blade + Livewire |
| Inertia | Stack definida como Blade + Livewire |
| API REST | Não há necessidade no momento (monolito) |
| Bootstrap | Substituído por Tailwind CSS |
| PHP-FPM | Apenas artisan serve em dev |
| Nginx | Apenas artisan serve em dev |
| Octane | Não necessário para o escopo atual |
| FrankenPHP | Não necessário para o escopo atual |
| RoadRunner | Não necessário para o escopo atual |
| Redis | Não implementado ainda (futuro) |

## Lista de Pendências Futuras

- [ ] Testes automatizados (PHPUnit + Pest)
- [ ] CI/CD (GitHub Actions)
- [ ] Traduções (pt_BR completo)
- [ ] Docker otimizado para produção (multi-stage build)
- [ ] Geração de relatórios em PDF
- [ ] Envio de emails transacionais
- [ ] Backup automático do banco
- [ ] Modo escuro (dark mode)
- [ ] Notificações no sistema
- [ ] Painel de auditoria
- [ ] Documentação de API (futura, se necessário)
- [ ] Deploy automatizado
