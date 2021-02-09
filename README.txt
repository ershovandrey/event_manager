How to install
1. Install Docker and Docker compose and run docker
2. Run `make up` in the root folder - it will boot up several docker containers
3. Run `make composer install` - it will install all required packages
4. Login into the Drupal using URL http://drupal.docker.localhost:8000/ user is `admin`, password is `admin`


What is not done:
● Limit and validate the allowed departments by shipping the custom module with some default
departments (f.e. Finance, IT, Consulting, ...) without hardcoding them.
● Create a block with a registration count that is shown on every page
● Make sure the registration count can be fetched by other modules
● Make sure that the registration count block is cached to prevent a load on every request whilst
keeping the count up to date. A new registration should result in an updated block on the next
request.
● Create a new role ”Department manager” and make sure it’s shipped with the custom module
● Create a new permission “Manage event registrations”
● Grant department managers the custom permission
● Create a new form to add a department:
○ Show the form at /admin/config/add-department
○ Existing departments stay as is
○ The form should only be accessible for department managers
○ A department should have a machine readable name and a human readable name.
○ A new deploy will not reset the departments to the ones provided by the custom
module, you can use a contributed module for this
