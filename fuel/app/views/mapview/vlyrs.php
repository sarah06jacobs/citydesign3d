<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<TITLE>City Design</TITLE>
<LINK REL="StyleSheet" HREF="/css/style.css" TYPE="text/css">

<script type="text/javascript" src="/js/jquery.js" ></script>
<script type="text/javascript" src="/js/nano.js" ></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script language="javascript">


var points = [];
var bld_ht = 3;
var bld_ground = 0;
var edit_id = -1;
var clickmode = 0;
var editlayer = "";

var layer_map = { 
                lyr_1001: "tatemono_2" ,
                lyr_1002: "tatemono_2_ts" ,
                lyr_2000: "tatemono_v" ,
                lyr_2001: "tatemono_v_ts" ,
            };

var step = 100;
var vrmlobj_id = -1;
var vrmlobj_file = "";
var vrmlobj_tfm = "";
var editType = 1; // 1 - building, 2- vrml
var panelOpen = 1;
var panelWidth = 270;
function togglePanel() {
    if( panelOpen == 1 ) {
        document.getElementById("contenttop").style.display = "none";
        document.getElementById("filler").style.display = "block";
        
        document.getElementById("footerbutton").value="＞";
        panelOpen = 0;
        timer=setInterval(
        function()
        {
          if(panelWidth<=30){
              clearTimeout(timer);
              panelWidth = 30;
              parent.frames["topframe"].setAttribute('cols','30,*');
          }
          else  {
            panelWidth -= 30;
              parent.frames["topframe"].setAttribute('cols',(panelWidth)+',*');
         }
        }
        ,2);
    }
    else {
        document.getElementById("contenttop").style.display = "block";
        document.getElementById("filler").style.display = "none";
        document.getElementById("footerbutton").value="＜";
        timer=setInterval(
        function()
        {
          if(panelWidth>=270){
              clearTimeout(timer);
              panelWidth = 270;
              parent.frames["topframe"].setAttribute('cols','270,*');
          }
          else  {
            panelWidth += 30;
              parent.frames["topframe"].setAttribute('cols',(panelWidth)+',*');
         }
        }
        ,2);
        panelOpen = 1;
    }
}

function openPanel( pname , clname ) {
	var i, pcontent;
	pcontent = document.getElementsByClassName(clname);
	for (i = 0; i < pcontent.length; i++) {
    	pcontent[i].style.display = "none";
  	}
  	if( pname != '' ) {
  		document.getElementById(pname).style.display = "block";
  	}
}

function openTab(tabname, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  document.getElementById(tabname).className += " active";
}

function stopEdit() {
    openPanel('addobjectdiv', 'panelcontent');
}

function setMapStatus(lid){
	var lobj = document.getElementById("mlayer"+lid);
	var setstr = (lobj.checked) ? "ON" : "OFF";
	lid = lid -1;
	var lyrswitch = [["top_annotation","middle_annotation"],
	["top_line","aza_polygon", "oaza_polygon"] ,
	["middle_road_line","city_road_0","city_road_1","city_road_2"] ,
	["middle_railway","city_railway"],
	["tatemono_2", "tatemono_v", "tatemono_2_ts", "tatemono_v_ts"] ];

	for(i=0;i<lyrswitch[lid].length;i++) {
		parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty(lyrswitch[lid][i],"status",setstr);
	}
    var dragonfly = parent.frames["dragonfmap"].dragonfly;
    if( lid == 0 ) {
        // building labels
        if( lobj.checked ) {
            dragonfly.setShapeLayerProperty("tatemono_2","LABELVISIBLE","ON");
            dragonfly.setShapeLayerProperty("tatemono_v","LABELVISIBLE","ON");
            dragonfly.setShapeLayerProperty("tatemono_2_ts","LABELVISIBLE","ON");
            dragonfly.setShapeLayerProperty("tatemono_v_ts","LABELVISIBLE","ON");
        }
        else {
            dragonfly.setShapeLayerProperty("tatemono_2","LABELVISIBLE","OFF");
            dragonfly.setShapeLayerProperty("tatemono_v","LABELVISIBLE","OFF");
            dragonfly.setShapeLayerProperty("tatemono_2_ts","LABELVISIBLE","OFF");
            dragonfly.setShapeLayerProperty("tatemono_v_ts","LABELVISIBLE","OFF");
        }
    }
}

