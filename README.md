LibraryHub - Library Management System in Core PHP
=====================================

Overview
--------

The Library Management System is a PHP-based application designed to manage various aspects of a library. The system provides an interface for the admin to perform various operations such as managing books, authors, publishers, categories, racks, issuing books, and managing student records.

Features
--------

*   Manage Books: Add, Edit, Delete, and View Books.
*   Manage Categories: Add, Edit, Delete, and View Categories.
*   Manage Authors: Add, Edit, Delete, and View Authors.
*   Manage Publishers: Add, Edit, Delete, and View Publishers.
*   Manage Racks: Assign books to specific racks for easy access.
*   Issue Books: Issue books to students with tracking functionality.
*   Manage Students: Add, Edit, Delete, and View student records.

Technologies Used
-----------------

*   **PHP**: Server-side scripting language for application logic.
*   **MySQL**: Database management system for storing book, student, and transaction data.
*   **HTML/CSS**: Front-end technologies for creating the user interface.
*   **JavaScript**: For interactive elements and form validations.
*   **Bootstrap 5**: For responsive design and modern UI components.

Installation Instructions
-------------------------

1.  Download or clone the repository from GitHub.
2.  Extract the files into your server directory (e.g., `/var/www/html` for Apache on Linux).
3.  Import the `libraryhub.sql` file into your MySQL database using phpMyAdmin or MySQL command line.
4.  Configure the database connection in `config/Database.php` with your MySQL credentials.
5.  Ensure your server is running PHP and MySQL.
6.  Open the application in your browser (e.g., `http://localhost/libraryhub/`).

Admin Dashboard
---------------

The admin has full control over the following modules:

*   **Books:** Admin can add new books, update book details, and delete books.
*   **Categories:** Admin can create and manage categories for books.
*   **Authors:** Admin can add and manage authors.
*   **Publishers:** Admin can add and manage publishers.
*   **Racks:** Admin can assign books to racks for efficient placement.
*   **Issue Books:** Admin can issue books to students, track issued books, and set return dates.
*   **Students:** Admin can manage student records, including adding, updating, and deleting student details.

Database Schema
---------------

The system uses the following tables:

*   **book:** Stores information about books (title, author, publisher, category, etc.).
*   **category:** Stores book categories (e.g., fiction, science, etc.).
*   **author:** Stores authors’ details (name, bio, etc.).
*   **publisher:** Stores publisher details.
*   **user:** Stores student information (name, contact details, etc.).
*   **rack:** Stores book rack assignments.
*   **issued_book   :** Tracks books issued to students and their return dates.

Usage
-----

Once the system is installed and running, the admin can log in to the admin panel with their credentials. From there, they can manage all the functionalities available. The main pages of the admin panel include:

*   Books Management: View, Add, Edit, Delete books.
*   Category Management: Add, Edit, Delete categories.
*   Author Management: Add, Edit, Delete authors.
*   Publisher Management: Add, Edit, Delete publishers.
*   Rack Management: Assign books to racks.
*   Issue Management: Issue books to students and track returns.
*   Student Management: Add, Edit, Delete students.

Contributing
------------

If you want to contribute to this project, please fork the repository, create a feature branch, make your changes, and submit a pull request.
