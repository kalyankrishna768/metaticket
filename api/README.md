MetaTicket - Online Ticket Booking System
Overview
MetaTicket is a comprehensive online ticket booking system designed to simplify bus travel. This project provides a robust platform for end-users to search, book, and manage bus tickets. It also includes dedicated portals for bus agencies to oversee their services and for an admin to manage the entire ecosystem.

Features
User Authentication: Secure sign-up and sign-in for users, agencies, and administrators.

Ticket Search and Booking: Users can search for available bus tickets based on origin, destination, and date.

Seat Selection: An interactive interface for selecting available seats on a bus.

Wallet System: An integrated digital wallet for users and agencies to manage funds and process transactions.

Booking Management:

Users: View past, upcoming, and canceled bookings.

Agencies: Manage their fleet of buses, routes, and new bookings in a dedicated dashboard.

Admin: Centralized control panel to manage all users, agencies, and bookings.

Ticket Selling Platform: Users can sell their tickets for upcoming journeys to other travelers.

Contact & FAQ: A support section for users to ask questions and find answers to common queries.

Responsive Design: The user interface is designed to be fully responsive, ensuring a seamless experience on both desktop and mobile devices.

Technologies Used
Backend: PHP (8.2.12+)

Database: MariaDB (10.4.32+), with a provided SQL dump for easy setup.

Frontend: HTML, CSS, and JavaScript.

Libraries: Font Awesome for icons, Google Fonts for typography.

Installation
Prerequisites
A web server (e.g., Apache, Nginx)

PHP 8.2.12 or higher

MariaDB 10.4.32 or higher

Steps
Clone the Repository
Clone the project from GitHub to your local machine or server.

git clone [https://github.com/kalyankrishna768/metaticket](https://github.com/kalyankrishna768/metaticket)

Database Setup

Create a new database named ticket.

Import the provided ticket booking.sql file into your new database using a tool like phpMyAdmin or the command line.

Configuration

Navigate to the config.php file and update the database credentials to match your setup.

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket";

Ensure all necessary files and assets are in the correct locations as per the project structure.

Run the Application
Place the project in your web server's root directory (e.g., C:\xampp\htdocs\ or /var/www/html/). You can then access the application via your browser.

File Structure
The project is structured with files organized in the root directory.

ticketexchange/
├── about.php
├── admin_bookings.php
├── admin_buses.php
├── admin_home.php
├── admin_messages.php
├── admin_wallet.php
├── agency_home.php
├── agency_wallet.php
├── assets/
│ ├── images/
│ └── style/
├── available_buses.php
├── boarding_points.php
├── bookings.php
├── busbuy.php
├── bushome.php
├── cancel.php
├── config.php
├── contact.php
├── contactDB.php
├── dashboard.php
├── details.php
├── error.php
├── explore_routes.php
├── FAQ.php
├── index.php
├── insert_bookings.php
├── manage_agency.php
├── manage_users.php
├── managebus.php
├── mybookings.php
├── myuploads.php
├── new_bookings.php
├── newsignin.php
├── newsigninDB.php
├── newsignup.php
├── newsignupDB.php
├── passenger_details.php
├── privacy.php
├── process_payment.php
├── profile.php
├── README.md
├── routes.php
├── search_bus.php
├── select_seat.php
├── sell.php
├── sellDB.php
├── success.php
├── ticket_template.php
├── ticket booking.sql
├── transaction_history.php
├── wallet.php
├── wallet_transactions.php
├── x_boarding_points.php
├── x_booking_success.php
├── x_bookings.php
├── x_insert_bookings.php
├── x_ticket_template.php
├── xticket.php
└── xticketbuy.php

Usage
User Flow
Sign In: Log in via newsignin.php or register at newsignup.php.

Search: Search for tickets on the main page (index.php) or the bus booking page (busbuy.php).

Book: Select a bus, choose your seats on select_seat.php, and enter passenger details on passenger_details.php.

Confirm: Select boarding/dropping points on boarding_points.php and proceed to payment.

View Ticket: View your booking details and e-ticket on success.php.

Agency Flow
Sign In: Log in as an agency (user_type = 3) to access agency_home.php.

Manage: Use the dashboard to manage buses, routes, and track new bookings and wallet transactions.

Contributing
Feel free to fork this repository, submit issues, or create pull requests to enhance the project.

License
This project is licensed under the MIT License.
