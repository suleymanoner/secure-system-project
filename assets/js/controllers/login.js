class Login {

    static counter = 0;

    constructor() {
      this.counter = 0;
    }

    static init(){
      if (window.localStorage.getItem("token")){
        window.location="index.html";
      }else{
        $('body').show();
      }
    }
    
    static showForgotForm(){
      $("#login-form-container").addClass("hidden");
      $("#forgot-form-container").removeClass("hidden");
    }
  
    static showRegisterForm(){
      $("#login-form-container").addClass("hidden");
      $("#register-form-container").removeClass("hidden");
    }
  
    static showLoginForm(){
      $("#login-form-container").removeClass("hidden");
      $("#register-form-container").addClass("hidden");
      $("#forgot-form-container").addClass("hidden");
    }

    static doRegister(){

      var auth_way;

      $("#register-link").prop('disabled',true);

      if(document.getElementById('otp-sms').checked) {
        auth_way = 'SMS';
      } else if(document.getElementById('otp-qrcode').checked) {
        auth_way = 'QRCODE';
      }
      
      var register_info = {
          "username": $("#reg-username").val(),
          "email": $("#reg-email").val(),
          "password": $("#reg-password").val(),
          "password_again": $("#reg-password-again").val(),
          "phone": $("#reg-phone").val(),
          "auth_way": auth_way
      };

      $.post("https://secure-project.herokuapp.com/api/register", register_info).done(function (data) {
          if(data['status'] == 'error') {
              toastr.error(data['response']);
              $("#register-link").prop('disabled', false);
          } else {
              toastr.success(data['response']);
              $("#register-link").prop('disabled', false);
              document.forms['register-form'].reset();
              window.location.href="https://secure-project.herokuapp.com/confirmation.html";
          }
      }).fail(function(error) {
          toastr.error(error);
          $("#register-link").prop('disabled', false);
      });
    }

    static doLogin() {

      var login_info = {
          "username": $("#log-username").val(),
          "password": $("#log-password").val(),
      };

      $("#login-link").prop('disabled', true);

      if(this.counter < 3) {
        this.counter++;

        $.post("https://secure-project.herokuapp.com/api/login", login_info).done(function (data) {
          if(data['status'] == 'error') {
            toastr.error(data['response']);
            $("#login-link").prop('disabled', false);
          } else {

            if(data['response'] == "Code sent to your phone! Please check it!") {
              $("#two-auth-container").removeClass("hidden");
              $("#login-form-container").addClass("hidden");
            } else if(data['status'] == "qr") {
              $("#two-auth-container").removeClass("hidden");
              $("#login-form-container").addClass("hidden");
              document.getElementById("qrcode-img").src = data['response'];
              $("#two-way-qr-img").removeClass("hidden");
            } else {
              window.localStorage.setItem("token", data['response']);
              window.location = "index.html";
            }
          }
        }).fail(function(error) {
              toastr.error(error);
              $("#login-link").prop('disabled', false);
        });
      } else {
        window.location.href="https://secure-project.herokuapp.com/captcha.php";
      }
    }

    static doCheckTwoAuthCode() {

      var remember = "false";

      if(document.getElementById('remember-code').checked) {
        remember = "true";
      }

      var check_info = {
        "code": $("#two-auth-code").val(),
        "username": $("#log-username").val(),
        "remember": remember
      };

      $("#enter-two-auth-code").prop('disabled', true);

      $.post("https://secure-project.herokuapp.com/api/check_two_auth_code", check_info).done(function (data) {
          if(data['status'] == 'error') {
            toastr.error(data['response']);
            $("#enter-two-auth-code").prop('disabled', false);
          } else {
            $("#enter-two-auth-code").prop('disabled', false);
            window.localStorage.setItem("token", data['response']);
            window.location = "index.html";
          }
        }).fail(function(error) {
              toastr.error(error);
              $("#enter-two-auth-code").prop('disabled', false);
        });
    }

    static doChangePassword() {

      var password_info = {
        "old_password": $("#old-pass").val(),
        "new_password": $("#new-pass").val(),
        "confirm_password": $("#new-confirm-pass").val(),
      };

      $("#change-password-link").prop('disabled', true);

      $.ajax({
          type: "POST",
          beforeSend: function(request) {
              request.setRequestHeader("Authentication", window.localStorage.getItem("token"));
          },
          url: "https://secure-project.herokuapp.com/api/changepassword",
          data: password_info,
          success: function(data) {
              if(data['status'] == 'error') {
                  toastr.error(data['response']);
                  $("#change-password-link").prop('disabled', false);
              } else {
                  toastr.success(data['response']);
                  $("#change-password-link").prop('disabled', false);
                  document.forms['change-password-form'].reset();
              }
          }
      });
    }

    static doForgotPassword() {

      var email = {
        "email": $("#forgot-email").val(),
      };

      $("#forgot-link").prop('disabled', true);

      if(this.counter < 5) {
        this.counter++;
      
        $.ajax({
          type: "POST",
          url: "https://secure-project.herokuapp.com/api/forgotpassword",
          data: email,
          success: function(data) {
              if(data['status'] == 'error') {
                  toastr.error(data['response']);
                  $("#forgot-link").prop('disabled', false);
              } else {
                  toastr.success(data['response']);
                  $("#forgot-link").prop('disabled', false);
                  document.getElementById('forgot-email').value = '';
              }
          }
        });
      } else {
        window.location.href="https://secure-project.herokuapp.com/captcha.php";
      }
    }

    static doLogout() {
      window.localStorage.clear();
      window.location = "login.html";
    }

}