function init(){
	openTab('tablink1', 'tabdiv1');
	openPanel('addobjectdiv', 'panelcontent');

    // set time slider
    var margin = 12;
    var cw = 10;
    var width = 200;
    var mmin = 0;
    var mmax = 360;
    // set month and day to current
    var d = new Date();
    var yyyy = d.getFullYear();
    // get months from year
    var yearmonths = (yyyy - 2000) * 12;
    var mm = d.getMonth()+1; // 1 - 12
    var dd = d.getDate()+1; // 1 - 31
    var mmstr = d.getMonth() < 9 ? "0" + (d.getMonth() + 1) : (d.getMonth() + 1);
    var ddstr = d.getDate() < 10 ? "0" + d.getDate() : d.getDate();
    var totmonths = yearmonths + mm;
    document.getElementById('timesliderval').value = totmonths;
    document.getElementById("timeslidertext").value = yyyy + "-" + mmstr + "-" + ddstr;
    document.getElementById('timesliderpuck').style.left = ((document.getElementById('timesliderval').value-mmin)*width/(mmax-mmin)-margin-cw/2) + "px";
    updateTimeSliderText();
}

function setPathData(path, value) {
    var firstStep = 6 / step * value;
    var secondStep = 2 / step * value;
    path.attr('d', 'M1,' + (7 - firstStep) + ' C6.33333333,' + (2 + secondStep) + ' 11.6666667,' + (1 + firstStep) + ' 17,' + (1 + firstStep) + ' C22.3333333,' + (1 + firstStep) + ' 27.6666667,' + (2 + secondStep) + ' 33,' + (7 - firstStep));
}


function datestr() {
    var d = new Date();
    var yyyy = d.getFullYear();
    var mm = d.getMonth() < 9 ? "0" + (d.getMonth() + 1) : (d.getMonth() + 1);
    var dd = d.getDate() < 10 ? "0" + d.getDate() : d.getDate();
    return yyyy + "-" + mm + "-" + dd;
}

function itemClicked(id,layerid,inclusive) {

    if ( (layerid-0) > 3000 ) {
        return;
    }

	if( clickmode > 0 ) {
		return;
	}

	var dragonfly = parent.frames["dragonfmap"].dragonfly;
	edit_id = id;

	var data = {
        layer: layer_map["lyr_" + layerid],
        id: id,
    };

	$.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/getobject",        // POST送信先のURL
        //data: data,
        data:JSON.stringify(data),  // JSONデータ本体
        contentType: 'application/json', // リクエストの Content-Type
        dataType: "json",           // レスポンスをJSONとしてパースする
        success: function(json_data) {   // 200 OK時
            // JSON Arrayの先頭が成功フラグ、失敗の場合2番目がエラーメッセージ
            if (!json_data['layer']) {    // サーバが失敗を返した場合
                alert("Transaction error. " + json_data[1]);
                return;
            }
            else {
            	var objects = json_data['objects'];
            	if(objects.length > 0) {
            		openTab('tablink2', 'tabdiv2');
            		openPanel('editobjectdiv', 'panelcontent');

                    editlayer = json_data['layer'];
                    document.getElementById("editdate").value = objects[0]['create_date'];
            		document.getElementById("edittname").value = objects[0]['tname'];
                    document.getElementById("editenddate").value = objects[0]['end_date'];
            	}
            }
            // 成功時処理
        },
        error: function() {         // HTTPエラー時
            alert("Server Error. Please try again later.");
        },
        complete: function() {      // 成功・失敗に関わらず通信が終了した際の処理
            //button.attr("disabled", false);  // ボタンを再び enableにする
        }
    });	
}


