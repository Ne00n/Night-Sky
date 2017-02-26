<?php

class Mail {

    private $_mail_from = 'From: '._mail_sender . "\r\n";
    private $_mail_to;
    private $_subject;
    private $_text;
    private $error;

    public function __construct($mail_to,$subject,$text) {
        $this->_subject = $subject;
        $this->_mail_to = $mail_to;
        $this->_text = $text;
    }

    public function run() {
      mail($this->_mail_to,$this->_subject,$this->_text,$this->_mail_from);
    }

    public function getlastError() {
      return $this->error;
    }

}

?>
