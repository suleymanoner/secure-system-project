class User {

    static init() {
        User.doShowUsername();
        User.doShow2FactorAuth();
        User.showRememberCookie();

        if(!window.localStorage.getItem("token")) {
            window.location = "login.html";
        } else {
            $("body").show();
        }
    }

    static doShowUsername() {
        $.ajax({
            type: "GET",
            beforeSend: function(request) {
                request.setRequestHeader("Authentication", window.localStorage.getItem("token"));
            },
            url: "https://secure-project.herokuapp.com/api/user",
            success: function(data) {
                if(data['status'] == 'error') {
                    toastr.error(data['message']);
                } else {
                    $('#welcome-text').append(data[0]['username']);
                    document.getElementById('change-two-factor-way').innerHTML = `2Factor Login: ${data[0]['two_factor_way']}. Click for change!`;
                }
            }
        });
    }

    static doShow2FactorAuth() {
        $.ajax({
            type: "GET",
            beforeSend: function(request) {
                request.setRequestHeader("Authentication", window.localStorage.getItem("token"));
            },
            url: "https://secure-project.herokuapp.com/api/show_two_factor",
            success: function(data) {
                if(data['status'] == 'error') {
                    toastr.error(data['message']);
                } else {

                    if(data['response'] == '1') {
                        document.getElementById('open-two-factor').innerText = "Disable 2-Factor Auth";
                        $("#open-two-factor").removeClass("btn-success");
                        $("#open-two-factor").addClass("btn-warning");
                    } else if(data['response' == '2']) {
                        document.getElementById('open-two-factor').innerText = "Enable 2-Factor Auth";
                        $("#open-two-factor").removeClass("btn-warning");
                        $("#open-two-factor").addClass("btn-success");
                    }
                }
            }
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
                    document.getElementById('old-pass').value = '';
                    document.getElementById('new-pass').value = '';
                    document.getElementById('new-confirm-pass').value = '';
                }
            }
        });
    }

    static doHandle2Factor() {

        var button_title = document.getElementById('open-two-factor').innerText;
        var action = "";

        if(button_title == 'Enable 2-Factor Auth') action = "enable"
        else if(button_title == 'Disable 2-Factor Auth') action = "disable"

        $.ajax({
            type: "POST",
            beforeSend: function(request) {
                request.setRequestHeader("Authentication", window.localStorage.getItem("token"));
            },
            data: {"action" : action},
            url: "https://secure-project.herokuapp.com/api/handle_two_factor_auth",
            success: function(data) {
                if(data['status'] == 'error') {
                    toastr.error(data['response']);
                } else {
                    toastr.success(data['response']);

                    if(data['response'] == 'Enabled!') {
                        document.getElementById('open-two-factor').innerText = "Disable 2-Factor Auth";
                        $("#open-two-factor").removeClass("btn-success");
                        $("#open-two-factor").addClass("btn-warning");
                    } else if(data['response'] == 'Disabled!') {
                        document.getElementById('open-two-factor').innerText = "Enable 2-Factor Auth";
                        $("#open-two-factor").removeClass("btn-warning");
                        $("#open-two-factor").addClass("btn-success");
                    }
                }
            }
        });
    }

    static doChange2FactorWay() {

        var button_title = document.getElementById('change-two-factor-way').innerText;
        var action = "";

        if(button_title.includes('QRCODE')) action = "SMS"
        else if(button_title.includes('SMS')) action = "QRCODE"

        $.ajax({
            type: "POST",
            beforeSend: function(request) {
                request.setRequestHeader("Authentication", window.localStorage.getItem("token"));
            },
            data: {"action" : action},
            url: "https://secure-project.herokuapp.com/api/change_two_factor_way",
            success: function(data) {
                if(data['status'] == 'error') {
                    toastr.error(data['response']);
                } else {
                    toastr.success(`It changed to the: ${data['response']}`);
                    document.getElementById('change-two-factor-way').innerText = `2Factor Login: ${data['response']}. Click for change!`;
                }
            }
        });
    }

    static showRememberCookie() {

        $.ajax({
            type: "GET",
            beforeSend: function(request) {
                request.setRequestHeader("Authentication", window.localStorage.getItem("token"));
            },
            url: "https://secure-project.herokuapp.com/api/get_remember_cookie",
            success: function(data) {
                if(data['status'] == 'error') {
                    toastr.error(data['message']);
                } else {
                    if(data['response'] == "yes") {
                        $("#delete-otp-container").removeClass("hidden");
                    } else if(data['response'] == "no") {
                        $("#delete-otp-container").addClass("hidden");
                    }
                }
            }
        });
    }


    static deletetOTPCookie() {

        $.ajax({
            type: "POST",
            beforeSend: function(request) {
                request.setRequestHeader("Authentication", window.localStorage.getItem("token"));
            },
            url: "https://secure-project.herokuapp.com/api/delete_remember_cookie",
            success: function(data) {
                if(data['status'] == 'error') {
                    toastr.error(data['response']);
                } else {
                    toastr.success('Remember me Disabled!');
                    $("#delete-otp-container").addClass("hidden");
                }
            }
        });
    }

    static doLogout() {
        window.localStorage.clear();
        window.location = "login.html";
    }

}