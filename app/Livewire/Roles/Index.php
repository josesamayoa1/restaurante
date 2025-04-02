<?php
namespace App\Livewire\Roles;

use Livewire\Component;
use App\Models\Role;

class Index extends Component
{
    public $roles;
    public $search = '';

    protected $listeners = ['refreshRoles' => '$refresh'];

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function render()
    {
        $query = Role::query();

        if ($this->search) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        $roles = $query->get();

        return view('livewire.roles.index', compact('roles'));
    }

    public function delete($id)
    {
        Role::find($id)->delete();
        $this->emit('refreshRoles');
        $this->dispatchBrowserEvent('notify', ['message' => 'Rol eliminado correctamente']);
    }
}
