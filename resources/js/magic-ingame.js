$(document).ready(function () {
    var count = 0;
    var active = false;
    var youpnum = "#p1";
    if (!active) {
        setInterval(function () {
            count++;
            if (count >= 4) {
                count = 0;
            }
            var dots = new Array(count % 10).join('.');
            document.getElementById('waitmessage').innerHTML = "Esperando jugadores." + dots;
        }, 1000);
    }
    (function update() {

        $.ajax({
            url: 'assets/ajax/ajax.php?update',
            success: function (data) {
                var a_data = data.split(",");
                youpnum = "#p" + a_data['2'];
                if (a_data[6] == a_data[7] == a_data[8] == a_data[9] && a_data[9] != "0") {
                    var str_winner = "GANA ";
                    switch (a_data[6]) {
                        case 0:
                            str_winner += "EL JUGADOR 1";
                            break;
                        case 1:
                            str_winner += "EL JUGADOR 2";
                            break;
                        case 2:
                            str_winner += "EL JUGADOR 3";
                            break;
                        case 3:
                            str_winner += "EL JUGADOR 4";
                            break;
                        case 4:
                            str_winner += "LA CASA";
                            break;
                    }
                    $(".playerbox").html(str_winner);
                    setTimeout(function () {

                    }, 5000);
                } else {
                    if (a_data['10'] != "") {
                        $("#home").html("CASA: " + a_data['10']);
                    }
                    if (a_data['4'] > "1") {
                        $("#content").show();
                        $("#waiting").hide();
                        if (a_data['5'] == a_data['2']) {
                            //su turno
                            $("#actions").show();

                        } else {
                            $("#actions").hide();
                        }
                        for (var i = 1; i <= 4; i++) {
                            var pnum = "#p" + i;
                            if (i == a_data['5']) {
                                $(pnum).addClass('selectedpl');
                            } else {
                                //$(pnum).removeClass('selectedpl');
                                $(pnum).addClass('playerbox');
                            }
                            if (a_data[5 + i] != 0) {
                                $(pnum).html(a_data[5 + i]);
                            } else {
                                $(pnum).html("0");
                            }
                        }
                    }
                }
                //alert(data);
            },
            complete: function () {
                setTimeout(update, 2000);
            }
        });
    })();
    //Button functions
    $("#btn-pedircarta").click(function () {
        $(this).prop('disabled', true);
        $.ajax({
            url: "assets/ajax/ajax.php?pedircarta",
            success: function (data) {
                $(youpnum).append(' ' + data);
                $("#btn-pedircarta").removeAttr('disabled');
            }
        });
    });
    $("#btn-plantarse").click(function () {
        $(this).prop('disabled', true);
        $.ajax({
            url: "assets/ajax/ajax.php?plantarse",
            success: function (data) {
                $("#btn-plantarse").removeAttr('disabled');
                $("#actions").hide();
            }
        });
    });
});