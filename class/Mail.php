<?php

class Mail {

    private $_mail_from = 'From: noreply@night.x8e.ru' . "\r\n";
    private $_mail_to;
    private $_subject;
    private $_text;

    public function __construct($mail_to,$subject,$text) {
        $this->_subject = $subject;
        $this->_mail_to = $mail_to;
        $this->_text = $text;
    }

    public function run() {
        mail($this->_mail_to,$this->_subject,$this->_text,$this->_mail_from);
    }
}

?>
