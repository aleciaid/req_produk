@forelse($products as $product)
<tr class="hover:bg-gray-50">
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="w-2 h-2 rounded-full bg-gray-400 mr-2"></div>
            <div class="text-sm font-medium text-gray-900">{{ $product->nama_produk }}</div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="space-y-1">
        @foreach($product->variants as $variant)
            <div class="flex items-center">
                <div class="w-2 h-2 rounded-full bg-gray-300 mr-2"></div>
                <span class="text-sm text-gray-600">{{ $variant->sku }}</span>
            </div>
        @endforeach
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="space-y-1">
        @foreach($product->variants as $variant)
            <div class="flex items-center">
                <div class="w-2 h-2 rounded-full" style="background-color: {{ $variant->warna }}"></div>
                <span class="ml-2 text-sm">{{ $variant->warna }}</span>
            </div>
        @endforeach
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="space-y-1">
        @foreach($product->variants as $variant)
            <div class="flex items-center">
                <div class="w-2 h-2 rounded-full bg-green-400 mr-2"></div>
                <span class="text-sm font-medium">Rp {{ number_format($variant->harga, 0, ',', '.') }}</span>
            </div>
        @endforeach
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2 py-1 text-xs font-medium rounded-full
            {{ $product->status === 'Request' ? 'bg-yellow-100 text-yellow-700 border border-yellow-300' : 
               ($product->status === 'proses' ? 'bg-blue-100 text-blue-700 border border-blue-300' : 
                'bg-green-100 text-green-700 border border-green-300') }}">
            {{ $product->status }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ $product->created_at->format('d M Y') }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <button class="text-blue-600 hover:text-blue-900 edit-product" data-id="{{ $product->id }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
        </button>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
        No product requests found
    </td>
</tr>
@endforelse