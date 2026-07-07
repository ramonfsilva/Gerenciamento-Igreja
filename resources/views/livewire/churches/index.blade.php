<?php

use App\Models\Church;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[\Livewire\Attributes\Layout("layouts.app")] class extends Component {
    use WithPagination;

    public $showForm = false;
    public $editId = null;
    public $name = "";
    public $cnpj = "";
    public $phone = "";
    public $email = "";
    public $pastor_name = "";
    public $address = "";
    public $city = "";
    public $state = "";
    public $search = "";
    public $admin_email = "";
    public $admin_name = "";
    public $admin_found = false;
    public $admin_role = "Admin";
    public $remove_admin = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editId = null;
    }

    public function edit($id)
    {
        $church = Church::findOrFail($id);
        $this->editId = $church->id;
        $this->name = $church->name;
        $this->cnpj = $church->cnpj;
        $this->phone = $church->phone;
        $this->email = $church->email;
        $this->pastor_name = $church->pastor_name;
        $this->address = $church->address;
        $this->city = $church->city;
        $this->state = $church->state;
        $this->remove_admin = false;

        $admin = \App\Models\User::where("church_id", $church->id)->first();
        if ($admin) {
            $this->admin_email = $admin->email;
            $this->admin_name = $admin->name;
            $this->admin_found = true;
        } else {
            $this->admin_email = "";
            $this->admin_name = "";
            $this->admin_found = false;
        }

        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            "name" => "required|string|max:255",
            "cnpj" => "nullable|string|max:18|unique:churches,cnpj," . $this->editId,
            "phone" => "nullable|string|max:20",
            "email" => "nullable|email|max:255",
            "pastor_name" => "nullable|string|max:255",
            "address" => "nullable|string",
            "city" => "nullable|string|max:255",
            "state" => "nullable|string|max:2",
        ]);

        $data = [
            "name" => $this->name,
            "cnpj" => $this->cnpj ?: null,
            "phone" => $this->phone ?: null,
            "email" => $this->email ?: null,
            "pastor_name" => $this->pastor_name ?: null,
            "address" => $this->address ?: null,
            "city" => $this->city ?: null,
            "state" => $this->state ?: null,
        ];

        if ($this->editId) {
            $church = Church::findOrFail($this->editId);
            $church->update($data);

            if ($this->admin_email) {
                $user = \App\Models\User::where("email", $this->admin_email)->first();
                if ($user) {
                    $user->update(["church_id" => $church->id]);
                } else {
                    $user = \App\Models\User::create([
                        "name" => $this->admin_name ?: explode("@", $this->admin_email)[0],
                        "email" => $this->admin_email,
                        "password" => bcrypt("12345678"),
                        "church_id" => $church->id,
                        "active" => true,
                    ]);
                    $user->assignRole($this->admin_role);
                }
                session()->flash("success", "Igreja atualizada! Usuário vinculado.");
            } elseif ($this->remove_admin) {
                \App\Models\User::where("church_id", $church->id)->update(["church_id" => null]);
                session()->flash("success", "Igreja atualizada! Administrador removido.");
            } else {
                session()->flash("success", "Igreja atualizada com sucesso!");
            }
        } else {
            $church = Church::create($data);
            if ($this->admin_email) {
                $user = \App\Models\User::where("email", $this->admin_email)->first();
                if ($user) {
                    $user->update(["church_id" => $church->id]);
                } else {
                    $user = \App\Models\User::create([
                        "name" => $this->admin_name ?: explode("@", $this->admin_email)[0],
                        "email" => $this->admin_email,
                        "password" => bcrypt("12345678"),
                        "church_id" => $church->id,
                        "active" => true,
                    ]);
                    $user->assignRole($this->admin_role);
                }
                session()->flash("success", "Igreja criada! Usuário vinculado.");
            } else {
                session()->flash("success", "Igreja criada com sucesso!");
            }
        }

        $this->resetForm();
    }

    public function toggleActive($id)
    {
        $church = Church::findOrFail($id);
        $church->update(["active" => !$church->active]);
        session()->flash("success", "Status alterado com sucesso!");
    }

    public function searchAdmin()
    {
        if (!$this->admin_email) return;

        $user = \App\Models\User::where("email", $this->admin_email)->first();
        if ($user) {
            $this->admin_name = $user->name;
            $this->admin_found = true;
        } else {
            $this->admin_name = "";
            $this->admin_found = false;
        }
    }


    public function resetForm()
    {
        $this->showForm = false;
        $this->editId = null;
        $this->name = "";
        $this->cnpj = "";
        $this->phone = "";
        $this->email = "";
        $this->pastor_name = "";
        $this->address = "";
        $this->city = "";
        $this->state = "";
        $this->admin_email = "";
        $this->admin_name = "";
        $this->admin_found = false;
        $this->remove_admin = false;
    }

    public function render(): mixed
    {
        $churches = Church::withCount("users")
            ->where("name", "like", "%{$this->search}%")
            ->orWhere("city", "like", "%{$this->search}%")
            ->orderBy("name")
            ->paginate(12);

        return view("livewire.churches.index", compact("churches"));
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-text-primary">Igrejas</h1>
        @can("church.create")
            <x-button wire:click="create">+ Nova Igreja</x-button>
        @endcan
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1 max-w-md">
            <input wire:model.live.debounce="search" type="text" placeholder="Buscar igreja..." class="w-full pl-10 pr-4 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    @if($showForm)
        <x-card>
            <h2 class="text-lg font-semibold text-text-primary mb-4">{{ $editId ? "Editar Igreja" : "Nova Igreja" }}</h2>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Nome *" />
                        <x-text-input wire:model="name" class="w-full" />
                        <x-input-error :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <x-input-label value="CNPJ" />
                        <x-text-input wire:model="cnpj" class="w-full" />
                        <x-input-error :messages="$errors->get('cnpj')" />
                    </div>
                    <div>
                        <x-input-label value="Telefone" />
                        <x-text-input wire:model="phone" class="w-full" />
                    </div>
                    <div>
                        <x-input-label value="Email" />
                        <x-text-input wire:model="email" type="email" class="w-full" />
                    </div>
                    <div>
                        <x-input-label value="Pastor" />
                        <x-text-input wire:model="pastor_name" class="w-full" />
                    </div>
                    <div>
                        <x-input-label value="Cidade" />
                        <x-text-input wire:model="city" class="w-full" />
                    </div>
                    <div>
                        <x-input-label value="Estado" />
                        <x-text-input wire:model="state" maxlength="2" class="w-full" />
                    </div>
                </div>
                <div>
                    <x-input-label value="Endereço" />
                    <x-textarea wire:model="address" rows="2" class="w-full" />
                </div>

                @if(isMaster())
                    <hr class="border-border">
                    <h3 class="text-base font-semibold text-text-primary">Vincular Administrador</h3>
                    <p class="text-sm text-text-secondary">Opcional. Informe o email do administrador da igreja.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Email do Administrador" />
                            <div class="flex gap-2">
                                <x-text-input wire:model="admin_email" class="flex-1" type="email" placeholder="admin@exemplo.com" />
                                <x-button variant="secondary" wire:click="searchAdmin" type="button">Buscar</x-button>
                            </div>
                        </div>
                        @if($admin_found)
                            <div>
                                <x-input-label value="Usuário encontrado" />
                                <div class="flex items-center gap-2">
                                    <x-text-input value="{{ $admin_name }}" class="flex-1 bg-surface-hover" disabled />
                                    @if($editId)
                                        <x-button variant="danger" size="sm" wire:click="$set('remove_admin', true)" type="button">Remover</x-button>
                                    @endif
                                </div>
                            </div>
                        @elseif($admin_email && $admin_name === "")
                            <div>
                                <x-input-label value="Nome do novo usuário" />
                                <x-text-input wire:model="admin_name" class="w-full" placeholder="Nome completo" />
                            </div>
                        @endif
                    </div>
                    @if($admin_email && $admin_name === "")
                        <p class="text-xs text-text-muted mt-1">Usuário será criado com senha padrão 12345678 e papel {{ $admin_role }}.</p>
                    @endif
                    @if($editId && $remove_admin)
                        <p class="text-sm text-danger mt-2">O administrador será desvinculado desta igreja ao salvar.</p>
                    @endif
                @endif
                <div class="flex gap-3">
                    <x-button type="submit">{{ $editId ? "Atualizar" : "Salvar" }}</x-button>
                    <x-button variant="secondary" wire:click="resetForm" type="button">Cancelar</x-button>
                </div>
            </form>
        </x-card>
    @endif

    @if($churches->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
            @foreach($churches as $church)
                <x-card>
                    <div class="flex flex-col h-full">
                        <div class="flex items-start justify-between mb-3">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-text-primary truncate">{{ $church->name }}</h3>
                                @if($church->city || $church->state)
                                    <p class="text-sm text-text-secondary truncate">{{ $church->city }}{{ $church->city && $church->state ? ", " : "" }}{{ $church->state }}</p>
                                @endif
                            </div>
                            <x-status :active="$church->active" class="ml-2 shrink-0" />
                        </div>
                        <div class="space-y-1.5 text-sm text-text-secondary flex-1">
                            @if($church->pastor_name)
                                <p class="truncate"><span class="text-text-muted">Pastor:</span> {{ $church->pastor_name }}</p>
                            @endif
                            @if($church->phone)
                                <p class="truncate"><span class="text-text-muted">Tel:</span> {{ $church->phone }}</p>
                            @endif
                            @if($church->cnpj)
                                <p class="truncate"><span class="text-text-muted">CNPJ:</span> {{ $church->cnpj }}</p>
                            @endif
                            <p><span class="text-text-muted">Usuários:</span> {{ $church->users_count }}</p>
                        </div>
                        <div class="flex gap-2 mt-4 pt-3 border-t border-border">
                            <x-button size="sm" variant="secondary" wire:click="edit({{ $church->id }})">Editar</x-button>
                            <x-button size="sm" variant="{{ $church->active ? 'danger' : 'success' }}" wire:click="toggleActive({{ $church->id }})">
                                {{ $church->active ? "Inativar" : "Ativar" }}
                            </x-button>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @else
        <x-card>
            <p class="text-center text-text-secondary py-8">Nenhuma igreja encontrada.</p>
        </x-card>
    @endif

    <div class="mt-4">
        {{ $churches->links() }}
    </div>
</div>
