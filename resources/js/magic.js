$(document).ready(function () {
    var audioElement = document.createElement('audio');
    audioElement.setAttribute('src', 'resources/sounds/join.mp3');
    //Loader
    $(".loader").delay(800).fadeOut(400, function () {
        $("#index-content").fadeIn(400);
    });
    //
    //  Global variables
    //
    var valid = false;
    //
    //  Functions
    //i
    //
    //  Buttons
    //
    $("#div-join").click(function () {
        $("#join").show();
        $("#div-join").addClass('selected');
        $("#div-create").removeClass('selected');
        $("#create").hide();
        $(".publicrow").fadeIn(150);
    });

    $("#div-register").click(function () {
        $("#register").show();
        $("#div-register").addClass('selected');
        $("#div-login").removeClass('selected');
        $("#login").hide();
    });

    $("#div-login").click(function () {
        $("#login").show();
        $("#div-login").addClass('selected');
        $("#div-register").removeClass('selected');
        $("#register").hide();
    });

    $("#div-create").click(function () {
        $("#join").hide();
        $("#div-join").removeClass('selected');
        $("#div-create").addClass('selected');
        $("#create").show();
        $(".publicrow").fadeOut(150);
    });

    $("#deletesession").click(function () {
        $.cookie('identifycode', "", {expires: -1, path: '/'});
        $.cookie('cookiecode', "", {expires: -1, path: '/'});
        $("#mainmenu").hide();
        $("#authmenu").fadeIn(300);
        $("#lbl-error-login").html('Sesión cerrada correctamente');
        $("#lbl-error-login").fadeIn(300);
    });
    $("#btn-join-go").click(function () {
        if (valid) {
            connecting(true);
            var salacode = $('#salacode').val();
            $.ajax({
                url: "assets/ajax/ajax.php?checkcode",
                data: {salacode: salacode},
                type: "POST",
                success: function (data) {
                    if (data != 0) {
                        join(salacode);
                    } else {
                        swal.close();
                        $("#lbl-error").html("La sala no existe.");
                        $("#lbl-error").fadeIn(300);
                        bordererror("#salacode", true);
                    }
                }
            });
        }
    });

    $("#btn-create-go").click(function () {
        var input = $("#salaname").val();
        if (valid) {
            connecting(false);
            $.ajax({
                url: "assets/ajax/ajax.php?createroom",
                data: {name: input},
                type: "POST",
                success: function (data) {
                    connecting(true);
                    join(data);
                }
            });
        }
    });
    $(".public").click(function () {
        swal({
            title: 'Conectando!',
            text: 'Conectando a la sala solicitada, por favor, espere...',
            timer: 6000, //timeout
            allowOutsideClick: false,
            allowEscapeKey: false,
            onOpen: () = > {
            swal.showLoading()
    }
    }).
        then((result) = > {
            if(result.dismiss === swal.DismissReason.timer;
    )
        {
            sweetAlert('Sala no encontrada', 'Lo lamentamos, no pudimos encontrar la sala solicitada. Verifica el estado de los servidores en status.magicgames.com', 'error');
        }
    })
    });
    $("#btn-login").click(function () {
        $("#index-content").hide();
        $(".loader").fadeIn(300);
        var username = $("#username").val();
        var password = $("#passwd").val();
        $.ajax({
            url: "auth.php?login",
            data: {username: username, password: password},
            type: "POST",
            success: function (data) {
                if (data != 0) {
                    var splitted = data.split("&");
                    $.cookie('identifycode', splitted[0], {expires: 7, path: '/'});
                    $.cookie('cookiecode', splitted[1], {expires: 7, path: '/'});
                    $(".titlemenu").html("Bienvenido, " + username + "!<a style='text-align:right;' id='deletesession'>  Cerrar sesión</a>");
                    $(".loader").hide();
                    $("#mainmenu").show();
                    $("#authmenu").hide();
                    $("#index-content").fadeIn(300);
                } else {
                    $(".loader").hide();
                    $("#index-content").fadeIn(300);
                    $("#lbl-error-login").html('Usuario o clave incorrectos');
                    bordererror("#username", true);
                    bordererror("#passwd", true);
                    $("#lbl-error-login").fadeIn(300);
                }
            }
        });
    });

    $("#btn-register").click(function () {
        $("#index-content").hide();
        $(".loader").fadeIn(300);
        var user = $("#rgstr-username").val();
        var passwd = $("#rgstr-passwd").val();
        var email = $("#rgstr-email").val();
        swal({
            title: 'Verificando...',
            text: 'Estamos verificando tus datos, por favor, espere...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            onOpen: () = > {
            swal.showLoading()
    }
    })
        $.ajax({
            url: "auth.php?register",
            data: {user: user, passwd: passwd, email: email},
            type: "POST",
            success: function (data) {
                var splitted = data.split("&");

                switch (splitted[0]) {
                    case "1":
                        //success
                        swal.close();
                        swal({
                            title: 'Registrando...',
                            text: 'Registrando tus datos..',
                            timer: 1500,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            onOpen: () = > {
                            swal.showLoading()
                }
            })
                $.cookie('identifycode', splitted[1], {expires: 7, path: '/'});
                $.cookie('cookiecode', splitted[2], {expires: 7, path: '/'});
                $(".titlemenu").html("Bienvenido, " + user + "!<a style='text-align:right;' id='deletesession'>  Cerrar sesión</a>");
                swal.close();
                $(".loader").hide();
                $("#mainmenu").show();
                $("#authmenu").hide();
                $("#index-content").fadeIn(300);
                break;
            case
                "2";
            :
                //user exists
                swal.close();
                swal("Usuario ya registrado", "El nombre de usuario o email ya está vinculado a una cuenta. Prueba con otro.", "error");
                $(".loader").hide();
                $("#index-content").fadeIn(300);
                break;
            case
                "3";
            :
                //Invalid email
                swal.close();
                swal("Email inválido", "A modo de evitar el fraude online y prevenir los robos de cuentas necesitamos que proporcione un email válido.", "error");
                $(".loader").hide();
                $("#index-content").fadeIn(300);
                break;
            }
            }
        });
    });

    $(".login-input").change(function () {
        bordererror("#username", false);
        bordererror("#passwd", false);
    });

    function bordererror(item, status) {
        if (status) {
            $(item).css({
                'border-color': '#ff524a',
                'border-style': 'solid'
            });
        } else {
            $(item).css({
                'border-style': 'none'
            });
        }
    }
    //
    //   Key events
    //
    $("#salaname").on('keyup', function () {
        var ths = $(this);
        if (ths.val().length < 1 || ths.val().length > 32) {
            valid = false;
            ths.css({
                'border-color': '#ff524a',
                'border-style': 'solid'
            });
            $("#lbl-error-create").html('El nombre es demasiado corto o largo.');
            $("#lbl-error-create").fadeIn(300);

        } else {
            valid = true;
            $("#lbl-error-create").fadeOut(300);
            ths.removeAttr("style");
        }
    });
    $("#salacode").on('keyup', function () {
        var ths = $(this);

        if (ths.val().length > 12) {
            valid = false;
            ths.css({
                'border-color': '#ff524a',
                'border-style': 'solid'
            });
            $("#lbl-error").html("El código no puede ser tan largo");
            $("#lbl-error").fadeIn(300);

        } else {
            valid = true;
            $("#lbl-error").fadeOut(300);
            ths.removeAttr("style");
        }
    });

    function join(salacode) {
        $.ajax({
            url: "assets/ajax/ajax.php?joinplayer",
            data: {salacode: salacode},
            type: "POST",
            success: function (data) {
                switch (data) {
                    case "1":
                        $.ajax({
                            url: "assets/modules/getgame.php",
                            data: {future: "nothing"},
                            type: "POST",
                            success: function (data) {
                                $("body").html(data);
                                audioElement.play();
                            }
                        });
                        break;
                    case "516":
                        alert("The security code does not match (Error code: 516)");
                        break;
                    case "517":
                        $.ajax({
                            url: "assets/modules/getgame.php",
                            data: {future: "nothing"},
                            type: "POST",
                            success: function (data) {
                                $("body").html(data);
                                audioElement.play();
                            }
                        });
                        break;
                }
                swal.close();
            }
        });
    }

    function connecting(bool_type) {
        if (bool_type) {
            swal({
                title: 'Conectando!',
                text: 'Conectando a la sala solicitada, por favor, espere...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                onOpen: () = > {
                swal.showLoading()
        }
        })
        } else {
            swal({
                title: 'Creando!',
                text: 'Creando una nueva sala, por favor, espere...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                onOpen: () = > {
                swal.showLoading()
        }
        })
        }
    }
});