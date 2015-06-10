<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="animals.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

    <title>Project Animals</title>
</head>

<body>
<div class="container">
    <form id="form">
        <div id="buttons">
            <button type="button" class="btn btn-primary btn-md" name="suggest">
                <span class="glyphicon glyphicon-globe" aria-hidden="true"> Предложи животни</span>
            </button>
            <button type="button" class="btn btn-danger btn-md" name="favorites">
                <span class="glyphicon glyphicon-star" aria-hidden="true"> Любими</span>
            </button>
            <button type="button" id="tabView" class="btn btn-warning btn-md" name="tabularView" disabled>
                <span id="btn-tab" class="glyphicon glyphicon-th" aria-hidden="true"> Изглед</span>
            </button>
        </div>
        <div class="table-responsive">
            <table class="table-bordered"></table>
        </div>
    </form>
</div>
<script>
    var nTabularView = 0;

    function printTh() {
    $("table").append(
        "<tr id='t_head'>" +
            "<th>Любимо?</th>" +
            "<th>Вид:</th>" +
            "<th>Име:</th>" +
            "<th>Снимка:</th>" +
        "</tr>");
    }

    function appendAnimal(oResponse, nLength) {
        var sTemp = '', sCheckbox, i;
        for(i=0; i<nLength; i++) {
            if (oResponse[i].is_fav == 1) sCheckbox = "<input type='hidden' name='checkbox[]' value=" + (-oResponse[i].id) + ">" +
            "<div class='checkbox'><input type='checkbox' name='checkbox[]' value=" + oResponse[i].id + " checked></div>";
            else sCheckbox = "<input type='hidden' name='checkbox[]' value=" + (-oResponse[i].id) + ">" +
            "<div class='checkbox'><input type='checkbox' name='checkbox[]' value=" + oResponse[i].id + "></div>";

            if(nTabularView == 0) {
                sTemp +=
                    "<tr>" +
                        "<td class='boxes'>" +
                        sCheckbox +
                        "</td>" +
                        "<td>" + oResponse[i].type + "</td>" +
                        "<td>" + oResponse[i].name + "</td>" +
                        "<td>" +
                        "<img class='img-responsive' src=" + oResponse[i].pic_url + " alt='wrong image link'>" +
                        "</td>" +
                    "</tr>";
            }
            else {
                if(i%4 == 0 && nLength-1 != i && i != 0) sTemp += "</tr>";
                if(i%4 == 0 || i == 0) sTemp += "<tr>";
                sTemp +=
                    "<td class='boxes'>" +
                        sCheckbox + "<div>" +
                        oResponse[i].type + "-" +
                        oResponse[i].name + "</div>" +
                        "<img class='img-responsive' src=" + oResponse[i].pic_url + " alt='wrong image link'>" +
                    "</td>";
                if(i == nLength-1) sTemp += "</tr>";
            }
        }
        if(nTabularView == 0) printTh();
        $("table").append(sTemp);
    }

    jQuery(document).ready(function() {
        var nLastView = 0;
        var nBtn = 0;
        $('.btn-primary, .btn-danger, .btn-warning').click(function() {
            var sTemp, nLamp = 0;
            if(this.name == 'suggest') {
                sTemp = "suggest";
                nLastView = 1;
                nTabularView = 0;
                $("#tabView").attr('disabled', 'disabled');
                $("#btn-tab").attr('class', 'glyphicon glyphicon-th');
            }
            else if(this.name == 'favorites') {
                sTemp = "favorites";
                nLastView = 2;
                $("#tabView").removeAttr('disabled');
            }
            else {
                if(!nBtn) {
                    $("#btn-tab").attr('class', 'glyphicon glyphicon-list-alt');
                    nBtn = 1;
                }
                else {
                    $("#btn-tab").attr('class', 'glyphicon glyphicon-th');
                    nBtn = 0;
                }
                if(nLastView == 2) {
                    sTemp = "changeView";
                    if (nTabularView == 0) nTabularView = 1;
                    else nTabularView = 0;
                }
                else nLamp = 1;
            }
            if(nLamp == 0) {
                jQuery.ajax({
                    url: "q.php?action=extractAnimals",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        'data': sTemp
                    },
                    success: function (oResponse) {
                        var nLength = Object.keys(oResponse).length;
                        $("table").empty();
                        appendAnimal(oResponse, nLength);
                    },
                    error: function (jqXHR, exception) {
                        console.log(jqXHR);
                        console.log(exception);
                        alert('error 1');
                    }
                });
            }
        });

        $("table").on("change", "[type=checkbox]", function() {
            var dataString = $("#form").serialize();
            jQuery.ajax({
                url: "q.php?action=clickAnimal",
                type: "POST",
                dataType: 'json',
                data: dataString,
                success: function (oResponse) {
                    var nLength = Object.keys(oResponse).length;
                    if(oResponse != false) {
                        $("table").empty();
                        appendAnimal(oResponse, nLength);
                    }
                    else if(nLength == 0 && oResponse != false) {
                        $("table").empty();
                    }
                },
                error: function (jqXHR, exception) {
                    console.log(jqXHR);
                    console.log(exception);
                    alert('error 2');
                }
            });
        });
    });

</script>
</body>

</html>