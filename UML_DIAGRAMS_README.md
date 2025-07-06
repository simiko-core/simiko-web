# 📊 Simiko UML Diagrams

This document provides comprehensive UML diagrams for the **Simiko Student Activity Management System**, a Laravel-based platform for managing student organizations (UKMs) and their activities.

## 📋 Overview

Simiko is a comprehensive student activity management system that provides:

-   **User Management**: Student registration, authentication, and profile management
-   **UKM Management**: Organization profiles, member registration, and administration
-   **Content Management**: Posts, events, and banner management
-   **Event Registration**: Paid event registration with payment processing
-   **Payment System**: Manual payment processing with proof upload
-   **Achievement & Gallery**: Showcase UKM achievements and activities
-   **Administrative Functions**: System-wide management and reporting

## 🎯 Use Case Diagram

**File**: `simiko_use_case_diagram.puml`

### Actors

1. **Student**: Regular users who can browse UKMs, register for events, and manage their profiles
2. **UKM Admin**: Organization administrators who manage their UKM's content and members
3. **Super Admin**: System administrators with full access to all features
4. **Guest User**: Anonymous users who can register for paid events without creating accounts
5. **Payment System**: External system for payment processing

### Key Use Cases

#### Authentication & User Management

-   Register Account, Login, Logout, View/Update Profile

#### UKM Management

-   View/Search UKMs, View UKM Profiles, Register for Membership, Manage UKM Profile/Members

#### Content Management

-   View Feeds/Posts/Events, Create/Edit/Delete Content, Manage Banners

#### Event Registration & Payment

-   Register for Events, Upload Payment Proof, Track Status, Manage Payment Configurations

#### Achievement & Gallery

-   View/Manage Achievements, View/Manage Activity Gallery

#### Administrative Functions

-   Manage Users/UKMs, View Statistics, Manage Transactions, Generate Reports

## 🏗️ Class Diagram

**File**: `simiko_class_diagram.puml`

### Core Classes

#### User Management

-   **User**: Core user entity with authentication and profile data
-   **Admin**: Links users to UKMs for administrative access
-   **PendaftaranAnggota**: UKM membership registrations

#### UKM Management

-   **UnitKegiatan**: Main UKM entity with basic information
-   **UnitKegiatanProfile**: Detailed UKM profiles with vision/mission
-   **Achievement**: UKM achievements and awards
-   **ActivityGallery**: UKM activity photos and media

#### Content Management

-   **Feed**: Unified content model for posts and events
-   **Banner**: Promotional banners linked to feeds

#### Payment System

-   **PaymentConfiguration**: Payment method and field configurations
-   **PaymentTransaction**: Payment records and status tracking
-   **AnonymousEventRegistration**: Guest registrations for events

#### Controllers

-   **authController**: Authentication and user management
-   **ukmController**: UKM-related operations
-   **feedController**: Content management
-   **paymentController**: Payment processing
-   **bannerController**: Banner management
-   **EventRegistrationController**: Public event registration flow

### Key Relationships

1. **User** → **Admin** (1:1): Users can be UKM administrators
2. **User** → **PendaftaranAnggota** (1:many): Users can register for multiple UKMs
3. **UnitKegiatan** → **Feed** (1:many): UKMs create multiple content items
4. **Feed** → **PaymentTransaction** (1:many): Events generate payment transactions
5. **PaymentConfiguration** → **PaymentTransaction** (1:many): Configurations create transactions
6. **AnonymousEventRegistration** → **PaymentTransaction** (1:many): Guest registrations create transactions

## 🔄 System Architecture

### API Endpoints

#### Authentication

-   `POST /api/login` - User login
-   `POST /api/register` - User registration
-   `POST /api/logout` - User logout
-   `GET /api/user/profile` - Get user profile

#### UKM Management

-   `GET /api/ukms` - List all UKMs
-   `GET /api/ukms/search` - Search UKMs
-   `GET /api/ukm/{id}/profile` - Get UKM profile
-   `GET /api/ukm/{id}/profile-full` - Get complete UKM profile
-   `POST /api/ukm/{id}/register` - Register for UKM membership

#### Content Management

-   `GET /api/feed` - Get all feeds
-   `GET /api/feed/{id}` - Get specific feed
-   `GET /api/posts` - Get posts only
-   `GET /api/events` - Get events only
-   `GET /api/banner` - Get active banners

#### Payment System

