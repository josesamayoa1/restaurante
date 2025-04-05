<div>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold text-gray-900">Mesas </h1>
                <p class="mt-2 text-sm text-gray-700">Administración de mesas.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <button wire:click="create()" type="button" class="block rounded-md bg-black px-3 py-2 text-center text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Nueva Asignación de Mesa
                </button>
            </div>
        </div>

        <!-- Buscador -->
        <div class="mt-4">
            <input wire:model.debounce.300ms="search" type="text" placeholder="Buscar órdenes..."
                   class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
        </div>

        <!-- Mensajes -->
        @if (session()->has('message'))
            <div class="mt-4 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-4 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabla -->
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-0">ID</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Mesa</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Usuario</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Caja</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Fecha</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Estado</th>
                                <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-0">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($ordenes as $orden)
                                <tr>
                                    <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-0">{{ $orden->id }}</td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ $orden->mesa->nombre }}</td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ $orden->mesero->nombre ?? '' }}</td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ $orden->caja->nombre }}</td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y H:i') }}</td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap">
                                        <span @class([
                                            'px-2 py-1 rounded-full text-xs',
                                            'bg-green-100 text-green-800' => $orden->estado === 'Disponible',
                                            'bg-yellow-100 text-yellow-800' => $orden->estado === 'En uso',
                                            'bg-blue-100 text-blue-800' => $orden->estado === 'Reservada',
                                            'bg-red-100 text-red-800' => $orden->estado === 'Finalizada',
                                        ])>
                                            {{ $orden->estado }}
                                        </span>
                                    </td>
                                    <td class="relative py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-0">
                                        <button wire:click="edit({{ $orden->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Editar<span class="sr-only">, Orden {{ $orden->id }}</span>
                                        </button>
                                        <button wire:click="confirmDelete({{ $orden->id }})" class="text-red-600 hover:text-red-900">
                                            Eliminar<span class="sr-only">, Orden {{ $orden->id }}</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0 text-center">
                                        No se encontraron Asignaciones de Mesas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $ordenes->links() }}
        </div>
    </div>

    <!-- Modal para crear/editar -->
    @if ($isOpen)
        <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                        <div>
                            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-blue-100">
                                <svg class="size-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="text-base font-semibold text-gray-900" id="modal-title">
                                    {{ $orden_id ? 'Editar Asignacion' : 'Nueva Asignacion' }}
                                </h3>
                                <div class="mt-2 space-y-4">
                                    <div>
                                        <label for="fecha" class="block text-sm/6 font-medium text-gray-900">Fecha y Hora</label>
                                        <div class="mt-2">
                                            <input wire:model="fecha" id="fecha" type="datetime-local" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        </div>
                                        @error('fecha') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="mesa_id" class="block text-sm/6 font-medium text-gray-900">Mesa</label>
                                        <div class="mt-2">
                                            <select wire:model="mesa_id" id="mesa_id" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                <option value="">Seleccione una mesa</option>
                                                @foreach($mesasDisponibles as $mesa)
                                                    <option value="{{ $mesa->id }}">{{ $mesa->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('mesa_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="usuario_id" class="block text-sm/6 font-medium text-gray-900">
                                            {{ $isAdmin ? 'Asignar a Usuario' : 'Asignarme' }}
                                        </label>
                                        <div class="mt-2">
                                            @if($isAdmin)
                                                <select wire:model="usuario_id" id="usuario_id" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                    <option value="">Seleccione un usuario</option>
                                                    @foreach($usuarios as $usuario)
                                                        <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" value="{{ auth()->user()->name }}" class="block w-full rounded-md bg-gray-100 px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 sm:text-sm/6" readonly>
                                            @endif
                                        </div>
                                        @error('usuario_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="caja_id" class="block text-sm/6 font-medium text-gray-900">Caja</label>
                                        <div class="mt-2">
                                            <select wire:model="caja_id" id="caja_id" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                <option value="">Seleccione una caja</option>
                                                @foreach($cajas as $caja)
                                                    <option value="{{ $caja->id }}">{{ $caja->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('caja_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="estado" class="block text-sm/6 font-medium text-gray-900">Estado</label>
                                        <div class="mt-2">
                                            <select wire:model="estado" id="estado" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                                <option value="Disponible">Disponible</option>
                                                <option value="En uso">En uso</option>
                                                <option value="Reservada">Reservada</option>
                                                <option value="Finalizada">Finalizada</option>
                                            </select>
                                        </div>
                                        @error('estado') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                            <button wire:click="store" type="button" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2">
                                {{ $orden_id ? 'Actualizar' : 'Crear' }}
                            </button>
                            <button wire:click="closeModal" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de confirmación para eliminar -->
    @if ($confirmingOrdenDeletion)
        <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500/10 transition-opacity" aria-hidden="true"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6">
                        <div>
                            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-red-100">
                                <svg class="size-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="text-base font-semibold text-gray-900" id="modal-title">Confirmar Eliminación</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">¿Estás seguro de que deseas eliminar esta orden? Esta acción no se puede deshacer.</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                            <button wire:click="delete" type="button" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 sm:col-start-2">
                                Eliminar
                            </button>
                            <button wire:click="cancelDelete" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
