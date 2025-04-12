<?php

namespace App\Livewire\Pagos;

use App\Models\Factura;
use App\Models\Pago;
use App\Models\TipoPago;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $isOpen = false;

    public $confirmingPagoDeletion = false;

    public $pago_id;

    public $factura_id;

    public $tipo_pago_id;

    public $monto;

    public $cambio = 0;

    public $facturasPendientes;

    public $tiposPago;

    protected $rules = [
        'factura_id' => 'required|exists:facturas,id',
        'tipo_pago_id' => 'required|exists:tipo_pagos,id',
        'monto' => 'required|numeric|min:0',
        'cambio' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->cargarFacturasPendientes();
        $this->tiposPago = TipoPago::all();
    }

    public function cargarFacturasPendientes()
    {
        $this->facturasPendientes = Factura::where('estado', 'Pendiente')->get();
    }

    public function updatedFacturaId($value)
    {
        if ($value) {
            $factura = Factura::find($value);
            $this->monto = $factura->total;
            $this->calcularCambio();
        } else {
            $this->reset(['monto', 'cambio']);
        }
    }

    public function updatedMonto($value)
    {
        $this->calcularCambio();
    }

    public function calcularCambio()
    {
        if ($this->factura_id && is_numeric($this->monto)) {
            $factura = Factura::find($this->factura_id);
            $this->cambio = max(0, $this->monto - $factura->total);
        } else {
            $this->cambio = 0;
        }
    }

    public function render()
    {
        $pagos = Pago::with(['factura.orden.mesa', 'tipoPago'])
            ->where(function ($q) {
                $q->where('monto', 'like', '%'.$this->search.'%')
                    ->orWhereHas('factura', function ($q) {
                        $q->where('nit', 'like', '%'.$this->search.'%')
                            ->orWhereHas('orden.mesa', function ($q) {
                                $q->where('nombre', 'like', '%'.$this->search.'%');
                            });
                    })
                    ->orWhereHas('tipoPago', function ($q) {
                        $q->where('nombre', 'like', '%'.$this->search.'%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.pagos.index', [
            'pagos' => $pagos,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
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
        $this->pago_id = '';
        $this->factura_id = '';
        $this->tipo_pago_id = '';
        $this->monto = '';
        $this->cambio = 0;
    }

    public function store()
    {
        $this->validate();

        $pago = Pago::updateOrCreate(['id' => $this->pago_id], [
            'factura_id' => $this->factura_id,
            'tipo_pago_id' => $this->tipo_pago_id,
            'monto' => $this->monto,
            'cambio' => $this->cambio,
        ]);

        // Actualizar estado de la factura si el monto cubre el total
        $factura = Factura::find($this->factura_id);
        if ($this->monto >= $factura->total) {
            $factura->estado = 'Pagada';
            $factura->save();
        }

        session()->flash('message',
            $this->pago_id ? 'Pago actualizado correctamente.' : 'Pago registrado correctamente.');

        $this->closeModal();
        $this->resetInputFields();
        $this->cargarFacturasPendientes(); // Actualizar la lista de facturas
    }

    public function edit($id)
    {
        $pago = Pago::findOrFail($id);
        $this->pago_id = $id;
        $this->factura_id = $pago->factura_id;
        $this->tipo_pago_id = $pago->tipo_pago_id;
        $this->monto = $pago->monto;
        $this->cambio = $pago->cambio;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingPagoDeletion = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingPagoDeletion = false;
    }

    public function delete()
    {
        $pago = Pago::find($this->confirmingPagoDeletion);

        // Revertir estado de la factura si es necesario
        $factura = $pago->factura;
        if ($factura->estado == 'Pagada') {
            $factura->estado = 'Pendiente';
            $factura->save();
        }

        $pago->delete();

        session()->flash('message', 'Pago eliminado correctamente.');
        $this->confirmingPagoDeletion = false;
        $this->facturasPendientes = Factura::where('estado', 'Pendiente')->get();
    }
}
