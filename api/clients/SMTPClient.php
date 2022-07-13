<?php
require_once dirname(__FILE__).'/../../vendor/autoload.php';
require_once dirname(__FILE__).'/../config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SMTPClient {

  private $mailer;

  public function __construct(){
    $this->mailer = new PHPMailer(true);

    try {
      $this->mailer->isSMTP();
      $this->mailer->Host = SMTP_HOST;
      $this->mailer->SMTPAuth = true;
      $this->mailer->Username = SMTP_USER;
      $this->mailer->Password = SMTP_PASSWORD;
      $this->mailer->SMTPSecure = 'tls';
      $this->mailer->Port = SMTP_PORT;
    } catch(Exception $e) {
      echo 'Email could not be sent. Mailer Error: '. $this->mailer->ErrorInfo;
    }
  }

  public function send_email($email, $type, $part1, $part2 = "") {
    
    $this->mailer->setFrom('suleymanoner1999@gmail.com', 'Secure System');
    $this->mailer->addAddress($email, 'Dear User');
    $this->mailer->isHTML(true);
    $this->mailer->Subject = $type;

    if($part2 != "") {
      $this->mailer->Body    = '<h3>'.$part1.'</h3><br /> '.BASE_URL.$part2;
    } else {
      $this->mailer->Body    = '<h3>'.$part1.'</h3><br />';
    }
    $this->mailer->send();
  }

}
