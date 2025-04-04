<?php
namespace App\Livewire\Roles;

use Livewire\Component;
use App\Models\Role;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $nombre, $role_id;
    public $isOpen = false;
    public $search = '';
    public $confirmingRoleDeletion = false;
    public $roleToDelete = null;

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:roles,name',
    ];


    public function mount()
    {
        $this->roles = Role::get();
    }

    public function render()
    {
        $roles = Role::where('name', 'like', '%'.$this->search.'%')
        ->orderBy('id', 'desc')
        ->paginate(10);


        return view('livewire.roles.index', compact('roles'));
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
        $this->resetErrorBag();
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->nombre = '';
        $this->role_id = '';
    }

    public function store()
    {
        $this->validate();

        Role::updateOrCreate(['id' => $this->role_id], [
            'name' => $this->nombre,
            'guard_name' => 'web',
        ]);

        session()->flash('message',
            $this->role_id ? 'Rol actualizado correctamente.' : 'Rol creado correctamente.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->role_id = $id;
        $this->nombre = $role->nombre;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingRoleDeletion = true;
        $this->roleToDelete = $id;
    }

    public function delete()
    {
        
        $role = Role::find($this->roleToDelete);

        // Verificar si el rol tiene usuarios asociados
        if($role->users()->count() > 0) {
            session()->flash('error', 'No se puede eliminar el rol porque tiene usuarios asociados.');
            $this->confirmingRoleDeletion = false;
            return;
        }

        $role->delete();
        session()->flash('message', 'Rol eliminado correctamente.');
        $this->confirmingRoleDeletion = false;
    }

}
