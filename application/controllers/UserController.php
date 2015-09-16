<?php


class UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        //echo "Controller INit";
    }

    public function indexAction()
    {
        // action body
        //$this->_helper->redirector('list');
    }

    public function loginAction()
    {
        session_start();
        // Requesting For Authorization Code

        if ((empty($_SESSION['ln_expires_at'])) || (time() > $_SESSION['ln_expires_at'])) {
            // Token has expired, clear the state
            session_destroy();
            $_SESSION = array();
            //echo "Clearing_session!";
            session_start();
        }


        //if (empty($_SESSION['ln_access_token'])) {
        // Start the authorization process

                $authConfig = array(
                    'response_type' => 'code',
                    'client_id' => 'YOUR_CLIENT_ID',
                    'state' => uniqid('', true), // unique long string
                    'redirect_uri' => 'http://eldiablo.app/user/process',
                    'scope' => 'r_basicprofile r_emailaddress',
                );


            // Authentication request Url
            $url = 'https://www.linkedin.com/uas/oauth2/authorization?' . http_build_query($authConfig);
     
            // Needed to identify request when it returns to us (Prevent CSRF)
            $_SESSION['ln_state'] = $authConfig['state'];
            //session_write_close();
            //echo $_SESSION['ln_state'];
            //echo $url;
            // Redirect to the Url for user to authenticate
            header("Location: $url");

        //}


    }



    public function processAction()
    {
            //this action handles the redirect by linkedin

            session_start();

            if (!empty($_GET) ){

                    if (isset($_GET['error'])) {
                        // LinkedIn returned an error
                        print $_GET['error'] . ': ' . $_GET['error_description']."<br>";
                        $login_screen= $this->url(array('controller'=>'user',
                                                'action'=>'inddex'));
                        echo "<br><br><a href='".$login_screen."'>Go to Login</a>"; 
                        exit;
                    }
                    elseif (isset($_GET['code'])) {
                        
                        //var_dump($_SESSION);
                        //echo $_SESSION['ln_state'];
                        // State recieved matches the state sent by application
                        if ($_SESSION['ln_state'] == $_GET['state']) {
                        // Move ahead to Get token so you can make API calls


                            $req_code=$_GET['code'];
                            $state=$_GET['state'];

                            //echo "Request Code : ".$req_code."<hr>";
                            //echo "Ser_Req_Code : ".serialize($req_code)."<hr>";

                            $params = array(
                                    'grant_type' => 'authorization_code',
                                    'code' => $req_code,
                                    'redirect_uri' => 'http://eldiablo.app/user/process',
                                    'client_id' => 'YOUR_CLIENT_ID',
                                    'client_secret' => 'YOUR_SECRET_API_KEY',                          
                            );

                            // Access Token request Url
                            $url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query($params);
     
                            // Making it a POST request as stated by linkedin
                            $context = stream_context_create(
                            array('http' => 
                                    array('method' => 'POST',
                                    )
                            )
                            );


                            // Retrieve access token information
                            $response = file_get_contents($url, false, $context);
 
                            // Native PHP object, please
                            $token = json_decode($response);

                            //print_r($token);
                            //echo "<hr>";
                 
                            // Store access token and expiration time
                            $_SESSION['ln_access_token'] = $token->access_token;  
                            $_SESSION['ln_expires_in']   = $token->expires_in; //(in seconds)
                            $_SESSION['ln_expires_at']   = time() + $_SESSION['ln_expires_in']; // absolute time

                    
                            //$resource='/v1/people/~:(id,num-connections,firstName,lastName,email-address)';
                            $resource='/v1/people/~:(id,num-connections,firstName,lastName,email-address,public-profile-url,picture-urls::(original),location,headline)?format=json';


                            //print "Access Token: ".$token->access_token."<hr>"; 
                            
                            //Adding Headers as Specified by LiinkedIn
                            $opts = array(
                                'http'=>array(
                                'method' => 'GET',
                                'header' => "Authorization: Bearer " . $token->access_token . "\r\n" . "x-li-format: json\r\n"
                                )
                            );
 
                            //LinkedIn API url
                            $url = 'https://api.linkedin.com' . $resource;
 
                            // Append query parameters if there are parameters
                            if (count($params)) 
                                { $url .= '?' . http_build_query($params); }
 
                    
                            // using OAuth2 access token as Authorization
                            $context = stream_context_create($opts);
 
                            // Requesting
                            $response = file_get_contents($url, false, $context);
 
                            // Decoding Json obj
                            $user_detail= json_decode($response,true);

                            //print_r($user_detail); making data available for the view
                            $this->view->user_data=$user_detail;

                            //Have Data,  perform checks and update/insert data.
                            /*
                            echo "<hr>";
                            echo "Name : ".$user_detail['firstName']."        ".$user_detail['lastName']."<br>";
                            echo "Id : ".$user_detail['id']."<br>";
                            echo "HeadLine : ".$user_detail['headline']."<br>";
                            echo "Location : ".$user_detail['location']['name']."<br>";
                            echo "No. of pic: ".$user_detail['pictureUrls']['_total']."<br>";
                            echo "Pic Url : ".$user_detail['pictureUrls']['values'][$user_detail['pictureUrls']['_total']-1]."<br>";
                            //echo "ThumbNail pic : ".$user_detail['pictureUrl']."<br>";
                            echo "Connections : ".$user_detail['numConnections']."<br>";
                            echo "Profile : ".$user_detail['publicProfileUrl']."<br>";
                            echo "Email : ".$user_detail['emailAddress']."<br>";
                            */
    
                            if($user_detail['pictureUrls']['_total']==0){
                                //if no profile pic the setting default from public folder
                                $dp=$this->view->baseUrl().'/img/default.png';
                            }
                            else{
                                
                                $dp=$user_detail['pictureUrls']['values'][$user_detail['pictureUrls']['_total']-1];
                            }
                            $this->view->ppic=$dp;
                            //Preparing Array of UserData
                            $udata = array(
                                'id' => $user_detail['id'],
                                'fname' => $user_detail['firstName'],
                                'lname' => $user_detail['lastName'],
                                'headline' => $user_detail['headline'],
                                'loc' => $user_detail['location']['name'],
                                'conn' => $user_detail['numConnections'],
                                'email' => $user_detail['emailAddress'],
                                'profileUrl' => $user_detail['publicProfileUrl'],
                                'picUrl' => $dp,
                                //'lname' => $title,
                            );

                            //Create an object of the Model
                            $obj=new Application_Model_DbTable_Linkedin();
                            //Check if User with that id exista
                            $userExists=$obj->ifExists($user_detail['id']);

                            if($userExists==0){
                                //User Dpesn't Exist , Insert new user Data
                                $obj->addUser($udata);
                                //echo "User Inseted !<br>";
                            }
                            else{
                                //If user Exists get its unique id form DB
                                $uid=$userExists['uid'];
                                $obj->updateUser($udata,$uid);
                                //echo "User Updated !<br>";
                            }



                        } 
                        else {
                        // CSRF attack? Or did you mix up your states?
                            echo "Sate doesn't Match the one generated by Application !";
                            exit;
                        }


                    }   //isset 'code'        
            }
            else{
                //Empty Get
                //exit('Invalid callback request. Oops. Sorry.');
                $this->_helper->redirector('index');
            }



    }

    public function listAction()
    {
        // action body
        $u_details=new Application_Model_DbTable_Linkedin();
        $this->view->ud=$u_details->fetchAll();
        

    }

    
    public function detailsAction()
    {
                $uid = $this->_getParam('id', 0);
                //echo "ID : ".$uid;
                if($uid<1){
                    echo "No Such User Exists !";
                    exit;
                }
                $u_details=new Application_Model_DbTable_Linkedin();
                $exists=$u_details->find($uid)->current();
                if($exists){
                    //echo "Found ! <br>";
                    $this->view->ud = $u_details->getUserDetails($uid);
                }
                else{
                    echo "No User Found!<br>";
                    exit;
                }
                
                
    }


}

