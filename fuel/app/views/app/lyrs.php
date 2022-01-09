<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<TITLE>City Design</TITLE>
<LINK REL="StyleSheet" HREF="/css/style.css" TYPE="text/css">


<script type="text/javascript" src="/js/jquery.js" ></script>
<script type="text/javascript" src="/js/nano.js" ></script>
<script type="text/javascript" src="/js/sliders.js" ></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script language="javascript">

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

var layer_map = { lyr_1000: "tatemono_1" ,
				lyr_1001: "tatemono_2" ,
				lyr_2000: "tatemono_v"
			};

function setMapStatus(lid){
	var lobj = document.getElementById("mlayer"+lid);
	var setstr = (lobj.checked) ? "ON" : "OFF";
	lid = lid -1;
	var lyrswitch = [["top_annotation","middle_annotation","city_annotation_0","city_annotation_1"],
	["top_line","aza_polygon", "oaza_polygon"] ,
	["middle_road_line","city_road_0","city_road_1","city_road_2"] ,
	["middle_railway","city_railway"],
	["tatemono_1" , "tatemono_2", "tatemono_v"] ];

	for(i=0;i<lyrswitch[lid].length;i++) {
		parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty(lyrswitch[lid][i],"status",setstr);
	}
}

function testButton() {
		//parent.frames["dragonfmap"].dragonfly.sendParameterString("-disable -kankyou");

		parent.frames["dragonfmap"].dragonfly.sendMessage("DistanceTool");
		//window.alert(parent.frames["dragonfmap"].dragonfly.getShapeLayerNames();
}

function init(){
	openTab('tablink1', 'tabdiv1');
	openPanel('addobjectdiv', 'panelcontent');
}

var step = 100;

function setPathData(path, value) {
    var firstStep = 6 / step * value;
    var secondStep = 2 / step * value;
    path.attr('d', 'M1,' + (7 - firstStep) + ' C6.33333333,' + (2 + secondStep) + ' 11.6666667,' + (1 + firstStep) + ' 17,' + (1 + firstStep) + ' C22.3333333,' + (1 + firstStep) + ' 27.6666667,' + (2 + secondStep) + ' 33,' + (7 - firstStep));
}

function setDesign( design_id ) {
	document.getElementById('new_design_id').value=design_id;
	document.getElementById('edit_design_id').value=design_id;
}

function datestr() {
    var d = new Date();
    var yyyy = d.getFullYear();
    var mm = d.getMonth() < 9 ? "0" + (d.getMonth() + 1) : (d.getMonth() + 1);
    var dd = d.getDate() < 10 ? "0" + d.getDate() : d.getDate();
    return yyyy + "-" + mm + "-" + dd;
}

var vrmlobj_id = -1;
var vrmlobj_file = "";
function setVrml(objid , vrmlfile, layer, tname, cdate, points) {
    vrmlobj_id = objid;
    vrmlobj_file = vrmlfile;
    editlayer = layer;

    var dragonfly = parent.frames["dragonfmap"].dragonfly;
    var wx = dragonfly.getCenterX();
    var wy = dragonfly.getCenterY();
    var geom = vrmlobj_id + ";" + wx + "," + wy + ";" + vrmlobj_file + ";" + vrmlobj_file;
    openTab('tablink2', 'tabdiv2');
    openPanel('editobjectdiv', 'panelcontent');

    document.getElementById("editwrlfile").value = vrmlobj_file;
    document.getElementById("edittname").value = tname;
    document.getElementById("editdate").value = cdate;

    openPanel('editbldpanelv' , 'editpanelcontent');
    setEditVrml(vrmlobj_id, geom);
}

function uploadVrml() {
    let params = "scrollbars=yes,resizable=yes,status=no,location=no,toolbar=no,menubar=no,width=600,height=500,left=100,top=100";
    //window.open('vrmlup?vrmlid='+vrmlobj_id , 'design',params);

    setVrml(6 , 'obj_6.wrl', 'tatemono_v', 'bld', '2022-01-01', '');
}

