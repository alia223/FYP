const urlParams = new URLSearchParams(window.location.search);
const yearandmonth = urlParams.get('ym');
const formattedyearandmonth = yearandmonth.split("-");
const month = formattedyearandmonth[1];
const year = formattedyearandmonth[0];
var tbl = document.getElementById("calendar");
if (tbl != null) {
    for (var i = 0; i < tbl.rows.length; i++) {
        for (var j = 0; j < tbl.rows[i].cells.length; j++)
            tbl.rows[i].cells[j].onclick = function () { getVal(this); };
    }
}
function getVal(cell) {
    document.cookie =  "day=" + cell.childNodes[0].childNodes[0].data;
    document.cookie =  "month=" + month;
    document.cookie =  "year=" + year;
    var cookieDate = document.cookie.split(";");
    var finalDate = "";
    for(var i = 0;i < cookieDate.length;i++) {
        if(cookieDate[i].trim().match(new RegExp('.*day=(.*)')) || cookieDate[i].trim().match(new RegExp('.*month=(.*)')) || cookieDate[i].trim().match(new RegExp('.*year=(.*)'))) {
            finalDate += cookieDate[i];
        }
    }
    finalDate = finalDate.split(" ");
    for(var i = 0;i < finalDate.length;i++) {
        var temp = finalDate[i].split("=");
        finalDate[i] = temp[1];
    }
    document.getElementById('hiddenDay').value = finalDate[2];
    document.getElementById('hiddenMonth').value = finalDate[0];
    document.getElementById('hiddenYear').value = finalDate[1];
}