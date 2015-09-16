The folder zapp contains the application based on Zend Framework 1.12

The basic overview of application:

Application works on the controller 'user' having several actions, namely:

user/index  -> Presents a login Screen for Logging via Linked Account.
user/login  -> Requests for an authorization code as per LinkedIn API.
users/process -> The call back url to which user gets directed to after application is approved. Further requests for access Tokens, and authenticated Api requests of behalf of User for user details.

user/list	->Lists the all the users present in the database, on clicking th user it redirects to user/details

user/details -> handles the requests for user details.


Setup Process:

* Add databse setting to application.ini (zapp/application/configs/application.ini)
		resources.db.adapter = PDO_MYSQL
		resources.db.params.host = localhost
		resources.db.params.username = root
		resources.db.params.password = "Your password"
		resources.db.params.dbname = zapp

		Folder 'sqlDump' contains my sql table instance, user lapp.sql it contains two details of two users through which i had logged in.

* setup Virtual host:
	setup  a virtual host and a server name for redirect Url to which linkedin app will return.
	
	<VirtualHost *:80>
        ServerName your_custom_server
        DocumentRoot path/to/zapp/public
     
        SetEnv APPLICATION_ENV "development"
     
        <Directory path/to/zapp/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    </VirtualHost>



* Create a linkedin app at https://www.linkedin.com/developer/apps:
		get your Client ID and Client Secret Key.
		Set the application permission.

		Paste the client ID at line 38, 98 at zapp/application/controllers/UserController.php
		Paste the client Secret Key at line 99 at zapp/application/controllers/UserController.php

		Also set the proper scope at line 47 according to the permissions specified in your registered Linkedin app.
	
		Also change the redirect_uri at line 46 to the redirect_uri specified at your likedin application.
			it should be your_custom_server/users/process, where your_custom_server is the server name secified in Virtual Host.	



~Sameer Barha
