# Decisões de Arquitetura

Este documento registra as principais decisões técnicas do projeto, os motivos que as fundamentam e as alternativas descartadas.

---

## 1. Por que Blade + Livewire + Volt?

**Decisão:** Usar Laravel Blade como template engine, Livewire 3 para componentes reativos e Volt (sintaxe funcional do Livewire) para CRUDs e telas simples.

**Alternativas consideradas:** React, Vue, Inertia, Livewire puro.

### Motivos

1. **Produtividade da equipe** — A equipe tem domínio de PHP e Blade. Livewire permite escrever frontend reativo sem sair do ecossistema Laravel/PHP.
2. **Sem necessidade de spa (single-page application)** — O sistema é administrativo, com fluxos de formulários e listagens. Não há demanda por interações em tempo real complexas que justifiquem React/Vue.
3. **Manutenção centralizada** — Toda a lógica (backend + frontend) está no PHP. Não há bifurcação de responsabilidades entre equipes ou repositórios.
4. **Curva de aprendizado baixa** — Um desenvolvedor PHP consegue produzir telas reativas em horas, sem precisar dominar ecossistema JS moderno (bundlers, estado, tipagem, etc.).
5. **Volt para CRUDs** — Volt reduz boilerplate: o template e a lógica ficam no mesmo arquivo `.blade.php`, sem necessidade de uma classe separada. Ideal para CRUDs previsíveis.
6. **Liveware para páginas complexas** — Dashboard, relatórios e telas com lógica de estado mais rica usam Livewire Component completo (classe + template separados), dando mais controle e organização.

### Trade-offs

| Ponto | Impacto |
|-------|---------|
| Limitação para UI complexa | Livewire não escala bem para interfaces extremamente dinâmicas (ex: arrastar e soltar em tempo real, gráficos interativos pesados). Para esses casos, Alpine.js + bibliotecas JS específicas preenchem a lacuna. |
| Performance em conexão lenta | Livewire faz requisições AJAX a cada interação. Em redes lentas, pode ser mais pesado que uma SPA. Aceitável para sistema administrativo local/banda larga. |

## 2. Por que não React?

**Decisão:** Excluir React do stack desde o início.

1. **Complexidade desnecessária** — React exigiria setup de JSX, estado global (Redux/Zustand), roteamento no frontend, chamadas API, tratamento de loading/erro. Para formulários e listagens, é canhão para matar passarinho.
2. **Duplicação de validação** — Validação precisaria ser replicada no frontend (React) e backend (Laravel), ou exigiria uma estratégia de API unificada.
3. **Manutenção** — Manter dois repositórios lógicos (ou até pastas separadas) com lógicas sobrepostas aumenta o custo de mudanças.
4. **Curva de aprendizado** — A equipe precisaria aprender React, hooks, ecossistema de estado, e ferramentas de build além do Vite.
5. **Overhead para o escopo** — O sistema não tem demanda por interações em tempo real, PWA, ou comportamento nativo que justifique React.

## 3. Por que não Vue?

**Decisão:** Excluir Vue do stack.

1. **Mesmo argumento do React** — Vue adicionaria complexidade similar: SPA, API, estado gerenciado, etc.
2. **Livewire é suficiente** — A reatividade que Vue proporciona (reatividade reativa, computed properties, watchers) é atendida por Livewire + Alpine.js no contexto do projeto.
3. **Padronização** — A comunidade Laravel tem adotado Livewire massivamente. Manter o ecossistema alinhado reduz atrito.

## 4. Por que não PHP-FPM + Nginx (em desenvolvimento)?

**Decisão:** Usar `php:8.4-cli` com `php artisan serve` no ambiente de desenvolvimento Docker.

1. **Simplicidade** — Um único container `app` roda o PHP e o servidor embutido. Não precisa de container Nginx separado, configuração de virtual hosts, ou integração PHP-FPM.
2. **Equivalência funcional** — `artisan serve` serve perfeitamente para desenvolvimento local. A diferença de performance é irrelevante em ambiente de dev.
3. **Menos recursos** — Reduz o número de containers em execução.

**Nota para produção:** Em produção, recomenda-se usar Nginx + PHP-FPM, FrankenPHP, ou Laravel Octane. A infraestrutura de dev não precisa espelhar a de produção — desde que o ambiente seja funcional e previsível.

## 5. Estratégia de Multi-Tenant

**Modelo adotado:** Multi-tenant por linha (row-level isolation) usando `church_id` como chave estrangeira em todas as tabelas de negócio.

### Por que não um banco por igreja?

| Abordagem | Prós | Contras | Decisão |
|-----------|------|---------|---------|
| Banco separado por igreja | Isolamento máximo, backup individual | Manutenção complexa, migrações em massa difíceis, conexão dinâmica | ❌ Descartado |
| Schema separado (ex: PostgreSQL) | Isolamento médio | Não suportado nativamente pelo MySQL, complexidade de migração | ❌ Descartado |
| `church_id` em todas as tabelas | Simples, performático, fácil de migrar | Risco de vazamento entre igrejas se query esquecer o filtro | ✅ **Adotado** |

### Como o isolamento é aplicado

1. **Toda migration** de entidade de negócio inclui `church_id` com FK para `churches`.
2. **Nos módulos** (controllers, Livewire, Volt), aplicamos manualmente `->where(church_id, currentChurch()->id)`.
3. **A exceção:** Master não tem `church_id` e visualiza tudo. Os queries tratam isso com `if (isMaster())`.
4. **Não usamos Global Scope** no model `User` para não interferir no login e na visão do Master.

### Proteções planejadas (sprints futuras)

- ✅ Já implementado: helpers `isMaster()` e `currentChurch()` garantem acesso controlado.
- 🔜 Futuro: trait reutilizável `BelongsToChurch` para aplicar `church_id` automaticamente em models.
- 🔜 Futuro: policy ou middleware de validação de `church_id` em todas as requests.

## 6. Estratégia de Permissões

**Biblioteca:** Spatie Laravel Permission v8.

### Por que Spatie Permission?

1. **Padrão de mercado** — É a biblioteca de ACL mais utilizada no ecossistema Laravel, bem documentada e testada.
2. **Granularidade** — Suporta permissões individuais, roles, e hierarquia via Gate/Policy.
3. **Integração com Laravel** — Funciona nativamente com `@can()`, `$user->can()`, `Gate::authorize()`, e middleware `can:`.
4. **Cache** — Faz cache de permissões para performance, com flush automático ao alterar.

### Modelo Adotado

- **Roles:** Master, Admin, Tesoureiro, Secretaria.
- **Permissões** são concedidas a roles (não diretamente a usuários), seguindo o padrão role-based access control (RBAC).
- **Master** recebe todas as permissões existentes via seeder.
- **Admin** recebe permissões de igreja e usuário.
- **Tesoureiro** e **Secretaria** terão permissões específicas nas sprints futuras.

### Hierarquia de Acesso

```
Master (sistema todo)
  └── Admin (igreja específica)
        ├── Tesoureiro (financeiro da igreja)
        └── Secretaria (membros da igreja)
```

### Como é feita a verificação

1. **Nas views** — Diretivas Blade `@can(permission)` e `@role(role)`.
2. **Nas rotas** — Middleware `can:permission` aplicado nos grupos de rota.
3. **Nos componentes Livewire/Volt** — Métodos `authorize()` e `can()` nas actions.
4. **Master** é verificado por `isMaster()` (helper) antes das verificações de role/permission.
