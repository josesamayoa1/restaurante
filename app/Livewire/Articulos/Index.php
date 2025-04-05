<?php

namespace App\Livewire\Articulos;

use App\Models\Articulo;
use App\Models\Orden;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $confirmingArticuloDeletion = false;
    public $articulo_id;
    public $orden_id;
    public $producto_id;
    public $cantidad = 1;
    public $estado;
    public $ordenesDisponibles;
    public $productos;
    public $estadosPermitidos = [];

    protected $rules = [
        'orden_id' => 'required|exists:ordens,id',
        'producto_id' => 'required|exists:productos,id',
        'cantidad' => 'required|integer|min:1',
        'estado' => 'required|in:Pendiente,En preparación,Listo,Entregado,Cancelado',
    ];

    public function mount()
    {
        $this->productos = Producto::all();
        $this->loadOrdenesDisponibles();
        $this->setEstadosPermitidos();
    }

    public function loadOrdenesDisponibles()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $this->ordenesDisponibles = Orden::whereIn('estado', ['En uso', 'Reservada'])->get();
        } elseif ($user->hasRole('mesero')) {
            $this->ordenesDisponibles = Orden::where('usuario_id', $user->id)
                ->whereIn('estado', ['En uso', 'Reservada'])
                ->get();
        } else {
            // Para cocineros o otros roles
            $this->ordenesDisponibles = Orden::whereIn('estado', ['En uso', 'Reservada'])->get();
        }
    }

    public function setEstadosPermitidos()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $this->estadosPermitidos = [
                'Pendiente',
                'En preparación',
                'Listo',
                'Entregado',
                'Cancelado'
            ];
        } elseif ($user->hasRole('cocinero')) {
            $this->estadosPermitidos = [
                'En preparación',
                'Listo'
            ];
        } elseif ($user->hasRole('mesero')) {
            $this->estadosPermitidos = [
                'Listo',
                'Pendiente',
                'Cancelado',
                'Entregado'
            ];
        }
    }

    public function render()
    {
        $user = Auth::user();
        $query = Articulo::with(['orden.mesa', 'producto'])
            ->where(function($q) {
                $q->where('cantidad', 'like', '%'.$this->search.'%')
                  ->orWhere('estado', 'like', '%'.$this->search.'%')
                  ->orWhereHas('orden.mesa', function($q) {
                      $q->where('nombre', 'like', '%'.$this->search.'%');
                  })
                  ->orWhereHas('producto', function($q) {
                      $q->where('nombre', 'like', '%'.$this->search.'%');
                  });
            });

        // Filtros por rol
        if ($user->hasRole('mesero')) {
            $query->whereHas('orden', function($q) use ($user) {
                $q->where('usuario_id', $user->id);
            });
        }

        $articulos = $query->orderBy('created_at', 'desc')
                          ->paginate(10);

        return view('livewire.articulos.index', [
            'articulos' => $articulos,
            'isAdmin' => $user->hasRole('admin'),
            'isMesero' => $user->hasRole('mesero'),
            'isCocinero' => $user->hasRole('cocinero'),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->estado = 'Pendiente';
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
        $this->articulo_id = '';
        $this->orden_id = '';
        $this->producto_id = '';
        $this->cantidad = 1;
        $this->estado = '';
    }

    public function store()
    {
        $this->validate();

        Articulo::updateOrCreate(['id' => $this->articulo_id], [
            'orden_id' => $this->orden_id,
            'producto_id' => $this->producto_id,
            'cantidad' => $this->cantidad,
            'estado' => $this->estado,
        ]);

        session()->flash('message',
            $this->articulo_id ? 'Artículo actualizado correctamente.' : 'Artículo creado correctamente.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $articulo = Articulo::findOrFail($id);
        $this->articulo_id = $id;
        $this->orden_id = $articulo->orden_id;
        $this->producto_id = $articulo->producto_id;
        $this->cantidad = $articulo->cantidad;
        $this->estado = $articulo->estado;

        $this->loadOrdenesDisponibles();
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingArticuloDeletion = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingArticuloDeletion = false;
    }

    public function delete()
    {
        $articulo = Articulo::find($this->confirmingArticuloDeletion);
        $articulo->delete();

        session()->flash('message', 'Artículo eliminado correctamente.');
        $this->confirmingArticuloDeletion = false;
    }

    public function cambiarEstado($id, $estado)
    {
        $articulo = Articulo::findOrFail($id);

        // Validar que el estado sea permitido para el rol
        if (in_array($estado, $this->estadosPermitidos)) {
            $articulo->estado = $estado;
            $articulo->save();
            session()->flash('message', 'Estado actualizado correctamente.');
        } else {
            session()->flash('error', 'No tienes permiso para realizar esta acción.');
        }
    }
}
