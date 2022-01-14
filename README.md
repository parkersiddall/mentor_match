# Mentor Match
## Description
Mentor Match is an application that pairs mentees and mentors based on shared characteristics and interests.

Administrators can create dynamic questionares with as many multiple choice questions as they wish. This forms are then distributed to applicants (mentors and mentees) in order to be completed. 

Then, with the click of a button, the software will match mentees with their best fit mentor. The algorithm takes into consideration the ration between mentors and mentees in order to ensure that a mentor is not overwhelmed with mentees.

## File structure
- `/db` includes information about how to set up the database (db_init_queries.sql) as well as the connection file (pdo.php).
- `/docs` just various stuff to reference while developing. Nothing crucial to the running of the application
- `/includes` contains the majority of the application and its backend logic
  - `/api` all of the files to manage the api endpoints. Names of files map to endpoints
  - `/component` html components that are reused in various views
  - `/utilities` various tools to help the app function correctly (some classes, middleware will go here)
  - `/views` the html that is returned to the browser to create the user interface
- `/public` all files that are "exposed"
  - `/assets` javascript and css
  
## Understanding the code
The app essentially consists of an API and views (which then get, post, put, delete data to the API). All views are initially empty templates, then via JS the data is populated.
The only view that is the expection to this is the `application.php` file in the `/public` directory. For the sake of simplicity I decided to put the backend logic and the view all together in the same PHP file.

Notes on the API endpoints can be found in the `/docs/api_postman_collection.json`. You can pull it into Postman to play around with it.

## Where to start
- `/public/index.php` is where you should begin. It is essentially the router that looks at the url and then includes the necessary backend logic to process the request correctly.
- `/includes/api` is where to look once you have figured out where the requests are being routed to. This is where you will find the backend logic. File names map to api endpoints.
- `/includes/views` will show you the UI that the user will see
  - `/add_new.php`: view for creating a new project
  - `/home.php`: view for the homepage
  - `/view_applicants.php` the most complex view. It shows all the applicants and matches. It also has a lot of buttons that call the apis.
- `/public/assets` has the javascript files that are used by the views to call the api. The names of these files map to the file names of the views.

## Run the app locally
1. Set up a MySql database and create a user. Get the connection string and user credentials and plug them into the .env file at the root of this directory.
2. Run all of the queries in `/db/db_init_queries.sql` to get the DB set up.
3. Run composer install
4. Set up your local server and have it pointing at the `/public` directory. I use the built in web server in PHP Storm.
5. Run the server

## Collaboration
To collaborate, create a branch with the following naming structure `<your_name>_<task_your_doing>`

## TODO
- Review error handling in APIs
- Set up user authentication (trying to find a library that can easily do this)
- Create stylesheets for views
- Password protect application forms


