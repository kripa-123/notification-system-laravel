# Scalable Multi-Tenant Notification System

A robust, asynchronous notification engine built with **Laravel 10**, **MySQL**, and **Redis**. Designed for high concurrency, reliability, and observability.

---

## 🚀 Setup Instructions

### Prerequisites
* PHP 
* Composer
* MySQL & Redis 

### Installation
1. **Clone the repository:**
   git clone [https://github.com/kripa-123/notification-system-laravel.git](https://github.com/kripa-123/notification-system-laravel.git)
   cd notification-system-laravel

2. **Install dependencies:**
    composer install

3. **Environment Setup:**
    cp .env.example .env
    php artisan key:generate

4. **Database & Migrations:**
    php artisan migrate --seed

## Configuration
Required .env Variables
DB_CONNECTION=mysql
DB_DATABASE=notification_db

QUEUE_CONNECTION=redis
CACHE_STORE=redis    

## Running the System
Start the Queue Worker:
php artisan queue:work

## Testing:
    The system includes a full suite of Feature and Unit tests covering rate limiting, job retries, and API integrity.

    php artisan test  

## API Documentation 

    1. **Create Notification**
    POST /api/notifications

```json
    Payload:
        {
            "user_id": 4,
            "tenant_id":3,
            "type": "sms",
            "payload": {
                "title": "SMS",
                "body": "The is test form sms.",
                "force_fail": true
            }
        }

    Response
    {
        "message": "Notification queued successfully",
        "id": "a15c3db1-8e91-4712-a304-9cb41fa9aae2"
    }

2. **Get Notification History (with Filtering)**

    GET /api/notifications
    Query Parameters:
    User can filter the history using the following optional parameters:

    type: Filter by channel (email, sms, push)

    user_id: Filter by specific user ID.

    status: Filter by current state (pending, processing, sent, failed)

    Example Request:
    GET /api/notifications?type=email&status=sent&user_id=1

    Response
    {
    "current_page": 1,
    "data": [
        {
            "id": "a15be646-463d-4215-affb-f476bfabcf25",
            "tenant_id": 1,
            "user_id": 1,
            "type": "email",
            "payload": {
                "body": "The database is seeded and the API is live.",
                "title": "AgentCIS Demo 2"
            },
            "status": "sent",
            "retry_count": 0,
            "error_message": null,
            "created_at": "2026-03-22T06:26:14.000000Z",
            "updated_at": "2026-03-22T06:26:32.000000Z"
        }
    ],
    "first_page_url": "http://127.0.0.1:8000/api/notifications?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://127.0.0.1:8000/api/notifications?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "http://127.0.0.1:8000/api/notifications?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "http://127.0.0.1:8000/api/notifications",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
    }

3. **Get Notification Summary**
    Returns cached global statistics of notification statuses to monitor system health without heavy database overhead.

    GET /api/notifications/summary

    Response

    {
        "total_sent": 29,
        "total_failed": 2,
        "pending_queue": 3,
        "last_updated": "2026-03-22 10:32:35"
    }


## Architectural Decisions
1. Service-Oriented Architecture: Logic is decoupled into NotificationService to keep Controllers thin and maintainable.

2. Data Transfer Objects (DTOs): Enforces strict typing and data integrity between the API and the Service layer.

3. Fail-Fast Validation: Uses Laravel Form Requests to reject malformed data before reaching the business logic.


## Assumptions
1. Authentication: Requests are assumed to be authenticated (using Laravel Sanctum in this demo).

2. Infrastructure: Redis is the primary driver for both Queues and Cache for optimal performance.

3. Multi-Tenancy: Each notification is associated with a tenant_id for future data partitioning.
