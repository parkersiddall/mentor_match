# Middleware
Various middleware will need to be created in order to manage authenticated and validation of the params in each call. 

- `is_authenticated.php`: Allows access to only those who have been authenticated and logged in.
- `is_not_authenticated.php`: Restricts access to those who are authenticated (ex. login and register pages).
- `is_project_creator.php`: Allows access only to those who are the creator of the requests content.

TODO:
- Add conditional functionality to return JSON for application/json requests.