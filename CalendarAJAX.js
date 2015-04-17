// JavaScript Document

var begin_changed = 0;
var end_changed   = 0;

begindate.setReturnFunction("setMultipleValues2");
begindate.setWeekStartDay(1);        // week is Monday - Sunday
begindate.showYearNavigation();      // show year select
begindate.showNavigationDropdowns(); // month and year dropdowns

enddate.setReturnFunction("setMultipleValues3");
enddate.setWeekStartDay(1);
enddate.showYearNavigation();
enddate.showNavigationDropdowns();

function CalCheckDate() {

    var xmlHttp = null;

    try {
        // Firefox, Internet Explorer 7. Opera 8.0+, Safari
        xmlHttp = new XMLHttpRequest();
    } catch (e) {
        // Internet Explorer 6.
        try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                alert("Your browser does not support AJAX!");
                return false;
            }
        }
    }


    var arr = new Array(
         "section="+document.forms["modify"].section_id.value,
         "id="+document.forms["modify"].bookings_id.value,
         "endyear="+document.forms["modify"].endyear.value,
         "endmonth="+document.forms["modify"].endmonth.value,
         "endday="+document.forms["modify"].endday.value,
         "endhour="+document.forms["modify"].endhour.value,
         "endminute="+document.forms["modify"].endminute.value,
         "beginyear="+document.forms["modify"].beginyear.value,
         "beginmonth="+document.forms["modify"].beginmonth.value,
         "beginday="+document.forms["modify"].beginday.value,
         "beginhour="+document.forms["modify"].beginhour.value,
         "beginminute="+document.forms["modify"].beginminute.value
    );

    var params = arr.join("&");
         
    xmlHttp.open("POST", url, true);
    
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.setRequestHeader("Content-length", params.length);
    xmlHttp.setRequestHeader("Connection", "close");
    
    xmlHttp.onreadystatechange=function() {
        if(xmlHttp.readyState==4) {
            try {
                // Get the data from the server's response
                if ( xmlHttp.responseText != "" ) {
                    document.getElementById("ajax_response").innerHTML=xmlHttp.responseText;
                    document.getElementById("ajax_response").className='mod_bookings_fail';
                    document.getElementById("ajax_response").style.display='block';
                }
                else {
                    document.getElementById("ajax_response").className='mod_bookings_ok';
                    document.getElementById("ajax_response").innerHTML='ok';
                    document.getElementById("ajax_response").style.display='block';
                }
                
                if ( document.forms["modify"].beginhour.value   != 0
                  || document.forms["modify"].beginminute.value != 0 
                  || document.forms["modify"].endhour.value     != 0
                  || document.forms["modify"].endminute.value   != 0  )
                {
                    document.getElementById("daylong").checked = false;
                }
                else {
                    document.getElementById("daylong").checked = true;
                }
                
                var begin_date = new Date(
                                     document.forms["modify"].beginyear.value,
                                     parseInt(document.forms["modify"].beginmonth.value),
                                     parseInt(document.forms["modify"].beginday.value),
                                     parseInt(document.forms["modify"].beginhour.value),
                                     parseInt(document.forms["modify"].beginminute.value),
                                     0
                                 );
                                 
                var end_date   = new Date(
                                     document.forms["modify"].endyear.value,
                                     parseInt(document.forms["modify"].endmonth.value),
                                     parseInt(document.forms["modify"].endday.value),
                                     parseInt(document.forms["modify"].endhour.value),
                                     parseInt(document.forms["modify"].endminute.value),
                                     0
                                 );
                                 
                // check if begin_date is greater than end_date; if so, set
                // the end date form to begin_date
                if ( begin_date.getTime() > end_date.getTime() )
                {
                    document.forms["modify"].endyear.value  = begin_date.getFullYear();
                    document.forms["modify"].endmonth.value = begin_date.getMonth();
                    document.forms["modify"].endday.value   = begin_date.getDate();
                }

                if ( document.forms["modify"].beginday.value   == document.forms["modify"].endday.value
                  && document.forms["modify"].beginmonth.value == document.forms["modify"].endmonth.value
                  && document.forms["modify"].beginyear.value  == document.forms["modify"].endyear.value )
                {
                    if ( parseInt(document.forms["modify"].beginhour.value) > parseInt(document.forms["modify"].endhour.value) )
                    {
                        document.forms["modify"].endhour.value = document.forms["modify"].beginhour.value;
                    }
                }
            }
            catch (e) {
                alert("JavaScript error! Maybe your browser does not support AJAX!");
                return false;
            }
            
            xmlHttp=null;
        }
    }

    xmlHttp.send(params);
}

function toggleDaylong(url)
{
    if ( document.getElementById("daylong").checked == true )
    {
        document.forms["modify"].beginhour.value   = 0; 
        document.forms["modify"].beginminute.value = 0; 
        document.forms["modify"].endhour.value     = 0; 
        document.forms["modify"].endminute.value   = 0; 
    }
    CalCheckDate();
}

function setMultipleValues2(y,m,d) {
    document.forms["modify"].beginyear.value=y;
    document.forms["modify"].beginmonth.value=m;
    document.forms["modify"].beginday.value=d;
    if ( end_changed < 1 ) {
        document.forms["modify"].endyear.value=y;
        document.forms["modify"].endmonth.value=m;
        document.forms["modify"].endday.value=d;
    }
    begin_changed = 1;
    CalCheckDate();
}

function setMultipleValues3(y,m,d) {
    document.forms["modify"].endyear.value=y;
    document.forms["modify"].endmonth.value=m;
    document.forms["modify"].endday.value=d;
    if ( begin_changed < 1 ) {
        document.forms["modify"].beginyear.value=y;
        document.forms["modify"].beginmonth.value=m;
        document.forms["modify"].beginday.value=d;
    }
    end_changed = 1;
    CalCheckDate();
}

function checkDate() {
    CalCheckDate();
}
