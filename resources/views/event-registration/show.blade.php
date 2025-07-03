<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Event {{ $event->title }} - Simiko</title>
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
            
            <!-- Error Messages -->
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

            @if(session('error'))
                <div class="bg-red-900 border-2 border-red-400 text-red-300 p-4 mb-6 hard-shadow">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- 2 Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- LEFT COLUMN - Event Information -->
                <div class="space-y-6">
                    <!-- Event Header -->
                    <div class="bg-gray-800 p-6 border-2 border-black hard-shadow sticky top-24">
                        @if($event->image)
                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover border-2 border-black mb-4">
                        @endif
                        
                        <div class="text-center mb-6">
                            <h1 class="text-2xl lg:text-3xl font-bold text-secondary mb-3 font-mono">{{ $event->title }}</h1>
                            <p class="text-gray-300 leading-relaxed">{{ $event->content }}</p>
                        </div>
                        
                        <!-- Event Details Grid -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-blue-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-building text-blue-400 text-xl mb-1"></i>
                                <p class="text-xs font-semibold text-blue-200 mb-1">Penyelenggara</p>
                                <p class="text-xs text-blue-100">{{ $event->unitKegiatan->name }}</p>
                            </div>
                            <div class="bg-green-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-calendar text-green-400 text-xl mb-1"></i>
                                <p class="text-xs font-semibold text-green-200 mb-1">Tanggal Event</p>
                                <p class="text-xs text-green-100">{{ $event->event_date?->format('d M Y') }}</p>
                            </div>
                            <div class="bg-purple-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-{{ $event->event_type === 'online' ? 'laptop' : 'map-marker-alt' }} text-purple-400 text-xl mb-1"></i>
                                <p class="text-xs font-semibold text-purple-200 mb-1">Format</p>
                                <p class="text-xs text-purple-100">{{ ucfirst($event->event_type) }}</p>
                            </div>
                            <div class="bg-yellow-900 border-2 border-black p-3 text-center">
                                <i class="fas fa-map-marker-alt text-yellow-400 text-xl mb-1"></i>
                                <p class="text-xs font-semibold text-yellow-200 mb-1">Lokasi</p>
                                <p class="text-xs text-yellow-100">{{ Str::limit($event->location, 15) }}</p>
                            </div>
                        </div>

                        <!-- Registration Fee Highlight -->
                        <div class="text-center mb-6">
                            <div class="bg-primary text-black px-4 py-3 border-2 border-black hard-shadow">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-tag mr-2"></i>
                                    <div>
                                        <p class="text-sm font-bold font-mono">Biaya Pendaftaran</p>
                                        <p class="text-xl font-bold font-mono">{{ $event->paymentConfiguration->getFormattedAmountAttribute() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Capacity Information -->
                        <div class="bg-gray-700 border-2 border-black p-4 mb-6">
                            <h3 class="font-semibold text-secondary mb-3 flex items-center font-mono">
                                <i class="fas fa-users text-primary mr-2"></i>
                                Kapasitas Peserta
                            </h3>
                            
                            @if($capacityInfo['is_unlimited'])
                                <div class="text-center">
                                    <div class="bg-green-800 border-2 border-green-400 text-green-200 px-4 py-3 mb-3">
                                        <i class="fas fa-infinity text-green-400 text-xl mr-2"></i>
                                        <span class="font-bold">Tidak Terbatas</span>
                                    </div>
                                    <p class="text-sm text-gray-300">
                                        <strong>{{ number_format($capacityInfo['current_registrations']) }}</strong> peserta sudah terdaftar
                                    </p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    <!-- Progress Bar -->
                                    <div class="bg-gray-600 rounded-full h-4 border-2 border-black">
                                        <div class="h-full rounded-full transition-all duration-300 {{ 
                                            $capacityInfo['percentage_filled'] >= 100 ? 'bg-red-500' : 
                                            ($capacityInfo['percentage_filled'] >= 80 ? 'bg-yellow-500' : 'bg-green-500')
                                        }}" style="width: {{ min(100, $capacityInfo['percentage_filled']) }}%"></div>
                                    </div>
                                    
                                    <!-- Capacity Stats -->
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-300">
                                            <strong>{{ number_format($capacityInfo['current_registrations']) }}</strong> dari 
                                            <strong>{{ number_format($capacityInfo['max_participants']) }}</strong> peserta
                                        </span>
                                        <span class="font-bold {{ 
                                            $capacityInfo['percentage_filled'] >= 100 ? 'text-red-400' : 
                                            ($capacityInfo['percentage_filled'] >= 80 ? 'text-yellow-400' : 'text-green-400')
                                        }}">
                                            {{ $capacityInfo['percentage_filled'] }}%
                                        </span>
                                    </div>
                                    
                                    <!-- Available Slots -->
                                    <div class="text-center">
                                        @if($capacityInfo['is_full'])
                                            <div class="bg-red-800 border-2 border-red-400 text-red-200 px-4 py-2">
                                                <i class="fas fa-times-circle text-red-400 mr-2"></i>
                                                <span class="font-bold">Event Penuh</span>
                                            </div>
                                        @else
                                            <div class="bg-green-800 border-2 border-green-400 text-green-200 px-4 py-2">
                                                <i class="fas fa-check-circle text-green-400 mr-2"></i>
                                                <strong>{{ number_format($capacityInfo['available_slots']) }}</strong> slot tersisa
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Payment Methods Preview -->
                        @if($event->paymentConfiguration->payment_methods)
                            <div class="bg-gray-700 border-2 border-black p-4 mb-4">
                                <h3 class="font-semibold text-secondary mb-3 flex items-center font-mono">
                                    <i class="fas fa-credit-card text-primary mr-2"></i>
                                    Metode Pembayaran Tersedia
                                </h3>
                                <div class="space-y-2">
                                    @foreach(array_slice($event->paymentConfiguration->payment_methods, 0, 3) as $method)
                                        <div class="flex items-center text-sm text-gray-300">
                                            <i class="fas fa-{{ str_contains($method['method'], 'Bank') ? 'university' : 'mobile-alt' }} text-primary mr-2"></i>
                                            <span>{{ $method['method'] }}</span>
                                        </div>
                                    @endforeach
                                    @if(count($event->paymentConfiguration->payment_methods) > 3)
                                        <p class="text-xs text-gray-400">+{{ count($event->paymentConfiguration->payment_methods) - 3 }} opsi lainnya</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- What Happens Next -->
                        <div class="bg-gray-700 border-2 border-black p-4">
                            <h3 class="font-semibold text-secondary mb-2 flex items-center font-mono">
                                <i class="fas fa-route text-primary mr-2"></i>
                                Langkah Selanjutnya
                            </h3>
                            <ol class="text-sm text-gray-300 space-y-1">
                                <li class="flex items-start">
                                    <span class="bg-primary text-black font-bold w-4 h-4 flex items-center justify-center text-xs mr-2 mt-0.5">1</span>
                                    Isi formulir pendaftaran
                                </li>
                                <li class="flex items-start">
                                    <span class="bg-primary text-black font-bold w-4 h-4 flex items-center justify-center text-xs mr-2 mt-0.5">2</span>
                                    Bayar via metode yang tersedia
                                </li>
                                <li class="flex items-start">
                                    <span class="bg-primary text-black font-bold w-4 h-4 flex items-center justify-center text-xs mr-2 mt-0.5">3</span>
                                    Upload bukti pembayaran
                                </li>
                                <li class="flex items-start">
                                    <span class="bg-primary text-black font-bold w-4 h-4 flex items-center justify-center text-xs mr-2 mt-0.5">4</span>
                                    Tunggu konfirmasi admin
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN - Registration Form -->
                <div>
                    <div class="bg-gray-800 border-2 border-black p-6 hard-shadow">
                        <div class="text-center mb-6">
                            <h2 class="text-2xl font-bold text-secondary mb-2 font-mono">Form Pendaftaran</h2>
                            <p class="text-gray-300">Lengkapi form di bawah untuk mendaftar event ini</p>
                        </div>

                        <form method="POST" action="{{ route('event.register', $event->registration_token) }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            
                            <!-- Basic Information -->
                            <div class="bg-gray-700 border-2 border-black p-5">
                                <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center font-mono">
                                    <i class="fas fa-user text-primary mr-2"></i>
                                    Informasi Dasar
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-semibold text-gray-200 mb-2">Nama Lengkap *</label>
                                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                               class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-semibold text-gray-200 mb-2">Email Address *</label>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                               class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">
                                    </div>
                                    
                                    <div>
                                        <label for="phone" class="block text-sm font-semibold text-gray-200 mb-2">Nomor Telepon</label>
                                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                               class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Fields -->
                            @if($event->paymentConfiguration->sanitized_custom_fields)
                                <div class="bg-gray-700 border-2 border-black p-5">
                                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center font-mono">
                                        <i class="fas fa-clipboard-list text-primary mr-2"></i>
                                        Informasi Tambahan
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        @foreach($event->paymentConfiguration->sanitized_custom_fields as $field)
                                            <div>
                                                <label for="custom_{{ $field['name'] }}" class="block text-sm font-semibold text-gray-200 mb-2">
                                                    {{ $field['label'] }}
                                                    @if($field['required'] ?? false) 
                                                        <span class="text-red-400">*</span> 
                                                    @endif
                                                </label>
                                                
                                                @if($field['type'] === 'text')
                                                    <input type="text" id="custom_{{ $field['name'] }}" 
                                                           name="custom_data[{{ $field['name'] }}]" 
                                                           value="{{ old('custom_data.' . $field['name']) }}"
                                                           @if($field['required'] ?? false) required @endif
                                                           @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                                           class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">
                                                
                                                @elseif($field['type'] === 'email')
                                                    <input type="email" id="custom_{{ $field['name'] }}" 
                                                           name="custom_data[{{ $field['name'] }}]" 
                                                           value="{{ old('custom_data.' . $field['name']) }}"
                                                           @if($field['required'] ?? false) required @endif
                                                           @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                                           class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">
                                                
                                                @elseif($field['type'] === 'number')
                                                    <input type="number" id="custom_{{ $field['name'] }}" 
                                                           name="custom_data[{{ $field['name'] }}]" 
                                                           value="{{ old('custom_data.' . $field['name']) }}"
                                                           @if($field['required'] ?? false) required @endif
                                                           @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                                           class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">
                                                
                                                @elseif($field['type'] === 'tel')
                                                    <input type="tel" id="custom_{{ $field['name'] }}" 
                                                           name="custom_data[{{ $field['name'] }}]" 
                                                           value="{{ old('custom_data.' . $field['name']) }}"
                                                           @if($field['required'] ?? false) required @endif
                                                           @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                                           class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">
                                                
                                                @elseif($field['type'] === 'url')
                                                    <input type="url" id="custom_{{ $field['name'] }}" 
                                                           name="custom_data[{{ $field['name'] }}]" 
                                                           value="{{ old('custom_data.' . $field['name']) }}"
                                                           @if($field['required'] ?? false) required @endif
                                                           @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                                           class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">
                                                
                                                @elseif($field['type'] === 'date')
                                                    <input type="date" id="custom_{{ $field['name'] }}" 
                                                           name="custom_data[{{ $field['name'] }}]" 
                                                           value="{{ old('custom_data.' . $field['name']) }}"
                                                           @if($field['required'] ?? false) required @endif
                                                           class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">
                                                
                                                @elseif($field['type'] === 'textarea')
                                                    <textarea id="custom_{{ $field['name'] }}" 
                                                              name="custom_data[{{ $field['name'] }}]" 
                                                              rows="3"
                                                              @if($field['required'] ?? false) required @endif
                                                              @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                                              class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">{{ old('custom_data.' . $field['name']) }}</textarea>
                                                
                                                @elseif($field['type'] === 'select')
                                                    <select id="custom_{{ $field['name'] }}" 
                                                            name="custom_data[{{ $field['name'] }}]"
                                                            @if($field['required'] ?? false) required @endif
                                                            class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">
                                                        <option value="">Pilih opsi</option>
                                                        @if(isset($field['options']))
                                                            @foreach(explode(',', $field['options']) as $option)
                                                                @php $option = trim($option); @endphp
                                                                <option value="{{ $option }}" 
                                                                        @if(old('custom_data.' . $field['name']) === $option) selected @endif>
                                                                    {{ $option }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                
                                                @elseif($field['type'] === 'file')
                                                    <input type="file" id="custom_{{ $field['name'] }}" 
                                                           name="custom_files[{{ $field['name'] }}]"
                                                           @if($field['required'] ?? false) required @endif
                                                           accept="image/*"
                                                           class="w-full px-4 py-3 border-2 border-black bg-gray-600 text-secondary focus:outline-none focus:bg-gray-500 transition duration-150">
                                                    <p class="text-sm text-gray-400 mt-2">Hanya file gambar yang diperbolehkan (JPG, PNG, GIF). Maksimal 10MB.</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Submit Button -->
                            <div class="text-center pt-4">
                                @if($capacityInfo['is_full'])
                                    <button type="button" disabled
                                            class="w-full bg-gray-600 text-gray-400 px-8 py-4 text-lg font-bold border-2 border-gray-500 cursor-not-allowed font-mono">
                                        <i class="fas fa-times-circle mr-2"></i>
                                        Event Sudah Penuh
                                    </button>
                                    <p class="text-sm text-red-400 mt-2">Pendaftaran ditutup karena kapasitas sudah penuh</p>
                                @elseif(!$capacityInfo['is_unlimited'] && $capacityInfo['available_slots'] <= 5)
                                    <button type="submit" 
                                            class="w-full bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 text-lg font-bold transition duration-150 border-2 border-black hard-shadow font-mono">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Daftar Sekarang - Tersisa {{ $capacityInfo['available_slots'] }} Slot!
                                    </button>
                                    <p class="text-sm text-orange-300 mt-2">
                                        <i class="fas fa-fire mr-1"></i>
                                        Buruan! Hanya tersisa {{ $capacityInfo['available_slots'] }} slot lagi
                                    </p>
                                @else
                                    <button type="submit" 
                                            class="w-full bg-primary text-black px-8 py-4 text-lg font-bold transition duration-150 border-2 border-black hard-shadow hover:bg-yellow-400 font-mono">
                                        <i class="fas fa-user-plus mr-2"></i>
                                        Daftar Event Sekarang
                                    </button>
                                    <p class="text-sm text-gray-400 mt-2">Kamu akan diarahkan ke halaman pembayaran setelah pendaftaran</p>
                                @endif
                            </div>
                        </form>
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
</body>
</html> 