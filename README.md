#A Simple LinkedIn App

A simple Linkedin app using LinkedIn api and LinkedIn OAuth2.0 to create list of users along with their details.
It uses Php zend Framework and Mysql.<br>

Requires :<br>
**Php >5**<br>
**Zend Framework 1.12**<br>
**Mysql >5**<br>

	
* Firstly <a href="https://www.linkedin.com/developer/apps">Register</a> your Application with LinkedIn. Get your **Client ID**(API Key) and **Client Secret**(Secret Key).

* Check the appropriate permissions you need for your application. i.e <b>r_basicprofile</b>,  <b>r_emailaddress</b> etc. Specify a valid callback url, preferably use <b>'https'</b>. <br>

* Use your client ID at line 38, 98 at application/controllers/UserController.php <br>
				```php	'client_id' => 'YOUR_CLIENT_ID' <br> 'client_id' => 'YOUR_CLIENT_ID'	```
	

* Use your client Secret Key at line 99 at zapp/application/controllers/UserController.php <br>
			```php	'client_secret' => 'YOUR_SECRET_API_KEY' 	```
	

* Also set the proper scope at line 41 according to the permissions specified in your registered LinkedIn app.<br>
			```php	'scope' => 'r_basicprofile r_emailaddress'	```
	
	

###Your Virtual host setup:

* Setup  a virtual host and a server name for redirect Url to which linkedin app will return.<br>
	```html
	<VirtualHost *:80>
        ServerName your_custom_server
        DocumentRoot path/to/thisapp/public
     
        SetEnv APPLICATION_ENV "development"
     
        <Directory path/to/thisapp/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    </VirtualHost>
    ```

###Db Setup :
   
* Add databse setting to application.ini (application/configs/application.ini): <br>

		resources.db.adapter = PDO_MYSQL
		resources.db.params.host = localhost
		resources.db.params.username = root
		resources.db.params.password = "Your password"
		resources.db.params.dbname = "Your db"

* Also Create a table in your Db:

		CREATE TABLE lapp (
				uid int(11) NOT NULL auto_increment,
				id varchar(11) NOT NULL,
				fname varchar(50) NOT NULL,
				lname varchar(50) NOT NULL,
				headline varchar(100) ,
				conn int(10),
				loc varchar(100),
				profileUrl varchar(500),
				picUrl varchar(500),
				email varchar(50),
				PRIMARY KEY (uid)
		);

* Also change the *redirect_uri* at line 46 (UserController.php) to the *redirect_uri* specified at your likedin application.<br>
It should be *your_custom_server/users/process*, where *your_custom_server* is the server name secified in Virtual Host.


###The basic overview of application:

*	Application works on the controller 'user' having several actions, namely:

		user/index  -> Presents a login Screen for Logging via Linked Account.<br>
		user/login  -> Requests for an authorization code as per LinkedIn API.<br>
		users/process -> The call back url to which user gets directed to after application is approved.<br> Further requests for access Tokens, and authenticated Api requests of behalf of User for user details.<br>
		user/list	->Lists the all the users present in the database, on clicking th user it redirects to user/details<br>
		user/details -> handles the requests for user details.<br>


###Must Ssee Links :
[Getting Started with the REST API](https://developer.linkedin.com/docs/rest-api)<br>
[Authenticating with OAuth 2.0 Guide](https://developer.linkedin.com/docs/oauth2)<br>
[Sign In with LinkedIn](https://developer.linkedin.com/docs/signin-with-linkedin)<br>
[LinkedIn Code Samples](https://developer-programs.linkedin.com/documents/code-samples)<br>

<br><br>


***3l-d1abl0* (Sameer Barha)**
