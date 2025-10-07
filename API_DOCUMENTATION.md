# Logbook Mobile API Documentation

## Base URL

```
/api/v1
```

## Authentication

The API uses Laravel Sanctum for authentication. Include the bearer token in the Authorization header:

```
Authorization: Bearer {token}
```

## Response Format

All API responses follow this format:

```json
{
    "status": "success|error",
    "message": "Description of the response",
    "data": {},
    "errors": {} // Only for validation errors
}
```

## API Endpoints

### Authentication

#### Register User

-   **POST** `/register`
-   **Body:**

```json
{
    "name": "string (required)",
    "username": "string (required, unique)",
    "email": "string (required, unique)",
    "password": "string (required, min:8)",
    "gelar": "string (optional)",
    "position": "integer (optional, exists in positions)",
    "access_level": "admin|operator|viewer (required)",
    "phone_number": "string (optional)",
    "address": "string (optional)",
    "city": "string (optional)",
    "state": "string (optional)",
    "zip_code": "string (optional)",
    "country": "string (optional)"
}
```

#### Login

-   **POST** `/login`
-   **Body:**

```json
{
    "login": "string (required) - email or username",
    "password": "string (required)",
    "device_name": "string (optional)",
    "device_info": "object (optional)"
}
```

#### Logout

-   **POST** `/logout`
-   **Headers:** Authorization: Bearer {token}

#### Get Profile

-   **GET** `/profile`
-   **Headers:** Authorization: Bearer {token}

#### Update Profile

-   **PUT** `/profile`
-   **Headers:** Authorization: Bearer {token}
-   **Body:** (same fields as register, all optional)

#### Change Password

-   **POST** `/change-password`
-   **Headers:** Authorization: Bearer {token}
-   **Body:**

```json
{
    "current_password": "string (required)",
    "new_password": "string (required, min:8)",
    "new_password_confirmation": "string (required)"
}
```

### Logbooks

#### Get All Logbooks

-   **GET** `/logbooks`
-   **Query Parameters:**
    -   `unit_id`: Filter by unit
    -   `start_date`: Filter from date (YYYY-MM-DD)
    -   `end_date`: Filter to date (YYYY-MM-DD)
    -   `shift`: Filter by shift (1,2,3)
    -   `is_approved`: Filter by approval status (true/false)
    -   `my_logbooks`: Get user's own logbooks (true/false)
    -   `sort_by`: Sort field (default: date)
    -   `sort_order`: Sort direction (asc/desc, default: desc)
    -   `per_page`: Items per page (default: 15)

#### Get Specific Logbook

-   **GET** `/logbooks/{id}`

#### Create Logbook

-   **POST** `/logbooks`
-   **Body:**

```json
{
    "unit_id": "integer (required)",
    "date": "date (required)",
    "judul": "string (required)",
    "shift": "1|2|3 (required)",
    "catatan": "string (optional)",
    "items": [
        {
            "judul": "string (required)",
            "catatan": "string (optional)",
            "tanggal_kegiatan": "date (required)",
            "mulai": "time HH:mm (required)",
            "selesai": "time HH:mm (required)",
            "tools": "string (optional)",
            "teknisi": "integer (required, user_id)"
        }
    ],
    "teknisi": ["array of user_ids (optional)"]
}
```

#### Update Logbook

-   **PUT** `/logbooks/{id}`
-   **Body:** (same as create, all fields optional except validations)

#### Delete Logbook

-   **DELETE** `/logbooks/{id}`

#### Approve Logbook (Admin only)

-   **POST** `/logbooks/{id}/approve`

#### Sign Logbook (Admin only)

-   **POST** `/logbooks/{id}/sign`

#### Get Logbook Statistics

-   **GET** `/logbooks-statistics`

### Logbook Items

#### Get Items for Logbook

-   **GET** `/logbooks/{logbookId}/items`

#### Get Specific Item

-   **GET** `/logbook-items/{id}`

#### Create Item

-   **POST** `/logbook-items`
-   **Body:**

```json
{
    "logbook_id": "integer (required)",
    "judul": "string (required)",
    "catatan": "string (optional)",
    "tanggal_kegiatan": "date (required)",
    "mulai": "time HH:mm (required)",
    "selesai": "time HH:mm (required)",
    "tools": "string (optional)",
    "teknisi": "integer (required, user_id)"
}
```

