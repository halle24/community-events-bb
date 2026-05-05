# Community Events BB (CEBB)

A web-based community event management platform built for the Christ Church, Barbados area. CEBB centralises the creation, discovery, and management of local community events — replacing informal methods like social media posts and word-of-mouth with a structured, accessible digital solution.

Built as a group project for COMP 3375 (Software Testing & Quality) at The University of the West Indies, Cave Hill Campus.

---

## Features

### For Users
- Register and log in securely
- Browse upcoming community events
- Register for or withdraw from events
- Duplicate registration prevention

### For Administrators
- Separate admin login portal with elevated privileges
- Create, edit, and manage events (date, time, location, description)
- Input validation (e.g. past date restrictions)
- Activity log tracking for accountability and auditing

### System
- Role-based access control (user vs. admin)
- Input sanitisation (SQL injection protection)
- Responsive design — accessible on desktop, tablet, and mobile
- Activity logging with timestamps, IP addresses, and action details

---

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP |
| Database | MySQL |
| Local Server | XAMPP (Apache) |
| Version Control | Git & GitHub |

---

## Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (or any local Apache/PHP/MySQL server)
- A web browser (Chrome, Firefox, or Edge recommended)

### Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/halle24/community-events-bb.git
   ```

2. **Move the project folder**  
   Place the `community-events` folder inside your XAMPP `htdocs` directory.

3. **Import the database**  
   - Open phpMyAdmin (`http://localhost/phpmyadmin`)
   - Create a new database called `CommunityEvent`
   - Import the `CommunityEvent.sql` file

4. **Configure the database connection**  
   Update `includes/mysql_connect.php` with your local database credentials.

5. **Run the app**  
   Navigate to `http://localhost/community-events` in your browser.

---

## Database Schema

The database consists of five tables:

- **userlogin** — Registered community members
- **adminlogin** — Administrators with event management privileges
- **events** — Event details (name, date, time, location, description, status)
- **attendees** — Many-to-many relationship tracking user event registrations
- **ActivityLogs** — System-wide audit log of all user and admin actions

---

## Development Methodology

The project followed an **Agile** development approach, structured across four sprints:

| Sprint | Focus |
|---|---|
| Sprint 1 | Requirements analysis and database design |
| Sprint 2 | Authentication and role-based access control |
| Sprint 3 | Event management and user interaction features |
| Sprint 4 | UI refinement, responsiveness, and testing |

---

## Testing

A structured multi-level testing strategy was applied:

- **Unit Testing** — Validated authentication logic, input validation, and password strength enforcement
- **Integration Testing** — Confirmed correct data flow between frontend, backend, and database
- **Alpha Testing** — Evaluated real-world usability, responsiveness, and performance across devices

---

## My Role

I served as the **Lead Frontend Developer** on this project, responsible for:
- Building and structuring the user-facing interface
- Implementing event registration and filtering interactions
- Input validation on the frontend
- Ensuring responsive layout across device sizes

---

## Team

| Name | Role |
|---|---|
| Christian Young | Project Manager & Database Developer |
| Amari Mottley | Backend Developer |
| Christopher Agard | Tester |
| Goodness Ogunkola | Documentalist |
| Halle Reckord | Frontend Developer |

---

## Notes

- This project was developed in a local environment using XAMPP and is not currently deployed to a live server.
- Passwords in the SQL dump are placeholder/test values used during development only.