<?php

class Mail {

    private $_mail_from = 'From: '_mail_sender . "\r\n";
    private $_mail_to;
    private $_subject;
    private $_text;
    private $error;
    private $DB;

    public function __construct($mail_to,$subject,$text) {
        $this->_subject = $subject;
        $this->_mail_to = $mail_to;
        $this->_text = $text;
    }

    public function run() {
      mail($this->_mail_to,$this->_subject,$this->_text,$this->_mail_from);
    }

    public function addbackLog($mail_to,$subject,$text) {

      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO emails_backlog(Target,Subject,Content) VALUES (?,?,?)");
      $stmt->bind_param('sss',$mail_to, $subject, $text);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }

    public function setDB($in_DB) {
      $this->DB = $in_DB;
    }

    public function getlastError() {
      return $this->error;
    }

}

?>
