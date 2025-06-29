# Event Participant Limits API Documentation

## Overview

This document describes the enhanced API endpoints that now include participant limit functionality for paid events. The system supports setting maximum participant limits and provides real-time availability information. **Payment configurations are now automatically activated/deactivated based on event capacity.**

## Features

-   **Maximum Participants**: Set limits on event capacity
-   **Real-time Availability**: Live tracking of available slots
-   **Registration Enforcement**: Prevent registrations when events are full
-   **Flexible Limits**: Support for both limited and unlimited events
-   **Automatic Status Management**: Payment configurations automatically become inactive when events reach capacity

## Automatic Status Management

Payment configurations no longer have manual active/inactive toggles. Instead, they are automatically managed:

### **Active Conditions**

-   Event has unlimited participants (`max_participants` is null)
-   Event has available slots (current registrations < max participants)
-   No event associated with the payment configuration

### **Inactive Conditions**

-   Event has reached maximum capacity (current registrations >= max participants)

This ensures that users cannot register for events that are already full, providing a better user experience and preventing overbooking.

## API Endpoints

### 1. Get All Feeds (Enhanced)

**Endpoint:** `GET /api/feed`

**Description:** Retrieve all feeds with participant information for paid events.

**New Response Fields for Paid Events:**

```json
{
    "success": true,
    "data": {
        "feeds": {
            "event": [
                {
                    "id": 11,
                    "type": "event",
                    "title": "Laravel Workshop",
                    "image_url": "http://localhost:8000/storage/feeds/sample.jpg",
                    "created_at": "2024-01-15T10:00:00.000000Z",
                    "is_paid": true,
                    "max_participants": 100,
                    "current_registrations": 75,
                    "available_slots": 25,
                    "is_full": false,
                    "ukm": {
                        "id": 2,
                        "name": "HMTE",
                        "logo_url": "http://localhost:8000/storage/ukms/logo.jpg"
                    }
                }
            ]
        }
    }
}
```

**Field Descriptions:**

-   `max_participants`: Maximum number of participants allowed (`null` if unlimited)
-   `current_registrations`: Current number of registered participants
-   `available_slots`: Number of available slots remaining (`null` if unlimited)
-   `is_full`: Boolean indicating if the event has reached capacity

### 2. Get Specific Feed (Enhanced)

**Endpoint:** `GET /api/feed/{id}`

**Description:** Retrieve detailed information about a specific feed with participant data.

**Enhanced Response for Paid Events:**

```json
{
    "success": true,
    "data": {
        "id": 11,
        "type": "event",
        "title": "Laravel Workshop",
        "content": "Join us for an exciting Laravel workshop...",
        "image_url": "http://localhost:8000/storage/feeds/sample.jpg",
        "event_date": "2024-07-15",
        "event_type": "Workshop",
        "location": "Room A101",
        "is_paid": true,
        "amount": 50000,
        "link": "https://payment.example.com/pay/11",
        "max_participants": 100,
        "current_registrations": 75,
        "available_slots": 25,
        "is_full": false,
        "ukm": {
            "id": 2,
            "name": "HMTE",
            "alias": "hmte",
            "logo_url": "http://localhost:8000/storage/ukms/logo.jpg"
        },
        "created_at": "2024-01-15T10:00:00.000000Z"
    }
}
```

### 3. Get Events Only (Enhanced)

**Endpoint:** `GET /api/events`

**Description:** Retrieve events only with participant information for paid events.

**Response:** Same format as `/api/feed` but filtered to events only.

### 4. Create Payment Transaction (Enhanced)

**Endpoint:** `POST /api/payment/transaction`

**Description:** Create a new payment transaction with participant limit validation.

**Request Body:**

```json
{
    "payment_configuration_id": 1,
    "feed_id": 11,
    "custom_data": {},
    "custom_files": {}
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "Transaction created successfully",
    "data": {
        "transaction": {
            "id": 123,
            "transaction_id": "TXN-HMTE-1234567890-1234",
            "amount": 50000,
            "status": "pending",
            "expires_at": "2024-01-22T10:00:00.000000Z",
            "payment_configuration": {
                "name": "Workshop Registration",
                "description": "Registration fee for Laravel workshop",
                "payment_methods": []
            },
            "event": {
                "id": 11,
                "title": "Laravel Workshop",
                "event_date": "2024-07-15"
            }
        }
    }
}
```

**Error Response - Event Full (400):**

```json
{
    "success": false,
    "message": "Event has reached maximum participant limit",
    "data": {
        "max_participants": 100,
        "current_registrations": 100,
        "available_slots": 0
    }
}
```

## Configuration

### Setting Maximum Participants

Maximum participants can be configured directly when creating or editing events:

1. Navigate to **Content Management > Posts & Events**
2. Create a new event or edit an existing event
3. In the **Event Information** section, set the **Maximum Participants** field
4. Leave blank for unlimited participants

The field is only visible for event-type content and is stored directly in the feeds table.

