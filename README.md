# Solutech Backend

This is the back-end code built on Laravel 8 that, together with its font-end code, makes up a small project for orders with vehicles.


## Quick Setup

### Quick Database Setup

To setup your database, run php artisan migrate

### Quick Dev Serve Setup

To run this project locally, navigate into the project from the terminal and type out 'php artisan serve'. 

Please note the port where the app runs, and on your front-end app in the main.js file, find the 'axios.defaults.baseURL' variable and make sure the port is 8000. If not, update the port in the main.js file to the new port, e.g., locahost:{new-port}


