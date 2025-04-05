<div class="font-sans">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold">RESTAURANTE </h1>
        <p class="text-sm">Dirección: Av. Principal #123</p>
        <p class="text-sm">Teléfono: 1234-5678</p>
        <p class="text-sm">NIT: 123456789</p>
    </div>

    <div class="border-t-2 border-b-2 border-black py-2 my-4">
        <h2 class="text-xl font-semibold text-center">FACTURA</h2>
        <div class="flex justify-between text-sm mt-2">
            <div>
                <p><strong>No.</strong> {{ $factura->id ?? 'NUEVA' }}</p>
                <p><strong>Fecha:</strong> {{ $fecha }}</p>
            </div>
            <div>
                <p><strong>NIT:</strong> {{ $nit }}</p>
                <p><strong>Mesa:</strong> {{ $orden->mesa->nombre }}</p>
            </div>
        </div>
    </div>

    <table class="w-full mb-4">
        <thead>
            <tr class="border-b border-gray-300">
                <th class="text-left py-2">Cantidad</th>
                <th class="text-left py-2">Descripción</th>
                <th class="text-right py-2">Precio Unit.</th>
                <th class="text-right py-2">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orden->articulos as $articulo)
                @if($articulo->estado != 'Cancelado')
                <tr class="border-b border-gray-200">
                    <td class="py-2">{{ $articulo->cantidad }}</td>
                    <td class="py-2">{{ $articulo->producto->nombre }}</td>
                    <td class="py-2 text-right">Q.{{ number_format($articulo->producto->precio, 2) }}</td>
                    <td class="py-2 text-right">Q. {{ number_format($articulo->producto->precio * $articulo->cantidad, 2) }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="border-t-2 border-black pt-4">
        <div class="flex justify-end">
            <div class="w-64">
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Subtotal:</span>
                    <span>Q. {{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">IVA (12%):</span>
                    <span>Q. {{ number_format($iva, 2) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold">
                    <span>TOTAL:</span>
                    <span>Q. {{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt 6 text-center">
        <p class="text-sm">Gracias por su preferencia</p>
        <p class="text-sm">¡Vuelva pronto!</p>
    </div>
</div>
</div>