**Note:** Payment configurations automatically become inactive when an event reaches its participant limit, so there's no need to manually manage the active/inactive status.

### Admin Panel Capacity Overview

The **Payment Management > Payment Methods** table now displays comprehensive capacity information:

#### **New Columns Added:**

-   **Current Registrations**: Badge showing number of current registrations
-   **Max Participants**: Badge showing maximum allowed participants or "Unlimited"
-   **Available Slots**: Badge with color coding:
    -   🟢 Green: Plenty of slots available
    -   🟡 Yellow: 20% or fewer slots remaining
    -   🔴 Red: Event is full
-   **Capacity**: Visual progress bar showing:
    -   Current/Max registration counts
    -   Percentage filled
    -   Color-coded progress bar (green → yellow → red)

#### **Enhanced Status Column:**

-   Tooltips show detailed availability information
-   "Available - X slots remaining" for limited events
-   "Available - Unlimited capacity" for unlimited events
-   "Event is full - Registration closed" for capacity events

#### **New Filter:**

-   **Availability Status** filter to show:
    -   Available for registration
    -   Full or unavailable
    -   All configurations

### Field Location in Database

The `max_participants` value is stored directly in the `feeds` table as a dedicated column:

```sql
-- feeds table structure
feeds (
    id,
    title,
    content,
    type,
    is_paid,
    max_participants, -- Integer field (nullable)
    ...
)
```

## Usage Examples

### Check Event Availability

```javascript
// Get event details
const response = await fetch("/api/feed/11");
const event = response.data;

if (event.is_paid && event.max_participants) {
    if (event.is_full) {
        console.log("Event is full!");
    } else {
        console.log(`${event.available_slots} slots remaining`);
    }
} else {
    console.log("Unlimited capacity");
}
```

### Handle Registration Attempt

```javascript
// Attempt to register
try {
    const response = await fetch("/api/payment/transaction", {
        method: "POST",
        body: JSON.stringify({
            payment_configuration_id: 1,
            feed_id: 11,
        }),
    });

    if (response.success) {
        console.log("Registration successful");
    }
} catch (error) {
    if (error.message.includes("maximum participant limit")) {
        console.log("Event is full, registration failed");
    }
}
```

## Implementation Details

### Backend Logic

1. **Availability Check**: Real-time calculation based on current registrations
2. **Registration Prevention**: Validation before transaction creation
3. **Count Method**: Uses `getTotalRegistrationsCount()` from Feed model
4. **Unlimited Events**: `null` values indicate unlimited capacity
5. **Automatic Status**: Payment configurations use `getIsActiveAttribute()` to determine status dynamically
6. **No Manual Toggle**: Removed manual active/inactive controls from admin panel
7. **Proof of Payment Logic**: Slots are only considered "taken" after proof of payment is uploaded or transaction is marked as paid

### Registration Counting Logic

The system now uses a more sophisticated counting mechanism:

-   **Confirmed Registrations**: Transactions with status `paid` OR `pending` with proof of payment uploaded
-   **Available Slots**: Only confirmed registrations count against the maximum participant limit
-   **Pending Without Proof**: Transactions in `pending` status without proof of payment do NOT count against the limit

This ensures that:

-   Users can register and create transactions without immediately consuming slots
-   Slots are only reserved after proof of payment is uploaded
-   Admins can see detailed breakdown of registration status

### Database Queries

The system uses efficient queries to count registrations:

```php
// Count confirmed registrations (paid + pending with proof)
$feed->transactions()
    ->where(function ($query) {
        $query->where('status', 'paid')
            ->orWhere(function ($q) {
                $q->where('status', 'pending')
                  ->whereNotNull('proof_of_payment');
            });
    })
    ->count()

// Count pending transactions with proof uploaded
$feed->getPendingWithProofCount()

// Count pending transactions without proof
$feed->getPendingWithoutProofCount()
```

### Performance Considerations

-   Participant counts are calculated on-demand
-   Consider caching for high-traffic events
-   Indexes on transaction status and feed_id for performance
-   **Optimized Admin Panel**: Eager loading of feeds relationship reduces database queries
-   **Efficient Filtering**: Database-level filtering for availability status
-   **Color-coded UI**: Instant visual feedback on capacity status

## Error Handling

### Common Error Scenarios

1. **Event Full**: Returns 400 with capacity details
2. **Invalid Event**: Returns 400 for non-existent or non-paid events
3. **Configuration Missing**: Graceful handling of missing payment configs

### Error Response Format

All error responses follow the standard API response format:

```json
{
    "success": false,
    "message": "Error description",
    "data": null // or error-specific data
}
```

## Testing

### Manual Testing Steps

1. Create a paid event with max participants = 5
2. Register 5 participants
3. Attempt 6th registration - should fail
4. Check API responses show correct counts

### API Testing

Use the generated Swagger documentation at `/api/documentation` to test all endpoints with the new participant limit fields.
