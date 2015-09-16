<h1>A Simple LinkedIn App</h1>

	A simple Linkedin app using LinkedIn api and LinkedIn OAuth2.0 to create list of users along with their details.
	It uses Php zend Framework and Mysql.

	Requires :
	Php >5
	Zend Framework 1.12
	Mysql >5

	<br><br><br>

	Firstly <a href="https://www.linkedin.com/developer/apps">Register</a> your Application with LinkedIn. Get your <b>Client ID</b>(API Key) and <b>Client Secret</b>(Secret Key).<br>
	Check the appropriate permissions you need for your application. i.e r_basicprofile, r_emailaddress etc.<br>
	Specify a valid callback url. preferably use 'https'. <br>

	Use your client ID at line 38, 98 at application/controllers/UserController.php
	'''		'client_id' => 'YOUR_CLIENT_ID',	'''
	'''		'client_id' => 'YOUR_CLIENT_ID',	'''

	Use your client Secret Key at line 99 at zapp/application/controllers/UserController.php
	'''		'client_secret' => 'YOUR_SECRET_API_KEY', 	'''

	Also set the proper scope at line 41 according to the permissions specified in your registered LinkedIn app.
	'''	'scope' => 'r_basicprofile r_emailaddress',		'''

	<h3>Your Virtual host setup:</h3>

	setup  a virtual host and a server name for redirect Url to which linkedin app will return.
	
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

    <h3>Db Setup : </h3>

    * Add databse setting to application.ini (application/configs/application.ini)
		resources.db.adapter = PDO_MYSQL
		resources.db.params.host = localhost
		resources.db.params.username = root
		resources.db.params.password = "Your password"
		resources.db.params.dbname = "Your db"

		<br>

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

	* Also change the redirect_uri at line 46(UserController.php) to the redirect_uri specified at your likedin application.<br>
	It should be your_custom_server/users/process, where your_custom_server is the server name secified in Virtual Host.


	<h3>The basic overview of application:</h3>

	*	Application works on the controller 'user' having several actions, namely:

		user/index  -> Presents a login Screen for Logging via Linked Account.
		user/login  -> Requests for an authorization code as per LinkedIn API.
		users/process -> The call back url to which user gets directed to after application is approved. Further requests for access Tokens, and authenticated Api requests of behalf of User for user details.

		user/list	->Lists the all the users present in the database, on clicking th user it redirects to user/details

		user/details -> handles the requests for user details.


	<h3>Must Ssee Links : </h3>
	<a href="https://developer.linkedin.com/docs/rest-api">Getting Started with the REST API</a>
	<a href="https://developer.linkedin.com/docs/oauth2">Authenticating with OAuth 2.0 Guide</a>
	<a href="https://developer.linkedin.com/docs/signin-with-linkedin">Sign In with LinkedIn</a>
	<a href="https://developer-programs.linkedin.com/documents/code-samples">LinkedIn Code Samples</a>

<br><br>


<b>~ 3l-d1abl0(Sameer Barha)</b>
