# Smart Library: A Web Application for Book Lending and Reservation

## Student Information
- **Name:** Darlington Jones (711336)
- **Course:** Web Development 1
- **Institution:** Inholland University of Applied Sciences
- **Project Type:** Individual Web Application Assignment

---

## What this project is about
Smart Library is my attempt to solve a very practical problem: many small school and community libraries still use paper notes, Excel sheets, or very basic systems to manage books and loans. That usually leads to confusion, lost records, and extra manual work.

This project turns that process into a clean web application where:
- members can browse, borrow, and reserve books,
- librarians can manage inventory and monitor loan activity,
- and both sides get a clearer overview of what is available, what is overdue, and what needs attention.

The goal is not just to build “another CRUD app,” but to build something that feels close to a real-world library workflow.

---

## Purpose of the application
The core purpose of Smart Library is to make library operations easier, faster, and more reliable.

Instead of manually tracking everything, the app provides:
- a centralized catalog,
- clear loan and reservation tracking,
- role-based dashboards,
- and secure user authentication.

For members, it creates a simple self-service experience.
For librarians, it reduces admin pressure and improves control over daily operations.

---

## Users and roles
Smart Library supports multiple user roles with different permissions.

### 1: Guest
- Can browse the catalog
- Cannot borrow or reserve books

### 2: Member (standard user)
- Can register and log in
- Can browse books and check availability
- Can borrow books that are available
- Can reserve books that are currently unavailable
- Can view personal dashboard data:
	- active loans
	- due dates
	- current reservations
	- overdue indicators

### 3: Librarian (admin)
- Can log in to the admin area
- Can add, edit, and remove books
- Can manage active loans
- Can process reservations
- Can mark books as returned
- Can monitor key dashboard statistics

---

## Main features

### Member features
- Catalog browsing with search and filtering
- Real-time availability display
- Borrow and reservation actions
- Personalized dashboard for loan/reservation status
- Overdue awareness through status indicators

### Librarian features
- Full book management (create, update, delete)
- Loan management workflows
- Reservation management workflows
- Dashboard insights for:
	- total books
	- total copies
	- available copies
	- active loans
	- overdue loans
	- pending/waiting reservations

---

## Technical approach

### Architecture
This project follows a layered MVC-based structure used in Web Development 1:

- **Models** represent domain entities (`Book`, `User`, `Loan`, `Reservation`)
- **Controllers** handle requests and view rendering
- **Services** contain business logic (borrowing, reservation state, workflows)
- **Repositories** contain SQL/data access

This separation keeps the code easier to test, maintain, and extend.

### Security and authentication
- Session-based login/logout
- Password hashing
- Prepared statements to reduce SQL injection risk
- Input validation
- Role-based access checks

### Front-end and usability
- Bootstrap-based UI styling
- Responsive layouts for desktop/tablet/mobile
- JavaScript enhancements for interaction and dynamic UI behavior
- Accessibility-focused markup patterns (labels, alt text, readable structure)

### Compliance goals
- **GDPR-minded design:** minimal personal data exposure
- **WCAG-aware approach:** clear labels, contrast-conscious UI, keyboard-friendly structure

---

## Complexity goals covered
To meet assignment complexity expectations, this app includes more than basic CRUD:

- Borrowing flow with due dates and overdue logic
- Reservation flow with waiting/ready states
- Role-specific dashboards and permissions
- Service + Repository layering with cleaner separation of concerns
- Practical security handling for authentication and data access

---

## Expected outcome
Smart Library is intended to provide:
- a realistic prototype for replacing manual library administration,
- a smoother borrowing/reservation experience for users,
- and a cleaner operational workflow for librarians.

It reflects real-world needs while aligning with technical, security, and accessibility expectations from the Web Development 1 course.

---

### Test accounts
Use these credentials to test both roles:

### Member account
- **Email:** darlingtonjones15@gmail.com
- **Password:** Darlington@12

### Admin account
- **Email:** danielbreczinski30@gmail.com
- **Password:** password123