function mapOutputCoords(outx,  alt, outy, li, si, pi, isadd, iscw) {
	
}

function changePref(obj) {
	if (obj.value == -1) {
		removeSelectOpts('oazaselect');
		removeSelectOpts('cityselect');
		return;
	}

    var data = {
        pref_code: obj.value
    };

    $.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/addrcity",        // POST送信先のURL
        data:JSON.stringify(data),  // JSONデータ本体
        contentType: 'application/json', // リクエストの Content-Type
        dataType: "json",           // レスポンスをJSONとしてパースする
        success: function(json_data) {   // 200 OK時
            // JSON Arrayの先頭が成功フラグ、失敗の場合2番目がエラーメッセージ
            if (!json_data['list']) {    // サーバが失敗を返した場合
                alert("Transaction error. ");
                return;
            }
            else {
                var select = document.getElementById('cityselect');
                removeSelectOpts('cityselect');
                removeSelectOpts('oazaselect');
   
                var list = json_data['list'];
                for ( i=0; i<list.length; i++ ) {
                    var lobj = list[i];
                    var option = document.createElement('option');
                    option.setAttribute('value', lobj['city_code']);
                    option.appendChild(document.createTextNode(lobj['city_name']));
                    select.appendChild(option);
                }
                var lat = json_data['lat'];
                var lon = json_data['lon'];
                var dragonfly = parent.frames["dragonfmap"].dragonfly;
                dragonfly.addAutoFly(" -dest "+lon+" 7000 "+ lat +" -etilt -90 -delay 0" );
            }
            // 成功時処理
        },
        error: function() {         // HTTPエラー時
            alert("Server Error. Please try again later.");
        },
        complete: function() {      // 成功・失敗に関わらず通信が終了した際の処理
        }
    });
}

function removeSelectOpts(name)
{
	var select = document.getElementById(name);
    var i, L = select.options.length - 1;
    for(i = L; i >= 1; i--) {
       select.remove(i);
    }
}

function filterPlaces() {
    var kw = document.getElementById('searchplacename').value;

    var data = {
        kw: kw
    };

    $.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/getplaces",        // POST送信先のURL
        data:JSON.stringify(data),  // JSONデータ本体
        contentType: 'application/json', // リクエストの Content-Type
        dataType: "json",           // レスポンスをJSONとしてパースする
        success: function(json_data) {   // 200 OK時
            // JSON Arrayの先頭が成功フラグ、失敗の場合2番目がエラーメッセージ
            if (!json_data['list']) {    // サーバが失敗を返した場合
                alert("Transaction error. ");
                return;
            }
            else {
                var list = json_data['list'];
                reloadPlaces(list);
            }
            // 成功時処理
        },
        error: function() {         // HTTPエラー時
            alert("Server Error. Please try again later.");
        },
        complete: function() {      // 成功・失敗に関わらず通信が終了した際の処理
        }
    });
}

function gotoPlace(wx,wy,cx,cy,alt,pitch,dir,out) {
    var dragonfly = parent.frames["dragonfmap"].dragonfly;
    
    var isEarth = dragonfly.isDisplayEarth();
    if( isEarth == 1 ) {
        dragonfly.setCameraMapPos(cx,cy,alt,pitch,dir);
        dragonfly.setCenterMapPos(cx,cy);
        dragonfly.SetPropertyD(36,out);
    }
    else {
        dragonfly.setCameraMapPos(wx,wy,alt,pitch,dir);
    }
    
}

