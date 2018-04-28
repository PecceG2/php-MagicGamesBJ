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