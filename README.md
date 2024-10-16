The user can log in with their credentials and then check their login status using a JWT token. There is a separate dashboard for the admin. If the admin logs in with their credentials, they can validate their login status on their dashboard. The admin can delete, add, and update users using their JWT token.

The JWT token has an expiry time of 3600 seconds (i.e., 1 hour). Users cannot access the admin dashboard or admin features without authorization. There is also a panel for users to register themselves using the registration panel.

Every "add" feature has its own validation function. For example, if an admin adds a new user, the system will first validate the input and check if any user with the same username or email already exists. Only after these checks are successful will the user be added.

The same applies to user registration. When a user registers, all values will be validated, and the system will check if a user with the same username or email exists. If yes, the registering user will be asked to change their credentials; if no, the user will be registered successfully.

Neither admin nor user can access their features with an expired token.
