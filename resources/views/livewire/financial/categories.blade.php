<?php

use App\Models\Category;
use Livewire\Volt\Component;

new #[\Livewire\Attributes\Layout("layouts.app")] class extends Component {
    public $showForm = false;
    public $editId = null;
    public $name = "";
    public $type = "income";
    public $active = true;

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editId = null;
    }

    public function edit($id)
    {
        $cat = Category::findOrFail($id);
        $this->editId = $cat->id;
        $this->name = $cat->name;
        $this->type = $cat->type;
        $this->active = $cat->active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            "name" => "required|string|max:255",
            "type" => "required|in:income,expense",
        ]);

        $data = [
            "name" => $this->name,
            "type" => $this->type,
            "active" => $this->active,
        ];

        if ($this->editId) {
            Category::findOrFail($this->editId)->update($data);
            session()->flash("success", "Categoria atualizada!");
        } else {
            Category::create($data);
            session()->flash("success", "Categoria criada!");
        }

        $this->resetForm();
    }

    public function toggleActive($id)
    {
        $cat = Category::findOrFail($id);
        $cat->update(["active" => !$cat->active]);
        session()->flash("success", "Status alterado!");
    }

    public function resetForm()
    {
        $this->showForm = false;
        $this->editId = null;
        $this->name = "";
        $this->type = "income";
        $this->active = true;
    }

    public function render(): mixed
    {
        $incomes = Category::where("type", "income")->orderBy("name")->get();
        $expenses = Category::where("type", "expense")->orderBy("name")->get();
        return view("livewire.financial.categories", compact("incomes", "expenses"));
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Categorias Financeiras</h1>
        @can("category.create")
            <x-button wire:click="create">+ Nova Categoria</x-button>
        @endcan
    </div>

    @if($showForm)
        <x-card>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $editId ? "Editar Categoria" : "Nova Categoria" }}</h2>
            <form wire:submit="save" class="space-y-4 max-w-md">
                <div>
                    <x-input-label value="Nome *" />
                    <x-text-input wire:model="name" class="w-full" />
                    <x-input-error :messages="$errors->get('name')" />
                </div>
                <div>
                    <x-input-label value="Tipo *" />
                    <select wire:model="type" class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                        <option value="income">Entrada</option>
                        <option value="expense">Saída</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" />
                </div>
                @if($editId)
                    <div class="flex items-center gap-3">
                        <x-input-label value="Ativo" />
                        <input type="checkbox" wire:model="active" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </div>
                @endif
                <div class="flex gap-3">
                    <x-button type="submit">{{ $editId ? "Atualizar" : "Salvar" }}</x-button>
                    <x-button variant="secondary" wire:click="resetForm" type="button">Cancelar</x-button>
                </div>
            </form>
        </x-card>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h2 class="text-lg font-semibold text-green-700 mb-4 flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span> Entradas
            </h2>
            <div class="space-y-3">
                @forelse($incomes as $cat)
                    <x-card>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $cat->name }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-status :active="$cat->active" />
                                @can("category.edit")
                                    <x-button size="sm" variant="secondary" wire:click="edit({{ $cat->id }})">Editar</x-button>
                                @endcan
                            </div>
                        </div>
                    </x-card>
                @empty
                    <x-card><p class="text-sm text-gray-500 text-center py-2">Nenhuma categoria.</p></x-card>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-red-700 mb-4 flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span> Saídas
            </h2>
            <div class="space-y-3">
                @forelse($expenses as $cat)
                    <x-card>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $cat->name }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-status :active="$cat->active" />
                                @can("category.edit")
                                    <x-button size="sm" variant="secondary" wire:click="edit({{ $cat->id }})">Editar</x-button>
                                @endcan
                            </div>
                        </div>
                    </x-card>
                @empty
                    <x-card><p class="text-sm text-gray-500 text-center py-2">Nenhuma categoria.</p></x-card>
                @endforelse
            </div>
        </div>
    </div>
</div>
