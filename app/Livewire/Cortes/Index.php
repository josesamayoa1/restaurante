<?php

namespace App\Livewire\Cortes;

use App\Models\Caja;
use App\Models\Corte;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $confirmingCorteDeletion = false;
    public $corte_id;
    public $saldo_inicial;
    public $saldo_final;
    public $caja_id;
    public $usuario_id;
    public $fecha;
    public $cajas;
    public $usuarios;

    protected $rules = [
        'saldo_inicial' => 'required|numeric|min:0',
        'saldo_final' => 'required|numeric|min:0',
        'caja_id' => 'required|exists:cajas,id',
        'usuario_id' => 'required|exists:users,id',
        'fecha' => 'required|date',
    ];

    public function mount()
    {
        $this->cajas = Caja::all();
        $this->usuarios = User::all();
    }

    public function render()
    {
        $user = Auth::user();
        $query = Corte::with(['caja', 'usuario'])
            ->where(function($q) {
                $q->where('saldo_inicial', 'like', '%'.$this->search.'%')
                  ->orWhere('saldo_final', 'like', '%'.$this->search.'%')
                  ->orWhere('fecha', 'like', '%'.$this->search.'%')
                  ->orWhereHas('caja', function($q) {
                      $q->where('nombre', 'like', '%'.$this->search.'%');
                  })
                  ->orWhereHas('usuario', function($q) {
                      $q->where('name', 'like', '%'.$this->search.'%');
                  });
            });

        // Si no es admin, filtrar solo sus cortes
        if (!$user->hasRole('admin')) {
            $query->where('usuario_id', $user->id);
        }

        $cortes = $query->orderBy('fecha', 'desc')
                        ->paginate(10);

        return view('livewire.cortes.index', [
            'cortes' => $cortes,
            'isAdmin' => $user->hasRole('admin'),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->fecha = now()->format('Y-m-d');

        // Si no es admin, asignar automáticamente el usuario logueado
        if (!auth()->user()->hasRole('admin')) {
            $this->usuario_id = auth()->id();
        }

        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetValidation();
    }

    public function resetInputFields()
    {
        $this->corte_id = '';
        $this->saldo_inicial = '';
        $this->saldo_final = '';
        $this->caja_id = '';
        $this->usuario_id = '';
        $this->fecha = '';
    }

    public function store()
    {
        $this->validate();

        Corte::updateOrCreate(['id' => $this->corte_id], [
            'saldo_inicial' => $this->saldo_inicial,
            'saldo_final' => $this->saldo_final,
            'caja_id' => $this->caja_id,
            'usuario_id' => $this->usuario_id,
            'fecha' => $this->fecha,
        ]);

        session()->flash('message',
            $this->corte_id ? 'Corte actualizado correctamente.' : 'Corte creado correctamente.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $corte = Corte::findOrFail($id);
        $this->corte_id = $id;
        $this->saldo_inicial = $corte->saldo_inicial;
        $this->saldo_final = $corte->saldo_final;
        $this->caja_id = $corte->caja_id;
        $this->usuario_id = $corte->usuario_id;
        $this->fecha = $corte->fecha;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingCorteDeletion = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingCorteDeletion = false;
    }

    public function delete()
    {
        $corte = Corte::find($this->confirmingCorteDeletion);
        $corte->delete();

        session()->flash('message', 'Corte eliminado correctamente.');
        $this->confirmingCorteDeletion = false;
    }
}
