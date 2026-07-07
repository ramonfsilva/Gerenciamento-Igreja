<aside x-data="{ open: false, finOpen: false }" class="w-64 bg-sidebar-bg border-r border-sidebar-border min-h-[calc(100vh-4rem)] shrink-0">
    <div class="p-4">
        <button @click="open = !open" class="lg:hidden mb-4 text-text-secondary hover:text-text-primary transition-colors">
            <x-icon name="menu" size="6" />
        </button>

        <nav :class="open ? 'block' : 'hidden'" class="lg:block space-y-1">
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                <x-icon name="dashboard" size="5" />
                <span>Dashboard</span>
            </x-sidebar-link>

            @can('member.view')
                <x-sidebar-link :href="route('members.index')" :active="request()->routeIs('members.*')" wire:navigate>
                    <x-icon name="members" size="5" />
                    <span>Membros</span>
                </x-sidebar-link>
            @endcan

            @can('report.view')
                <x-sidebar-link :href="route('reports.members')" :active="request()->routeIs('reports.*')" wire:navigate>
                    <x-icon name="reports" size="5" />
                    <span>Relatórios</span>
                </x-sidebar-link>
            @endcan

            @can('financial.view')
                <div>
                    <button @click="finOpen = !finOpen" class="flex items-center justify-between w-full px-4 py-2.5 rounded-lg text-text-secondary hover:text-text-primary hover:bg-surface-hover transition-all duration-fast">
                        <span class="flex items-center gap-3">
                            <x-icon name="finance" size="5" />
                            <span>Financeiro</span>
                        </span>
                        <x-icon name="chevron-down" size="4" x-bind:class="finOpen ? 'rotate-180' : ''" class="transition-transform duration-fast" />
                    </button>
                    <div x-show="finOpen" class="ml-8 space-y-1 mt-1">
                        <x-sidebar-link :href="route('financial.dashboard')" :active="request()->routeIs('financial.dashboard')" wire:navigate>
                            <span>Dashboard</span>
                        </x-sidebar-link>
                        <x-sidebar-link :href="route('financial.incomes')" :active="request()->routeIs('financial.incomes')" wire:navigate>
                            <span>Entradas</span>
                        </x-sidebar-link>
                        <x-sidebar-link :href="route('financial.expenses')" :active="request()->routeIs('financial.expenses')" wire:navigate>
                            <span>Saídas</span>
                        </x-sidebar-link>
                        <x-sidebar-link :href="route('financial.reports')" :active="request()->routeIs('financial.reports')" wire:navigate>
                            <span>Relatórios</span>
                        </x-sidebar-link>
                        @can('category.view')
                            <x-sidebar-link :href="route('financial.categories')" :active="request()->routeIs('financial.categories')" wire:navigate>
                                <span>Categorias</span>
                            </x-sidebar-link>
                        @endcan
                    </div>
                </div>
            @endcan

            @can('church.view')
                <x-sidebar-link :href="route('churches.index')" :active="request()->routeIs('churches.*')" wire:navigate>
                    <x-icon name="church" size="5" />
                    <span>Igrejas</span>
                </x-sidebar-link>
            @endcan

            @can('user.view')
                <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')" wire:navigate>
                    <x-icon name="users" size="5" />
                    <span>Usuários</span>
                </x-sidebar-link>
            @endcan

            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-text-muted hover:bg-surface-hover transition-all duration-fast cursor-not-allowed">
                <x-icon name="settings" size="5" />
                <span>Configurações</span>
            </a>
        </nav>
    </div>
</aside>
