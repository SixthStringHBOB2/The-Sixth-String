# SixthString

This project is a simple PHP application using Docker. It includes routing and database migration support, and serves static assets like CSS and images.

## Getting Started

### 1. Clone the repository

First, clone the repository to your local machine:

```bash
git clone https://github.com/SixthStringHBOB2/The-Sixth-String.git
cd The-Sixth-String
```

### 2. Build and Run the Docker Containers

Use the following command to build the Docker images and run the containers in detached mode:

```bash
docker-compose up --build -d
```

This will set up the application, start the web and database containers, and run them in the background.

### 3. Access the Application

Once the containers are up and running, you can access the application at [http://localhost](http://localhost) in your web browser.

### 4. Add a New Migration

To add a new database migration:

1. Create a new SQL file in the `src/database/migrations` directory.
2. Name the file with a version number to maintain the correct migration order, for example, `V2__Add_new_table.sql`.
3. Ensure that you only add SQL commands in the new file, and avoid modifying any previous migrations to prevent checksum errors.

**Example Migration SQL:**

```sql
CREATE TABLE example_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);
```

After adding the new migration, it will be automatically applied when you run the migration command (assuming your migration setup is configured properly).

### 5. Add a New Route

To add a new route to the application, follow these steps:

1. Open `index.php`.
2. Use `$router->get()`, `$router->post()`, or `$router->any()` methods to define the route.

**Example of Adding a New Route:**

```php
$router->get('/new-route', function () {
    echo "This is a new route!";
});
```

### 6. Add Static Assets (CSS, Images, JS)

To add static assets like CSS, JavaScript, and image files:

1. Place them inside the `src/public` directory. This is where assets are stored for direct access by Apache.

   **Example structure:**
    - `src/public/css/style.css`
    - `src/public/images/logo.png`
    - `src/public/js/app.js`

2. Static files are served automatically when accessed via URLs like:

    - `http://localhost/assets/css/style.css`
    - `http://localhost/assets/images/logo.png`
    - `http://localhost/assets/js/app.js`

**Why Store Static Files in `public`?**

The `public` directory is used to store files that should be publicly accessible (such as CSS, JS, and images). These files are served directly by Apache, bypassing PHP, which improves performance and accessibility.

### 7. Stop the Application

To stop the running containers, use the following command:

```bash
docker-compose down
```

This will stop and remove the containers, but your data will remain intact in Docker volumes.

## Notes

- The main routing logic is handled in the `index.php` file. If you need to add more routes or functionality, you can update this file.
- Ensure that you do not modify previous migration files, as it may cause issues with checksum validation during migrations.
- Docker Compose is used to simplify the management of the application, including the web server, PHP environment, and database.

---

### How to Include `home.php` Instead of Echoing HTML in `index.php`

Instead of echoing HTML directly inside `index.php`, you can **include** the `home.php` view file. This keeps your code more organized and separates the logic from the presentation.

**Steps to include `home.php`:**

1. **In `index.php`**: Instead of using `echo` to output HTML, simply include the `home.php` file using PHP’s `include` or `require` statements.

   Update your `index.php` as follows:

   ```php
   <?php

   require_once 'Router.php';

   $router = new Router();

   // Route for home page
   $router->get('/', function () {
       include 'views/home.php';  // Include home.php instead of echoing HTML directly
   });

   // Route for path params (e.g., /post/123)
   $router->get('/post/{id}', function ($id, $queryParams) {
       echo "<html><body><h1>Post ID: $id</h1>";
       echo "<p>Query Parameters: " . json_encode($queryParams) . "</p></body></html>";
   });

   // Route for search query params (e.g., /search?q=example)
   $router->get('/search', function ($queryParams) {
       $searchQuery = isset($queryParams['q']) ? $queryParams['q'] : 'No query provided';
       echo "<html><body><h1>Search Results for: $searchQuery</h1></body></html>";
   });

   $router->serveStatic($_SERVER['REQUEST_URI'], __DIR__);
   $router->handleRequest();
   ```

2. **In `views/home.php`**: Ensure the HTML structure is properly included and output to the browser when requested.

   **Example content for `views/home.php`:**

   ```php
   <?php
   echo "<html><body><h1>Welcome to the Home Page!</h1><p>This is the homepage of the application.</p></body></html>";
   ```

By including the `home.php` view file, you separate the routing logic from the presentation, making it easier to manage and expand.

