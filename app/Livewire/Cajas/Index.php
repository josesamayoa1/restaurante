<?php

namespace App\Livewire\Cajas;

use App\Models\Caja;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $confirmingCajaDeletion = false;
    public $caja_id;
    public $nombre;

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:cajas,nombre',
    ];

    public function render()
    {
        $cajas = Caja::where('nombre', 'like', '%'.$this->search.'%')
                     ->orderBy('id', 'desc')
                     ->paginate(10);

        return view('livewire.cajas.index', [
            'cajas' => $cajas,
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
        $this->caja_id = '';
        $this->nombre = '';
    }

    public function store()
    {
        $this->validate();

        Caja::updateOrCreate(['id' => $this->caja_id], [
            'nombre' => $this->nombre,
        ]);

        session()->flash('message',
            $this->caja_id ? 'Caja actualizada correctamente.' : 'Caja creada correctamente.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $caja = Caja::findOrFail($id);
        $this->caja_id = $id;
        $this->nombre = $caja->nombre;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingCajaDeletion = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingCajaDeletion = false;
    }

    public function delete()
    {
        $caja = Caja::find($this->confirmingCajaDeletion);


        if ($caja->cortes()->count() > 0) {
            session()->flash('error', 'No se puede eliminar la caja porque tiene artículos asociados.');
            return;
        }
        $caja->delete();

        session()->flash('message', 'Caja eliminada correctamente.');
        $this->confirmingCajaDeletion = false;
    }
}
