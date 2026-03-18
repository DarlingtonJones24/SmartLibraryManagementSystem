# Smart Library

## Student
- Name: Darlington Jones
- Student number: 711336

## What it is
I built Smart Library as a library management web application. Members can browse books, borrow books, reserve unavailable books, and track their own loans. Admin users can manage books, loans, and reservations.

## To run the project
1. Run `docker-compose up`
2. Open `http://localhost`
3. to view my database, you can search `http://localhost:8080`

## The login Accounts
### Member
- Email: darlingtonjones15@gmail.com
- Password: Darlington@12

### Admin
- Email: danielbreczinski30@gmail.com
- Password: password123

## Main files
- Entry point and routing: `app/public/index.php`
- Controllers: `app/src/Controllers`
- Services: `app/src/Services`
- Repositories: `app/src/Repository`
- Views: `app/src/Views`
- Database export: `SmartLibraryManagementSystem.sql`

## What i paid attention to
- MVC is used throughout the project with controllers, services, repositories, and views separated.
- Repositories contain the PDO database queries.
- Services contain the business rules for borrowing, returning, and reservations.
- JSON API endpoints are available through the routes in `app/public/index.php` and the API controllers.
- JavaScript uses the API to update parts of the page without a full refresh.

## Security
- Passwords are hashed.
- PDO prepared statements are used for database queries.
- Sessions are used for login.
- Role checks are used for member and admin actions.

## Data protection and WCAG
- Only basic user data needed for the system is stored.
- Users only see their own loans and reservations.
- Forms use labels and the pages use clear headings and readable structure.
- The layout uses Bootstrap and is designed to stay usable on different screen sizes.
