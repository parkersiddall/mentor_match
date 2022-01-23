# User registration and authentication

## New user registration
The registration process for new users follows these steps:

1. New user completes the registration form and provides the following information:
   - email
   - password (and confirmation of password)
   - school name
   - school city
   - school state
   - school country
   - accept terms and conditions

2. A new user is created with the email and password, however their account is not yet activated.
3. A manual evaluation of the school information is completed. Based on this evaluation, the following happens
   - In the case this user is from a new school, the school is added to the database after ensuring all data is correct. The user is then associated with the school by assigning the foreign key in the user table.
   - in the case the user is from a school already in the database, the user is associated to the existed school.
4. If step 3 goes well, the user account is activated, they are notified via email. Then they can effectively log in.

### DB tables
TODO
### Endpoints
TODO
## User authentication
TODO


# TODO: Don't forget to do these
- add new tables (registation, school, user) to DB
- update match_form table, add foreign key for user_id