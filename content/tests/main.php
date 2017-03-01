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
    $this->Verify = new Verify($this->DB,true);
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
    $activation_hash = $this->User->registerUser("Tester","test@test.com",$password,$password,"LET",true);
    $this->assertEquals($this->User->getLastError(),NULL);
    $this->assertEquals($this->Verify->checkHash($activation_hash),true);
    $this->User->enableUser($activation_hash);
    $this->assertEquals($this->User->getLastError(),NULL);
    $this->assertEquals($this->Verify->checkHash($activation_hash.'a'),false);
  }

  public function testContacts() {
    #Add a Contact
    $activation_hash = $this->Contact->addContact("test2@test.com",true);
    $this->assertEquals($this->Contact->getLastError(),NULL);
    $this->assertEquals($this->Verify->checkEmailHash($activation_hash),true);
    $this->assertEquals($this->Verify->checkEmailHash($activation_hash.'a'),false);
    $this->assertEquals($this->Contact->enableContact($activation_hash),NULL);
    $this->assertEquals($this->Contact->enableContact($activation_hash.'a'),"MySQL Error");
  }

}
?>
