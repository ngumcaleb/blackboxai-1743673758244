# WhatsApp-inspired Chat + Marketplace

A full-stack web application combining real-time chat functionality with a marketplace, built with HTML, Tailwind CSS, JavaScript, PHP, and MySQL.

## Features

- **User Authentication**: Registration, login, and profile management
- **Real-Time Chat**: WhatsApp-style messaging with status indicators
- **Marketplace**: Create shops, add products, and message sellers
- **Mobile-Friendly**: Responsive design that works on all devices
- **Simple Setup**: Works with XAMPP/WAMP or any basic PHP/MySQL server

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Web server (Apache recommended)

## Installation

1. **Clone or download** the project files to your web server directory
2. **Create a database** named `chat_marketplace` in MySQL
3. **Import the database schema** from `database/database.sql`
4. **Configure database connection** in `includes/config.php`:
   ```php
   $host = 'localhost'; // Your database host
   $db   = 'chat_marketplace'; // Database name
   $user = 'root'; // Database username
   $pass = ''; // Database password
   ```
5. **Set up file permissions**:
   - Ensure the web server has write permissions to:
     - `assets/images/profiles/`
     - `assets/images/products/`

## Running the Application

1. Start your web server (Apache) and MySQL server
2. Access the application in your browser at:
   ```
   http://localhost/simple_chat_marketplace/
   ```
3. Register a new account or use the sample accounts:
   - Email: `john@example.com` / Password: `password`
   - Email: `jane@example.com` / Password: `password`

## Project Structure

```
simple_chat_marketplace/
├── assets/               # Static assets
│   ├── css/              # CSS files
│   ├── js/               # JavaScript files
│   └── images/           # Images (profiles, products)
├── database/             # Database setup
│   └── database.sql      # SQL schema and sample data
├── includes/             # PHP includes
│   ├── config.php        # Database configuration
│   ├── auth.php          # Authentication functions
│   └── functions.php     # Helper functions
├── chat/                 # Chat system
│   ├── chat.php          # Chat interface
│   ├── send_message.php  # Message sending handler
│   └── fetch_messages.php # Message fetching handler
├── marketplace/          # Marketplace system
│   ├── index.php         # Marketplace homepage
│   ├── shop.php          # Shop creation
│   ├── add_product.php   # Product addition
│   └── view_product.php  # Product details
├── user/                 # User management
│   ├── register.php      # Registration
│   ├── login.php         # Login
│   ├── logout.php        # Logout
│   └── profile.php       # Profile management
├── index.php             # Application entry point
└── README.md             # This file
```

## Customization

- **Styling**: Modify Tailwind CSS classes in the HTML files
- **Configuration**: Update database settings in `includes/config.php`
- **Features**: Extend functionality by modifying the PHP scripts

## Troubleshooting

- **Database connection issues**: Verify credentials in `config.php`
- **File upload problems**: Check directory permissions
- **Session problems**: Ensure PHP sessions are enabled

## License

This project is open-source and available under the MIT License.