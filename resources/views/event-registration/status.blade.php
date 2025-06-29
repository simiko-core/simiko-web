<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Registrasi - {{ $event->title }} - Simiko</title>
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
            
            <!-- Status Messages - Only show error and info, success will be popup -->
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
                
                <!-- LEFT COLUMN - Event & Registration Information -->
                <div class="space-y-6">
                    <!-- Event & Registration Details -->
                    <div class="bg-gray-800 border-2 border-black p-6 hard-shadow sticky top-24">
                        @if($event->image)
                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="w-full h-32 object-cover border-2 border-black mb-4">
                        @endif
                        
                        <div class="text-center mb-6">
                            <h1 class="text-2xl lg:text-3xl font-bold text-secondary mb-2 font-mono">Status Registrasi</h1>
                            <p class="text-gray-300">{{ $event->title }}</p>
                        </div>
                        
                        <!-- Registration Info Grid -->
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="bg-blue-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-user text-blue-400 text-lg mb-1"></i>
                                <p class="text-xs font-semibold text-blue-200 mb-1">Peserta</p>
                                <p class="text-xs text-blue-100">{{ Str::limit($transaction->getUserName(), 15) }}</p>
                            </div>
                            <div class="bg-green-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-envelope text-green-400 text-lg mb-1"></i>
                                <p class="text-xs font-semibold text-green-200 mb-1">Email</p>
                                <p class="text-xs text-green-100">{{ Str::limit($transaction->getUserEmail(), 15) }}</p>
                            </div>
                            <div class="bg-purple-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-receipt text-purple-400 text-lg mb-1"></i>
                                <p class="text-xs font-semibold text-purple-200 mb-1">ID Transaksi</p>
                                <p class="text-xs text-purple-100 font-mono">{{ Str::limit($transaction->transaction_id, 12) }}</p>
                            </div>
                            <div class="bg-yellow-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-calendar text-yellow-400 text-lg mb-1"></i>
                                <p class="text-xs font-semibold text-yellow-200 mb-1">Tanggal Daftar</p>
                                <p class="text-xs text-yellow-100">{{ $transaction->created_at->format('d M Y') }}</p>
                            </div>
                        </div>

                        <!-- Status & Amount Highlight -->
                        <div class="text-center mb-6">
                            <div class="bg-primary text-black px-4 py-3 border-2 border-black hard-shadow mb-3">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-money-bill-wave mr-2"></i>
                                    <div>
                                        <p class="text-sm font-bold font-mono">Jumlah</p>
                                        <p class="text-xl font-bold font-mono">{{ $transaction->getFormattedAmountAttribute() }}</p>
                                    </div>
                                </div>
                            </div>
                            <span class="px-4 py-2 text-sm font-bold border-2 border-black
                                @if($transaction->status === 'pending') bg-yellow-600 text-black
                                @elseif($transaction->status === 'paid') bg-green-600 text-black
                                @else bg-red-600 text-white @endif">
                                <i class="fas fa-circle mr-1"></i>
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </div>

                        <!-- Capacity Information -->
                        <div class="bg-gray-700 border-2 border-black p-4 mb-4">
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

                        <!-- Additional Registration Info -->
                        <div class="bg-gray-700 border-2 border-black p-4 mb-4">
                            <h3 class="font-semibold text-secondary mb-2 flex items-center font-mono">
                                <i class="fas fa-info-circle text-primary mr-2"></i>
                                Informasi Registrasi
                            </h3>
                            <div class="text-sm text-gray-300 space-y-1">
                                <p><strong>ID Transaksi:</strong> {{ $transaction->transaction_id }}</p>
                                <p><strong>Event:</strong> {{ $event->title }}</p>
                                <p><strong>Penyelenggara:</strong> {{ $event->unitKegiatan->name }}</p>
                                <p><strong>Tanggal Daftar:</strong> {{ $transaction->created_at->format('d M Y H:i') }}</p>
                                @if($transaction->expires_at)
                                    <p><strong>Berakhir:</strong> {{ $transaction->expires_at->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Custom Data Display -->
                        @if($transaction->custom_data && count($transaction->custom_data) > 0)
                            <div class="bg-gray-700 border-2 border-black p-4">
                                <h3 class="font-semibold text-secondary mb-3 flex items-center font-mono">
                                    <i class="fas fa-clipboard-list text-primary mr-2"></i>
                                    Informasi Tambahan
                                </h3>
                                <div class="space-y-2">
                                    @foreach($transaction->custom_data as $key => $value)
                                        <div class="bg-gray-600 border-2 border-black p-3">
                                            <p class="text-xs font-semibold text-gray-300">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                                            <p class="text-sm text-gray-200">{{ $value }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT COLUMN - Payment Status -->
                <div>
                    <!-- Payment Status -->
                    <div class="bg-gray-800 border-2 border-black p-8 mb-8 hard-shadow">
                        <h2 class="text-2xl font-bold text-secondary mb-6 flex items-center font-mono">
                            <i class="fas fa-credit-card text-primary mr-3"></i>
                            Status Pembayaran
                        </h2>
                        
                        @if($transaction->status === 'paid')
                            <div class="text-center py-12">
                                <div class="bg-green-900 border-2 border-green-400 w-32 h-32 flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-check text-green-400 text-5xl"></i>
                                </div>
                                <h3 class="text-3xl font-bold text-green-400 mb-4 font-mono">Pembayaran Terkonfirmasi!</h3>
                                <p class="text-gray-300 text-lg mb-6">Registrasi kamu sudah dikonfirmasi dengan sukses.</p>
                                @if($transaction->paid_at)
                                    <div class="bg-green-900 border-2 border-green-400 p-4 inline-block mb-6">
                                        <p class="text-sm font-semibold text-green-300">Dikonfirmasi pada:</p>
                                        <p class="text-green-200">{{ $transaction->paid_at->format('d M Y H:i') }}</p>
                                    </div>
                                @endif
                                
                                <!-- PDF Download Button -->
                                <div class="mt-6">
                                    <a href="{{ route('event.download-receipt', [$event->registration_token, $transaction->transaction_id]) }}" 
                                       target="_blank"
                                       class="bg-primary text-black px-8 py-3 text-lg font-bold transition duration-150 border-2 border-black hard-shadow hover:bg-yellow-400 inline-flex items-center font-mono">
                                        <i class="fas fa-download mr-2"></i>
                                        Download Receipt
                                    </a>
                                    <p class="text-sm text-gray-400 mt-2">Unduh bukti pembayaran resmi dalam format PDF</p>
                                </div>
                            </div>
                        
                        @elseif($transaction->proof_of_payment)
                            <div class="text-center py-12">
                                <div class="bg-yellow-900 border-2 border-yellow-400 w-32 h-32 flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-hourglass-half text-yellow-400 text-5xl"></i>
                                </div>
                                <h3 class="text-3xl font-bold text-yellow-400 mb-4 font-mono">Sedang Direview</h3>
                                <p class="text-gray-300 text-lg mb-4">Bukti pembayaran sudah diupload dan sedang direview oleh tim kami.</p>
                                <p class="text-sm text-gray-400">Harap tunggu konfirmasi dari admin.</p>
                            </div>
                        
                        @elseif($transaction->status === 'pending')
                            <div class="text-center py-12">
                                <div class="bg-orange-900 border-2 border-orange-400 w-32 h-32 flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-exclamation-triangle text-orange-400 text-5xl"></i>
                                </div>
                                <h3 class="text-3xl font-bold text-orange-400 mb-4 font-mono">Pembayaran Diperlukan</h3>
                                <p class="text-gray-300 text-lg mb-6">Harap selesaikan pembayaran untuk mengkonfirmasi registrasi.</p>
                                <a href="{{ route('event.payment', [$event->registration_token, $transaction->transaction_id]) }}" 
                                   class="bg-primary text-black px-8 py-3 text-lg font-bold transition duration-150 border-2 border-black hard-shadow hover:bg-yellow-400 inline-flex items-center font-mono">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Selesaikan Pembayaran
                                </a>
                            </div>
                        
                        @elseif($transaction->status === 'failed')
                            <div class="text-center py-12">
                                <div class="bg-red-900 border-2 border-red-400 w-32 h-32 flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-times text-red-400 text-5xl"></i>
                                </div>
                                <h3 class="text-3xl font-bold text-red-400 mb-4 font-mono">Pembayaran Gagal</h3>
                                <p class="text-gray-300 text-lg mb-4">Terjadi masalah dengan pembayaran. Harap hubungi support.</p>
                                @if($transaction->notes)
                                    <div class="bg-red-900 border-2 border-red-400 p-4 inline-block">
                                        <p class="text-sm font-semibold text-red-300">Catatan:</p>
                                        <p class="text-red-200">{{ $transaction->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        
                        @elseif($transaction->status === 'expired')
                            <div class="text-center py-12">
                                <div class="bg-gray-700 border-2 border-gray-500 w-32 h-32 flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-clock text-gray-400 text-5xl"></i>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-400 mb-4 font-mono">Registrasi Kedaluwarsa</h3>
                                <p class="text-gray-300 text-lg">Registrasi ini sudah kedaluwarsa. Silakan daftar ulang jika event masih buka.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center space-y-4">
                        @if($transaction->status === 'pending' && !$transaction->proof_of_payment)
                            <div>
                                <a href="{{ route('event.payment', [$event->registration_token, $transaction->transaction_id]) }}" 
                                   class="bg-primary text-black px-8 py-3 text-lg font-bold transition duration-150 border-2 border-black hard-shadow hover:bg-yellow-400 inline-flex items-center mr-4 font-mono">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Selesaikan Pembayaran
                                </a>
                            </div>
                        @endif
                        
                        <div class="flex justify-center space-x-4">
                            <button onclick="window.location.reload()" 
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 font-bold transition duration-150 inline-flex items-center border-2 border-black hard-shadow">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Refresh Status
                            </button>
                            <a href="{{ url('/') }}" 
                               class="border-2 border-primary text-primary hover:bg-primary hover:text-black px-6 py-2 font-bold transition duration-150 inline-flex items-center">
                                <i class="fas fa-home mr-2"></i>
                                Kembali ke Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Popup Modal -->
    @if(session('success'))
        <div id="successModal" class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity duration-300">
            <div class="bg-gray-800 border-4 border-black p-8 mx-4 max-w-md w-full transition-all duration-300 ease-out hard-shadow" id="modalContent">
                <div class="text-center">
                    <!-- Success Icon -->
                    <div class="bg-green-900 border-2 border-green-400 w-20 h-20 flex items-center justify-center mx-auto mb-6 hard-shadow">
                        <i class="fas fa-check text-green-400 text-3xl"></i>
                    </div>
                    
                    <!-- Success Title -->
                    <h3 class="text-2xl font-bold text-secondary mb-4 font-mono">Upload Berhasil!</h3>
                    
                    <!-- Success Message -->
                    <p class="text-gray-300 text-lg mb-6 leading-relaxed">
                        Bukti pembayaran berhasil diupload!<br>
                        <span class="text-gray-400">Harap tunggu verifikasi admin.</span>
                    </p>
                    
                    <!-- Additional Info -->
                    <div class="bg-yellow-900 border-2 border-yellow-600 p-4 mb-6">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-hourglass-half text-yellow-400 mr-2"></i>
                            <span class="text-yellow-200 text-sm font-bold">Status: Sedang Direview</span>
                        </div>
                    </div>
                    
                    <!-- Close Button -->
                    <button onclick="closeSuccessModal()" 
                            class="bg-primary text-black px-8 py-3 text-lg font-bold transition duration-150 border-2 border-black hard-shadow hover:bg-yellow-400 font-mono">
                        <i class="fas fa-check mr-2"></i>
                        Mengerti!
                    </button>
                </div>
            </div>
        </div>
    @endif

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
        // Success Modal Functions
        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            const modalContent = document.getElementById('modalContent');
            
            // Add exit animation
            modal.classList.add('opacity-0');
            modalContent.classList.add('scale-75');
            
            // Hide modal after animation
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        // Show modal with entrance animation
        @if(session('success'))
            window.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('successModal');
                const modalContent = document.getElementById('modalContent');
                
                // Initial state - hidden
                modal.classList.add('opacity-0');
                modalContent.classList.add('scale-75');
                
                // Trigger entrance animation after a short delay
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modalContent.classList.remove('scale-75');
                    modalContent.classList.add('scale-100');
                }, 200);
            });

            // Close modal when clicking outside
            document.addEventListener('click', function(event) {
                const modal = document.getElementById('successModal');
                
                if (modal && event.target === modal) {
                    closeSuccessModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeSuccessModal();
                }
            });
        @endif


    </script>
</body>
</html> 