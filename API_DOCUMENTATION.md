# 📚 Simiko API Documentation

Complete API documentation for the **Simiko** - Student Activity Management System.

## 🔗 Quick Links

- **Swagger UI**: 
  - Local: `http://localhost:8000/api/documentation`
  - Production: `https://simiko.rizkirmdhn.web.id/api/documentation`
- **Base URL**: 
  - Local: `http://localhost:8000/api`
  - Production: `https://simiko.rizkirmdhn.web.id/api`
- **API Version**: `1.0.0`

---

## 🔐 Authentication

All protected endpoints require Bearer token authentication.

### Headers
```http
Authorization: Bearer {your_access_token}
Content-Type: application/json
Accept: application/json
```

**Note:** For the registration endpoint (`/register`), use `multipart/form-data` instead of `application/json` to support file uploads.

---

## 📋 Endpoints Overview

### 🔑 Authentication Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `POST` | `/login` | User login | ❌ |
| `POST` | `/register` | User registration | ❌ |
| `POST` | `/logout` | User logout | ✅ |
| `GET` | `/user/profile` | Get user profile | ✅ |

### 🏛️ UKM (Unit Kegiatan Mahasiswa) Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/ukms` | Get all UKMs | ✅ |
| `GET` | `/ukms/search` | Search UKMs by name | ✅ |
| `GET` | `/ukm/{id}/profile` | Get UKM basic profile | ✅ |
| `GET` | `/ukm/{id}/profile-full` | Get UKM complete profile | ✅ |
| `POST` | `/ukm/{id}/register` | Register for UKM membership | ✅ |

### 📱 Feed Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/feed` | Get all feeds (posts & events) | ✅ |
| `GET` | `/feed/{id}` | Get specific feed item | ✅ |
| `GET` | `/posts` | Get posts only | ✅ |
| `GET` | `/events` | Get events only | ✅ |

### 🎨 Banner Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/banner` | Get active banners | ✅ |

---

## 🚀 Usage Examples

### 1. User Registration

**Local:**
```bash
curl -X POST http://localhost:8000/api/register \
  -F "name=John Doe" \
  -F "email=john@example.com" \
  -F "password=password123" \
  -F "phone=08123456789" \
  -F "img_photo=@/path/to/photo.jpg"
```

**Production:**
```bash
curl -X POST https://simiko.rizkirmdhn.web.id/api/register \
  -F "name=John Doe" \
  -F "email=john@example.com" \
  -F "password=password123" \
  -F "phone=08123456789" \
  -F "img_photo=@/path/to/photo.jpg"
```

**Note:** The `img_photo` field is optional. If you don't want to upload a photo, simply omit the `-F "img_photo=@/path/to/photo.jpg"` part.

**JavaScript/Frontend Example:**
```javascript
const formData = new FormData();
formData.append('name', 'John Doe');
formData.append('email', 'john@example.com');
formData.append('password', 'password123');
formData.append('phone', '08123456789');
// Optional: formData.append('img_photo', fileInput.files[0]);

fetch('http://localhost:8000/api/register', {
  method: 'POST',
  body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

**Response:**
```json
{
  "status": true,
  "message": "Registration successful",
  "data": {
    "access_token": "1|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "08123456789"
    }
  }
}
```

### 2. User Login

**Local:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Production:**
```bash
curl -X POST https://simiko.rizkirmdhn.web.id/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### 3. Get All UKMs

```bash
curl -X GET http://localhost:8000/api/ukms \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "status": true,
  "message": "UKM data retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Himpunan Mahasiswa Informatika",
      "logo": "logo_unit_kegiatan/hmif-logo.png",
      "unit_kegiatan_profile": [
        {
          "id": 1,
          "unit_kegiatan_id": 1,
          "description": "Student organization for computer science students"
        }
      ]
    }
  ]
}
```

### 4. Get Feeds with Filtering

```bash
# Get all feeds
curl -X GET http://localhost:8000/api/feed \
  -H "Authorization: Bearer {your_token}"

# Get only posts
curl -X GET http://localhost:8000/api/posts \
  -H "Authorization: Bearer {your_token}"

# Get only events
curl -X GET http://localhost:8000/api/events \
  -H "Authorization: Bearer {your_token}"

# Get feeds by UKM
curl -X GET "http://localhost:8000/api/feed?ukm_id=1" \
  -H "Authorization: Bearer {your_token}"

# Get events by UKM
curl -X GET "http://localhost:8000/api/events?ukm_id=1" \
  -H "Authorization: Bearer {your_token}"
```