function newGeom(lyr) {
    editlayer = lyr;
	openPanel('newobjectdiv', 'panelcontent');
	
    document.getElementById("newdate").value = datestr();

    var dragonfly = parent.frames["dragonfmap"].dragonfly;
	parent.frames["dragonfmap"].dragonfly.sendParameterString(" -newgeometry BUILDING CREATED_0");
	parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","STROKE","180:180:190:255");
	parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","DRAWTOOL","POINT"); 
	parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","SRID","4612");
	parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","MAXSCALE","0");
	parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","LIFTHT","0");
	parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","DLSHAPECOUNT","1");
	parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","DLSETEDITSHAPEINDEX","0");
	parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","DLSETHLPOINTINDEX","-1");
	parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","DEPTH","OFF");
	parent.frames["dragonfmap"].dragonfly.sendParameterString(" -seteditgeometry CREATED_0");

	clickmode = 2;
        
    if( lyr == "tatemono_1" ) {
        openPanel('newbldpanel1' , 'newpanelcontent');
    }
    else {
        openPanel('newbldpanel2' , 'newpanelcontent');
    }
}

var points = [];
var bld_ht = 3;
var edit_id = -1;
var clickmode = 0;
var editlayer = "";
function setNewPolyCoord(outx, alt, outy, pi, isadd, iscw) {
	var coord = { x: outx, z: alt, y: outy };
	if ( isadd == 1 ) {
		if ( pi >= points.length ) {
			points.push( coord );
		}
		else {
			points.splice(pi, 0, coord);
		}
	}
	else if ( isadd == 0 ) {
		if ( pi < points.length ) {
			points[pi] = coord;
		}
	}
	else if ( isadd == -1 ) {
		if ( pi < points.length ) {
			points.splice( pi , 1 );
		}
	}
}