#### Update Item

-   **PUT** `/logbook-items/{id}`

#### Delete Item

-   **DELETE** `/logbook-items/{id}`

#### Get Items by Technician

-   **GET** `/logbook-items/by-teknisi`
-   **Query Parameters:**
    -   `teknisi_id`: Technician user ID (default: current user)
    -   `start_date`: Filter from date
    -   `end_date`: Filter to date
    -   `per_page`: Items per page

#### Get Technician Summary

-   **GET** `/teknisi-summary`
-   **Query Parameters:**
    -   `teknisi_id`: Technician user ID (default: current user)

### Units

#### Get All Units

-   **GET** `/units`

#### Get Specific Unit

-   **GET** `/units/{id}`

#### Create Unit (Admin only)

-   **POST** `/units`
-   **Body:**

```json
{
    "nama": "string (required, unique)"
}
```

#### Update Unit (Admin only)

-   **PUT** `/units/{id}`

#### Delete Unit (Admin only)

-   **DELETE** `/units/{id}`

### Users

#### Get All Users (Admin only)

-   **GET** `/users`
-   **Query Parameters:**
    -   `access_level`: Filter by access level
    -   `position`: Filter by position
    -   `search`: Search by name, email, or username
    -   `per_page`: Items per page

#### Get Specific User

-   **GET** `/users/{id}`

#### Create User (Admin only)

-   **POST** `/users`
-   **Body:** (same as register endpoint)

#### Update User (Admin only)

-   **PUT** `/users/{id}`

#### Delete User (Admin only)

-   **DELETE** `/users/{id}`

#### Get Technicians List

-   **GET** `/technicians`

#### Get Positions List

-   **GET** `/positions`

### Notifications

#### Get User Notifications

-   **GET** `/notifications`
-   **Query Parameters:**
    -   `is_read`: Filter by read status (true/false)
    -   `per_page`: Items per page

#### Get Unread Count

-   **GET** `/notifications/unread-count`

#### Create Notification (Admin only)

-   **POST** `/notifications`
-   **Body:**

```json
{
    "title": "string (required)",
    "message": "string (required)",
    "type": "string (optional)",
    "recipients": ["array of user_ids (optional, default: all users)"]
}
```

#### Mark as Read

-   **PATCH** `/notifications/{id}/mark-as-read`

#### Mark All as Read

-   **PATCH** `/notifications/mark-all-as-read`

#### Delete Notification

-   **DELETE** `/notifications/{id}`

## Error Codes

-   **200**: Success
-   **201**: Created
-   **400**: Bad Request
-   **401**: Unauthorized
-   **403**: Forbidden
-   **404**: Not Found
-   **422**: Validation Error
-   **500**: Server Error

## Sample Responses

### Successful Login Response

```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "username": "johndoe",
            "access_level": "operator",
            "position": {
                "no": 1,
                "name": "Engineer"
            }
        },
        "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz..."
    }
}
```

### Validation Error Response

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### Paginated Response

```json
{
    "status": "success",
    "data": {
        "current_page": 1,
        "data": [...],
        "first_page_url": "http://localhost/api/v1/logbooks?page=1",
        "from": 1,
        "last_page": 5,
        "last_page_url": "http://localhost/api/v1/logbooks?page=5",
        "next_page_url": "http://localhost/api/v1/logbooks?page=2",
        "path": "http://localhost/api/v1/logbooks",
        "per_page": 15,
        "prev_page_url": null,
        "to": 15,
        "total": 75
    }
}
```

## Security Notes

1. All endpoints (except register/login) require authentication
2. Admin-only endpoints check user access level
3. Users can only edit their own logbooks (unless admin)
4. Approved logbooks can only be modified by admins
5. Rate limiting is applied to prevent abuse
6. Input validation prevents SQL injection and XSS attacks

## Mobile Implementation Tips

1. Store the auth token securely in the device
2. Implement automatic token refresh mechanism
3. Cache frequently accessed data (units, positions, technicians)
4. Use pagination for large datasets
5. Implement offline sync capabilities for critical data
6. Handle network connectivity issues gracefully
7. Use proper loading states and error handling