### 5. Register for UKM Membership

```bash
curl -X POST http://localhost:8000/api/ukm/1/register \
  -H "Authorization: Bearer {your_token}" \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "status": true,
  "message": "Registration submitted successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "unit_kegiatan_id": 1,
    "status": "pending",
    "created_at": "2024-06-25T10:30:00.000000Z",
    "updated_at": "2024-06-25T10:30:00.000000Z"
  }
}
```

### 6. Get UKM Full Profile

```bash
curl -X GET http://localhost:8000/api/ukm/1/profile-full \
  -H "Authorization: Bearer {your_token}"
```

**Response:**
```json
{
  "status": true,
  "message": "UKM full profile retrieved successfully",
  "data": {
    "description": "Comprehensive description of the UKM",
    "vision": "Our vision statement",
    "mission": "Our mission statement",
    "achievements": [
      {
        "title": "Programming Competition Winner",
        "description": "Won first place in national programming competition",
        "image_url": "http://localhost:8000/storage/achievements/achievement.jpg"
      }
    ],
    "recent_posts": [
      {
        "title": "Recent Activity Post",
        "type": "post",
        "image_url": "http://localhost:8000/storage/feeds/post.jpg"
      }
    ],
    "activity_gallery": [
      {
        "image_url": "http://localhost:8000/storage/activity_galleries/gallery.jpg"
      }
    ]
  }
}
```

### 7. Search UKMs

```bash
# Search for UKMs containing "Informatika"
curl -X GET "http://localhost:8000/api/ukms/search?q=Informatika" \
  -H "Authorization: Bearer {your_token}"
```

### 8. Create Payment Transaction (Event-linked)

```bash
# Create a payment transaction for an event
curl -X POST http://localhost:8000/api/payment/create-transaction \
  -H "Authorization: Bearer {your_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "payment_configuration_id": 1,
    "feed_id": 5,
    "custom_data": {
      "student_id": "2021001",
      "phone": "08123456789"
    }
  }'
```

**Response:**
```json
{
  "status": true,
  "message": "Transaction created successfully",
  "data": {
    "transaction": {
      "id": 1,
      "transaction_id": "TXN-HMIF-1732627200-1234",
      "amount": 50000,
      "currency": "IDR",
      "status": "pending",
      "expires_at": "2024-12-02T10:30:00.000000Z",
      "payment_configuration": {
        "name": "Workshop Registration Fee",
        "description": "Registration fee for tech workshop",
        "payment_methods": [
          {
            "method": "Bank Transfer BCA",
            "account_number": "1234567890",
            "account_name": "HMIF UNS"
          }
        ]
      },
      "event": {
        "id": 5,
        "title": "Tech Workshop 2024",
        "event_date": "2024-12-25"
      }
    }
  }
}
```

### Feed Detail (Event)
```json
{
  "id": 1,
  "type": "event",
  "title": "Tech Workshop 2024",
  "content": "Join us for an exciting tech workshop...",
  "image_url": "https://example.com/image.jpg",
  "event_date": "2024-12-25",
  "event_type": "offline", // or "online"
  "location": "Building A, 3rd Floor",
  "is_paid": true,
  "payment_configuration": {
    "id": 1,
    "name": "Workshop Registration Fee",
    "description": "Registration fee for tech workshop",
    "amount": 50000,
    "currency": "IDR",
    "payment_methods": [
      {
        "method": "Bank Transfer BCA",
        "account_number": "1234567890",
        "account_name": "HMIF UNS",
        "bank_name": "Bank Central Asia"
      }
    ],
    "custom_fields": [
      {
        "label": "Student ID",
        "name": "student_id",
        "type": "text",
        "required": true
      }
    ]
  },
  "ukm": {
    "id": 1,
    "name": "Himpunan Mahasiswa Informatika",
    "alias": "HMIF",
    "logo_url": "https://example.com/logo.jpg"
  },
  "created_at": "2024-06-25T10:30:00.000000Z"
}
```

