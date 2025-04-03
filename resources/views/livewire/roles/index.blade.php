<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Lista de Roles</h2>
        @if(auth()->user()->isAdmin())
            <button wire:click="$emit('openModal', 'roles.create')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Crear Rol
            </button>
        @endif
    </div>

    <div class="mb-4">
        <input type="text" wire:model="search" placeholder="Buscar roles..." class="w-full p-2 border rounded">
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($roles as $role)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $role->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $role->nombre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if(auth()->user()->isAdmin())
                            <button wire:click="$emit('openModal', 'roles.edit', {{ json_encode(['role' => $role->id]) }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                            <button wire:click="delete({{ $role->id }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
