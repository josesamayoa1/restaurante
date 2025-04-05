<?php

namespace App\Livewire\Ordenes;

use App\Models\Caja;
use App\Models\Mesa;
use App\Models\Orden;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $confirmingOrdenDeletion = false;
    public $orden_id;
    public $fecha;
    public $usuario_id;
    public $mesa_id;
    public $caja_id;
    public $estado;
    public $mesasDisponibles;
    public $cajas;
    public $usuarios;

    protected $rules = [
        'fecha' => 'required|date',
        'usuario_id' => 'required|exists:users,id',
        'mesa_id' => 'required|exists:mesas,id',
        'caja_id' => 'required|exists:cajas,id',
        'estado' => 'required|in:Disponible,En uso,Reservada,Finalizada',
    ];

    public function mount()
    {
        $this->cajas = Caja::all();
        $this->usuarios = User::all();
        $this->loadMesasDisponibles();
    }

    public function loadMesasDisponibles()
    {
        // Obtener mesas que no tienen órdenes en estado "En uso" o "Reservada"
        $this->mesasDisponibles = Mesa::whereDoesntHave('ordenes', function($query) {
            $query->whereIn('estado', ['En uso', 'Reservada']);
        })->get();
    }

    public function render()
    {
        $user = Auth::user();
        $query = Orden::with(['mesa', 'mesero', 'caja'])
            ->where(function($q) {
                $q->where('fecha', 'like', '%'.$this->search.'%')
                  ->orWhere('estado', 'like', '%'.$this->search.'%')
                  ->orWhereHas('mesa', function($q) {
                      $q->where('nombre', 'like', '%'.$this->search.'%');
                  })
                  ->orWhereHas('mesero', function($q) {
                      $q->where('name', 'like', '%'.$this->search.'%');
                  })
                  ->orWhereHas('caja', function($q) {
                      $q->where('nombre', 'like', '%'.$this->search.'%');
                  });
            });

        // Si no es admin, filtrar solo sus órdenes
        if (!$user->hasRole('admin')) {
            $query->where('usuario_id', $user->id);
        }

        $ordenes = $query->orderBy('fecha', 'desc')
                         ->paginate(10);

        return view('livewire.ordenes.index', [
            'ordenes' => $ordenes,
            'isAdmin' => $user->hasRole('admin'),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->fecha = now()->format('Y-m-d\TH:i');
        $this->estado = 'En uso';

        // Si no es admin, asignar automáticamente el usuario logueado
        if (!auth()->user()->hasRole('admin')) {
            $this->usuario_id = auth()->id();
        }

        $this->loadMesasDisponibles();
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
        $this->orden_id = '';
        $this->fecha = '';
        $this->usuario_id = '';
        $this->mesa_id = '';
        $this->caja_id = '';
        $this->estado = '';
    }

    public function store()
    {
        $this->validate();

        Orden::updateOrCreate(['id' => $this->orden_id], [
            'fecha' => $this->fecha,
            'usuario_id' => $this->usuario_id,
            'mesa_id' => $this->mesa_id,
            'caja_id' => $this->caja_id,
            'estado' => $this->estado,
        ]);

        session()->flash('message',
            $this->orden_id ? 'Orden actualizada correctamente.' : 'Orden creada correctamente.');

        $this->closeModal();
        $this->resetInputFields();
        $this->loadMesasDisponibles(); // Actualizar lista de mesas disponibles
    }

    public function edit($id)
    {
        $orden = Orden::findOrFail($id);
        $this->orden_id = $id;
        $this->fecha = $orden->fecha;
        $this->usuario_id = $orden->usuario_id;
        $this->mesa_id = $orden->mesa_id;
        $this->caja_id = $orden->caja_id;
        $this->estado = $orden->estado;

        $this->loadMesasDisponibles();
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingOrdenDeletion = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingOrdenDeletion = false;
    }

    public function delete()
    {
        $orden = Orden::find($this->confirmingOrdenDeletion);
        $orden->delete();

        session()->flash('message', 'Orden eliminada correctamente.');
        $this->confirmingOrdenDeletion = false;
        $this->loadMesasDisponibles(); // Actualizar lista de mesas disponibles
    }

    public function asignarme($mesaId)
    {
        $this->mesa_id = $mesaId;
        $this->usuario_id = auth()->id();
        $this->fecha = now()->format('Y-m-d\TH:i');
        $this->estado = 'En uso';
        $this->caja_id = Caja::first()->id; // O la lógica para asignar caja

        $this->store();
    }
}