function itemClicked(id,layerid,inclusive) {

    if ( (layerid-0) > 3000 ) {
        return;
    }

	if( clickmode > 0 ) {
		return;
	}
	stopEdit();

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
            		points = objects[0]['geom'];
                    document.getElementById("editdate").value = objects[0]['create_date'];
            		document.getElementById("edittname").value = objects[0]['tname'];

                    if ( layerid < 2000 ) {
                        setWallImage(objects[0]['wallid']);
                		document.getElementById("edit_design_id").value = objects[0]['designid'];
                		bld_ht = objects[0]['floorht'] * objects[0]['floornum'];
    	            	var geomstr = objects[0]['geomstr'];
                        if( editlayer == "tatemono_1" ) {
                            openPanel('editbldpanel1' , 'editpanelcontent');
                        }
                        else if( editlayer == "tatemono_2" ) {
                            openPanel('editbldpanel2' , 'editpanelcontent');
                        }
                        setEditShape( id, geomstr );
                    }
                    else {
                        // vrml
                        if( editlayer == "tatemono_v" ) {

                        }
                    }
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

function setWallImage(v) {
	document.getElementById("wallidselect").value = v;
	document.getElementById("wallimage").src = "/assets/walls/dm2_" + v + ".png";
}

function setEditVrml(id, geom) {
    var dragonfly = parent.frames["dragonfmap"].dragonfly;
    dragonfly.setShapeLayerProperty(editlayer,"HIDEOBJECT",id);
    dragonfly.sendParameterString(" -newgeometry POINT CREATED_0");
    dragonfly.setShapeLayerProperty("CREATED_0","STROKE","15:240:200:255");
    dragonfly.setShapeLayerProperty("CREATED_0","DRAWTOOL","POINT"); //POINT, 
    dragonfly.setShapeLayerProperty("CREATED_0","SRID","4612");
    dragonfly.setShapeLayerProperty("CREATED_0","MAXSCALE","0");
    dragonfly.setShapeLayerProperty("CREATED_0","LIFTHT","0");
    dragonfly.setShapeLayerProperty("CREATED_0","SYMBOLFILETYPE","VRML");
    dragonfly.setShapeLayerProperty("CREATED_0","SYMBOLS", "/cgi-bin/DFCgi.exe?vrml=");
    dragonfly.setShapeLayerProperty("CREATED_0","SYMBOLATTDESC","NAME");
    dragonfly.setShapeLayerProperty("CREATED_0","LABELSCALE", "40");
    dragonfly.setShapeLayerProperty("CREATED_0","DLSHAPECOUNT","1");
    dragonfly.setShapeLayerProperty("CREATED_0","DLSETEDITSHAPEINDEX","0");
    dragonfly.setShapeLayerProperty("CREATED_0","DLSETHLPOINTINDEX","-1");
    dragonfly.setShapeLayerProperty("CREATED_0","DLINSERTSHAPE",geom);
    dragonfly.setShapeLayerProperty("CREATED_0","DEPTH","OFF");
}

function setEditShape( id, geom ) {
	var dragonfly = parent.frames["dragonfmap"].dragonfly;
	dragonfly.setShapeLayerProperty(editlayer,"HIDEOBJECT",id);
	dragonfly.sendParameterString(" -newgeometry BUILDING CREATED_0");
	dragonfly.setShapeLayerProperty("CREATED_0","STROKE","15:240:200:255");
	dragonfly.setShapeLayerProperty("CREATED_0","DRAWTOOL","POINT"); //POINT, 
	dragonfly.setShapeLayerProperty("CREATED_0","SRID","4612");
	dragonfly.setShapeLayerProperty("CREATED_0","MAXSCALE","0");
	dragonfly.setShapeLayerProperty("CREATED_0","LIFTHT","0");
	dragonfly.setShapeLayerProperty("CREATED_0","DLSHAPECOUNT","1");
	dragonfly.setShapeLayerProperty("CREATED_0","DLSETEDITSHAPEINDEX","0");
	dragonfly.setShapeLayerProperty("CREATED_0","DLSETHLPOINTINDEX","-1");
	dragonfly.setShapeLayerProperty("CREATED_0","DLINSERTSHAPE",geom);
	dragonfly.setShapeLayerProperty("CREATED_0","DEPTH","OFF");
    dragonfly.setShapeLayerProperty("CREATED_0","SYMBOLATTDESC","NAME");
    dragonfly.setShapeLayerProperty("CREATED_0","MAXLABELS","1");
	
	//dragonfly.sendParameterString(" -seteditgeometry CREATED_0");
	
}

function endEditTool() {
	
	var dragonfly = parent.frames["dragonfmap"].dragonfly;
	dragonfly.setShapeLayerProperty("CREATED_0","EDITFLAGS" , "");
	dragonfly.sendParameterString(" -seteditgeometry ");
	clickmode = 0;
}

function setEditTool(tool) {
	var dragonfly = parent.frames["dragonfmap"].dragonfly;
	dragonfly.setShapeLayerProperty("CREATED_0","EDITFLAGS" , tool);
	dragonfly.sendParameterString(" -seteditgeometry CREATED_0");
	clickmode = 1;
}

function deleteObject() {
	var data = {
		id: edit_id,
        layer: editlayer
    }

    $.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/deletegeom",        // POST送信先のURL
        //data: data,
        data:JSON.stringify(data),  // JSONデータ本体
        contentType: 'application/json', // リクエストの Content-Type
        dataType: "json",           // レスポンスをJSONとしてパースする
        success: function(json_data) {   // 200 OK時
            // JSON Arrayの先頭が成功フラグ、失敗の場合2番目がエラーメッセージ
            if (!json_data['result']) {    // サーバが失敗を返した場合
                alert("Transaction error. " + json_data[1]);
                return;
            }
            else {
            	alert("オブジェクト削除完了しました。");
            }
            // 成功時処理
        },
        error: function() {         // HTTPエラー時
            alert("Server Error. Please try again later.");
        },
        complete: function() {      // 成功・失敗に関わらず通信が終了した際の処理
        	clickmode = 3;
        	stopEdit();
            //button.attr("disabled", false);  // ボタンを再び enableにする
        }
    });
}

function saveEditObject() {
	var data = {
		id: edit_id,
		tname: document.getElementById("edittname").value,
        date: document.getElementById("editdate").value,
        coords: points,
        ht: bld_ht,
        layer: editlayer,
        designid: document.getElementById("edit_design_id").value,
        wallid: document.getElementById("wallidselect").value
    };

	$.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/editgeom",        // POST送信先のURL
        //data: data,
        data:JSON.stringify(data),  // JSONデータ本体
        contentType: 'application/json', // リクエストの Content-Type
        dataType: "json",           // レスポンスをJSONとしてパースする
        success: function(json_data) {   // 200 OK時
            // JSON Arrayの先頭が成功フラグ、失敗の場合2番目がエラーメッセージ
            if (!json_data['result']) {    // サーバが失敗を返した場合
                alert("Transaction error. " + json_data[1]);
                return;
            }
            else {
            	clickmode = 1;
            	alert("オブジェクト編集完了しました。");
            }
            // 成功時処理
        },
        error: function() {         // HTTPエラー時
            alert("Server Error. Please try again later.");
        },
        complete: function() {      // 成功・失敗に関わらず通信が終了した際の処理
        	stopEdit();
            //button.attr("disabled", false);  // ボタンを再び enableにする
        }
    });
}

