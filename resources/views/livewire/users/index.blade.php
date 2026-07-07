<?php

use App\Models\Church;
use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

new #[\Livewire\Attributes\Layout("layouts.app")] class extends Component {
    use WithPagination;

    public $showForm = false;
    public $showPasswordForm = false;
    public $editId = null;
    public $passwordUserId = null;
    public $church_id = "";
    public $name = "";
    public $email = "";
    public $password = "";
    public $password_confirmation = "";
    public $new_password = "";
    public $new_password_confirmation = "";
    public $role = "";
    public $active = true;
    public $search = "";

    public function mount()
    {
        if (!isMaster()) {
            $this->church_id = (string) auth()->user()->church_id;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editId = null;
        if (!isMaster()) {
            $this->church_id = (string) auth()->user()->church_id;
        }
    }

    public function edit($id)
    {
        $user = User::with("roles")->findOrFail($id);
        $this->editId = $user->id;
        $this->church_id = (string) $user->church_id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->active = $user->active;
        $this->role = $user->roles->first()?->name ?? "";
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            "name" => "required|string|max:255",
            "email" => "required|email|max:255|unique:users,email," . $this->editId,
            "role" => "required|string|exists:roles,name",
        ];

        if (!$this->editId) {
            $rules["password"] = "required|string|min:6|confirmed";
            $rules["password_confirmation"] = "required";
        }

        if (isMaster()) {
            $rules["church_id"] = "nullable|exists:churches,id";
        }

        $this->validate($rules);

        $data = [
            "name" => $this->name,
            "email" => $this->email,
            "active" => $this->active,
        ];

        if (isMaster()) {
            $data["church_id"] = $this->church_id ?: null;
        }

        if ($this->editId) {
            $user = User::findOrFail($this->editId);

            if (!isMaster() && $user->church_id !== auth()->user()->church_id) {
                abort(403);
            }

            if ($this->password) {
                $data["password"] = bcrypt($this->password);
            }

            $user->update($data);
            $user->syncRoles([$this->role]);
            session()->flash("success", "Usuário atualizado com sucesso!");
        } else {
            $data["password"] = bcrypt($this->password);
            $user = User::create($data);
            $user->assignRole($this->role);
            session()->flash("success", "Usuário criado com sucesso!");
        }

        $this->resetForm();
    }

    public function showPasswordForm($id)
    {
        $this->passwordUserId = $id;
        $this->new_password = "";
        $this->new_password_confirmation = "";
        $this->showPasswordForm = true;
    }

    public function savePassword()
    {
        $this->validate([
            "new_password" => "required|string|min:6|confirmed",
            "new_password_confirmation" => "required",
        ]);

        $user = User::findOrFail($this->passwordUserId);

        if (!isMaster() && $user->church_id !== auth()->user()->church_id) {
            abort(403);
        }

        $user->update(["password" => bcrypt($this->new_password)]);
        session()->flash("success", "Senha alterada com sucesso!");
        $this->showPasswordForm = false;
        $this->passwordUserId = null;
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);

        if (!isMaster() && $user->church_id !== auth()->user()->church_id) {
            abort(403);
        }

        $user->update(["active" => !$user->active]);
        session()->flash("success", "Status alterado com sucesso!");
    }

    public function resetForm()
    {
        $this->showForm = false;
        $this->showPasswordForm = false;
        $this->editId = null;
        $this->passwordUserId = null;
        $this->name = "";
        $this->email = "";
        $this->password = "";
        $this->password_confirmation = "";
        $this->new_password = "";
        $this->new_password_confirmation = "";
        $this->role = "";
        $this->active = true;
        if (!isMaster()) {
            $this->church_id = (string) auth()->user()->church_id;
        } else {
            $this->church_id = "";
        }
    }

    public function render(): mixed
    {
        $query = User::with("church", "roles");

        if (!isMaster()) {
            $query->where("church_id", auth()->user()->church_id);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where("name", "like", "%{$this->search}%")
                  ->orWhere("email", "like", "%{$this->search}%");
            });
        }

        $users = $query->orderBy("name")->paginate(10);
        $churches = Church::active()->orderBy("name")->get();
        $roles = Role::all();

        return view("livewire.users.index", compact("users", "churches", "roles"));
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-text-primary">Usuários</h1>
        @can("user.create")
            <x-button wire:click="create">+ Novo Usuário</x-button>
        @endcan
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1 max-w-md">
            <input wire:model.live.debounce="search" type="text" placeholder="Buscar usuário..." class="w-full pl-10 pr-4 py-2 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    @if($showForm)
        <x-card>
            <h2 class="text-lg font-semibold text-text-primary mb-4">{{ $editId ? "Editar Usuário" : "Novo Usuário" }}</h2>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(isMaster())
                        <div>
                            <x-input-label value="Igreja" />
                            <select wire:model="church_id" class="w-full border-input focus:border-primary focus:ring-primary rounded-md shadow-sm">
                                <option value="">Sem igreja (Master)</option>
                                @foreach($churches as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <x-input-label value="Nome *" />
                        <x-text-input wire:model="name" class="w-full" />
                        <x-input-error :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <x-input-label value="Email *" />
                        <x-text-input wire:model="email" type="email" class="w-full" />
                        <x-input-error :messages="$errors->get('email')" />
                    </div>
                    @if(!$editId)
                        <div>
                            <x-input-label value="Senha *" />
                            <x-text-input wire:model="password" type="password" class="w-full" />
                            <x-input-error :messages="$errors->get('password')" />
                        </div>
                        <div>
                            <x-input-label value="Confirmar Senha *" />
                            <x-text-input wire:model="password_confirmation" type="password" class="w-full" />
                        </div>
                    @endif
                    <div>
                        <x-input-label value="Perfil *" />
                        <select wire:model="role" class="w-full border-input focus:border-primary focus:ring-primary rounded-md shadow-sm">
                            <option value="">Selecione...</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" />
                    </div>
                    @if($editId)
                        <div class="flex items-center gap-3 mt-6">
                            <x-input-label value="Ativo" />
                            <input type="checkbox" wire:model="active" class="rounded border-input text-primary focus:ring-primary">
                        </div>
                    @endif
                </div>
                <div class="flex gap-3">
                    <x-button type="submit">{{ $editId ? "Atualizar" : "Salvar" }}</x-button>
                    <x-button variant="secondary" wire:click="resetForm" type="button">Cancelar</x-button>
                </div>
            </form>
        </x-card>
    @endif

    @if($showPasswordForm)
        <x-card>
            <h2 class="text-lg font-semibold text-text-primary mb-4">Alterar Senha</h2>
            <form wire:submit="savePassword" class="space-y-4 max-w-md">
                <div>
                    <x-input-label value="Nova Senha *" />
                    <x-text-input wire:model="new_password" type="password" class="w-full" />
                    <x-input-error :messages="$errors->get('new_password')" />
                </div>
                <div>
                    <x-input-label value="Confirmar Nova Senha *" />
                    <x-text-input wire:model="new_password_confirmation" type="password" class="w-full" />
                </div>
                <div class="flex gap-3">
                    <x-button type="submit">Salvar Senha</x-button>
                    <x-button variant="secondary" wire:click="resetForm" type="button">Cancelar</x-button>
                </div>
            </form>
        </x-card>
    @endif

    <x-table :headers="isMaster() ? ['Nome', 'Email', 'Igreja', 'Perfil', 'Status', 'Ações'] : ['Nome', 'Email', 'Perfil', 'Status', 'Ações']">
        @forelse($users as $user)
            <tr class="hover:bg-muted">
                <td class="px-6 py-4 text-sm font-medium text-text-primary">{{ $user->name }}</td>
                <td class="px-6 py-4 text-sm text-text-secondary">{{ $user->email }}</td>
                @if(isMaster())
                    <td class="px-6 py-4 text-sm text-text-secondary">{{ $user->church?->name ?? "-" }}</td>
                @endif
                <td class="px-6 py-4">
                    <x-badge variant="indigo">{{ $user->roles->first()?->name ?? "-" }}</x-badge>
                </td>
                <td class="px-6 py-4">
                    <x-status :active="$user->active" />
                </td>
                @can("user.edit")
                    <td class="px-6 py-4">
                        <div class="flex gap-2 justify-end">
                            <x-button size="sm" variant="secondary" wire:click="edit({{ $user->id }})">Editar</x-button>
                            <x-button size="sm" variant="warning" wire:click="showPasswordForm({{ $user->id }})">Senha</x-button>
                            <x-button size="sm" variant="{{ $user->active ? 'danger' : 'success' }}" wire:click="toggleActive({{ $user->id }})">
                                {{ $user->active ? "Inativar" : "Ativar" }}
                            </x-button>
                        </div>
                    </td>
                @endcan
            </tr>
        @empty
            <tr>
                <td colspan="{{ isMaster() ? 6 : 5 }}" class="px-6 py-12 text-center text-sm text-text-secondary">Nenhum usuário encontrado.</td>
            </tr>
        @endforelse
    </x-table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
