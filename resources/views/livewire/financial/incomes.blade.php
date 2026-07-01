<?php

use App\Models\Category;
use App\Models\Income;
use App\Models\Member;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[\Livewire\Attributes\Layout("layouts.app")] class extends Component {
    use WithPagination;

    public $showForm = false;
    public $editId = null;
    public $member_id = "";
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
        $inc = Income::findOrFail($id);
        $this->editId = $inc->id;
        $this->member_id = (string) $inc->member_id;
        $this->category_id = (string) $inc->category_id;
        $this->date = $inc->date->format("Y-m-d");
        $this->amount = number_format($inc->amount, 2, ",", "");
        $this->payment_method = $inc->payment_method;
        $this->description = $inc->description;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            "category_id" => "required|exists:categories,id",
            "date" => "required|date",
            "amount" => "required|string",
            "payment_method" => "required|string",
            "member_id" => "nullable|exists:members,id",
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
            "member_id" => $this->member_id ?: null,
            "description" => $this->description ?: null,
        ];

        if ($this->editId) {
            Income::findOrFail($this->editId)->update($data);
            session()->flash("success", "Entrada atualizada!");
        } else {
            Income::create($data);
            session()->flash("success", "Entrada registrada!");
        }

        $this->resetForm();
    }

    public function delete($id)
    {
        Income::findOrFail($id)->delete();
        session()->flash("success", "Entrada excluída!");
    }

    public function resetForm()
    {
        $this->showForm = false;
        $this->editId = null;
        $this->member_id = "";
        $this->category_id = "";
        $this->date = "";
        $this->amount = "";
        $this->payment_method = "Dinheiro";
        $this->description = "";
    }

    public function render(): mixed
    {
        $query = Income::with("category", "member")->where("church_id", auth()->user()->church_id);

        if ($this->filterMonth) $query->whereMonth("date", $this->filterMonth);
        if ($this->filterYear) $query->whereYear("date", $this->filterYear);
        if ($this->filterCategoryId) $query->where("category_id", $this->filterCategoryId);

        $incomes = $query->orderByDesc("date")->paginate(12);
        $total = (clone $query)->sum("amount");
        $categories = Category::where("type", "income")->where("active", true)->orderBy("name")->get();

        return view("livewire.financial.incomes", compact("incomes", "total", "categories"));
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-800">Entradas</h1>
        @can("income.create")
            <x-button wire:click="create">+ Nova Entrada</x-button>
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
                Total: <span class="font-semibold text-green-600">R$ {{ number_format($total, 2, ",", ".") }}</span>
            </div>
        </div>
    </x-card>

    @if($showForm)
        <x-card>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $editId ? "Editar Entrada" : "Nova Entrada" }}</h2>
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
                    <x-input-label value="Membro (opcional)" />
                    <select wire:model="member_id" class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                        <option value="">Nenhum</option>
                        @foreach(Member::where("church_id", auth()->user()->church_id)->where("active", true)->orderBy("name")->get() as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
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

    @if($incomes->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6">
            @foreach($incomes as $inc)
                <x-card>
                    <div class="flex flex-col h-full">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $inc->description ?? $inc->category?->name }}</p>
                                <p class="text-sm text-gray-500">{{ $inc->category?->name }}</p>
                            </div>
                            <span class="text-lg font-bold text-green-600 shrink-0">R$ {{ number_format($inc->amount, 2, ",", ".") }}</span>
                        </div>
                        <div class="space-y-1 text-sm text-gray-500 flex-1">
                            <p>📅 {{ $inc->date->format("d/m/Y") }}</p>
                            <p>💳 {{ $inc->payment_method }}</p>
                            @if($inc->member)
                                <p>👤 {{ $inc->member->name }}</p>
                            @endif
                        </div>
                        <div class="flex gap-2 mt-4 pt-3 border-t border-gray-100">
                            @can("income.edit")
                                <x-button size="sm" variant="secondary" wire:click="edit({{ $inc->id }})">Editar</x-button>
                            @endcan
                            @can("income.delete")
                                <x-button size="sm" variant="danger" wire:click="delete({{ $inc->id }})" wire:confirm="Excluir esta entrada?">Excluir</x-button>
                            @endcan
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @else
        <x-card>
            <p class="text-center text-gray-500 py-8">Nenhuma entrada no período.</p>
        </x-card>
    @endif

    <div class="mt-4">
        {{ $incomes->links() }}
    </div>
</div>
