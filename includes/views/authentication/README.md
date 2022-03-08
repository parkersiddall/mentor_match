# User registration and authentication

## Endpoints
- `/register` form fields:
  - email
  - password
  - confirm_password
  - school_name
  - school_city
  - school_country
  - accept_terms (bool)
  - approved (bool)
- `/login` form fields:
  - email
  - password
- `/logout` the logic for this endpoint is written directly into index.php

## Session data
The user_id of the authenticated user is stored in the `$_SESSION['USER']` field.