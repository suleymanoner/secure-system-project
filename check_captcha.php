<html>
  <head>
    <link rel="icon" type="image/x-icon" href="https://secure-project.herokuapp.com/assets/favicon.ico">
    <title>Check Captcha</title>
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
  </head>
  <body>
      <div style="text-align: center" >
        <h1>Check captcha</h1>
      </div>
  </body>
</html>

<?php
require_once dirname(__FILE__).'/api/clients/HCaptchaClient.php';

$captchaClient = new HCaptchaClient();
$captchaClient->takeCaptchaResponse();
?>