function reloadPlaces(list) {
    // placestable
    var table = document.getElementById('placestable');
    var rowCount = table.rows.length;
    for (var i = rowCount-1; i >=0; i--) {
        table.deleteRow(i);
    }

    var rootdiv = document.getElementById('placeslist');
    rowCount = 0;
    for ( var i=0; i<list.length; i++ ) {
        var obj = list[i];

        var row = table.insertRow(i);
        var cell1 = row.insertCell(0);
        cell1.setAttribute("style" ,"width:200px;outline-color:lightgrey;outline-style: solid;outline-width: 1px;");
        var span = document.createElement("span");
        span.id = "place" + obj['places_id'];
        span.setAttribute("style" ,"width:200px;height:30px;margin-left: 2px;margin-top: 2px;display: inline-block;padding: 3px;");
        span.setAttribute("onclick","gotoPlace("+obj['lon']+","+obj['lat']+","  + obj['center_lon']+","+obj['center_lat']+ "," +obj['alt']+","+obj['pitch']+","+obj['dir']+","+obj['out']+");");
        span.innerHTML = " " + obj['pname'] + " ";
        cell1.appendChild(span);

        var cell2 = row.insertCell(1);
        cell2.setAttribute("style" ,"width:30px;text-align: center;");
        var at = document.createElement("a");
        at.setAttribute("onclick","placeToCB('"+obj['url']+"');");
        at.setAttribute("style" ,"color:green;text-decoration: none;");
        at.innerHTML = "url";
        cell2.appendChild(at);

        rowCount++;
    }
}

function placeToCB(url) {
    var base = "<?= $host; ?>/hawkeye/";
    alert( base + url );
    var text = base + url;
    if (window.clipboardData) { // Internet Explorer
        window.clipboardData.setData("Text", text);
    } else {
        unsafeWindow.netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        const clipboardHelper = Components.classes["@mozilla.org/widget/clipboardhelper;1"].getService(Components.interfaces.nsIClipboardHelper);
        clipboardHelper.copyString(text);
    }
}

function changeCity(obj) {
	if (obj.value == -1) {
		removeSelectOpts('oazaselect');
		return;
	}
    var data = {
        pref_code: document.getElementById('prefselect').value,
        city_code: document.getElementById('cityselect').value
    };

    $.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/addroaza",        // POST送信先のURL
        data:JSON.stringify(data),  // JSONデータ本体
        contentType: 'application/json', // リクエストの Content-Type
        dataType: "json",           // レスポンスをJSONとしてパースする
        success: function(json_data) {   // 200 OK時
            // JSON Arrayの先頭が成功フラグ、失敗の場合2番目がエラーメッセージ
            if (!json_data['list']) {    // サーバが失敗を返した場合
                alert("Transaction error. ");
                return;
            }
            else {
                var select = document.getElementById('oazaselect');
                removeSelectOpts('oazaselect');
   
                var list = json_data['list'];
                for ( i=0; i<list.length; i++ ) {
                    var lobj = list[i];
                    var option = document.createElement('option');
                    option.setAttribute('value', lobj['oaza_code']);
                    option.appendChild(document.createTextNode(lobj['oaza_name']));
                    select.appendChild(option);
                }
                var lat = json_data['lat'];
                var lon = json_data['lon'];
                var dragonfly = parent.frames["dragonfmap"].dragonfly;
                dragonfly.addAutoFly(" -dest "+lon+" 3000 "+ lat +" -etilt -90 -delay 0" );
            }
            // 成功時処理
        },
        error: function() {         // HTTPエラー時
            alert("Server Error. Please try again later.");
        },
        complete: function() {      // 成功・失敗に関わらず通信が終了した際の処理
        }
    });
}

function changeOaza(obj) {
	if (obj.value == -1) {
		return;
	}

    var data = {
        pref_code: document.getElementById('prefselect').value,
        city_code: document.getElementById('cityselect').value,
        oaza_code: document.getElementById('oazaselect').value
    };

    $.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/addraza",        // POST送信先のURL
        data:JSON.stringify(data),  // JSONデータ本体
        contentType: 'application/json', // リクエストの Content-Type
        dataType: "json",           // レスポンスをJSONとしてパースする
        success: function(json_data) {   // 200 OK時
            // JSON Arrayの先頭が成功フラグ、失敗の場合2番目がエラーメッセージ
            if (!json_data['lat']) {    // サーバが失敗を返した場合
                alert("Transaction error. ");
                return;
            }
            else {
                var lat = json_data["lat"];
                var lon = json_data["lon"];
                var dragonfly = parent.frames["dragonfmap"].dragonfly;
                dragonfly.addAutoFly(" -dest "+lon+" 800 "+ lat +" -etilt -90 -delay 0" );
            }
            // 成功時処理
        },
        error: function() {         // HTTPエラー時
            alert("Server Error. Please try again later.");
        },
        complete: function() {      // 成功・失敗に関わらず通信が終了した際の処理
        }
    });
}

