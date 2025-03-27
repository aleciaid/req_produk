<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow-y: auto;
            padding: 1rem;
        }
        .modal.show {
            display: block;
        }
        @media (min-width: 768px) {
            .modal-content {
                max-width: 700px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="?status=Request" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Pending Requests</h2>
                        <p class="text-2xl font-semibold text-gray-700">{{ $counts['request'] }}</p>
                    </div>
                </div>
            </a>

            <a href="?status=proses" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">In Progress</h2>
                        <p class="text-2xl font-semibold text-gray-700">{{ $counts['proses'] }}</p>
                    </div>
                </div>
            </a>

            <a href="?status=selesai" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Completed</h2>
                        <p class="text-2xl font-semibold text-gray-700">{{ $counts['selesai'] }}</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                    <h1 class="text-2xl font-semibold text-gray-800">Product Requests</h1>
                    <div class="flex items-center mt-4 md:mt-0">
                        <div class="relative mr-4">
                            <input type="text" id="searchInput" placeholder="Search products..." 
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="button" id="newRequestBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            New Request
                        </button>
                    </div>
                </div>

                <div id="alertContainer"></div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="productsTableBody">
                            @include('products.table-rows')
                        </tbody>
                    </table>
                </div>

                <!-- Skeleton Loading Table -->
                <div id="skeletonLoader" class="hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @for ($i = 0; $i < 5; $i++)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-4 bg-gray-200 rounded animate-pulse w-3/4"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-4 bg-gray-200 rounded animate-pulse w-1/2"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-4 bg-gray-200 rounded animate-pulse w-1/2"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-4 bg-gray-200 rounded animate-pulse w-1/2"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-4 bg-gray-200 rounded animate-pulse w-24"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-4 bg-gray-200 rounded animate-pulse w-24"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="h-4 bg-gray-200 rounded animate-pulse w-8 ml-auto"></div>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <div class="mt-4" id="paginationContainer">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="requestModal" class="modal" data-mode="create">
        <div class="relative mx-auto p-6 border shadow-lg rounded-lg bg-white modal-content">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-2xl font-semibold text-gray-800" id="modalTitle">Request Product</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="productRequestForm" class="mt-4">
                @csrf
                <input type="hidden" id="productId" name="id">
                <div class="mb-4">
                    <label class="block text-gray-700 text-base font-semibold mb-2" for="nama_produk">Product Name</label>
                    <input type="text" id="nama_produk" name="nama_produk" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <div class="text-red-500 text-xs italic" id="nama_produk_error"></div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-base font-semibold mb-2">Product Variants</label>
                </div>

                <div id="variantsContainer">
                    <div class="variant-row mb-4">
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-gray-600 text-sm mb-1">SKU</label>
                                <input type="number" name="variants[0][sku]" placeholder="SKU" required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div class="flex-1">
                                <label class="block text-gray-600 text-sm mb-1">Color</label>
                                <input type="text" name="variants[0][warna]" placeholder="Color" required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div class="flex-1">
                                <label class="block text-gray-600 text-sm mb-1">Price</label>
                                <input type="number" name="variants[0][harga]" placeholder="Price" required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <button type="button" class="remove-variant px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600" disabled>×</button>
                        </div>
                    </div>
                </div>

                <button type="button" id="addVariant" class="mb-4 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Add Variant
                </button>

                <input type="hidden" name="status" value="Request">

                <div class="flex justify-end gap-4">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                    <button type="button" id="submitButton"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let variantCount = 1;
        let searchTimeout = null;
        let modal, form, modalTitle, submitButton;

        // Initialize DOM elements after document is ready
        $(document).ready(function() {
            modal = document.getElementById('requestModal');
            form = document.getElementById('productRequestForm');
            modalTitle = document.getElementById('modalTitle');
            submitButton = document.getElementById('submitButton');
        });

        function openModal(mode = 'create', productData = null) {
            form.reset();
            $('#variantsContainer').empty();
            variantCount = 0;

            if (mode === 'edit' && productData) {
                modalTitle.textContent = 'Edit Product';
                submitButton.textContent = 'Update';
                modal.dataset.mode = 'edit';
                
                // Fill form with product data
                $('#productId').val(productData.id);
                $('#nama_produk').val(productData.nama_produk);
                
                // Add variant rows for each existing variant
                productData.variants.forEach(variant => {
                    addVariantRow(variant);
                });
            } else {
                modalTitle.textContent = 'Request Product';
                submitButton.textContent = 'Submit';
                modal.dataset.mode = 'create';
                addVariantRow(); // Add initial empty variant row
            }
            
            modal.classList.add('show');
        }

        function addVariantRow(data = null) {
            let newRow = `
                <div class="variant-row mb-4">
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-gray-600 text-sm mb-1">SKU</label>
                            <input type="number" name="variants[${variantCount}][sku]" placeholder="SKU" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="${data ? data.sku : ''}">
                        </div>
                        <div class="flex-1">
                            <label class="block text-gray-600 text-sm mb-1">Color</label>
                            <input type="text" name="variants[${variantCount}][warna]" placeholder="Color" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="${data ? data.warna : ''}">
                        </div>
                        <div class="flex-1">
                            <label class="block text-gray-600 text-sm mb-1">Price</label>
                            <input type="number" name="variants[${variantCount}][harga]" placeholder="Price" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="${data ? data.harga : ''}">
                        </div>
                        <button type="button" class="remove-variant px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600"${variantCount === 0 ? ' disabled' : ''}>×</button>
                    </div>
                </div>
            `;
            $('#variantsContainer').append(newRow);
            variantCount++;
        }

        function closeModal() {
            modal.classList.remove('show');
            form.reset();
            $('#variantsContainer').empty();
            variantCount = 0;
            addVariantRow();
        }

        $(document).ready(function() {
            // New Request button click handler
            $('#newRequestBtn').click(function() {
                openModal();
            });
            
            // Edit button click handler
            $(document).on('click', '.edit-product', function() {
                const productId = $(this).data('id');
                
                // Show skeleton loader
                $('#productsTableBody').parent().addClass('hidden');
                $('#skeletonLoader').removeClass('hidden');
                
                // Fetch product data
                $.get(`/products/${productId}/edit`, function(response) {
                    openModal('edit', response.data);
                    
                    // Hide skeleton loader and show table
                    $('#skeletonLoader').addClass('hidden');
                    $('#productsTableBody').parent().removeClass('hidden');
                });
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Search functionality
            let searchTimeout;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                let searchValue = $(this).val();
                
                // Show skeleton loader immediately
                $('#productsTableBody').parent().addClass('hidden');
                $('#skeletonLoader').removeClass('hidden');
                
                searchTimeout = setTimeout(() => {
                    $.get('{{ route("products.list") }}', { search: searchValue }, function(response) {
                        // Update table content
                        $('#productsTableBody').html($(response).find('#productsTableBody').html());
                        
                        // Update pagination if it exists
                        let newPagination = $(response).find('#paginationContainer').html();
                        if (newPagination) {
                            $('#paginationContainer').html(newPagination);
                        }
                        
                        // Hide skeleton loader and show table
                        $('#skeletonLoader').addClass('hidden');
                        $('#productsTableBody').parent().removeClass('hidden');
                    });
                }, 200);
            });

            $('#submitButton').click(function() {
                let form = $('#productRequestForm');
                let formData = form.serialize();
                let isEdit = modal.dataset.mode === 'edit';
                let url = isEdit ? `/products/${$('#productId').val()}` : '{{ route("products.store") }}';
                let method = isEdit ? 'PUT' : 'POST';

                // Clear previous errors
                $('.error-message').remove();
                $('.border-red-500').removeClass('border-red-500');
                
                // Show skeleton loader before submission
                $('#productsTableBody').parent().addClass('hidden');
                $('#skeletonLoader').removeClass('hidden');

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        // Reset form and close modal
                        form[0].reset();
                        closeModal();
                        showAlert('success', response.message || (isEdit ? 'Product updated successfully.' : 'Product request has been submitted successfully.'));
                        
                        // Wait for alert to be visible then refresh
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        $('#skeletonLoader').addClass('hidden');
                        $('#productsTableBody').parent().removeClass('hidden');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                // Handle nested validation errors for variants
                                if (key.includes('variants.')) {
                                    let matches = key.match(/variants\.(\d+)\.(\w+)/);
                                    if (matches) {
                                        let [, index, field] = matches;
                                        let input = $(`input[name="variants[${index}][${field}]"]`);
                                        input.addClass('border-red-500');
                                        input.after(`<div class="text-red-500 text-xs mt-1 error-message">${errors[key][0]}</div>`);
                                    }
                                } else {
                                    $(`#${key}`).addClass('border-red-500');
                                    $(`#${key}`).after(`<div class="text-red-500 text-xs mt-1 error-message">${errors[key][0]}</div>`);
                                }
                            });
                            showAlert('error', 'Please check the form for errors.');
                        } else {
                            showAlert('error', 'An error occurred while processing your request.');
                        }
                    }
                });
            });

            function refreshTable(params = {}) {
                // Show skeleton loader
                $('#productsTableBody').parent().addClass('hidden');
                $('#skeletonLoader').removeClass('hidden');
                
                // Clear any existing timeouts
                if (window.refreshTimeout) {
                    clearTimeout(window.refreshTimeout);
                }

                $.get('{{ route("products.list") }}', params, function(response) {
                    window.refreshTimeout = setTimeout(() => {
                        $('#productsTableBody').html($(response).find('#productsTableBody').html());
                        
                        // Only update pagination if it exists in response
                        let newPagination = $(response).find('#paginationContainer').html();
                        if (newPagination) {
                            $('#paginationContainer').html(newPagination);
                        }
                        
                        // Hide skeleton loader and show table
                        $('#skeletonLoader').addClass('hidden');
                        $('#productsTableBody').parent().removeClass('hidden');
                    }, 200); // Further reduced delay for better responsiveness
                });
            }

            function showAlert(type, message) {
                let bgColor = type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';
                let alertHtml = `
                    <div class="mb-4 p-4 ${bgColor} border-l-4 rounded">
                        ${message}
                    </div>
                `;
                $('#alertContainer').html(alertHtml);
                setTimeout(() => {
                    $('#alertContainer').empty();
                }, 3000);
            }

            $(document).on('click', '#addVariant', function() {
                addVariantRow();
            });

            $(document).on('click', '.remove-variant', function() {
                $(this).closest('.variant-row').remove();
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == modal) closeModal();
        }
    </script>
</body>
</html>