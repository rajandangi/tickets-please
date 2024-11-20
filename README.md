## Tickets Please
A simple ticketing system API that allows users to create, view, update and delete tickets.

## Postman Collection
[Postman Collection](https://www.postman.com/rajandangi/tickets-please)
![Postman Collection](/public/docs/images/postmancollection.png)

## Resources
- [A Specification for building APIS in JSON](https://jsonapi.org/)
- [Eloquent: API Resources](https://laravel.com/docs/11.x/eloquent-resources)
- [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum)
- [Scribe API Documentation Generator](https://scribe.knuckles.wtf/)

## Installation
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
The API documentation can be found at `/docs` route. https://tickets-please.test/docs
![API Documentation](/public/docs/images/Documentation.png)