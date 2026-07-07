<?php

use App\Models\Member;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[\Livewire\Attributes\Layout("layouts.app")] class extends Component {
    use WithPagination;

    public $showForm = false;
    public $editId = null;
    public $name = "";
    public $phone = "";
    public $whatsapp = "";
    public $email = "";
    public $birth_date = "";
    public $gender = "";
    public $marital_status = "";
    public $role_function = "";
    public $status = "Ativo";
    public $conversion_date = "";
    public $baptism_date = "";
    public $notes = "";
    public $search = "";
    public $filterStatus = "";
    public $filterRole = "";

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterRole() { $this->resetPage(); }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editId = null;
    }

    public function edit($id)
    {
        $m = Member::findOrFail($id);
        $this->editId = $m->id;
        $this->name = $m->name;
        $this->phone = $m->phone;
        $this->whatsapp = $m->whatsapp;
        $this->email = $m->email;
        $this->birth_date = $m->birth_date?->format("Y-m-d");
        $this->gender = $m->gender;
        $this->marital_status = $m->marital_status;
        $this->role_function = $m->role_function;
        $this->status = $m->status;
        $this->conversion_date = $m->conversion_date?->format("Y-m-d");
        $this->baptism_date = $m->baptism_date?->format("Y-m-d");
        $this->notes = $m->notes;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            "name" => "required|string|max:255",
            "phone" => "nullable|string|max:20",
            "whatsapp" => "nullable|string|max:20",
            "email" => "nullable|email|max:255",
            "birth_date" => "nullable|date",
            "gender" => "nullable|in:Masculino,Feminino,Não informado",
            "marital_status" => "nullable|in:Solteiro(a),Casado(a),Divorciado(a),Viúvo(a),Não informado",
            "role_function" => "nullable|string|max:255",
            "status" => "required|in:Ativo,Congregado,Visitante,Afastado,Transferido",
            "conversion_date" => "nullable|date",
            "baptism_date" => "nullable|date",
            "notes" => "nullable|string",
        ]);

        $data = [
            "church_id" => isMaster() ? ($this->editId ? Member::find($this->editId)->church_id : null) : auth()->user()->church_id,
            "name" => $this->name,
            "phone" => $this->phone ?: null,
            "whatsapp" => $this->whatsapp ?: null,
            "email" => $this->email ?: null,
            "birth_date" => $this->birth_date ?: null,
            "gender" => $this->gender ?: null,
            "marital_status" => $this->marital_status ?: null,
            "role_function" => $this->role_function ?: null,
            "status" => $this->status,
            "conversion_date" => $this->conversion_date ?: null,
            "baptism_date" => $this->baptism_date ?: null,
            "notes" => $this->notes ?: null,
        ];

        if ($this->editId) {
            $m = Member::findOrFail($this->editId);
            if (!isMaster()) $data["church_id"] = $m->church_id;
            $m->update($data);
            session()->flash("success", "Membro atualizado com sucesso!");
        } else {
            if (isMaster() && !$data["church_id"]) {
                session()->flash("error", "Selecione uma igreja para o membro.");
                return;
            }
            Member::create($data);
            session()->flash("success", "Membro cadastrado com sucesso!");
        }

        $this->resetForm();
    }

    public function toggleActive($id)
    {
        $m = Member::findOrFail($id);
        $m->update(["active" => !$m->active]);
        session()->flash("success", "Status alterado com sucesso!");
    }

    public function clearFilters()
    {
        $this->search = "";
        $this->filterStatus = "";
        $this->filterRole = "";
    }

    public function resetForm()
    {
        $this->showForm = false;
        $this->editId = null;
        $this->name = "";
        $this->phone = "";
        $this->whatsapp = "";
        $this->email = "";
        $this->birth_date = "";
        $this->gender = "";
        $this->marital_status = "";
        $this->role_function = "";
        $this->status = "Ativo";
        $this->conversion_date = "";
        $this->baptism_date = "";
        $this->notes = "";
    }

    public function render(): mixed
    {
        $query = Member::query();

        if (!isMaster()) {
            $query->where("church_id", auth()->user()->church_id);
        }

        if ($this->search) {
            $query->where("name", "like", "%{$this->search}%");
        }

        if ($this->filterStatus) {
            $query->where("status", $this->filterStatus);
        }

        if ($this->filterRole) {
            $query->where("role_function", $this->filterRole);
        }

        $members = $query->orderBy("name")->paginate(12);
        $roles = Member::whereNotNull("role_function")->distinct()->pluck("role_function");
        $totalMembers = (clone $query)->count();
        $totalActive = (clone $query)->where("active", true)->count();

        return view("livewire.members.index", compact("members", "roles", "totalMembers", "totalActive"));
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-text-primary font-heading">Membros</h1>
            <p class="text-sm text-text-secondary mt-1">{{ $totalMembers }} membro(s) — {{ $totalActive }} ativo(s)</p>
        </div>
        @can("member.create")
            <x-button wire:click="create">+ Novo Membro</x-button>
        @endcan
    </div>

    <x-card :padding="false">
        <div class="p-4 flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <x-text-input wire:model.live.debounce="search" type="text" placeholder="Buscar por nome..." class="w-full pl-10" />
                <x-icon name="search" size="5" class="absolute left-3 top-2.5 text-text-muted" />
            </div>
            <select wire:model.live="filterStatus" class="px-4 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent bg-surface text-text-primary">
                <option value="">Todas situações</option>
                <option value="Ativo">Ativo</option>
                <option value="Congregado">Congregado</option>
                <option value="Visitante">Visitante</option>
                <option value="Afastado">Afastado</option>
                <option value="Transferido">Transferido</option>
            </select>
            <select wire:model.live="filterRole" class="px-4 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent bg-surface text-text-primary">
                <option value="">Todas funções</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}">{{ $role }}</option>
                @endforeach
            </select>
            <x-button variant="secondary" size="sm" wire:click="clearFilters" type="button">Limpar</x-button>
        </div>
    </x-card>

    @if($showForm)
        <x-card>
            <h2 class="text-lg font-semibold text-text-primary font-heading mb-4">{{ $editId ? "Editar Membro" : "Novo Membro" }}</h2>
            <form wire:submit="save" class="space-y-6">
                <div>
                    <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-3">Dados Pessoais</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Nome *" />
                            <x-text-input wire:model="name" class="w-full" />
                            <x-input-error :messages="$errors->get('name')" />
                        </div>
                        <div>
                            <x-input-label value="Data de Nascimento" />
                            <x-text-input wire:model="birth_date" type="date" class="w-full" />
                        </div>
                        <div>
                            <x-input-label value="Gênero" />
                            <select wire:model="gender" class="w-full border-input focus:border-primary focus:ring-primary rounded-md shadow-sm bg-surface text-text-primary">
                                <option value="">Selecione</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                                <option value="Não informado">Não informado</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Estado Civil" />
                            <select wire:model="marital_status" class="w-full border-input focus:border-primary focus:ring-primary rounded-md shadow-sm bg-surface text-text-primary">
                                <option value="">Selecione</option>
                                <option value="Solteiro(a)">Solteiro(a)</option>
                                <option value="Casado(a)">Casado(a)</option>
                                <option value="Divorciado(a)">Divorciado(a)</option>
                                <option value="Viúvo(a)">Viúvo(a)</option>
                                <option value="Não informado">Não informado</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-3">Contato</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label value="Telefone" />
                            <x-text-input wire:model="phone" class="w-full" />
                        </div>
                        <div>
                            <x-input-label value="WhatsApp" />
                            <x-text-input wire:model="whatsapp" class="w-full" />
                        </div>
                        <div>
                            <x-input-label value="Email" />
                            <x-text-input wire:model="email" type="email" class="w-full" />
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-3">Informações da Igreja</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Função/Cargo" />
                            <x-text-input wire:model="role_function" placeholder="Ex: Diácono, Pastor, Secretário..." class="w-full" />
                        </div>
                        <div>
                            <x-input-label value="Situação" />
                            <select wire:model="status" class="w-full border-input focus:border-primary focus:ring-primary rounded-md shadow-sm bg-surface text-text-primary">
                                <option value="Ativo">Ativo</option>
                                <option value="Congregado">Congregado</option>
                                <option value="Visitante">Visitante</option>
                                <option value="Afastado">Afastado</option>
                                <option value="Transferido">Transferido</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Data de Conversão" />
                            <x-text-input wire:model="conversion_date" type="date" class="w-full" />
                        </div>
                        <div>
                            <x-input-label value="Data de Batismo" />
                            <x-text-input wire:model="baptism_date" type="date" class="w-full" />
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-3">Observações</h3>
                    <x-textarea wire:model="notes" rows="3" class="w-full" />
                </div>

                <div class="flex gap-3">
                    <x-button type="submit">{{ $editId ? "Atualizar" : "Salvar" }}</x-button>
                    <x-button variant="secondary" wire:click="resetForm" type="button">Cancelar</x-button>
                </div>
            </form>
        </x-card>
    @endif

    @if($members->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6">
            @foreach($members as $member)
                <x-card>
                    <div class="flex flex-col h-full">
                        <div class="flex items-start justify-between mb-2">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-text-primary truncate">{{ $member->name }}</h3>
                                @if($member->role_function)
                                    <p class="text-sm text-text-secondary truncate">{{ $member->role_function }}</p>
                                @endif
                            </div>
                            <x-status :active="$member->active" class="ml-2 shrink-0" />
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            @php
                                $statusColors = [
                                    "Ativo" => "green", "Congregado" => "blue",
                                    "Visitante" => "yellow", "Afastado" => "red",
                                    "Transferido" => "gray",
                                ];
                            @endphp
                            <x-badge :variant="$statusColors[$member->status] ?? 'gray'">{{ $member->status }}</x-badge>
                            @if($member->birth_date)
                                <x-badge variant="indigo">{{ $member->birth_date->format("d/m/Y") }}</x-badge>
                            @endif
                        </div>
                        <div class="space-y-1 text-sm text-text-secondary flex-1">
                            @if($member->phone)
                                <p class="truncate flex items-center gap-1.5">
                                    <x-icon name="phone" size="4" class="shrink-0" />
                                    {{ $member->phone }}
                                </p>
                            @endif
                            @if($member->whatsapp)
                                <p class="truncate flex items-center gap-1.5">
                                    <x-icon name="chat" size="4" class="shrink-0" />
                                    {{ $member->whatsapp }}
                                </p>
                            @endif
                            @if($member->email)
                                <p class="truncate flex items-center gap-1.5">
                                    <x-icon name="mail" size="4" class="shrink-0" />
                                    {{ $member->email }}
                                </p>
                            @endif
                        </div>
                        <div class="flex gap-2 mt-4 pt-3 border-t border-border">
                            @can("member.edit")
                                <x-button size="sm" variant="secondary" wire:click="edit({{ $member->id }})">Editar</x-button>
                            @endcan
                            @can("member.delete")
                                <x-button size="sm" variant="{{ $member->active ? 'danger' : 'success' }}" wire:click="toggleActive({{ $member->id }})">
                                    {{ $member->active ? "Inativar" : "Ativar" }}
                                </x-button>
                            @endcan
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @else
        <x-card>
            <p class="text-center text-gray-500 py-8">Nenhum membro encontrado.</p>
        </x-card>
    @endif

    <div class="mt-4">
        {{ $members->links() }}
    </div>
</div>
