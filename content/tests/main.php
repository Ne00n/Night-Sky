<?php

#Load config
include 'content/config.php';
#Load all Class files
function dat_loader($class) {
    include 'class/' . $class . '.php';
}
spl_autoload_register('dat_loader');

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
	}

	public function testMySQLConnection() {
	  $this->assertEquals($this->DB->GetConnection()->connect_error,NULL);
	}

  public function testEscape() {
    $result = Page::escape("<script>alert('attacked')</script>");
	  $this->assertEquals($result,"&lt;script&gt;alert(&#039;attacked&#039;)&lt;/script&gt;");
  }



}
?>
