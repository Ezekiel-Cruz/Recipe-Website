# Recipe Website

## Overview
This is a modern recipe website that allows users to browse, add, and manage recipes. The website includes features such as user authentication, recipe categories, and a user profile section.

## Features
- **Home**: Displays featured recipes and categories.
- **Add Recipe**: A form for users to submit new recipes.
- **Categories**: Lists all available recipe categories.
- **My Profile**: Shows user profile information and submitted recipes.
- **Login**: Allows users to log into their accounts.
- **Sign Up**: Registration form for new users.

## Project Structure
```
recipe-website
├── assets
│   ├── css
│   │   ├── main.css
│   │   └── styles.css
│   ├── js
│   │   └── scripts.js
│   └── fonts
│       └── fonts.css
├── config
│   └── database.php
├── includes
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── pages
│   ├── home.php
│   ├── add-recipe.php
│   ├── categories.php
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
2. Set up a database and update the `config/database.php` file with your database credentials.
3. Run the application on a local server (e.g., XAMPP, WAMP).
4. Access the website via your browser at `http://localhost/recipe-website`.

## Usage
- Users can sign up for an account or log in to access their profiles.
- Users can add new recipes, view categories, and manage their submitted recipes.

## Contributing
Feel free to fork the repository and submit pull requests for any improvements or features you'd like to add.

## License
This project is open-source and available under the MIT License.