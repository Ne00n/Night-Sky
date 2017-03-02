<?php

#Load config
include 'content/config.php';
#Load all Class files
include 'class/Database.php';
include 'class/Verify.php';
include 'class/Main.php';
include 'class/Page.php';
include 'class/Contact.php';
include 'class/User.php';

class TestsMain extends PHPUnit_Framework_TestCase
{

	private $DB;
  private $Verify;
  private $Main;
  private $Contact;

	public function setUp() {
		$this->DB = new Database;
		$this->DB->InitDB();
    $this->Verify = new Verify($this->DB,true,1);
    $this->Main = new Main($this->DB,$this->Verify);
    $this->Contact = new Contact($this->DB,$this->Verify);
    $this->User = new User($this->DB);
	}

	public function testMySQLConnection() {
	  $this->assertEquals($this->DB->GetConnection()->connect_error,NULL);
	}

  public function testEscape() {
    $result = Page::escape("<script>alert('attacked')</script>");
	  $this->assertEquals($result,"&lt;script&gt;alert(&#039;attacked&#039;)&lt;/script&gt;");
  }

  public function testReg() {
    #Add a User
    $password = Page::randomPassword();
    echo "Password used: ".$password;
    $activation_hash = $this->User->registerUser("Tester","test@test.com",$password,$password,"LET",true); //Which as obviously the ID 1
    $this->assertEquals($this->User->getLastError(),NULL); //Check for Errors
		#Validate our Hash that the Object gave us
    $this->assertEquals($this->Verify->checkHash($activation_hash),true);
		//Enable the Account
    $this->User->enableUser($activation_hash);
    $this->assertEquals($this->User->getLastError(),NULL); //Check for Errors
		//Try a wrong Hash which should be incorrect
    $this->assertEquals($this->Verify->checkHash($activation_hash.'a'),false);
		#Add a Second Account for Permission Validation
		$activation_hash = $this->User->registerUser("Tester2","test3@test.com",$password,$password,"LET",true); //Which has obviously the ID 2
		$this->assertEquals($this->User->getLastError(),NULL); //Check for Errors
		//Enable the Account
    $this->User->enableUser($activation_hash);
    $this->assertEquals($this->User->getLastError(),NULL); //Check for Errors
  }

  public function testContacts() {
    #Add a Contact
    $activation_hash = $this->Contact->addContact("test2@test.com",true);
    $this->assertEquals($this->Contact->getLastError(),NULL); //Check for Errors
		//Validate our Hash that the Object gave us
    $this->assertEquals($this->Verify->checkEmailHash($activation_hash),true);
		//Try a wrong Hash which should be incorrect
    $this->assertEquals($this->Verify->checkEmailHash($activation_hash.'a'),false);
		//Enable the Contact
    $this->assertEquals($this->Contact->enableContact($activation_hash),NULL);
		//We need to switch to ID 2, this above happend with ID 1
		$this->Verify = new Verify($this->DB,true,2);
		$this->Contact = new Contact($this->DB,$this->Verify);
		#Add a Contact to our Second Account
		$activation_hash = $this->Contact->addContact("test4@test.com",true);
    $this->assertEquals($this->Contact->getLastError(),NULL); //Check for Errors
		//Enable the Contact
		$this->assertEquals($this->Contact->enableContact($activation_hash),NULL);
		//Check if Verify allows us to set 1 as ID to contact, which should not work, since 1 is assigned to the Account before
		$this->assertEquals($this->Verify->checkContactID(3,0),false);
		//But we should be able to acess or own Contact
		$this->assertEquals($this->Verify->checkContactID(4,0),true);
  }

}
?>
