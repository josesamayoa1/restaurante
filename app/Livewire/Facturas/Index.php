<?php

namespace App\Livewire\Facturas;

use App\Models\Factura;
use App\Models\Orden;
use App\Models\Articulo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isPreviewOpen = false;
    public $confirmingFacturaDeletion = false;
    public $factura_id;
    public $nit;
    public $orden_id;
    public $total = 0;
    public $iva = 0;
    public $fecha;
    public $estado;
    public $ordenesDisponibles;
    public $articulosFactura = [];
    public $previewHtml = '';

    protected $rules = [
        'nit' => 'required|string|min:3|max:20',
        'orden_id' => 'required|exists:ordens,id',
        'total' => 'required|numeric|min:0',
        'iva' => 'required|numeric|min:0',
        'fecha' => 'required|date',
        'estado' => 'required|in:Pendiente,Pagada,Anulada',
    ];

    public function mount()
    {
        $this->loadOrdenesDisponibles();
    }

    public function loadOrdenesDisponibles()
    {
        $user = Auth::user();

        $this->ordenesDisponibles = Orden::where('estado', 'En uso')
            ->whereDoesntHave('factura')
            ->when(!$user->hasRole('admin') && !$user->hasRole('cajero'), function($query) use ($user) {
                $query->where('usuario_id', $user->id);
            })
            ->get();
    }

    public function calcularTotales()
    {
        if ($this->orden_id) {
            $this->articulosFactura = Articulo::where('orden_id', $this->orden_id)
                ->where('estado', '!=', 'Cancelado')
                ->with('producto')
                ->get();

            $subtotal = $this->articulosFactura->sum(function($articulo) {
                return $articulo->cantidad * $articulo->producto->precio;
            });

            $this->iva = $subtotal * 0.12;
            $this->total = $subtotal + $this->iva;
        }
    }

    public function generarPreview($factura_id=null)
    {

        if ($factura_id) {
            $factura = Factura::find($factura_id);

            if (!$factura) {
            session()->flash('message', 'No se encontró la factura.');
            return;
            }

            $this->nit = $factura->nit;
            $this->fecha = $factura->fecha;
            $this->total = $factura->total;
            $this->iva = $factura->iva;
        }

        $id_orden = $factura_id ? $factura->orden_id : $this->orden_id;

        if (!$id_orden) {
            session()->flash('message', 'No se encontró la orden.');
            return;
        }

        $orden = Orden::with(['mesa', 'articulos.producto'])->find($id_orden);

        $this->previewHtml = view('facturas.preview', [
            'orden' => $orden,
            'nit' => $this->nit,
            'fecha' => $this->fecha,
            'subtotal' => $this->total - $this->iva,
            'iva' => $this->iva,
            'total' => $this->total
        ])->render();

        $this->isPreviewOpen = true;
    }

    public function render()
    {
        $user = Auth::user();
        $query = Factura::with(['orden.mesa', 'orden.mesero'])
            ->where(function($q) {
                $q->where('nit', 'like', '%'.$this->search.'%')
                  ->orWhere('total', 'like', '%'.$this->search.'%')
                  ->orWhere('estado', 'like', '%'.$this->search.'%')
                  ->orWhereHas('orden.mesa', function($q) {
                      $q->where('nombre', 'like', '%'.$this->search.'%');
                  });
            });

        // Filtros por rol
        if (!$user->hasRole('admin') && !$user->hasRole('cajero')) {
            $query->whereHas('orden', function($q) use ($user) {
                $q->where('usuario_id', $user->id);
            });
        }

        $facturas = $query->orderBy('fecha', 'desc')
                         ->paginate(10);

        return view('livewire.facturas.index', [
            'facturas' => $facturas,
            'isAdmin' => $user->hasRole('admin'),
            'isCajero' => $user->hasRole('cajero'),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->fecha = now()->format('Y-m-d');
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

    public function openPreviewModal()
    {
        $this->isPreviewOpen = true;
    }

    public function closePreviewModal()
    {
        $this->isPreviewOpen = false;
    }

    public function resetInputFields()
    {
        $this->factura_id = '';
        $this->nit = '';
        $this->orden_id = '';
        $this->total = 0;
        $this->iva = 0;
        $this->fecha = '';
        $this->estado = '';
        $this->articulosFactura = [];
    }

    public function updatedOrdenId()
    {

        $this->calcularTotales();
    }

    public function store()
    {
        $this->validate();

        Factura::updateOrCreate(['id' => $this->factura_id], [
            'nit' => $this->nit,
            'orden_id' => $this->orden_id,
            'total' => $this->total,
            'iva' => $this->iva,
            'fecha' => $this->fecha,
            'estado' => $this->estado,
        ]);

        // Cambiar estado de la orden a "Finalizada"
        Orden::where('id', $this->orden_id)->update(['estado' => 'Finalizada']);

        session()->flash('message',
            $this->factura_id ? 'Factura actualizada correctamente.' : 'Factura creada correctamente.');

        $this->closeModal();
        $this->resetInputFields();
        $this->loadOrdenesDisponibles();
    }

    public function edit($id)
    {
        $factura = Factura::findOrFail($id);
        $this->factura_id = $id;
        $this->nit = $factura->nit;
        $this->orden_id = $factura->orden_id;
        $this->total = $factura->total;
        $this->iva = $factura->iva;
        $this->fecha = $factura->fecha;
        $this->estado = $factura->estado;

        $this->calcularTotales();
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingFacturaDeletion = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingFacturaDeletion = false;
    }

    public function delete()
    {
        $factura = Factura::find($this->confirmingFacturaDeletion);

        // Cambiar estado de la orden a "En uso" nuevamente
        Orden::where('id', $factura->orden_id)->update(['estado' => 'En uso']);

        $factura->delete();

        session()->flash('message', 'Factura eliminada correctamente.');
        $this->confirmingFacturaDeletion = false;
        $this->loadOrdenesDisponibles();
    }
}
