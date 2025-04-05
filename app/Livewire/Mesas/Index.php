<?php

namespace App\Livewire\Mesas;

use App\Models\Mesa;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $confirmingMesaDeletion = false;
    public $mesa_id;
    public $nombre;

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:mesas,nombre',
    ];

    public function render()
    {
        $mesas = Mesa::where('nombre', 'like', '%'.$this->search.'%')
                     ->orderBy('id', 'desc')
                     ->paginate(10);

        return view('livewire.mesas.index', [
            'mesas' => $mesas,
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
        $this->mesa_id = '';
        $this->nombre = '';
    }

    public function store()
    {
        $this->validate();

        Mesa::updateOrCreate(['id' => $this->mesa_id], [
            'nombre' => $this->nombre,
        ]);

        session()->flash('message',
            $this->mesa_id ? 'Mesa actualizada correctamente.' : 'Mesa creada correctamente.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $mesa = Mesa::findOrFail($id);
        $this->mesa_id = $id;
        $this->nombre = $mesa->nombre;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingMesaDeletion = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingMesaDeletion = false;
        $this->reset();
    }

    public function delete()
    {
        $mesa = Mesa::find($this->confirmingMesaDeletion);
        if ($mesa->ordenes()->count() > 0) {
            session()->flash('error', 'No se puede eliminar la mesa porque tiene órdenes asociadas.');
            $this->confirmingMesaDeletion = false;
            return;
        }
        $mesa->delete();

        session()->flash('message', 'Mesa eliminada correctamente.');
        $this->confirmingMesaDeletion = false;
    }
}
