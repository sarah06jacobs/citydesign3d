<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<TITLE>City Design</TITLE>
<LINK REL="StyleSheet" HREF="/css/style.css" TYPE="text/css">


<script type="text/javascript" src="/js/jquery.js" ></script>
<script type="text/javascript" src="/js/nano.js" ></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script language="javascript">


var points = [];
var bld_ht = 3;
var edit_id = -1;
var clickmode = 0;
var editlayer = "";

var layer_map = { lyr_1000: "tatemono_1" ,
                lyr_1001: "tatemono_2" ,
                lyr_2000: "tatemono_v"
            };

var step = 100;
var vrmlobj_id = -1;
var vrmlobj_file = "";
var vrmlobj_tfm = "";
var editType = 1; // 1 - building, 2- vrml

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

function setTFMForm() {
    if( vrmlobj_tfm != "" ) {
        tarr = vrmlobj_tfm.split(";");

        for( var i=0;i<tarr.length;i++ ) {

            farr = tarr[i].split(":");
            varr = farr[1].split(",");
            if( farr[0] === "S" ) {
                var scl = varr[0];
                document.getElementById('vrmlscale').value = parseInt(scl * 100);
            }
            else {
                var rot = varr[0];
                if( (varr[1]-0) == 1 ) { // X
                    document.getElementById('vrmlxrot').value = rot;
                }
                if( (varr[2]-0) == 1 ) { // Y
                    document.getElementById('vrmlyrot').value = rot;
                }
                if( (varr[3]-0) == 1 ) { // Z
                    document.getElementById('vrmlzrot').value = rot;
                }
            }
        }
        updateTFMSliders();
    }
}


function setVrml(objid , vrmlfile, layer, tname, cdate, point_str, tfm) {
    vrmlobj_id = objid;
    edit_id = objid;
    vrmlobj_file = vrmlfile;
    editlayer = layer;
    editType = 2;
    vrmlobj_tfm = tfm;

    setTFMForm();
    var dragonfly = parent.frames["dragonfmap"].dragonfly;
    var geom = "";

    if( point_str === "" ) {
        var wx = dragonfly.getCenterX();
        var wy = dragonfly.getCenterY();
        var geom = vrmlobj_id + ";" + wx + "," + wy + ";" + vrmlobj_file + ";" + vrmlobj_file;
        var coord = { x: wx, z: 0, y: wy };
        points = coord;
    }
    else {
        parr = point_str.split(" ");
        var geom = vrmlobj_id + ";" + parr[0] + "," + parr[1] + ";" + vrmlobj_file + ";" + vrmlobj_file;
        var coord = { x: parr[0], z: 0, y: parr[1] };
        points = coord;
    }
    openTab('tablink2', 'tabdiv2');
    openPanel('editobjectdiv', 'panelcontent');

    document.getElementById("editwrlfile").value = vrmlobj_file;
    document.getElementById("edittname").value = tname;
    document.getElementById("editdate").value = cdate;

    openPanel('editbldpanelv' , 'editpanelcontent');
    setEditVrml(vrmlobj_id, geom);
}

function updateTFM() {
    var dragonfly = parent.frames["dragonfmap"].dragonfly;

    var s = document.getElementById('vrmlscale').value / 100;
    var x = document.getElementById('vrmlxrot').value;
    var y = document.getElementById('vrmlyrot').value;
    var z = document.getElementById('vrmlzrot').value;

    var tfstr = "S:"+s+","+s+","+s+";R:"+x+",1,0,0;R:"+y+",0,1,0;R:"+z+",0,0,1";
    parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","DLTRANSFORMSHAPE",tfstr);
    vrmlobj_tfm = tfstr;
}

function sliderKeyUp(obj) {
    var v = obj.value;
    if(!isNaN(v)) {
        updateTFMSliders();
    }
}

function updateTFMSliders()
{
    var smin = 1;
    var smax = 1000;
    var margin = 12;
    var cw = 10;
    var width = 150;

    var rmin = 0;
    var rmax = 360;

    document.getElementById('vrmlscalepuck').style.left = ((document.getElementById('vrmlscale').value-smin)*width/(smax-smin)-margin-cw/2) + "px";

    document.getElementById('vrmlrotxpuck').style.left = ((document.getElementById('vrmlxrot').value-rmin)*width/(rmax-rmin)-margin-cw/2) + "px";
    document.getElementById('vrmlrotypuck').style.left = ((document.getElementById('vrmlyrot').value-rmin)*width/(rmax-rmin)-margin-cw/2) + "px";
    document.getElementById('vrmlrotzpuck').style.left = ((document.getElementById('vrmlzrot').value-rmin)*width/(rmax-rmin)-margin-cw/2) + "px";
}

function changeVrml() {
    var vid = vrmlobj_id;
    stopEdit();
    let params = "scrollbars=yes,resizable=yes,status=no,location=no,toolbar=no,menubar=no,width=600,height=500,left=100,top=100";
    window.open('vrmlup?vrmlid='+vid , 'design',params);
}