function updateTimeSliderText() {
    var v = document.getElementById("timeslidertext").value;

    // validate date
    var dateArray = v.split("-");
    if(dateArray.length == 3) {
        var yr = dateArray[0]-0;
        var mn = dateArray[1]-0;
        var d = dateArray[2]-0;

        if( yr >= 2000 && yr <= 2050 && mn >= 1 && mn <= 12 && d >= 1 && d <= 31 ) {
            updateTimeData( v );

            var totmonths = ((yr-2000)*12)+mn;

            var margin = 12;
            var cw = 10;
            var width = 200;
            var mmin = 0;
            var mmax = 360;
            document.getElementById('timesliderval').value = totmonths;
            document.getElementById('timesliderpuck').style.left = ((document.getElementById('timesliderval').value-mmin)*width/(mmax-mmin)-margin-cw/2) + "px";
        }

    }
}

function updateTimeSlider() {
    var v = document.getElementById("timesliderval").value;
    
    var fromdate = new Date(2000, v, 1);

    var fyear = fromdate.getFullYear();
    var fmonth = fromdate.getMonth() - -1;
    var fmstr = fmonth + "";
    if( fmonth < 10 ) {
        fmstr = "0" + fmstr;
    }

    var from_date_str = fyear + "-" + fmstr + "-01";
    document.getElementById("timeslidertext").value = from_date_str;

    updateTimeData( from_date_str );
}

function updateTimeData( from_date_str ) {
    if( date_filter_from !== from_date_str) {
        date_filter_from = from_date_str;
        pm_filter_from = getTimeStamp(from_date_str);
        
        var dragonfly = parent.frames["dragonfmap"].dragonfly;
        dragonfly.setShapeLayerProperty("tatemono_2_ts","CGIREQUEST", "/api/gis/getlayer?pool=hawk&fromts=0");
        dragonfly.setShapeLayerProperty("tatemono_2_ts","RELOADLAYER", "1");
        dragonfly.setShapeLayerProperty("tatemono_v_ts","CGIREQUEST", "/api/gis/getlayer?pool=hawk&gty=POINT&fromts=0");
        dragonfly.setShapeLayerProperty("tatemono_v_ts","RELOADLAYER", "1");

        editlayer = "";
        stopEdit();
    }
}

var date_filter_from = "";
var date_filter_to = "";

function getTimeStamp(myDate) {
    myDate = myDate.split("-");
    var newDate = new Date( myDate[0], myDate[1] - 1, myDate[2]);
    return (newDate.getTime()/1000);
}

</script>

<script>

strippx=function(v){return parseInt(v.replace('px',''),10);}

dragslider=function(d,container,width,min,max,val){
        var oldx=strippx(d.style.left);
        var dragging=false;
        var ox,posx,x;
        
        var margin=12; //cursor margin
        var cw=10; //cursor width
        
        if (self.event&&event.touches) event.preventDefault();
        
        d.onmousemove=function(e){
            if (e) x=e.screenX; else x=event.screenX;
            if (self.event&&event.touches) x=e.touches[0].screenX;
            
            if (!dragging){ox=x;dragging=true;return;}
            
            posx=oldx+x-ox;
            if (posx<0-margin-cw/2) posx=0-margin-cw/2;
            if (posx>width-margin-cw/2) posx=width-margin-cw/2;
            d.style.left=posx+'px'; 
            gid(container).value=Math.round((posx+margin+cw/2)*(max-min)/width)+min;
            updateTFM();      
        }
        d.ontouchmove=d.onmousemove;
        
        d.onmouseup=function(){
            d.onmousemove=null;d.ontouchmove=null;  
            document.onmousemove=null; document.onmouseup=null;
            updateTFM();
        }
        document.onmousemove=d.onmousemove; document.onmouseup=d.onmouseup;
        d.ontouchend=d.onmouseup;
}


