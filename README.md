## Tickets Please
Welcome to Tickets Please! This is a simple ticketing system API built with Laravel, designed to help you master best practices in API development. It comes packed with features like user management, ticket authorization, error handling, and comprehensive API documentation.

## Postman Collection
Check out our [Postman Collection](https://www.postman.com/rajandangi/tickets-please) to quickly get started with the API.
![Postman Collection](/public/docs/images/postmancollection.png)

## Resources
Here are some great resources to help you along the way:
- [A Specification for building APIs in JSON](https://jsonapi.org/)
- [Eloquent: API Resources](https://laravel.com/docs/11.x/eloquent-resources)
- [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum)
- [Scribe API Documentation Generator](https://scribe.knuckles.wtf/)

## Best Practices Implemented
- API URL Design
- Setting up Versioning in API URLs
- Implementing token-based authentication and revoking authentication tokens
- Designing API response payloads as per JSON API specification https://jsonapi.org
- Creating resources with POST requests
- Resource updates with PATCH and PUT requests
- Conditionally omitting and including data in API responses
- Sorting and filtering in API responses
- Granular permissions on user tokens
- User Access authorization policies on resources based on roles and permissions
- Principle of least privilege on validation rules
- Uniform error handling
- API documentation


## Installation
Getting started is easy! Just follow these steps:
1. Clone the repository
```bash
git clone
```
2. Install dependencies
```bash
composer install
```
3. Create a `.env` file
```bash
cp .env.example .env
```
4. Generate an application key
```bash
php artisan key:generate
```
5. Create a database and update the `.env` file with your database credentials
6. Run the migrations
```bash
php artisan migrate
```
7. Seed the database
```bash
php artisan db:seed
```
8. Start the server and you're good to go

## Other Commands
1. To generate API documentation
```bash
php artisan scribe:generate
```

## Documentation
The API documentation can be found at `/docs` route.
![API Documentation](/public/docs/images/Documentation.png)