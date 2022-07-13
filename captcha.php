<html>
  <head>
    <link rel="icon" type="image/x-icon" href="https://secure-project.herokuapp.com/assets/favicon.ico">
    <title>Solve Captcha</title>
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
  </head>
  <body>
    <div style="text-align:center">
      <form action="check_captcha.php" method="POST">
        <div class="h-captcha" data-sitekey="b3dd8d68-1f59-424b-b1b5-c8f2875d8608"></div>
        <br />
        <input type="submit" value="Submit"/>
      </form>
    </div>
  </body>
</html>