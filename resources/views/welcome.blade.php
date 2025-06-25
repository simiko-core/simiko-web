<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simiko - Student Activity Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#8b5cf6',
                        secondary: '#1f2937',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-graduation-cap text-primary text-2xl mr-2"></i>
                        <span class="text-2xl font-bold text-secondary">Simiko</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ url('/admin') }}" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                        Admin Panel
                    </a>
                    <a href="{{ url('/admin-panel') }}" class="bg-primary hover:bg-purple-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300">
                        UKM Panel
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-16 bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-secondary mb-6">
                        Manage Student 
                        <span class="text-primary">Activities</span>
                        with Ease
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Simiko is a comprehensive platform for managing student organizations (UKM), 
                        events, memberships, and activities. Streamline your campus life management.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ url('/api/documentation') }}" class="bg-primary hover:bg-purple-600 text-white px-8 py-3 rounded-lg text-lg font-semibold transition duration-300 text-center">
                            <i class="fas fa-code mr-2"></i>
                            API Documentation
                        </a>
                        <a href="#features" class="border-2 border-primary text-primary hover:bg-primary hover:text-white px-8 py-3 rounded-lg text-lg font-semibold transition duration-300 text-center">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div class="relative">
                        <div class="bg-white rounded-2xl shadow-2xl p-8 transform rotate-3 hover:rotate-0 transition duration-500">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-blue-100 rounded-lg p-4 text-center">
                                    <i class="fas fa-users text-blue-600 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-blue-800">UKM Management</p>
                                </div>
                                <div class="bg-green-100 rounded-lg p-4 text-center">
                                    <i class="fas fa-calendar text-green-600 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-green-800">Events</p>
                                </div>
                                <div class="bg-purple-100 rounded-lg p-4 text-center">
                                    <i class="fas fa-trophy text-purple-600 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-purple-800">Achievements</p>
                                </div>
                                <div class="bg-yellow-100 rounded-lg p-4 text-center">
                                    <i class="fas fa-user-plus text-yellow-600 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-yellow-800">Registration</p>
                                </div>
                                <div class="bg-red-100 rounded-lg p-4 text-center">
                                    <i class="fas fa-rss text-red-600 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-red-800">Content</p>
                                </div>
                                <div class="bg-indigo-100 rounded-lg p-4 text-center">
                                    <i class="fas fa-chart-bar text-indigo-600 text-2xl mb-2"></i>
                                    <p class="text-sm font-semibold text-indigo-800">Analytics</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-secondary mb-4">
                    Powerful Features for Student Organizations
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to manage student activities, from organization profiles to event management and member registration.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- UKM Management -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition duration-300">
                    <div class="bg-blue-100 rounded-lg w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-building text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4">UKM Management</h3>
                    <p class="text-gray-600 mb-4">
                        Comprehensive organization management with profiles, categories, and administrative controls.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Organization profiles & categories</li>
                        <li>• Admin role management</li>
                        <li>• Registration controls</li>
                    </ul>
                </div>

                <!-- Event Management -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition duration-300">
                    <div class="bg-green-100 rounded-lg w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-calendar-alt text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4">Event Management</h3>
                    <p class="text-gray-600 mb-4">
                        Create and manage events with detailed information, pricing, and payment methods.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Online & offline events</li>
                        <li>• Payment integration</li>
                        <li>• Event categories</li>
                    </ul>
                </div>

                <!-- Member Registration -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition duration-300">
                    <div class="bg-purple-100 rounded-lg w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-user-plus text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4">Member Registration</h3>
                    <p class="text-gray-600 mb-4">
                        Streamlined membership application process with approval workflows.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Application management</li>
                        <li>• Approval workflows</li>
                        <li>• Member tracking</li>
                    </ul>
                </div>

                <!-- Content Management -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition duration-300">
                    <div class="bg-yellow-100 rounded-lg w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-rss text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4">Content Management</h3>
                    <p class="text-gray-600 mb-4">
                        Publish posts, announcements, and manage promotional banners effectively.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Posts & announcements</li>
                        <li>• Banner management</li>
                        <li>• Media galleries</li>
                    </ul>
                </div>

                <!-- Achievement Tracking -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition duration-300">
                    <div class="bg-red-100 rounded-lg w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-trophy text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4">Achievement Tracking</h3>
                    <p class="text-gray-600 mb-4">
                        Showcase organization achievements, awards, and recognition.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Achievement galleries</li>
                        <li>• Award documentation</li>
                        <li>• Success stories</li>
                    </ul>
                </div>

                <!-- API Integration -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition duration-300">
                    <div class="bg-indigo-100 rounded-lg w-16 h-16 flex items-center justify-center mb-6">
                        <i class="fas fa-code text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4">API Integration</h3>
                    <p class="text-gray-600 mb-4">
                        Complete REST API with comprehensive documentation for mobile app integration.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• RESTful API endpoints</li>
                        <li>• Swagger documentation</li>
                        <li>• Mobile app ready</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-20 bg-secondary text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold mb-4">
                    Trusted by Student Organizations
                </h2>
                <p class="text-xl text-gray-300">
                    Powering campus life management across institutions
                </p>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-bold text-primary mb-2">10+</div>
                    <div class="text-gray-300">Student Organizations</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-bold text-primary mb-2">95+</div>
                    <div class="text-gray-300">Published Events</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-bold text-primary mb-2">33+</div>
                    <div class="text-gray-300">Achievements Tracked</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-bold text-primary mb-2">100%</div>
                    <div class="text-gray-300">API Coverage</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-primary to-purple-500">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">
                Ready to Streamline Your Student Activities?
            </h2>
            <p class="text-xl text-purple-100 mb-8">
                Join the modern way of managing student organizations with Simiko's powerful platform.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/admin') }}" class="bg-white text-primary hover:bg-gray-100 px-8 py-3 rounded-lg text-lg font-semibold transition duration-300 inline-flex items-center justify-center">
                    <i class="fas fa-cog mr-2"></i>
                    Admin Dashboard
                </a>
                <a href="{{ url('/api/documentation') }}" class="border-2 border-white text-white hover:bg-white hover:text-primary px-8 py-3 rounded-lg text-lg font-semibold transition duration-300 inline-flex items-center justify-center">
                    <i class="fas fa-book mr-2"></i>
                    API Docs
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-secondary text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-graduation-cap text-primary text-2xl mr-2"></i>
                        <span class="text-2xl font-bold">Simiko</span>
                    </div>
                    <p class="text-gray-300 mb-4 max-w-md">
                        Student Activity Management System - Empowering campus life through technology.
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
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/admin') }}" class="text-gray-300 hover:text-primary transition duration-300">Admin Panel</a></li>
                        <li><a href="{{ url('/admin-panel') }}" class="text-gray-300 hover:text-primary transition duration-300">UKM Panel</a></li>
                        <li><a href="{{ url('/api/documentation') }}" class="text-gray-300 hover:text-primary transition duration-300">API Documentation</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Features</h3>
                    <ul class="space-y-2">
                        <li><span class="text-gray-300">UKM Management</span></li>
                        <li><span class="text-gray-300">Event Management</span></li>
                        <li><span class="text-gray-300">Member Registration</span></li>
                        <li><span class="text-gray-300">Content Management</span></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400">
                    © {{ date('Y') }} Simiko. Built with Laravel & Filament. All rights reserved.
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

        // Add scroll effect to navigation
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.classList.add('bg-opacity-95', 'backdrop-blur-sm');
            } else {
                nav.classList.remove('bg-opacity-95', 'backdrop-blur-sm');
            }
        });
    </script>
</body>
</html>