dragtime=function(d,container,width,min,max,val){
        var oldx=strippx(d.style.left);
        var dragging=false;
        var ox,posx,x;
        
        var margin=12; //cursor margin
        var cw=10; //cursor width
        
        if (self.event&&event.touches) event.preventDefault();
        
        d.onmousemove=function(e){
            if (e) x=e.screenX; else x=event.screenX;
            if (self.event&&event.touches) x=e.touches[0].screenX;
            
            if (!dragging){ox=x;dragging=true;return;}
            
            posx=oldx+x-ox;
            if (posx<0-margin-cw/2) posx=0-margin-cw/2;
            if (posx>width-margin-cw/2) posx=width-margin-cw/2;
            d.style.left=posx+'px'; 
            gid(container).value=Math.round((posx+margin+cw/2)*(max-min)/width)+min;
            //updateTFM();
            updateTimeSlider();     
        }
        d.ontouchmove=d.onmousemove;
        
        d.onmouseup=function(){
            d.onmousemove=null;d.ontouchmove=null;  
            document.onmousemove=null; document.onmouseup=null;
            //updateTFM();
            updateTimeSlider();   
        }
        document.onmousemove=d.onmousemove; document.onmouseup=d.onmouseup;
        d.ontouchend=d.onmouseup;
}

</script>
<style>
body{
background-color:#788880;
font-color:#F6FEFE;
color:#F6FEFE;
display: flex;
flex-direction: column;
}

html, body {
  height: 100%;
}
.content {
  flex: 1 0 auto;
}


/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}

.panelcontent {
    display: none;
}
.newpanelcontent {
    display: none;
}
.editpanelcontent {
    display: none;
}
.footer {
  flex-shrink: 0;
}

</style>
</HEAD>
<BODY leftmargin="1" rightmargin="1" topmargin="1" bottommargin="1" onload="init();">

<div class="content" id="contenttop">

<div class="tab">
  <button id="tablink1" class="tablinks" onclick="openTab('tablink1', 'tabdiv1');">レイヤー</button>
  <button id="tablink2" class="tablinks" onclick="openTab('tablink2', 'tabdiv2');">オブジェクト</button>
  <button id="tablink3" class="tablinks" onclick="openTab('tablink3', 'tabdiv3');">検索</button>
</div>

<div id="tabdiv1" class="tabcontent">
			
	<table width="100%">
		<tr><td colspan="2" style="border-bottom:black solid 1px;"><b>Layers</b></td></tr>
	</table>
	<table>
	<tr>
		<td width="10px">&nbsp; </td>
		<td width="5px"><input type="checkbox" id="mlayer1" onclick="setMapStatus(1);"></td>
		<td>注記</td>
	</tr>

	<tr>
		<td width="10px">&nbsp; </td>
		<td width="5px"><input type="checkbox" id="mlayer2" onclick="setMapStatus(2);"></td>
		<td>境界線</td>
	</tr>

	<tr>
		<td width="10px">&nbsp; </td>
		<td width="5px"><input type="checkbox" id="mlayer3" onclick="setMapStatus(3);"></td>
		<td>道路</td>
	</tr>

	<tr>
		<td width="10px">&nbsp; </td>
		<td width="5px"><input type="checkbox" id="mlayer4" onclick="setMapStatus(4);"></td>
		<td>鉄道線</td>
	</tr>

	<tr>
		<td width="10px">&nbsp; </td>
		<td width="5px"><input type="checkbox" id="mlayer5" onclick="setMapStatus(5);" checked></td>
		<td>建物</td>
	</tr>


	</table>