function resetForms() {
	document.getElementById("newwallidselect").selectedIndex = 0;
	document.getElementById("wallidselect").selectedIndex = 0;
	document.getElementById("new_design_id").value = "";
	document.getElementById("edit_design_id").value = "";
	document.getElementById("newtname").value = "";
	document.getElementById("edittname").value = "";
	
}

function saveObject() {

	var data = {
        coords: points,
        ht: bld_ht,
        date: document.getElementById("newdate").value,
        tname: document.getElementById("newtname").value,
        wallid: document.getElementById("newwallidselect").value,
        designid: document.getElementById("new_design_id").value,
        layer: editlayer
    };

	$.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/addobject",        // POST送信先のURL
        //data: data,
        data:JSON.stringify(data),  // JSONデータ本体
        contentType: 'application/json', // リクエストの Content-Type
        dataType: "json",           // レスポンスをJSONとしてパースする
        success: function(json_data) {   // 200 OK時
            // JSON Arrayの先頭が成功フラグ、失敗の場合2番目がエラーメッセージ
            if (!json_data['result']) {    // サーバが失敗を返した場合
                alert("Transaction error. " + json_data[1]);
                return;
            }
            else {
            	alert("オブジェクト追加完了しました。");
            }
            // 成功時処理
        },
        error: function() {         // HTTPエラー時
            alert("Server Error. Please try again later.");
        },
        complete: function() {      // 成功・失敗に関わらず通信が終了した際の処理
        	stopEdit();
            //button.attr("disabled", false);  // ボタンを再び enableにする
        }
    });
}

function handleAction(val,shp,lyr) {
	bld_ht = val - 0;
}

function mapOutputCoords(outx,  alt, outy, li, si, pi, isadd, iscw) {
	if ( li == 1000000 ) { // dragonfly add layer
		setNewPolyCoord(outx, alt, outy, pi, isadd, iscw);
	}
}

function stopEdit() {
	var dragonfly = parent.frames["dragonfmap"].dragonfly;
	openPanel('addobjectdiv', 'panelcontent');
	dragonfly.sendParameterString(" -deletegeometry CREATED_0");
	dragonfly.setShapeLayerProperty(editlayer,"HIDEOBJECT","0");
	if( clickmode > 0 && editlayer !== "") {
		dragonfly.setShapeLayerProperty(editlayer,"RELOADLAYER", "1");
	}
	clickmode = 0;
	points = [];
	bld_ht = 3;
	edit_id = -1;
	editlayer = "";
	resetForms();
}

function changeWallImg(obj, imgid) {
	document.getElementById(imgid).src = "/assets/walls/dm2_" + obj.value + ".png";
}

function createNewDesign() {
	let params = "scrollbars=yes,resizable=yes,status=no,location=no,toolbar=no,menubar=no,width=600,height=500,left=100,top=100";
    window.open('design' , 'design',params);
}

