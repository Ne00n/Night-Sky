<?php

#Load config
include 'content/configs/config.php';
include 'content/configs/regex.php';
#Load needed Class files
include 'class/Database.php';
include 'class/Verify.php';
include 'class/Main.php';
include 'class/Page.php';
include 'class/Contact.php';
include 'class/User.php';
include 'class/Group.php';
include 'class/History.php';
include 'class/LoadBalancer.php';
include 'class/StatusPage.php';
//Load Test files
include 'content/tests/Contact.php';
include 'content/tests/Status.php';
include 'content/tests/User.php';
include 'content/tests/History.php';
include 'content/tests/Group.php';

use PHPUnit\Framework\TestCase;

class TestsMain extends TestCase {

	private $DB;
  private $Verify;
  private $Main;
  private $Contact;
	private $StatusPage;

	public function setUp() {
		$this->DB = new Database;
		$this->DB->InitDB();
		$this->CleanUP();
	}

  public function testComponents() {
		//Testing escape
		$result = Page::escape("<script>alert('attacked')</script>");
		$this->assertEquals($result,"&lt;script&gt;alert(&#039;attacked&#039;)&lt;/script&gt;");
		//Test MySQL connection
		$this->assertEquals($this->DB->GetConnection()->connect_error,NULL);
		//Run User Tests
		$U = new User_Tests();
		$U->launch();
		//Run Contact Tests
		$CT = new Contact_Tests();
		$CT->launch();
		//Run Groups Tests (Additional since Contact also includes some Groups Tests)
		$GR = new Group_Tests();
		$GR->launch();
		//Run History Tests
		$H = new History_Tests();
		$H->launch();
		//Run Status Tests
		$SP = new Status_Tests();
		$SP->launch();
  }

	private function CleanUP() {
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

		$stmt = $this->DB->GetConnection()->prepare("TRUNCATE TABLE `status_pages`");
		$stmt->execute();
	}
}
?>
