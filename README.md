# Gerenciamento de Igrejas

Sistema SaaS multi-igreja para gestão eclesiástica.

## Stack

- **Laravel 11** (PHP 8.4)
- **Blade + Livewire 3 + Volt**
- **Tailwind CSS + Alpine.js**
- **Vite**
- **Spatie Permission**
- **MySQL 8**
- **Docker**

## Requisitos

- Docker
- Docker Compose

## Instalação

```bash
# 1. Clone o repositório
git clone <url-do-repositorio>
cd Gerenciamento-Igreja

# 2. Inicie os containers
docker compose up -d --build

# 3. Instale as dependências do frontend
npm install && npm run build

# 4. Execute as migrations e seeders
docker compose exec app php artisan migrate --seed

# 5. Acesse a aplicação
# App: http://localhost:8080
# phpMyAdmin: http://localhost:8081
```

## Comandos Úteis

```bash
# Acessar o container
docker compose exec app bash

# Executar comandos Artisan
docker compose exec app php artisan <comando>

# Ver logs do container
docker compose logs -f app

# Parar os containers
docker compose down

# Recriar o banco do zero
docker compose exec app php artisan migrate:fresh --seed

# Build do frontend
npm run build

# Watch do frontend (desenvolvimento)
npm run dev
```

## Credenciais de Desenvolvimento

| Nome | Email | Senha | Perfil |
|------|-------|-------|--------|
| Administrador Master | admin@system.local | 12345678 | Master |
| Administrador da Igreja | admin@igreja.local | 12345678 | Admin |

> ⚠️ Senha `12345678` é apenas para ambiente local/desenvolvimento.

## Estrutura do Projeto

```
app/
├── Livewire/
│   ├── Dashboard.php          # Dashboard (Livewire Component)
│   └── Forms/
│       └── LoginForm.php      # Formulário de login
├── Models/
│   ├── Church.php             # Model de Igreja
│   └── User.php               # Model de Usuário
├── helpers.php                # Funções helpers (isMaster, currentChurch)
database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 2026_06_16_023236_create_permission_tables.php  (Spatie)
│   ├── 2026_06_16_023239_create_churches_table.php
│   └── 2026_06_16_023643_add_church_foreign_key_to_users.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── RolePermissionSeeder.php
    ├── ChurchSeeder.php
    └── UserSeeder.php
resources/views/
├── layouts/
│   ├── app.blade.php          # Layout autenticado (sidebar + navbar)
│   └── guest.blade.php        # Layout público (login)
├── components/
│   ├── sidebar.blade.php      # Sidebar de navegação
│   └── sidebar-link.blade.php # Link da sidebar
└── livewire/
    ├── dashboard.blade.php    # Dashboard
    ├── churches/
    │   └── index.blade.php    # CRUD Igrejas (Volt)
    └── users/
        └── index.blade.php    # CRUD Usuários (Volt)
```

## Perfis e Permissões

### Master
- Gerenciar igrejas (CRUD completo)
- Gerenciar usuários de qualquer igreja
- Visualizar dashboard geral (total igrejas, total usuários)

### Admin
- Gerenciar usuários da própria igreja
- Visualizar dashboard da igreja
- Visualizar igrejas

### Tesoureiro (reservado)
- Acesso financeiro (futuro)

### Secretaria (reservado)
- Acesso membros (futuro)

## Multi-Tenant

O sistema é multi-igreja. O isolamento por `church_id` é aplicado nas consultas:

- **Master**: visualiza dados de todas as igrejas
- **Admin/Secrataria/Tesoureiro**: visualiza apenas dados da própria igreja
- O Master possui `church_id = null`

## Docker

### Serviços

| Serviço | Porta | Descrição |
|---------|-------|-----------|
| app | 8080 | Aplicação Laravel (PHP CLI + artisan serve) |
| mysql | 3307 | Banco de dados MySQL 8 |
| phpmyadmin | 8081 | Admin do MySQL (apenas dev) |

## Licença

MIT
