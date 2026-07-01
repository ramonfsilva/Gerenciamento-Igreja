<?php

use App\Models\Category;
use App\Models\Expense;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[\Livewire\Attributes\Layout("layouts.app")] class extends Component {
    use WithPagination;

    public $showForm = false;
    public $editId = null;
    public $category_id = "";
    public $date = "";
    public $amount = "";
    public $payment_method = "Dinheiro";
    public $description = "";
    public $filterMonth = "";
    public $filterYear = "";
    public $filterCategoryId = "";

    public function mount()
    {
        $this->filterMonth = now()->month;
        $this->filterYear = now()->year;
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->date = now()->format("Y-m-d");
        $this->editId = null;
    }

    public function edit($id)
    {
        $exp = Expense::findOrFail($id);
        $this->editId = $exp->id;
        $this->category_id = (string) $exp->category_id;
        $this->date = $exp->date->format("Y-m-d");
        $this->amount = number_format($exp->amount, 2, ",", "");
        $this->payment_method = $exp->payment_method;
        $this->description = $exp->description;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            "category_id" => "required|exists:categories,id",
            "date" => "required|date",
            "amount" => "required|string",
            "payment_method" => "required|string",
            "description" => "nullable|string",
        ]);

        $amount = (float) str_replace([".", ","], ["", "."], $this->amount);

        $data = [
            "church_id" => auth()->user()->church_id,
            "category_id" => $this->category_id,
            "user_id" => auth()->id(),
            "date" => $this->date,
            "amount" => $amount,
            "payment_method" => $this->payment_method,
            "description" => $this->description ?: null,
        ];

        if ($this->editId) {
            Expense::findOrFail($this->editId)->update($data);
            session()->flash("success", "Saída atualizada!");
        } else {
            Expense::create($data);
            session()->flash("success", "Saída registrada!");
        }

        $this->resetForm();
    }

    public function delete($id)
    {
        Expense::findOrFail($id)->delete();
        session()->flash("success", "Saída excluída!");
    }

    public function resetForm()
    {
        $this->showForm = false;
        $this->editId = null;
        $this->category_id = "";
        $this->date = "";
        $this->amount = "";
        $this->payment_method = "Dinheiro";
        $this->description = "";
    }

    public function render(): mixed
    {
        $query = Expense::with("category")->where("church_id", auth()->user()->church_id);

        if ($this->filterMonth) $query->whereMonth("date", $this->filterMonth);
        if ($this->filterYear) $query->whereYear("date", $this->filterYear);
        if ($this->filterCategoryId) $query->where("category_id", $this->filterCategoryId);

        $expenses = $query->orderByDesc("date")->paginate(12);
        $total = (clone $query)->sum("amount");
        $categories = Category::where("type", "expense")->where("active", true)->orderBy("name")->get();

        return view("livewire.financial.expenses", compact("expenses", "total", "categories"));
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-800">Saídas</h1>
        @can("expense.create")
            <x-button wire:click="create">+ Nova Saída</x-button>
        @endcan
    </div>

    <x-card :padding="false">
        <div class="p-4 flex flex-wrap items-center gap-3">
            <select wire:model.live="filterMonth" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->locale("pt_BR")->translatedFormat("F") }}</option>
                @endfor
            </select>
            <select wire:model.live="filterYear" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                @for($y = now()->year - 5; $y <= now()->year; $y++)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
            <select wire:model.live="filterCategoryId" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Todas categorias</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
            <div class="ml-auto text-sm text-gray-500">
                Total: <span class="font-semibold text-red-600">R$ {{ number_format($total, 2, ",", ".") }}</span>
            </div>
        </div>
    </x-card>

    @if($showForm)
        <x-card>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $editId ? "Editar Saída" : "Nova Saída" }}</h2>
            <form wire:submit="save" class="space-y-4 max-w-lg">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Categoria *" />
                        <select wire:model="category_id" class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="">Selecione</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" />
                    </div>
                    <div>
                        <x-input-label value="Valor *" />
                        <x-text-input wire:model="amount" class="w-full" placeholder="0,00" />
                        <x-input-error :messages="$errors->get('amount')" />
                    </div>
                    <div>
                        <x-input-label value="Data *" />
                        <x-text-input wire:model="date" type="date" class="w-full" />
                        <x-input-error :messages="$errors->get('date')" />
                    </div>
                    <div>
                        <x-input-label value="Forma de Pagamento" />
                        <select wire:model="payment_method" class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option>Dinheiro</option>
                            <option>PIX</option>
                            <option>Cartão</option>
                            <option>Transferência</option>
                            <option>Outro</option>
                        </select>
                    </div>
                </div>
                <div>
                    <x-input-label value="Descrição" />
                    <x-textarea wire:model="description" rows="2" class="w-full" />
                </div>
                <div class="flex gap-3">
                    <x-button type="submit">{{ $editId ? "Atualizar" : "Salvar" }}</x-button>
                    <x-button variant="secondary" wire:click="resetForm" type="button">Cancelar</x-button>
                </div>
            </form>
        </x-card>
    @endif

    @if($expenses->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6">
            @foreach($expenses as $exp)
                <x-card>
                    <div class="flex flex-col h-full">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $exp->description ?? $exp->category?->name }}</p>
                                <p class="text-sm text-gray-500">{{ $exp->category?->name }}</p>
                            </div>
                            <span class="text-lg font-bold text-red-600 shrink-0">R$ {{ number_format($exp->amount, 2, ",", ".") }}</span>
                        </div>
                        <div class="space-y-1 text-sm text-gray-500 flex-1">
                            <p>📅 {{ $exp->date->format("d/m/Y") }}</p>
                            <p>💳 {{ $exp->payment_method }}</p>
                            @if($exp->description)
                                <p class="text-gray-400 italic">{{ \Illuminate\Support\Str::limit($exp->description, 60) }}</p>
                            @endif
                        </div>
                        <div class="flex gap-2 mt-4 pt-3 border-t border-gray-100">
                            @can("expense.edit")
                                <x-button size="sm" variant="secondary" wire:click="edit({{ $exp->id }})">Editar</x-button>
                            @endcan
                            @can("expense.delete")
                                <x-button size="sm" variant="danger" wire:click="delete({{ $exp->id }})" wire:confirm="Excluir esta saída?">Excluir</x-button>
                            @endcan
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @else
        <x-card>
            <p class="text-center text-gray-500 py-8">Nenhuma saída no período.</p>
        </x-card>
    @endif

    <div class="mt-4">
        {{ $expenses->links() }}
    </div>
</div>
