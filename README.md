# Timplado de Platito

Timplado de Platito is a culinary platform for food enthusiasts. Users can browse, share, and manage recipes from various cuisines around the world. You can access the online version here: [https://timplado-de-platito.great-site.net/](https://timplado-de-platito.great-site.net/)

## Features

- Browse, search, and share recipes
- Submit your own culinary creations with detailed instructions
- Categorize recipes by cuisine type and difficulty level
- User profiles with saved favorites and recipe collections
- Recipe details with preparation time, cooking time, and servings information
- Responsive design for desktop

## Setup Instructions

### 1. Clone the Repository
```
git clone <https://github.com/Ezekiel-Cruz/Recipe-Website.git>
cd Recipe-Website
```

### 2. Database Setup
You have two options to populate the database:
- Manually import the SQL schema: `mysql -u root < database/schema.sql`
- Create your own recipes through the web interface

### 3. Configure Database Connection
Edit `config/database.php` with your MySQL credentials.

### 4. Run the Project
- Place the project in your web server's root (e.g., htdocs for XAMPP)
- Access via http://localhost/Recipe-Website/

### 5. Default Login
After installation, you can login with the default test account:
- Username: test_user
- Password: password123

## Directory Structure

```
Recipe-Website/
├── /assets           - CSS, JS, images, and fonts
│   ├── /css          - Various CSS files for styling
│   ├── /js           - JavaScript files
│   ├── /img          - Images used across the site
│   └── /fonts        - Custom fonts
├── /config           - Database configuration
├── /database         - SQL schema and initialization scripts
├── /includes         - Shared PHP components
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── /pages            - Main application pages
│   ├── home.php
│   ├── add-recipe.php
│   ├── recipes-categories.php
│   ├── profile.php
│   ├── login.php
│   ├── signup.php
│   ├── recipe-detail.php
│   └── edit-recipe.php
├── /uploads          - User-uploaded content
├── /classes          - OOP class definitions
│   ├── User.php
│   ├── Recipe.php
│   └── Category.php
├── /api              - API endpoints
└── index.php         - Main entry point
```

## Recent Updates

### Profile System Improvements (May 2025)
- Enhanced profile page styling with improved cards and animations
- Fixed alignment of recipe management buttons
- Added modern styling to the edit profile forms

### Recipe Display Enhancements
- Enhanced recipe display with metadata (difficulty, prep/cook time, servings)
- Combined recipes and categories browsing into a single interface
- Added Popular Categories section with animation effects
- Improved recipe cards with consistent sizing and responsive design

### Class-Based Architecture
- `Recipe` class for recipe management and search
- `User` class for authentication, profiles, and favorites
- `Category` class for organizing recipes by cuisine

### Front-End Features
- Responsive design that works on desktop
- Modern UI with animation effects
- Intuitive recipe browsing and searching
- Clean, user-friendly forms for recipe submission

### Back-End Features
- Secure user authentication system
- Efficient database schema for recipes and categories
- Image upload and management for recipes
- Search functionality across recipe database

## Contributing
Feel free to fork the repository and submit pull requests for any improvements or features you'd like to add.

## License
This project is open-source and available under the MIT License.
