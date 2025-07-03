<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ditutup - {{ $event->title }} - Simiko</title>
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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Event Header -->
            <div class="bg-gray-800 border-2 border-black p-8 mb-8 hard-shadow">
                @if($event->image)
                    <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="w-full h-64 object-cover border-2 border-black mb-6">
                @endif
                
                <div class="text-center mb-6">
                    <h1 class="text-3xl lg:text-4xl font-bold text-secondary mb-4 font-mono">{{ $event->title }}</h1>
                    <p class="text-lg text-gray-300 leading-relaxed">{{ $event->content }}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-blue-900 border-2 border-black p-4 text-center">
                        <i class="fas fa-building text-blue-400 text-2xl mb-2"></i>
                        <p class="text-sm font-semibold text-blue-200 mb-1">Penyelenggara</p>
                        <p class="text-sm text-blue-100">{{ $event->unitKegiatan->name }}</p>
                    </div>
                    <div class="bg-green-900 border-2 border-black p-4 text-center">
                        <i class="fas fa-calendar text-green-400 text-2xl mb-2"></i>
                        <p class="text-sm font-semibold text-green-200 mb-1">Tanggal Event</p>
                        <p class="text-sm text-green-100">{{ $event->event_date?->format('d M Y') }}</p>
                    </div>
                    <div class="bg-purple-900 border-2 border-black p-4 text-center">
                        <i class="fas fa-{{ $event->event_type === 'online' ? 'laptop' : 'map-marker-alt' }} text-purple-400 text-2xl mb-2"></i>
                        <p class="text-sm font-semibold text-purple-200 mb-1">Format</p>
                        <p class="text-sm text-purple-100">{{ ucfirst($event->event_type) }}</p>
                    </div>
                    <div class="bg-yellow-900 border-2 border-black p-4 text-center">
                        <i class="fas fa-map-marker-alt text-yellow-400 text-2xl mb-2"></i>
                        <p class="text-sm font-semibold text-yellow-200 mb-1">Lokasi</p>
                        <p class="text-sm text-yellow-100">{{ $event->location }}</p>
                    </div>
                </div>
            </div>

            <!-- Registration Closed Message -->
            <div class="bg-gray-800 border-2 border-black p-8 text-center hard-shadow">
                <div class="bg-red-900 border-2 border-red-400 w-32 h-32 flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-ban text-red-400 text-6xl"></i>
                </div>
                
                <h2 class="text-3xl font-bold text-red-400 mb-6 font-mono">Pendaftaran Ditutup</h2>
                
                <div class="bg-red-900 border-2 border-red-400 p-6 mb-6">
                    @php
                        // Use the reason passed from controller, or determine it from event status
                        $reason = $reason ?? null;
                        
                        // If no reason is set, check the actual event status
                        if (!$reason) {
                            if ($event->max_participants && $event->getTotalRegistrationsCount() >= $event->max_participants) {
                                $reason = 'capacity_full';
                            } else {
                                $reason = 'date_passed';
                            }
                        }
                    @endphp
                    
                    @if($reason === 'capacity_full')
                        <p class="text-lg text-red-200 mb-4">
                            Maaf, pendaftaran untuk event ini sudah ditutup karena telah mencapai kapasitas maksimum peserta.
                        </p>
                        <div class="bg-gray-800 border-2 border-black p-4 inline-block">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-users text-red-400 text-xl mr-2"></i>
                                <div>
                                    <p class="text-sm font-semibold text-red-300">Kapasitas Peserta</p>
                                    <p class="text-red-200">{{ $event->getTotalRegistrationsCount() }}/{{ $event->max_participants }} Peserta</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-lg text-red-200 mb-4">
                            Maaf, pendaftaran untuk event ini sudah ditutup karena tanggal event sudah berlalu.
                        </p>
                        <div class="bg-gray-800 border-2 border-black p-4 inline-block">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-calendar-times text-red-400 text-xl mr-2"></i>
                                <div>
                                    <p class="text-sm font-semibold text-red-300">Tanggal Event</p>
                                    <p class="text-red-200">{{ $event->event_date?->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-blue-900 border-2 border-blue-400 p-6 mb-8">
                    <div class="flex items-start justify-center">
                        <i class="fas fa-lightbulb text-blue-400 text-2xl mr-3 mt-1"></i>
                        <div>
                            <h3 class="font-bold text-blue-200 mb-2 font-mono">Cari event lain?</h3>
                            <p class="text-blue-200 text-sm">
                                Cek homepage kami untuk event mendatang lainnya dan aktivitas dari {{ $event->unitKegiatan->name }} serta organisasi mahasiswa lainnya.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ url('/') }}" 
                       class="bg-primary text-black px-8 py-3 text-lg font-bold transition duration-150 border-2 border-black hard-shadow hover:bg-yellow-400 inline-flex items-center justify-center font-mono">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Homepage
                    </a>
                    <a href="{{ url('/api/documentation') }}" 
                       class="border-2 border-primary text-primary hover:bg-primary hover:text-black px-8 py-3 text-lg font-bold transition duration-150 inline-flex items-center justify-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Jelajahi Event
                    </a>
                </div>
            </div>

            <!-- Event Statistics (if available) -->
            @if($event->getRegistrationsCount() > 0)
                <div class="bg-gray-800 border-2 border-black p-8 mt-8 hard-shadow">
                    <h3 class="text-xl font-bold text-secondary mb-6 text-center font-mono">Statistik Event</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="bg-blue-900 border-2 border-black p-4 mb-2">
                                <i class="fas fa-users text-blue-400 text-3xl"></i>
                            </div>
                            <div class="text-2xl font-bold text-blue-400">{{ $event->getRegistrationsCount() }}</div>
                            <div class="text-gray-300 text-sm">Total Registrasi</div>
                        </div>
                        <div class="text-center">
                            <div class="bg-green-900 border-2 border-black p-4 mb-2">
                                <i class="fas fa-check-circle text-green-400 text-3xl"></i>
                            </div>
                            <div class="text-2xl font-bold text-green-400">{{ $event->getPaidRegistrationsCount() }}</div>
                            <div class="text-gray-300 text-sm">Peserta Terkonfirmasi</div>
                        </div>
                        <div class="text-center">
                            <div class="bg-purple-900 border-2 border-black p-4 mb-2">
                                <i class="fas fa-building text-purple-400 text-3xl"></i>
                            </div>
                            <div class="text-xl font-bold text-purple-400">{{ $event->unitKegiatan->name }}</div>
                            <div class="text-gray-300 text-sm">Penyelenggara</div>
                        </div>
                    </div>
                </div>
            @endif
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