function editDesign(editid) {
	let params = "scrollbars=yes,resizable=yes,status=no,location=no,toolbar=no,menubar=no,width=600,height=500,left=100,top=100";
    window.open('design?design_id=' + document.getElementById(editid).value + '&layer=tatemono_2' , 'design', params);
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
        url:"/api/city/addroaza",        // POST送信先のURL
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
                dragonfly.addAutoFly(" -dest "+lon+" 1000 "+ lat +" -etilt -90 -delay 0" );
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

var date_filter_from = "";
var date_filter_to = "";
function setDataRange(  ) {
    var dragonfly = parent.frames["dragonfmap"].dragonfly;
    var dstr = document.getElementById("frombld").value;
    
    var dateArray = dstr.split("to");
    if( dateArray.length > 1 ) {
        var fromts = dateArray[0].trim();
        var tots = dateArray[1].trim();
        //CGIREQUEST /api/gis/getlayer?pool=hawk
        if( date_filter_from !== fromts || date_filter_to !== tots ) {
            date_filter_from = fromts;
            date_filter_to = tots;
            pm_filter_from = getTimeStamp(fromts);
            pm_filter_to = getTimeStamp(tots);
            
            dragonfly.setShapeLayerProperty("tatemono_1","CGIREQUEST", "/api/gis/getlayer?pool=hawk&fromts="+pm_filter_from+"&tots="+pm_filter_to);
            dragonfly.setShapeLayerProperty("tatemono_1","RELOADLAYER", "1");
            dragonfly.setShapeLayerProperty("tatemono_2","CGIREQUEST", "/api/gis/getlayer?pool=hawk&fromts="+pm_filter_from+"&tots="+pm_filter_to);
            dragonfly.setShapeLayerProperty("tatemono_2","RELOADLAYER", "1");

            editlayer = "";
            stopEdit();
        }
    }
    else {
        date_filter_from = "";
        date_filter_to = "";
        
        dragonfly.setShapeLayerProperty("tatemono_1","CGIREQUEST", "/api/gis/getlayer?pool=hawk");
        dragonfly.setShapeLayerProperty("tatemono_1","RELOADLAYER", "1");
        dragonfly.setShapeLayerProperty("tatemono_2","CGIREQUEST", "/api/gis/getlayer?pool=hawk");
        dragonfly.setShapeLayerProperty("tatemono_2","RELOADLAYER", "1");
    }

    function getTimeStamp(myDate) {
        myDate = myDate.split("-");
        var newDate = new Date( myDate[0], myDate[1] - 1, myDate[2]);
        return (newDate.getTime()/1000);
    }
}

</script>

<style>
body{
background-color:#788880;
font-color:#F6FEFE;
color:#F6FEFE;
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

</style>
</HEAD>
<BODY leftmargin="1" rightmargin="1" topmargin="1" bottommargin="1" onload="init();">


<div class="tab">
  <button id="tablink1" class="tablinks" onclick="openTab('tablink1', 'tabdiv1');">レイヤー</button>
  <button id="tablink2" class="tablinks" onclick="openTab('tablink2', 'tabdiv2');">オブジェクト</button>
  <button id="tablink3" class="tablinks" onclick="openTab('tablink3', 'tabdiv3');">検索</button>
</div>

<div id="tabdiv1" class="tabcontent">
			
	<table width="100%">
		<tr><td colspan="2" style="border-bottom:black solid 1px;"><b>Layers</b></td></tr>
	</table>
		<form>
	<table>
	<tr>
		<td width="10px">&nbsp; </td>
		<td width="5px"><input type="checkbox" id="mlayer1" onclick="setMapStatus(1);"></td>
		<td>注記</td>
	</tr>

	<tr>
		<td width="10px">&nbsp; </td>
		<td width="5px"><input type="checkbox" id="mlayer2" onclick="setMapStatus(2);"></td>
		<td>業界線</td>
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
        <td> 表示範囲：タイム
        </td>
    </tr>
    <tr>
        <td> 
            <input type="text" name="frombld" id="frombld" />
        </td>
    </tr>
</table>
<script>
    var example = flatpickr('#frombld',{
      dateFormat: 'Y-m-d',
      allowInput: false,
      defaultHour: 12, 
      defaultMinute: 0, 
      disableMobile: false,
      mode: "range",
      prevArrow: '&lt;',
      nextArrow: '&gt;',
      parseDate: false,
      time_24hr: true,
      onChange: function() {
        //alert("onChange.");
      },
      onClose: function() {
        //alert("onClose.");
        setDataRange();
      }
    });
</script>
<hr>
<table>
		<tr>
		<td colspan="9999">
		<input type="button" onclick="newGeom('tatemono_1')" value="一般建物作成" /><br>
        <input type="button" onclick="newGeom('tatemono_2')" value="壁テキスチャー登録建物作成" /><br>
        <input type="button" onclick="uploadVrml()" value="VRMLオブジェクト登録" /><br>
	</td>
</tr>
</table>
</div>

<div id="newobjectdiv" class="panelcontent">
	<table>
		<tr>
			<td>
				名前：
			</td>
			<td>
				<input type="text" id="newtname" name="newtname" value="" />
			</td>
		</tr>
        <tr>
            <td>
                作成日：
            </td>
            <td>
                <input type="text" id="newdate" name="newdate" value="" />
            </td>
        </tr>
<script>
    var example = flatpickr('#newdate',{
      dateFormat: 'Y-m-d',
      allowInput: true,
      time_24hr: true,
      onClose: function() {
      }
    });
</script>
		<tr>
		<td colspan="9999">
		<tr>
			<td colspan="999">
				
                <div id="newbldpanel1" class="newpanelcontent" >
            		壁画像：<select id="newwallidselect" name="newwallidselect" onchange="changeWallImg(this,'newwallimage');">
            			<? for ($r=1;$r<=$wallcount;$r++) { ?>
            				<option value=<?= $r; ?>>wall <?= $r; ?></option>
            			<? } ?>
            		</select>
		              <img id="newwallimage" src="/assets/walls/dm2_1.png" width="20" height="20"/>
                </div>
                <div id="newbldpanel2" class="newpanelcontent" >
                    ID:<input type="text" name="new_design_id" id="new_design_id" value="" size="2" readonly="readonly"/>
                    <input type="button" onclick="createNewDesign();" value="壁テキスチャー登録" />
                </div>
            </td>
        </tr>
		<tr>
                    <td colspan="2">
                            <input type="button" onclick="stopEdit()" value="キャンセル" />
                            &nbsp;&nbsp;
                            <input type="button" onclick="saveObject()" value="保存" />
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
				<input type="text" id="edittname" name="edittname" value="" />
			</td>
		</tr>
        <tr>
            <td>
                作成日：
            </td>
            <td>
                <input type="text" id="editdate" name="editdate" value="" />
<script>
    var example = flatpickr('#editdate',{
      dateFormat: 'Y-m-d',
      allowInput: true,
      time_24hr: true,
      onClose: function() {
      }
    });
</script>
            </td>
        </tr>
		<tr>
			<td colspan="3">
                <div id="editbldpanel1" class="editpanelcontent" >
				壁画像：<select id="wallidselect" name="wallidselect" onchange="changeWallImg(this,'wallimage');">
					<? for ($r=1;$r<=$wallcount;$r++) { ?>
						<option value=<?= $r; ?>>wall <?= $r; ?></option>
					<? } ?>
				</select>
				<img id="wallimage" src="/assets/walls/dm2_1.png" width="20" height="20"/>
                </div>
                <div id="editbldpanel2" class="editpanelcontent" >
                    ID:<input type="text" name="edit_design_id" id="edit_design_id" value="" size=2 readonly="readonly"/>
                    <input type="button" onclick="editDesign('edit_design_id');" value="壁テキスチャー編集" />
                </div>
                <div id="editbldpanelv" class="editpanelcontent" >
                    <table>
                        <tr>
                            <td>
                                WRL:<input type="text" name="editwrlfile" id="editwrlfile" value="" readonly="readonly"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Scale
                                <br>
    <div style="position:relative;width:150px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div ontouchstart="dragslider(this,'vrmlscale',150,1,1000,100);" 
        onmousedown="dragslider(this,'vrmlscale',150,1,1000,100);" 
        style="position:absolute;top:-15px;left:50px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
    
    </div>
<input id="vrmlscale" value="50">
                            
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Xrot
                                <input type="text" style="width:30px;" id="vrmlxrot" value="0" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Yrot
                                <input type="text" style="width:30px;" id="vrmlyrot" value="0" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Zrot
                                <input type="text" style="width:30px;" id="vrmlzrot" value="0" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="button" onclick="setEditTool('vw')" value="Edit Points" />
				<input type="button" onclick="endEditTool()" value="Stop Edit" />
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="button" onclick="stopEdit()" value="キャンセル" />
				&nbsp;&nbsp;<input type="button" onclick="saveEditObject()" value="保存" />
				&nbsp;&nbsp;<input type="button" onclick="deleteObject()" value="削除" />
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
 
	
</div>

</form>
</BODY>
</HTML>