</div>

<div id="tabdiv2" class="tabcontent">

<div id="addobjectdiv" class="panelcontent">
<table>
    <tr>
        <td> オブジェクト表示日付
        </td>
    </tr>
    <tr>
        <td>
<div style="position:relative;width:200px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div id="timesliderpuck" ontouchstart="dragtime(this,'timesliderval',200,0,360,0);" 
        onmousedown="dragtime(this,'timesliderval',200,0,360,0);" 
        style="position:absolute;top:-15px;left:0px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
    </div> 
            
        </td>
    </tr>
    <tr>
        <td> 
            <input type="text" style="width:200px;" id="timeslidertext" value="2000-01-01"/>
            <input type="button" value="適応" onclick="updateTimeSliderText();" />
            <input type="hidden" id="timesliderval" value="22" onchange="updateTimeSlider();" />
        </td>
    </tr>
</table>
</div>

<div id="editobjectdiv" class="panelcontent">
	<table>
		<tr>
			<td>
				名前：
			</td>
			<td>
				<input type="text" id="edittname" name="edittname" value="" disabled="1" />
			</td>
		</tr>
        <tr>
            <td>
                作成日：
            </td>
            <td>
                <input type="text" id="editdate" name="editdate" value=""  disabled="1" />
            </td>
        </tr>
        <tr>
            <td>
                終了日：
            </td>
            <td>
                <input type="text" id="editenddate" name="editenddate" value=""  disabled="1" />
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="button" onclick="stopEdit()" value="閉じる" />
            </td>
        </tr>
	</table>
</div>

</div>

<div id="tabdiv3" class="tabcontent">
    
    <table>
        <tr>
            <td>
                <select id="prefselect" name="prefselect" onchange="changePref(this);">
                	<option value=-1>選択してください</option>
                    <? foreach ($prefecture as $pref) { ?>
                    <option value="<?= $pref["pref_code"] ?>"><?= $pref["pref_name"] ?></option>
                    <? } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <select id="cityselect" name="cityselect" onchange="changeCity(this);">
                    <option value=-1>選択してください</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <select id="oazaselect" name="oazaselect" onchange="changeOaza(this);">
                    <option value=-1>選択してください</option>
                </select>
            </td>
        </tr>
        
    </table>
    <hr>
    <h4>
    お気に入り場所</h4>
    <br>
    <form id="placescsvform" action="placescsv" method="post">
    filter:<br>
    <input type="textbox" value="" id="searchplacename" style="width:200px" onkeyup="filterPlaces();"> 
    <br>
    <div id="placeslist" style="overflow-y:scroll;height:430px;width:250px;">
        <br />
        <table id="placestable" name="placestable" style="width:230px;">
            <? foreach ($places as $place) { ?>
            <tr>
                <td style="width:200px;outline-color:lightgrey;outline-style: solid;outline-width: 1px;">

                <span id="place<?= $place['places_id'] ?>" style="width:200px;height:30px;margin-left: 2px;margin-top: 2px;display: inline-block;padding: 3px;" 
            onclick="gotoPlace(<?= $place['lon']; ?>,<?= $place['lat']; ?>,<?= $place['center_lon']; ?>,<?= $place['center_lat']; ?>,<?= $place['alt']; ?>,<?= $place['pitch']; ?>,<?= $place['dir']; ?>, <?= $place['out']; ?>);"> <?= $place['pname'] ?> </span>

                </td>
                <td style="width:30px;text-align: center;">
                    <a style="color:green;text-decoration: none;" onclick="placeToCB('<?= $place['url']; ?>');">url</a>
                </td>
            </tr>
            <? } ?>
        </table>
    </div>
    </form>
</div>

</div>
<div id="filler" style="display: none" class="content" >
&nbsp;<br>
</div>
<footer class="footer" style="text-align: right;width:100%;">
    <input type="button" id="footerbutton" onclick="togglePanel();" value="＜">
</footer>
</BODY>
</HTML>