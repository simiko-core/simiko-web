<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran {{ $event->title }} - Simiko</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: '#111827', // dark blue-gray
                        primary: '#facc15', // yellow
                        secondary: '#f9fafb', // off-white
                        accent: '#000000', // black
                    },
                    boxShadow: {
                      'neo': '4px 4px 0px #000000',
                    },
                    fontFamily: {
                        'sans': ['ui-sans-serif', 'system-ui'],
                        'mono': ['"Space Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        .hard-shadow {
            box-shadow: 4px 4px 0px #000;
        }
        .hard-shadow-hover:hover {
            box-shadow: 6px 6px 0px #000;
        }
        /* Rich content styling */
        .rich-content {
            color: #d1d5db;
        }
        .rich-content h1, .rich-content h2, .rich-content h3 {
            color: #f9fafb;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .rich-content p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        .rich-content strong {
            color: #facc15;
            font-weight: bold;
        }
        .rich-content ul, .rich-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .rich-content li {
            margin-bottom: 0.25rem;
            line-height: 1.5;
        }
        .rich-content em {
            font-style: italic;
            color: #9ca3af;
        }
        .rich-content a {
            color: #facc15;
            text-decoration: underline;
        }
        .rich-content a:hover {
            color: #fbbf24;
        }
    </style>
</head>
<body class="bg-background text-secondary font-sans">
    <!-- Navigation -->
    <nav class="bg-background fixed w-full z-50 border-b-4 border-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-graduation-cap text-primary text-2xl mr-2"></i>
                        <span class="text-2xl font-bold text-secondary font-mono">Simiko</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ url('/') }}" class="text-secondary hover:text-primary px-3 py-2 text-sm font-medium transition duration-150 border-2 border-transparent hover:border-primary">
                        <i class="fas fa-home mr-1"></i>
                        Home
                    </a>
                    <a href="{{ url('/admin') }}" class="bg-primary text-black px-4 py-2 text-sm font-bold transition duration-150 border-2 border-black hard-shadow hover:bg-yellow-400">
                        Panel Admin
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-16 bg-background min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Status Messages -->
            @if(session('success'))
                <div class="bg-green-900 border-2 border-green-400 text-green-300 p-4 mb-6 hard-shadow">
                    <div class="flex">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-900 border-2 border-blue-400 text-blue-300 p-4 mb-6 hard-shadow">
                    <div class="flex">
                        <i class="fas fa-info-circle mr-2 mt-1"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-900 border-2 border-red-400 text-red-300 p-4 mb-6 hard-shadow">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- 2 Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- LEFT COLUMN - Transaction & Event Information -->
                <div class="space-y-6">
                    <!-- Event & Transaction Details -->
                    <div class="bg-gray-800 border-2 border-black p-6 hard-shadow sticky top-24">
                        @if($event->image)
                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="w-full h-32 object-cover border-2 border-black mb-4">
                        @endif
                        
                        <div class="text-center mb-6">
                            <h1 class="text-2xl lg:text-3xl font-bold text-secondary mb-2 font-mono">Pembayaran Diperlukan</h1>
                            <p class="text-gray-300">Selesaikan registrasi dengan melakukan pembayaran</p>
                        </div>
                        
                        <!-- Event Info Grid -->
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="bg-blue-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-calendar text-blue-400 text-lg mb-1"></i>
                                <p class="text-xs font-semibold text-blue-200 mb-1">Event</p>
                                <p class="text-xs text-blue-100">{{ Str::limit($event->title, 20) }}</p>
                            </div>
                            <div class="bg-green-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-user text-green-400 text-lg mb-1"></i>
                                <p class="text-xs font-semibold text-green-200 mb-1">Peserta</p>
                                <p class="text-xs text-green-100">{{ Str::limit($transaction->getUserName(), 15) }}</p>
                            </div>
                            <div class="bg-purple-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-receipt text-purple-400 text-lg mb-1"></i>
                                <p class="text-xs font-semibold text-purple-200 mb-1">ID Transaksi</p>
                                <p class="text-xs text-purple-100 font-mono">{{ Str::limit($transaction->transaction_id, 12) }}</p>
                            </div>
                            <div class="bg-yellow-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-clock text-yellow-400 text-lg mb-1"></i>
                                <p class="text-xs font-semibold text-yellow-200 mb-1">Berakhir</p>
                                <p class="text-xs text-yellow-100">{{ $transaction->expires_at?->format('d M Y') }}</p>
                            </div>
                        </div>

                        <!-- Amount Highlight -->
                        <div class="text-center mb-6">
                            <div class="bg-primary text-black px-4 py-3 border-2 border-black hard-shadow">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-money-bill-wave mr-2"></i>
                                    <div>
                                        <p class="text-sm font-bold font-mono">Jumlah Pembayaran</p>
                                        <p class="text-xl font-bold font-mono">{{ $transaction->getFormattedAmountAttribute() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="px-3 py-1 text-sm font-bold border-2 border-black
                                    @if($transaction->status === 'pending') bg-yellow-600 text-black
                                    @elseif($transaction->status === 'paid') bg-green-600 text-black
                                    @else bg-red-600 text-white @endif">
                                    <i class="fas fa-circle mr-1"></i>
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Selected Payment Method Preview -->
                        <div class="bg-gray-700 border-2 border-black p-4" id="selected-method-preview" style="display: none;">
                            <h3 class="font-semibold text-secondary mb-3 flex items-center font-mono">
                                <i class="fas fa-credit-card text-primary mr-2"></i>
                                Metode Pembayaran Dipilih
                            </h3>
                            <div id="method-preview-content">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>

                        <!-- Capacity Information -->
                        <div class="bg-gray-700 border-2 border-black p-4 mt-4">
                            <h3 class="font-semibold text-secondary mb-3 flex items-center font-mono">
                                <i class="fas fa-users text-primary mr-2"></i>
                                Kapasitas Event
                            </h3>
                            
                            @if($capacityInfo['is_unlimited'])
                                <div class="text-center">
                                    <div class="bg-green-800 border-2 border-green-400 text-green-200 px-3 py-2 mb-2 text-sm">
                                        <i class="fas fa-infinity text-green-400 mr-2"></i>
                                        <span class="font-bold">Tidak Terbatas</span>
                                    </div>
                                    <p class="text-xs text-gray-300">
                                        {{ number_format($capacityInfo['current_registrations']) }} peserta terdaftar
                                    </p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <!-- Mini Progress Bar -->
                                    <div class="bg-gray-600 rounded-full h-2 border border-black">
                                        <div class="h-full rounded-full transition-all duration-300 {{ 
                                            $capacityInfo['percentage_filled'] >= 100 ? 'bg-red-500' : 
                                            ($capacityInfo['percentage_filled'] >= 80 ? 'bg-yellow-500' : 'bg-green-500')
                                        }}" style="width: {{ min(100, $capacityInfo['percentage_filled']) }}%"></div>
                                    </div>
                                    
                                    <!-- Compact Stats -->
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-300">
                                            {{ number_format($capacityInfo['current_registrations']) }}/{{ number_format($capacityInfo['max_participants']) }}
                                        </span>
                                        <span class="font-bold {{ 
                                            $capacityInfo['percentage_filled'] >= 100 ? 'text-red-400' : 
                                            ($capacityInfo['percentage_filled'] >= 80 ? 'text-yellow-400' : 'text-green-400')
                                        }}">
                                            {{ $capacityInfo['percentage_filled'] }}%
                                        </span>
                                    </div>
                                    
                                    @if($capacityInfo['available_slots'] <= 5 && !$capacityInfo['is_full'])
                                        <div class="bg-orange-800 border border-orange-400 text-orange-200 px-2 py-1 text-xs text-center">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            {{ $capacityInfo['available_slots'] }} slot tersisa!
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Transaction Details -->
                        <div class="bg-gray-700 border-2 border-black p-4 mt-4">
                            <h3 class="font-semibold text-secondary mb-2 flex items-center font-mono">
                                <i class="fas fa-info-circle text-primary mr-2"></i>
                                Informasi Transaksi
                            </h3>
                            <div class="text-sm text-gray-300 space-y-1">
                                <p><strong>ID Transaksi:</strong> {{ $transaction->transaction_id }}</p>
                                <p><strong>Event:</strong> {{ $event->title }}</p>
                                <p><strong>Penyelenggara:</strong> {{ $event->unitKegiatan->name }}</p>
                                @if($transaction->expires_at)
                                    <p><strong>Berakhir:</strong> {{ $transaction->expires_at->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN - Payment Flow -->
                <div>

            @if($transaction->status === 'pending')
                <!-- Step 1: Choose Payment Method -->
                <div class="bg-gray-800 border-2 border-black p-8 mb-8 hard-shadow" id="payment-method-selection">
                    <h2 class="text-2xl font-bold text-secondary mb-6 flex items-center font-mono">
                        <span class="bg-primary text-black font-bold w-8 h-8 flex items-center justify-center text-sm mr-3">1</span>
                        Pilih Metode Pembayaran
                    </h2>
                    
                    @if($event->paymentConfiguration->payment_methods)
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($event->paymentConfiguration->payment_methods as $index => $method)
                                <button onclick="selectPaymentMethod({{ $index }})" 
                                        class="payment-method-btn border-2 border-gray-500 p-6 hover:border-primary hover:bg-gray-700 transition duration-150 text-left group focus:outline-none hard-shadow-hover"
                                        data-method-index="{{ $index }}">
                                    <div class="flex items-center">
                                        <div class="bg-gray-600 group-hover:bg-primary group-hover:text-black border-2 border-black p-3 mr-4 transition duration-150">
                                            <i class="fas fa-{{ str_contains($method['method'], 'Bank') ? 'university' : 'mobile-alt' }} text-xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-secondary mb-1 group-hover:text-primary transition duration-150 font-mono">{{ $method['method'] }}</h3>
                                            @if(isset($method['bank_name']))
                                                <p class="text-gray-400 text-sm">{{ $method['bank_name'] }}</p>
                                            @endif
                                            @if(isset($method['description']))
                                                <p class="text-gray-400 text-sm">{{ Str::limit($method['description'], 40) }}</p>
                                            @endif
                                        </div>
                                        <div class="ml-auto">
                                            <div class="method-check-icon hidden">
                                                <i class="fas fa-check-circle text-green-400 text-xl"></i>
                                            </div>
                                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-primary transition duration-150 chevron-icon"></i>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Step 2: Payment Details and Upload (Hidden initially) -->
                <div class="hidden" id="payment-details-section">
                    <!-- Payment Details Card -->
                    <div class="bg-gray-800 border-2 border-black p-8 mb-8 hard-shadow">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-secondary flex items-center font-mono">
                                <span class="bg-primary text-black font-bold w-8 h-8 flex items-center justify-center text-sm mr-3">2</span>
                                Detail Pembayaran
                            </h2>
                            <button onclick="changePaymentMethod()" class="text-primary hover:text-yellow-300 text-sm font-bold transition duration-150">
                                <i class="fas fa-edit mr-1"></i>
                                Ganti Metode
                            </button>
                        </div>

                        <div id="selected-payment-details" class="bg-gray-700 border-2 border-black p-6 mb-6">
                            <!-- Payment details will be populated by JavaScript -->
                        </div>

                        <div class="bg-yellow-900 border-2 border-yellow-600 p-4">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-400 mr-3 mt-1"></i>
                                <div class="text-yellow-200">
                                    <h3 class="font-semibold mb-2 font-mono">Instruksi Penting:</h3>
                                    <ol class="text-sm space-y-1 list-decimal list-inside">
                                        <li>Transfer dengan jumlah yang tepat: <strong>{{ $transaction->getFormattedAmountAttribute() }}</strong></li>
                                        <li>Gunakan ID transaksi: <strong>{{ $transaction->transaction_id }}</strong> sebagai keterangan transfer</li>
                                        <li>Simpan bukti transfer/screenshot</li>
                                        <li>Upload bukti pembayaran di bawah setelah melakukan transfer</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Proof Section -->
                    <div class="bg-gray-800 border-2 border-black p-8 hard-shadow">
                        <h2 class="text-2xl font-bold text-secondary mb-6 flex items-center font-mono">
                            <span class="bg-primary text-black font-bold w-8 h-8 flex items-center justify-center text-sm mr-3">3</span>
                            Upload Bukti Pembayaran
                        </h2>
                        
                        @if($errors->any())
                            <div class="bg-red-900 border-2 border-red-400 text-red-300 p-4 mb-6 hard-shadow">
                                <div class="flex">
                                    <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                                    <div>
                                        <h3 class="font-semibold mb-1 font-mono">Harap perbaiki error berikut:</h3>
                                        <ul class="list-disc list-inside">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('event.upload-proof', [$event->registration_token, $transaction->transaction_id]) }}" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-6">
                                <label for="proof_file" class="block text-sm font-semibold text-gray-200 mb-3">
                                    <i class="fas fa-file-upload mr-1"></i>
                                    Bukti Pembayaran *
                                </label>
                                <div class="border-2 border-dashed border-gray-500 p-6 text-center hover:border-primary transition duration-150 drop-zone"
                                     id="drop-zone" 
                                     ondrop="handleDrop(event)" 
                                     ondragover="handleDragOver(event)" 
                                     ondragleave="handleDragLeave(event)">
                                    <input type="file" id="proof_file" name="proof_file" required accept=".jpg,.jpeg,.png,.pdf"
                                           class="hidden" onchange="updateFileName(this)">
                                    <label for="proof_file" class="cursor-pointer">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-300 mb-1">Klik untuk upload atau drag and drop</p>
                                        <p class="text-sm text-gray-400">JPG, PNG, PDF (Max 4MB)</p>
                                    </label>
                                    <p id="file-name" class="text-sm text-primary mt-2 hidden"></p>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" 
                                        class="bg-primary text-black px-8 py-4 text-lg font-bold transition duration-150 border-2 border-black hard-shadow hover:bg-yellow-400 font-mono">
                                    <i class="fas fa-upload mr-2"></i>
                                    Upload Bukti Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            @else
                <div class="bg-gray-800 border-2 border-black p-8 hard-shadow">
                    <h2 class="text-2xl font-bold text-secondary mb-6 flex items-center font-mono">
                        <i class="fas fa-check-circle text-primary mr-3"></i>
                        Status Pembayaran
                    </h2>
                    
                    @if($transaction->status === 'paid')
                        <div class="text-center py-12">
                            <div class="bg-green-900 border-2 border-green-400 w-24 h-24 flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-check text-green-400 text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-green-400 mb-4 font-mono">Pembayaran Terkonfirmasi!</h3>
                            <p class="text-gray-300 mb-6">Registrasi kamu sudah dikonfirmasi. Detail lebih lanjut akan dikirim via email.</p>
                            @if($transaction->paid_at)
                                <p class="text-sm text-gray-400">Dikonfirmasi pada: {{ $transaction->paid_at->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                    @elseif($transaction->proof_of_payment)
                        <div class="text-center py-12">
                            <div class="bg-yellow-900 border-2 border-yellow-400 w-24 h-24 flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-hourglass-half text-yellow-400 text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-yellow-400 mb-4 font-mono">Sedang Direview</h3>
                            <p class="text-gray-300">Bukti pembayaran sudah diupload dan sedang direview oleh tim kami.</p>
                        </div>
                    @endif
                </div>
            @endif

                    <!-- Status Check Link -->
                    <div class="text-center mt-8">
                        <a href="{{ route('event.status', [$event->registration_token, $transaction->transaction_id]) }}" 
                           class="inline-flex items-center text-primary hover:text-yellow-300 font-bold transition duration-150">
                            <i class="fas fa-search mr-2"></i>
                            Cek Status Registrasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-accent text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center mb-4">
                <i class="fas fa-graduation-cap text-primary text-2xl mr-2"></i>
                <span class="text-2xl font-bold font-mono">Simiko</span>
            </div>
            <p class="text-gray-300">
                © {{ date('Y') }} Simiko. Platform lengkap untuk manajemen Unit Kegiatan Mahasiswa.
            </p>
        </div>
    </footer>

    <script>
        // Payment methods data
        const paymentMethods = @json($event->paymentConfiguration->payment_methods ?? []);
        let selectedMethodIndex = null;

        function selectPaymentMethod(methodIndex) {
            selectedMethodIndex = methodIndex;
            const method = paymentMethods[methodIndex];
            
            // Update visual state of payment method buttons
            updatePaymentMethodButtons(methodIndex);
            
            // Update payment method preview in left sidebar
            updatePaymentMethodPreview(method);
            
            // Update selected payment details
            const detailsContainer = document.getElementById('selected-payment-details');
            
            let detailsHtml = `
                <div class="flex items-start">
                    <div class="bg-primary border-2 border-black p-3 mr-4">
                        <i class="fas fa-${method.method.includes('Bank') ? 'university' : 'mobile-alt'} text-black text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-secondary mb-3 font-mono">${method.method}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            `;
            
            if (method.account_number) {
                detailsHtml += `
                    <div class="bg-gray-600 border-2 border-black p-4">
                        <p class="text-sm font-semibold text-blue-300 mb-1">Nomor Rekening</p>
                        <p class="text-lg font-mono font-bold text-gray-200">${method.account_number}</p>
                    </div>
                `;
            }
            
            if (method.account_name) {
                detailsHtml += `
                    <div class="bg-gray-600 border-2 border-black p-4">
                        <p class="text-sm font-semibold text-green-300 mb-1">Nama Rekening</p>
                        <p class="text-lg font-semibold text-gray-200">${method.account_name}</p>
                    </div>
                `;
            }
            
            if (method.bank_name) {
                detailsHtml += `
                    <div class="bg-gray-600 border-2 border-black p-4">
                        <p class="text-sm font-semibold text-purple-300 mb-1">Bank</p>
                        <p class="text-lg font-semibold text-gray-200">${method.bank_name}</p>
                    </div>
                `;
            }
            
            detailsHtml += '</div>';
            
            if (method.description) {
                detailsHtml += `
                    <div class="mt-4 bg-gray-600 border-2 border-black p-4">
                        <p class="text-sm font-semibold text-gray-300 mb-1">Informasi Tambahan</p>
                        <p class="text-gray-300">${method.description}</p>
                    </div>
                `;
            }
            
            detailsHtml += `
                    </div>
                </div>
            `;
            
            detailsContainer.innerHTML = detailsHtml;
            
            // Hide selection section and show details section
            document.getElementById('payment-method-selection').style.display = 'none';
            document.getElementById('payment-details-section').classList.remove('hidden');
            
            // Smooth scroll to payment details
            document.getElementById('payment-details-section').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }

        function changePaymentMethod() {
            // Show selection section and hide details section
            document.getElementById('payment-method-selection').style.display = 'block';
            document.getElementById('payment-details-section').classList.add('hidden');
            
            // Hide payment method preview in sidebar
            document.getElementById('selected-method-preview').style.display = 'none';
            
            // Reset payment method button visual states
            resetPaymentMethodButtons();
            
            // Reset selected method index
            selectedMethodIndex = null;
            
            // Scroll back to payment method selection
            document.getElementById('payment-method-selection').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }

        function updatePaymentMethodButtons(selectedIndex) {
            const buttons = document.querySelectorAll('.payment-method-btn');
            buttons.forEach((button, index) => {
                const checkIcon = button.querySelector('.method-check-icon');
                const chevronIcon = button.querySelector('.chevron-icon');
                
                if (index === selectedIndex) {
                    // Selected state
                    button.classList.add('border-primary', 'bg-gray-700');
                    button.classList.remove('border-gray-500');
                    checkIcon.classList.remove('hidden');
                    chevronIcon.classList.add('hidden');
                } else {
                    // Unselected state
                    button.classList.remove('border-primary', 'bg-gray-700');
                    button.classList.add('border-gray-500');
                    checkIcon.classList.add('hidden');
                    chevronIcon.classList.remove('hidden');
                }
            });
        }

        function resetPaymentMethodButtons() {
            const buttons = document.querySelectorAll('.payment-method-btn');
            buttons.forEach((button) => {
                const checkIcon = button.querySelector('.method-check-icon');
                const chevronIcon = button.querySelector('.chevron-icon');
                
                // Reset to unselected state
                button.classList.remove('border-primary', 'bg-gray-700');
                button.classList.add('border-gray-500');
                checkIcon.classList.add('hidden');
                chevronIcon.classList.remove('hidden');
            });
        }

        function updatePaymentMethodPreview(method) {
            const previewContainer = document.getElementById('selected-method-preview');
            const previewContent = document.getElementById('method-preview-content');
            
            let previewHtml = `
                <div class="flex items-center">
                    <div class="bg-primary border-2 border-black p-2 mr-3">
                        <i class="fas fa-${method.method.includes('Bank') ? 'university' : 'mobile-alt'} text-black"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-secondary font-mono">${method.method}</p>
                        ${method.bank_name ? `<p class="text-sm text-gray-300">${method.bank_name}</p>` : ''}
                        ${method.account_number ? `<p class="text-sm font-mono text-gray-300">${method.account_number}</p>` : ''}
                    </div>
                </div>
            `;
            
            previewContent.innerHTML = previewHtml;
            previewContainer.style.display = 'block';
        }

        function updateFileName(input) {
            const fileName = document.getElementById('file-name');
            const uploadButton = document.querySelector('button[type="submit"]');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // Size in MB
                const maxSize = 4; // 4MB limit
                
                if (file.size > maxSize * 1024 * 1024) {
                    fileName.innerHTML = `<span class="text-red-400"><i class="fas fa-exclamation-triangle mr-1"></i>File terlalu besar (${fileSize}MB). Max ukuran: ${maxSize}MB</span>`;
                    fileName.classList.remove('hidden');
                    uploadButton.disabled = true;
                    uploadButton.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    fileName.innerHTML = `<span class="text-green-400"><i class="fas fa-check-circle mr-1"></i>Dipilih: ${file.name} (${fileSize}MB)</span>`;
                    fileName.classList.remove('hidden');
                    uploadButton.disabled = false;
                    uploadButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            } else {
                fileName.classList.add('hidden');
                uploadButton.disabled = false;
                uploadButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // Drag and drop functionality
        function handleDragOver(event) {
            event.preventDefault();
            const dropZone = document.getElementById('drop-zone');
            dropZone.classList.add('border-primary', 'bg-gray-700');
            dropZone.classList.remove('border-gray-500');
        }

        function handleDragLeave(event) {
            event.preventDefault();
            const dropZone = document.getElementById('drop-zone');
            dropZone.classList.remove('border-primary', 'bg-gray-700');
            dropZone.classList.add('border-gray-500');
        }

        function handleDrop(event) {
            event.preventDefault();
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('proof_file');
            
            // Reset drop zone styles
            dropZone.classList.remove('border-primary', 'bg-gray-700');
            dropZone.classList.add('border-gray-500');
            
            // Get dropped files
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                if (allowedTypes.includes(file.type)) {
                    // Set the file to the input
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    fileInput.files = dt.files;
                    
                    // Update file name display
                    updateFileName(fileInput);
                } else {
                    alert('Harap upload file JPG, PNG, atau PDF saja.');
                }
            }
        }
    </script>
</body>
</html> 