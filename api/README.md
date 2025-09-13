MetaTicket - Online Ticket Booking System
Overview
MetaTicket is a comprehensive online ticket booking system designed for bus travel. This project allows users to search, book, and manage bus tickets, while providing agencies with tools to oversee bookings and transactions. The system includes user authentication, ticket purchasing, booking confirmation, and wallet management features.
Features

User Authentication: Secure login and session management for users.
Ticket Search and Booking: Search for available tickets based on date, origin, and destination, with an option to buy tickets.
Boarding Point Selection: Users can select boarding and dropping points with scheduled times.
Booking Confirmation: Detailed confirmation page with QR code generation for ticket verification.
Agency Management: Agencies can view, confirm, or cancel bookings.
Wallet System: Integrated wallet for users and admin with transaction tracking.
Responsive Design: Optimized for both desktop and mobile devices.

Technologies Used

Backend: PHP with MySQL database.
Frontend: HTML, CSS, JavaScript, with libraries like Font Awesome and Google Fonts.
Database: MariaDB (via phpMyAdmin dump).
Assets: Images stored in ticketexchange/assets/images/.

Installation
Prerequisites

Web server (e.g., Apache)
PHP 8.2.12 or higher
MariaDB 10.4.32 or higher
Composer (optional for dependency management)

Steps

Clone the Repository
git clone <repository-url>
cd ticketexchange

Set Up the Database

Import the ticket_booking.sql file into your MariaDB database using phpMyAdmin or the command line:mysql -u root -p ticket < ticket_booking.sql

Update config.php with your database credentials.

Configure the Project

Update the file paths in config.php to match your server environment.
Ensure the ticketexchange/assets/images/ directory contains the required images.

Run the Application

Place the project in your web server's root directory (e.g., /var/www/html/).
Access it via your browser (e.g., http://localhost/ticketexchange/index.php).

File Structure

ticketexchange/x_boarding_points.php: Handles boarding point selection and form submission.
ticketexchange/x_booking_success.php: Displays booking confirmation details.
ticketexchange/x_bookings.php: Agency interface for managing bookings.
ticketexchange/x_insert_bookings.php: Processes booking data and inserts into the database.
ticketexchange/x_ticket_template.php: Generates e-ticket details.
ticketexchange/xticket.php: Lists available tickets for search and purchase.
ticketexchange/xticketbuy.php: Facilitates ticket purchase with passenger details.
ticketexchange/assets/images/: Directory for project images.
ticket_booking.sql: Database schema and initial data.

Usage

User Flow

Log in via newsignin.php.
Search for tickets on xticket.php.
Buy a ticket on xticketbuy.php.
Select boarding/dropping points on x_boarding_points.php.
View booking confirmation on x_booking_success.php.
Access ticket details on x_ticket_template.php.

Agency Flow

Log in as an agency and manage bookings on x_bookings.php.

Contributing
Feel free to fork this repository, submit issues, or create pull requests for enhancements.
License
This project is licensed under the MIT License - see the LICENSE file for details.
Contact
For support, email support@example.com or visit the project documentation.
