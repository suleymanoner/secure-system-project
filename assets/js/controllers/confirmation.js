class Confirmation {

    static init() {
        Confirmation.doCheckForEmail();

        if(window.localStorage.getItem("token")) {
            window.location.href = "login.html";
        } else {
            $("body").show();
        }
    }

    static doCheckForEmail() {

        const link = window.location.href

        const params = new URLSearchParams(document.location.search);
        const random = params.get("rndm");

        var confirm_info = {
            "link" : link,
            "random" : random
        }

        if(random !== null) {
            $.ajax({
                type: "POST",
                url: "https://secure-project.herokuapp.com/api/confirm",
                data: confirm_info,
                success: function(data) {
                    if(data['status'] == 'error') {
                        toastr.error(data['response']);
                    } else {
                        toastr.success("You confirmed your account!")
                        window.localStorage.setItem("token", data['response']);
                        window.location = "index.html";
                    }
                }
            });
        }
    }
}