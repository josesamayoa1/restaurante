<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $confirmingProductoDeletion = false;
    public $producto_id;
    public $nombre;
    public $descripcion;
    public $precio;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'precio' => 'required|numeric|min:0',
    ];

    public function render()
    {
        $productos = Producto::where('nombre', 'like', '%'.$this->search.'%')
                     ->orWhere('descripcion', 'like', '%'.$this->search.'%')
                     ->orderBy('id', 'desc')
                     ->paginate(10);

        return view('livewire.productos.index', [
            'productos' => $productos,
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
        $this->producto_id = '';
        $this->nombre = '';
        $this->descripcion = '';
        $this->precio = '';
    }

    public function store()
    {
        $this->validate();

        Producto::updateOrCreate(['id' => $this->producto_id], [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
        ]);

        session()->flash('message',
            $this->producto_id ? 'Producto actualizado correctamente.' : 'Producto creado correctamente.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $this->producto_id = $id;
        $this->nombre = $producto->nombre;
        $this->descripcion = $producto->descripcion;
        $this->precio = $producto->precio;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingProductoDeletion = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingProductoDeletion = false;
    }

    public function delete()
    {
        $producto = Producto::find($this->confirmingProductoDeletion);

        if ($producto->articulos()->count() > 0) {
            session()->flash('error', 'No se puede eliminar el producto porque tiene artículos asociados.');
            $this->confirmingProductoDeletion = false;
            return;
        }

        $producto->delete();




        session()->flash('message', 'Producto eliminado correctamente.');
        $this->confirmingProductoDeletion = false;
    }
}
