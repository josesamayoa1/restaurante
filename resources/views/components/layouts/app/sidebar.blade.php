<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    @if(auth()->user()->hasRole('admin'))
                        <flux:navlist.item icon="shield-check" :href="route('roles.index')" :current="request()->routeIs('roles.index')" wire:navigate>{{ __('Role') }}</flux:navlist.item>
                        <flux:navlist.item icon="circle-stack" :href="route('cajas.index')" :current="request()->routeIs('cajas.index')" wire:navigate>{{ __('Cajas') }}</flux:navlist.item>
                        <flux:navlist.item icon="shopping-cart" :href="route('productos.index')" :current="request()->routeIs('productos.index')" wire:navigate>{{ __('Productos') }}</flux:navlist.item>
                        <flux:navlist.item icon="rectangle-stack" :href="route('mesas.index')" :current="request()->routeIs('mesas.index')" wire:navigate>{{ __('Mesas') }}</flux:navlist.item>

                    @endif
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                @if(auth()->user()->hasRole('mesero') ||auth()->user()->hasRole('admin'))
                    <flux:navlist.item icon="cube" :href="route('ordenes.index')" :current="request()->routeIs('ordenes.index')" wire:navigate>{{ __('Mesas') }}</flux:navlist.item>
                @endif
                @if(auth()->user()->hasRole('cocinero')||auth()->user()->hasRole('admin') || auth()->user()->hasRole('mesero') )
                    <flux:navlist.item icon="fire" :href="route('articulos.index')" :current="request()->routeIs('articulos.index')" wire:navigate>{{ __('Artículos') }}</flux:navlist.item>
                @endif
                @if(auth()->user()->hasRole('cajero') ||auth()->user()->hasRole('admin'))
                    <flux:navlist.item icon="clipboard-document" :href="route('cortes.index')" :current="request()->routeIs('cortes.index')" wire:navigate>{{ __('Cortes') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('facturas.index')" :current="request()->routeIs('facturas.index')" wire:navigate>{{ __('Facturas') }}</flux:navlist.item>
                    <flux:navlist.item icon="credit-card" :href="route('pagos.index')" :current="request()->routeIs('pagos.index')" wire:navigate>{{ __('Pagos') }}</flux:navlist.item>
                @endif
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->nombre"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->nombre }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->nombre }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