-   `GET /api/payment/configurations` - Get payment configurations
-   `POST /api/payment/create-transaction` - Create payment transaction
-   `GET /api/payment/transactions` - Get user transactions
-   `POST /api/payment/transactions/{id}/upload-proof` - Upload payment proof

### Public Event Registration

-   `GET /event/register/{token}` - Show registration form
-   `POST /event/register/{token}` - Submit registration
-   `GET /event/{token}/payment/{transactionId}` - Show payment page
-   `POST /event/{token}/payment/{transactionId}/upload-proof` - Upload proof
-   `GET /event/{token}/status/{transactionId}` - Check status
-   `GET /event/{token}/receipt/{transactionId}` - Download receipt

## 🎨 Key Features

### 1. Multi-Role Access Control

-   **Students**: Browse, register, manage profiles
-   **UKM Admins**: Manage their organization's content and members
-   **Super Admins**: Full system access and management

### 2. Flexible Content Management

-   **Posts**: Regular content updates
-   **Events**: Time-based activities with optional payment
-   **Banners**: Promotional content

### 3. Advanced Payment System

-   **Manual Payment Processing**: Bank transfer, e-wallet support
-   **Custom Fields**: Configurable registration forms
-   **File Uploads**: Proof of payment and custom documents
-   **Status Tracking**: Real-time payment status updates

### 4. Event Registration

-   **Guest Registration**: No account required for event registration
-   **Capacity Management**: Participant limits and availability tracking
-   **Token-Based Access**: Secure registration links

### 5. Comprehensive UKM Profiles

-   **Basic Info**: Name, alias, category, logo
-   **Detailed Profiles**: Vision, mission, description
-   **Achievements**: Awards and recognitions
-   **Activity Gallery**: Photos and media
-   **Recent Content**: Latest posts and events

## 🔧 Technical Implementation

### Database Design

-   **Eloquent ORM**: Laravel's ORM for database interactions
-   **Relationships**: Well-defined foreign key relationships
-   **Scopes**: Automatic data filtering based on user roles
-   **Observers**: Automatic model event handling

### Security Features

-   **Laravel Sanctum**: API authentication
-   **Role-Based Access**: Spatie Permission package
-   **File Validation**: Secure file upload handling
-   **CSRF Protection**: Cross-site request forgery protection

### Performance Optimizations

-   **Eager Loading**: Prevents N+1 query problems
-   **Database Indexes**: Optimized query performance
-   **Caching**: API response caching
-   **File Storage**: Efficient file management

## 📊 Data Flow

### Event Registration Flow

1. **UKM Admin** creates paid event with payment configuration
2. **System** generates unique registration token
3. **Guest/Student** accesses registration link
4. **System** creates anonymous registration record
5. **System** creates payment transaction
6. **Guest** uploads payment proof
7. **UKM Admin** verifies payment
8. **System** updates transaction status

### UKM Management Flow

1. **Super Admin** creates UKM
2. **Super Admin** assigns UKM Admin
3. **UKM Admin** manages profile and content
4. **Students** browse and register for UKM
5. **UKM Admin** approves/rejects registrations

## 🚀 Future Enhancements

### Planned Features

-   **Real-time Notifications**: WebSocket-based updates
-   **Mobile App**: Native iOS/Android applications
-   **Payment Gateway Integration**: Automated payment processing
-   **Advanced Analytics**: Detailed reporting and insights
-   **Email Campaigns**: Automated communication system

### Technical Improvements

-   **API Versioning**: Backward-compatible API updates
-   **Rate Limiting**: API usage throttling
-   **Microservices**: Service-oriented architecture
-   **Containerization**: Docker deployment support

## 📝 Usage Instructions

### Generating Diagrams

1. **PlantUML**: Use PlantUML to render the `.puml` files
2. **Online Editor**: Use [PlantUML Online Server](http://www.plantuml.com/plantuml/uml/)
3. **VS Code**: Install PlantUML extension for live preview

### Viewing Diagrams

1. Open the `.puml` files in a PlantUML-compatible viewer
2. The diagrams will automatically render with proper styling
3. Export to PNG, SVG, or PDF formats as needed

## 🤝 Contributing

When updating the UML diagrams:

1. **Maintain Consistency**: Follow existing naming conventions
2. **Update Relationships**: Ensure all relationships are properly documented
3. **Add New Features**: Include new use cases and classes as they're implemented
4. **Version Control**: Keep diagrams in sync with codebase changes

---

**Last Updated**: December 2024  
**Version**: 1.0.0  
**Author**: Simiko Development Team
