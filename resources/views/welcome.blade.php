<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simiko - Platform Manajemen Kegiatan Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/assets/image.png">
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
                    <a href="{{ url('/admin-panel') }}" class="bg-primary text-black px-4 py-2 text-sm font-bold transition duration-150 border-2 border-black hard-shadow hover:bg-yellow-400">
                        Panel UKM
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-16 bg-background min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <h1 class="text-4xl lg:text-6xl font-bold text-secondary mb-6 font-mono">
                        Sistem Manajemen <br>
                        <span class="text-primary">Unit Kegiatan</span>
                        Mahasiswa
                    </h1>
                    <p class="text-xl text-gray-300 mb-8 leading-relaxed">
                        Platform lengkap untuk mengelola UKM dari member hingga seluruh mahasiswa. 
                        Admin posting berita, pengumuman, event, hingga payment—semua dalam satu sistem terintegrasi.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ url('/admin') }}" class="bg-primary text-black px-8 py-3 text-lg font-bold transition duration-150 border-2 border-black hard-shadow hover:bg-yellow-400 text-center">
                            <i class="fas fa-users-cog mr-2"></i>
                            Kelola UKM Sekarang
                        </a>
                        <a href="#features" class="border-2 border-primary text-primary hover:bg-primary hover:text-black px-8 py-3 text-lg font-bold transition duration-150 text-center">
                            Lihat Fitur Lengkap
                        </a>
                    </div>
                </div>
                <div class="flex justify-center" data-aos="fade-left">
                    <div class="relative">
                        <div class="bg-gray-800 p-8 border-4 border-black hard-shadow">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-blue-900 border-2 border-black p-4 text-center">
                                    <i class="fas fa-users text-blue-400 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-blue-200">Manajemen UKM</p>
                                </div>
                                <div class="bg-green-900 border-2 border-black p-4 text-center">
                                    <i class="fas fa-calendar text-green-400 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-green-200">Acara</p>
                                </div>
                                <div class="bg-purple-900 border-2 border-black p-4 text-center">
                                    <i class="fas fa-trophy text-purple-400 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-purple-200">Prestasi</p>
                                </div>
                                <div class="bg-yellow-900 border-2 border-black p-4 text-center">
                                    <i class="fas fa-user-plus text-yellow-400 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-yellow-200">Pendaftaran</p>
                                </div>
                                <div class="bg-red-900 border-2 border-black p-4 text-center">
                                    <i class="fas fa-rss text-red-400 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-red-200">Konten</p>
                                </div>
                                <div class="bg-indigo-900 border-2 border-black p-4 text-center">
                                    <i class="fas fa-chart-bar text-indigo-400 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-indigo-200">Analitik</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Simiko Section -->
    <section class="py-20 bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Illustration Section -->
            <div class="text-center mb-20">
                <h2 class="text-3xl lg:text-4xl font-bold text-secondary mb-8 font-mono">
                    Tentang <span class="text-primary">Simiko</span>
                </h2>
                <p class="text-xl text-gray-300 max-w-4xl mx-auto mb-12 leading-relaxed">
                    Simiko adalah platform revolusioner yang mengintegrasikan seluruh aspek manajemen Unit Kegiatan Mahasiswa dalam satu ekosistem digital yang powerful dan user-friendly.
                </p>
                
                <!-- Illustration Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
                    <div class="bg-background p-8 border-2 border-black hard-shadow" data-aos="fade-up" data-aos-delay="100">
                        <div class="bg-blue-900 border-2 border-black w-20 h-20 mx-auto flex items-center justify-center mb-6">
                            <i class="fas fa-sitemap text-blue-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-4 font-mono">Centralized Management</h3>
                        <p class="text-gray-400">
                            Satu platform untuk mengelola semua aspek UKM, dari member management hingga financial tracking.
                        </p>
                    </div>
                    
                    <div class="bg-background p-8 border-2 border-black hard-shadow"  data-aos="fade-up" data-aos-delay="200">
                        <div class="bg-green-900 border-2 border-black w-20 h-20 mx-auto flex items-center justify-center mb-6">
                            <i class="fas fa-rocket text-green-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-4 font-mono">Modern Technology</h3>
                        <p class="text-gray-400">
                            Dibangun dengan teknologi terdepan untuk performa optimal dan user experience yang luar biasa.
                        </p>
                    </div>
                    
                    <div class="bg-background p-8 border-2 border-black hard-shadow" data-aos="fade-up" data-aos-delay="300">
                        <div class="bg-purple-900 border-2 border-black w-20 h-20 mx-auto flex items-center justify-center mb-6">
                            <i class="fas fa-users text-purple-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-4 font-mono">Community Focused</h3>
                        <p class="text-gray-400">
                            Membangun ekosistem yang mendukung kolaborasi dan pertumbuhan komunitas mahasiswa.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission Section -->
    <section class="py-20 bg-accent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Vision -->
                <div class="bg-background p-10 border-4 border-black hard-shadow" data-aos="fade-right">
                    <div class="flex items-center mb-6">
                        <div class="bg-primary border-2 border-black w-12 h-12 flex items-center justify-center mr-4">
                            <i class="fas fa-eye text-black text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-secondary font-mono">Visi Kami</h3>
                    </div>
                    <p class="text-gray-300 text-lg leading-relaxed mb-6">
                        Menjadi platform digital terdepan yang memberdayakan Unit Kegiatan Mahasiswa untuk mencapai potensi maksimal melalui manajemen yang efisien, transparan, dan terintegrasi.
                    </p>
                    <div class="border-l-4 border-primary pl-4">
                        <p class="text-primary font-semibold italic">
                            "Membangun masa depan organisasi mahasiswa yang lebih terorganisir dan berdampak"
                        </p>
                    </div>
                </div>

                <!-- Mission -->
                <div class="bg-background p-10 border-4 border-black hard-shadow" data-aos="fade-left">
                    <div class="flex items-center mb-6">
                        <div class="bg-primary border-2 border-black w-12 h-12 flex items-center justify-center mr-4">
                            <i class="fas fa-bullseye text-black text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-secondary font-mono">Misi Kami</h3>
                    </div>
                    <ul class="space-y-4 text-gray-300">
                        <li class="flex items-start">
                            <div class="bg-primary w-2 h-2 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <span>Menyediakan tools manajemen yang powerful namun mudah digunakan untuk seluruh aspek operasional UKM</span>
                        </li>
                        <li class="flex items-start">
                            <div class="bg-primary w-2 h-2 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <span>Mengintegrasikan sistem pembayaran, pendaftaran, dan manajemen event dalam satu platform</span>
                        </li>
                        <li class="flex items-start">
                            <div class="bg-primary w-2 h-2 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <span>Memfasilitasi transparansi dan akuntabilitas dalam pengelolaan organisasi mahasiswa</span>
                        </li>
                        <li class="flex items-start">
                            <div class="bg-primary w-2 h-2 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <span>Mendukung pertumbuhan dan perkembangan komunitas mahasiswa melalui teknologi digital</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-background">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl lg:text-4xl font-bold text-secondary mb-4 font-mono">
                    Fitur Lengkap Manajemen UKM
                </h2>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    Dari manajemen member, posting berita & pengumuman, hingga event management dengan payment gateway. 
                    Semua kebutuhan UKM dalam satu platform yang powerful dan terintegrasi.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- UKM Management -->
                <div class="bg-background p-8 border-2 border-black transition duration-300 hard-shadow-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-blue-900 border-2 border-black w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-building text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4 font-mono">Manajemen Member UKM</h3>
                    <p class="text-gray-400 mb-4">
                        Kelola member dari pendaftaran hingga kelulusan. Data lengkap setiap member, status keanggotaan, hingga histori aktivitas dalam UKM.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Database member yang lengkap</li>
                        <li>• Status keanggotaan real-time</li>
                        <li>• Histori aktivitas member</li>
                    </ul>
                </div>

                <!-- Event Management -->
                <div class="bg-background p-8 border-2 border-black transition duration-300 hard-shadow-hover">
                    <div class="bg-green-900 border-2 border-black w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-calendar-alt text-green-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4 font-mono">Event & Payment System</h3>
                    <p class="text-gray-400 mb-4">
                        Buat event dengan sistem pembayaran terintegrasi. Kelola pendaftaran, track pembayaran, dan monitor kehadiran peserta dalam satu dashboard.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Event creation & management</li>
                        <li>• Payment gateway integration</li>
                        <li>• Registration & attendance tracking</li>
                    </ul>
                </div>

                <!-- Member Registration -->
                <div class="bg-background p-8 border-2 border-black transition duration-300 hard-shadow-hover" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-purple-900 border-2 border-black w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-user-plus text-purple-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4 font-mono">Recruitment Management</h3>
                    <p class="text-gray-400 mb-4">
                        Sistem recruitment dari opening hingga penerimaan member baru. Kelola formulir pendaftaran, seleksi, hingga proses onboarding member baru.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Form pendaftaran yang customizable</li>
                        <li>• Sistem seleksi & scoring</li>
                        <li>• Onboarding member baru</li>
                    </ul>
                </div>

                <!-- Content Management -->
                <div class="bg-background p-8 border-2 border-black transition duration-300 hard-shadow-hover" data-aos="fade-up" data-aos-delay="400">
                    <div class="bg-yellow-900 border-2 border-black w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-rss text-yellow-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4 font-mono">Berita & Pengumuman</h3>
                    <p class="text-gray-400 mb-4">
                        Posting berita, pengumuman, dan informasi penting untuk seluruh mahasiswa. Kelola konten dengan editor yang powerful dan rich media support.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• News & announcement posting</li>
                        <li>• Rich text editor & media upload</li>
                        <li>• Targeting & scheduling posts</li>
                    </ul>
                </div>

                <!-- Achievement Tracking -->
                <div class="bg-background p-8 border-2 border-black transition duration-300 hard-shadow-hover" data-aos="fade-up" data-aos-delay="500">
                    <div class="bg-red-900 border-2 border-black w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-trophy text-red-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4 font-mono">Galeri & Achievement</h3>
                    <p class="text-gray-400 mb-4">
                        Dokumentasi kegiatan UKM dan showcase prestasi yang diraih. Album foto kegiatan, video dokumenter, hingga galeri penghargaan.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Album kegiatan & dokumentasi</li>
                        <li>• Achievement & awards gallery</li>
                        <li>• Video & multimedia content</li>
                    </ul>
                </div>

                <!-- API Integration -->
                <div class="bg-background p-8 border-2 border-black transition duration-300 hard-shadow-hover" data-aos="fade-up" data-aos-delay="600">
                    <div class="bg-indigo-900 border-2 border-black w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-code text-indigo-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4 font-mono">Reporting & Analytics</h3>
                    <p class="text-gray-400 mb-4">
                        Dashboard analytics untuk monitor performa UKM. Data member, engagement rate, financial report, hingga growth metrics dalam satu tempat.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Member analytics & statistics</li>
                        <li>• Financial & payment reports</li>
                        <li>• Event performance metrics</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-20 bg-background text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl lg:text-4xl font-bold mb-4 font-mono">
                    Terbukti Efektif & Terintegrasi
                </h2>
                <p class="text-xl text-gray-300">
                    Dari manajemen member hingga payment system, semua berjalan smooth dalam satu platform yang reliable.
                </p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-4xl lg:text-5xl font-bold text-primary mb-2">10+</div>
                    <div class="text-gray-300">Organisasi Mahasiswa</div>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-4xl lg:text-5xl font-bold text-primary mb-2">95+</div>
                    <div class="text-gray-300">Event Terpublikasi</div>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-4xl lg:text-5xl font-bold text-primary mb-2">33+</div>
                    <div class="text-gray-300">Prestasi Tercatat</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary" data-aos="zoom-in">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl lg:text-4xl font-bold text-black mb-6 font-mono">
                Siap Upgrade Manajemen UKM?
            </h2>
            <p class="text-xl text-gray-800 mb-8">
                Mulai kelola member, posting berita, buat event, hingga terima pembayaran dalam satu platform. 
                Semua kebutuhan UKM, satu solusi lengkap.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/admin-panel') }}" class="bg-accent text-primary hover:bg-gray-800 px-8 py-3 text-lg font-bold transition duration-150 inline-flex items-center justify-center border-2 border-black hard-shadow">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Mulai Kelola UKM
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-accent text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-graduation-cap text-primary text-2xl mr-2"></i>
                        <span class="text-2xl font-bold font-mono">Simiko</span>
                    </div>
                    <p class="text-gray-300 mb-4 max-w-md">
                        Platform lengkap untuk manajemen Unit Kegiatan Mahasiswa dengan fitur berita, event, payment, dan member management.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-primary transition duration-300">
                            <i class="fab fa-github text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition duration-300">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition duration-300">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4 font-mono">Link Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/admin') }}" class="text-gray-300 hover:text-primary transition duration-300">Panel Admin</a></li>
                        <li><a href="{{ url('/admin-panel') }}" class="text-gray-300 hover:text-primary transition duration-300">Panel UKM</a></li>
                        <li><a href="{{ url('/api/documentation') }}" class="text-gray-300 hover:text-primary transition duration-300">Dokumentasi API</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4 font-mono">Fitur</h3>
                    <ul class="space-y-2">
                        <li><span class="text-gray-300">Manajemen UKM</span></li>
                        <li><span class="text-gray-300">Manajemen Event</span></li>
                        <li><span class="text-gray-300">Pendaftaran Member</span></li>
                        <li><span class="text-gray-300">Manajemen Konten</span></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400">
                    © {{ date('Y') }} Simiko. Dibangun dengan Laravel & Filament. Hak cipta dilindungi.
                </p>
            </div>
        </div>
    </footer>

    <!-- Smooth Scroll Script -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init({
        duration: 800,
        once: true
      });
    </script>
</body>
</html>