function uploadVrml() {
    let params = "scrollbars=yes,resizable=yes,status=no,location=no,toolbar=no,menubar=no,width=600,height=500,left=100,top=100";
    window.open('vrmlup?vrmlid=-1' , 'design',params);

    //setVrml(6 , 'obj_6.wrl', 'tatemono_v', 'bld', '2022-01-01', '');
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

function setPointCoord(outx, alt, outy, pi, isadd, iscw) {
    var coord = { x: outx, z: alt, y: outy };
    points = coord;
}

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
                    document.getElementById("editdate").value = objects[0]['create_date'];
            		document.getElementById("edittname").value = objects[0]['tname'];

                    if ( layerid < 2000 ) {
                        editType = 1;
                        points = objects[0]['geom'];
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

                            var coord = { x: objects[0]['geom'][0], z: 0, y: objects[0]['geom'][1] };
                            points = coord;

                            editType = 2;
                            var geomstr = objects[0]['geomstr'];
                            var garr = geomstr.split(";");

                            setVrml(objects[0]['gid'] , objects[0]['wrl'], editlayer, objects[0]['tname'], objects[0]['create_date'], garr[1] , objects[0]['tfm']);
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
    dragonfly.setShapeLayerProperty("CREATED_0","DLTRANSFORMSHAPE",vrmlobj_tfm);
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
    if (editType == 1) {

        saveEditBuilding();
    }
    else if (editType == 2) {

        saveEditVrml();
    }
}

function saveEditVrml() {
    var data = {
        id: edit_id,
        tname: document.getElementById("edittname").value,
        date: document.getElementById("editdate").value,
        coords: points,
        layer: editlayer,
        wrlfile: document.getElementById("editwrlfile").value,
        tfm: vrmlobj_tfm
    };

    $.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/editvrml",        // POST送信先のURL
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

function saveEditBuilding() {

	var data = {
		id: edit_id,
		tname: document.getElementById("edittname").value,
        date: document.getElementById("editdate").value,
        coords: points,
        ht: bld_ht,
        layer: editlayer,
        designid: document.getElementById("edit_design_id").value,
        wallid: document.getElementById("wallidselect").value,
        wrlfile: document.getElementById("editwrlfile").value,
        tfm: vrmlobj_tfm
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
	document.getElementById("editwrlfile").value = "";

    document.getElementById('vrmlscale').value = 100;
    document.getElementById('vrmlxrot').value = 0;
    document.getElementById('vrmlyrot').value = 0;
    document.getElementById('vrmlzrot').value = 0;
    updateTFMSliders();
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
        if (editType == 1) {
            setNewPolyCoord(outx, alt, outy, pi, isadd, iscw);
        }
        else if (editType == 2) { // vrml
            setPointCoord(outx, alt, outy, pi, isadd, iscw);
        }
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
    vrmlobj_id = -1;
    vrmlobj_file = "";
    vrmlobj_tfm = "";
    editType = 1;
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
                            <td colspan="4">
                                WRL:<input type="text" name="editwrlfile" id="editwrlfile" value="" readonly="readonly"/> <input type="button" onclick="changeVrml()" value="変更"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                縮尺
                            </td>
                            <td>
                                <br>
    <div style="position:relative;width:150px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div id="vrmlscalepuck" ontouchstart="dragslider(this,'vrmlscale',150,1,1000,100);" 
        onmousedown="dragslider(this,'vrmlscale',150,1,1000,100);" 
        style="position:absolute;top:-15px;left:<?= ((100-1)*150/(1000-1)-12-5); ?>px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
        <!--  -->
    
    </div> 
                            </td>
                            <td>
                                <input id="vrmlscale" value="100" type="text" style="width:30px;" onchange="updateTFM()"> %
                            </td>
                        </tr>
                        <tr>
                            <td>
                                X軸
                            </td>
                            <td>
    <div style="position:relative;width:150px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div id="vrmlrotxpuck" ontouchstart="dragslider(this,'vrmlxrot',150,0,360,0);" 
        onmousedown="dragslider(this,'vrmlxrot',150,0,360,0);" 
        style="position:absolute;top:-15px;left:<?= (-12-5); ?>px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
    </div> 
                            </td>
                            <td>
                                <input type="text" style="width:30px;" id="vrmlxrot" value="0" onchange="updateTFM()" />°
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Y軸
                            </td>
                            <td>
    <div style="position:relative;width:150px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div id="vrmlrotypuck" ontouchstart="dragslider(this,'vrmlyrot',150,0,360,0);" 
        onmousedown="dragslider(this,'vrmlyrot',150,0,360,0);" 
        style="position:absolute;top:-15px;left:<?= (-12-5); ?>px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
    </div> 
                            </td>
                            <td><input type="text" style="width:30px;" id="vrmlyrot" value="0" onchange="updateTFM()"/>°
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Z軸
                            </td>
                            <td>
    <div style="position:relative;width:150px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div id="vrmlrotzpuck" ontouchstart="dragslider(this,'vrmlzrot',150,0,360,0);" 
        onmousedown="dragslider(this,'vrmlzrot',150,0,360,0);" 
        style="position:absolute;top:-15px;left:<?= (-12-5); ?>px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
    </div> 
                            </td>
                            <td><input type="text" style="width:30px;" id="vrmlzrot" value="0" onchange="updateTFM()"/>°</td>
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