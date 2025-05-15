# Recipe Website

## Overview
This is a modern recipe website that allows users to browse, add, and manage recipes. The website includes features such as user authentication, recipe categories, and a user profile section.

## Features
- **Home**: Displays featured recipes and categories.
- **Add Recipe**: A form for users to submit new recipes with details like:
  - Title, ingredients, instructions
  - Difficulty level, preparation time, cooking time
  - Images and notes
- **Recipes & Categories**: Combined interface to browse all recipes and filter by category.
- **My Profile**: Shows user profile information and submitted recipes.
- **Login**: Allows users to log into their accounts.
- **Sign Up**: Registration form for new users.

## Project Structure
```
recipe-website
├── assets
│   ├── css
│   │   ├── main.css
│   │   ├── styles.css
│   │   ├── alerts.css
│   │   └── recipes.css
│   ├── js
│   │   └── scripts.js
│   └── fonts
│       └── fonts.css
├── config
│   └── database.php
├── database
│   ├── schema.sql
│   └── init_db.php
├── includes
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── pages
│   ├── home.php
│   ├── add-recipe.php
│   ├── recipes-categories.php
│   ├── profile.php
│   ├── login.php
│   ├── signup.php
│   ├── recipe-detail.php
│   └── edit-recipe.php
├── uploads
│   └── recipes
├── classes
│   ├── User.php
│   ├── Recipe.php
│   └── Category.php
├── api
│   └── recipes.php
├── index.php
├── .htaccess
└── README.md
```

## Installation
1. Clone the repository to your local machine.
2. Set up a database using one of these methods:
   - Run the provided database initialization script: `php database/init_db.php`
   - Manually import the SQL schema: `mysql -u root < database/schema.sql`
3. Update the `config/database.php` file with your database credentials if different from defaults.
4. Run the application on a local server (e.g., XAMPP, WAMP).
5. Access the website via your browser at `http://localhost/recipe-website`.

## Default Login
After installation, you can login with the default test account:
- Username: test_user
- Password: password123

## Usage
- Users can sign up for an account or log in to access their profiles.
- Users can add new recipes, view recipes by category, and manage their submitted recipes.

## Recent Updates
- Reorganized database files into a dedicated `database` directory
- Added automated database initialization script (`init_db.php`)
- Combined all SQL schema into a single consolidated file
- Combined recipes and categories browsing into a single interface
- Enhanced recipe display with metadata (difficulty, prep/cook time, servings, etc.)
- Added Popular Categories section with animation effects
- Made both Recipe Categories and Popular Categories sections responsive and equal in size
- Moved CSS to dedicated files for better organization
- Added back-to-top button for better user experience
- Updated navigation links in header and footer
- Removed duplicate files and functions

## Class-Based Design
The application is moving towards a class-based architecture:
- `Recipe` class for recipe management
- `User` class for user authentication and profiles
- `Category` class for recipe categorization

Legacy procedural functions are still available in `includes/functions.php` for backward compatibility.

## Contributing
Feel free to fork the repository and submit pull requests for any improvements or features you'd like to add.

## License
This project is open-source and available under the MIT License.