---

## 📊 Response Format

All API responses follow a consistent format:

### Success Response
```json
{
  "status": true,
  "message": "Operation successful",
  "data": {
    // Response data
  }
}
```

### Error Response
```json
{
  "status": false,
  "message": "Error message",
  "code": 400,
  "error": "ERROR_CODE",
  "errors": {
    // Validation errors (if applicable)
  }
}
```

---

## 🔍 Data Models

### User Profile
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "08123456789",
  "photo_url": "https://example.com/photo.jpg"
}
```

### Feed Summary
```json
{
  "id": 1,
  "type": "post", // or "event"
  "title": "Sample Post Title",
  "image_url": "https://example.com/image.jpg",
  "created_at": "2024-06-25T10:30:00.000000Z",
  "ukm_alias": "HMIF"
}
```

### Feed Detail (Event)
```json
{
  "id": 1,
  "type": "event",
  "title": "Tech Workshop 2024",
  "content": "Join us for an exciting tech workshop...",
  "image_url": "https://example.com/image.jpg",
  "event_date": "2024-12-25",
  "event_type": "offline", // or "online"
  "location": "Building A, 3rd Floor",
  "is_paid": true,
  "payment_configuration": {
    "id": 1,
    "name": "Workshop Registration Fee",
    "description": "Registration fee for tech workshop",
    "amount": 50000,
    "currency": "IDR",
    "payment_methods": [
      {
        "method": "Bank Transfer BCA",
        "account_number": "1234567890",
        "account_name": "HMIF UNS",
        "bank_name": "Bank Central Asia"
      }
    ],
    "custom_fields": [
      {
        "label": "Student ID",
        "name": "student_id",
        "type": "text",
        "required": true
      }
    ]
  },
  "ukm": {
    "id": 1,
    "name": "Himpunan Mahasiswa Informatika",
    "alias": "HMIF",
    "logo_url": "https://example.com/logo.jpg"
  },
  "created_at": "2024-06-25T10:30:00.000000Z"
}
```

---

## 🚨 Error Codes

| Code | Error Type | Description |
|------|------------|-------------|
| `400` | `BAD_REQUEST` | Invalid request |
| `401` | `UNAUTHORIZED` | Authentication required |
| `403` | `FORBIDDEN` | Access denied |
| `404` | `NOT_FOUND` | Resource not found |
| `422` | `VALIDATION_ERROR` | Validation failed |
| `500` | `SERVER_ERROR` | Internal server error |

---

## 🎯 Query Parameters

### Feed Endpoints
- `type`: Filter by feed type (`post` or `event`)
- `ukm_id`: Filter by UKM ID

### Search Endpoints
- `q`: Search query string

---

## 🔧 Development & Testing

### Start Development Server
```bash
php artisan serve --port=8000
```

### Generate API Documentation
```bash
php artisan l5-swagger:generate
```

### Access Swagger UI
- **Local**: `http://localhost:8000/api/documentation`
- **Production**: `https://simiko.rizkirmdhn.web.id/api/documentation`

---

## 📝 Sample Data

The API comes with comprehensive sample data including:

- **10 UKMs**: HMIF, HMTE, HMTM, HMTS, UKM Foto, UKM Musik, UKM Sport, UKM PA, UKM Robot, UKM Debat
- **31 Users**: 1 super admin + 10 UKM admins + 20 regular users
- **95 Feeds**: Posts and events with realistic content
- **33 Achievements**: Various awards and recognitions
- **65 Activity Gallery Items**: Images with captions
- **5 Active Banners**: Promotional banners

### Test Credentials

**Super Admin:**
- Email: `admin@example.com`
- Password: `password123`

**Regular User:**
- Email: `user1@example.com`
- Password: `password123`

---

## 🛡️ Security

- All endpoints use Laravel Sanctum for authentication
- Tokens expire based on configuration
- CORS is configured for API access
- Input validation on all endpoints
- SQL injection protection via Eloquent ORM

---

## 📞 Support

For questions or issues:
1. Check the Swagger UI documentation
2. Review this documentation
3. Check Laravel logs in `storage/logs/`

---

**Happy coding!** 🚀 