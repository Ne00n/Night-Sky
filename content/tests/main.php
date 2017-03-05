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
include 'class/Group.php';

use PHPUnit\Framework\TestCase;

class TestsMain extends TestCase {

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
		$this->Group = new Group($this->DB,$this->Verify);
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

		//Check if the Login works fine...
		$this->Verify->ValidateLogin("Tester",$password);
		$this->assertEquals($this->Verify->getLastError(),NULL); //Check for Errors
		$this->Verify->ValidateLogin("Tester2",$password);
		$this->assertEquals($this->Verify->getLastError(),NULL); //Check for Errors
		//Check a incorrect password
		$this->Verify->ValidateLogin("Tester",$password.'a');
		$this->assertEquals($this->Verify->getLastError(),"Incorrect Login details"); //Check for Errors
  }

  public function testContacts() {
    #Add a Contact
    $activation_hash = $this->Contact->addContact("test2@test.com",$groups=array(),true);
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
		$activation_hash = $this->Contact->addContact("test4@test.com",$groups=array(),true);
    $this->assertEquals($this->Contact->getLastError(),NULL); //Check for Errors
		//Enable the Contact
		$this->assertEquals($this->Contact->enableContact($activation_hash),NULL);

		//Check if Verify allows us to set 1/3 as ID to contact, which should not work, since 1/3 is assigned to the Account before
		$this->Contact->setID(1);
		$this->assertEquals($this->Contact->getLastError(),"Invalid ID"); //Check for Errors
		$this->Contact->resetError();
		$this->Contact->setID(3);
		$this->assertEquals($this->Contact->getLastError(),"Invalid ID"); //Check for Errors
		$this->Contact->resetError();
		//But we should be able to acess our own Contact
		$this->Contact->setID(2);
		$this->assertEquals($this->Contact->getLastError(),NULL); //Check for Errors
		$this->Contact->setID(4);
		$this->assertEquals($this->Contact->getLastError(),NULL); //Check for Errors
  }

	public function testCleanUP() {
		$stmt = $this->DB->GetConnection()->prepare("TRUNCATE TABLE `checks`");
		$stmt->execute();

		$stmt = $this->DB->GetConnection()->prepare("TRUNCATE TABLE `emails`");
		$stmt->execute();

		$stmt = $this->DB->GetConnection()->prepare("TRUNCATE TABLE `groups`");
		$stmt->execute();

		$stmt = $this->DB->GetConnection()->prepare("TRUNCATE TABLE `groups_checks`");
		$stmt->execute();

		$stmt = $this->DB->GetConnection()->prepare("TRUNCATE TABLE `groups_emails`");
		$stmt->execute();

		$stmt = $this->DB->GetConnection()->prepare("TRUNCATE TABLE `history`");
		$stmt->execute();

		$stmt = $this->DB->GetConnection()->prepare("TRUNCATE TABLE `remote`");
		$stmt->execute();

		$stmt = $this->DB->GetConnection()->prepare("TRUNCATE TABLE `threads`");
		$stmt->execute();

		$stmt = $this->DB->GetConnection()->prepare("TRUNCATE TABLE `users`");
		$stmt->execute();
	}

}
?>
