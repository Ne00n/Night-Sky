<?php

class Mail {

    private $_mail_from = 'From: noreply@night.x8e.ru' . "\r\n";
    private $_mail_to;
    private $_subject;
    private $_text;
    private $success = true;

    public function __construct($mail_to,$subject,$text) {
        $this->_subject = $subject;
        $this->_mail_to = $mail_to;
        $this->_text = $text;
    }

    public function run() {
        #mail($this->_mail_to,$this->_subject,$this->_text,$this->_mail_from);

        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = _mail_host;
        $mail->SMTPAuth = true;
        $mail->Username = _mail_user;
        $mail->Password = _mail_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('noreply@night.x8e.ru', 'Night-Sky');
        $mail->addAddress($this->_mail_to);

        $mail->Subject = $this->_subject;
        $mail->Body    = $this->_text;

        if(!$mail->send()) {
            #echo 'Message could not be sent.';
            #echo 'Mailer Error: ' . $mail->ErrorInfo;
            $this->success = false;
        }

    }

    public function checkSuccess() {

      return $this->success;

    }
}

?>
