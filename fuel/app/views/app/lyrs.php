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

var layer_map = { lyr_1000: "tatemono_1" ,
                lyr_1001: "tatemono_2" ,
                lyr_2000: "tatemono_v"
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

function setMapStatus(lid){
	var lobj = document.getElementById("mlayer"+lid);
	var setstr = (lobj.checked) ? "ON" : "OFF";
	lid = lid -1;
	var lyrswitch = [["top_annotation","middle_annotation"],
	["top_line","aza_polygon", "oaza_polygon"] ,
	["middle_road_line","city_road_0","city_road_1","city_road_2"] ,
	["middle_railway","city_railway"],
	["tatemono_1" , "tatemono_2", "tatemono_v"] ];

	for(i=0;i<lyrswitch[lid].length;i++) {
		parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty(lyrswitch[lid][i],"status",setstr);
	}

    var dragonfly = parent.frames["dragonfmap"].dragonfly;
    if( lid == 0 ) {
        // building labels
        if( lobj.checked ) {
            dragonfly.setShapeLayerProperty("tatemono_1","LABELVISIBLE","ON");
            dragonfly.setShapeLayerProperty("tatemono_2","LABELVISIBLE","ON");
            dragonfly.setShapeLayerProperty("tatemono_v","LABELVISIBLE","ON");
        }
        else {
            dragonfly.setShapeLayerProperty("tatemono_1","LABELVISIBLE","OFF");
            dragonfly.setShapeLayerProperty("tatemono_2","LABELVISIBLE","OFF");
            dragonfly.setShapeLayerProperty("tatemono_v","LABELVISIBLE","OFF");
        }
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

function updateGround(obj) {
    bld_ground = obj.value;
    if( bld_ground == "" ) {
        bld_ground = "0";
    }
    parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","DLSETGROUNDHT",bld_ground);
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
            else if (farr[0] === "R") {
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
            else if (farr[0] === "T") {
                var tx = varr[0];
                var ty = varr[1];
                var tz = varr[2];
                document.getElementById('vrmlxmove').value = tx;
                document.getElementById('vrmlymove').value = ty;
                document.getElementById('vrmlzmove').value = tz;
            }
        }
    }
    updateTFMSliders();
}

function setVrmlChild(vrmlid, layer) {
    // just refresh the layer to show the added data
    var dragonfly = parent.frames["dragonfmap"].dragonfly;
    dragonfly.setShapeLayerProperty(editlayer,"RELOADLAYER", "1");
    dragonfly.setShapeLayerProperty(editlayer,"HIDEOBJECT",edit_id);
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
        var geom = vrmlobj_id + ";" + wx + "," + wy + ";" + tname + ";" + vrmlobj_file;
        var coord = { x: wx, z: 0, y: wy };
        points = coord;
    }
    else {
        parr = point_str.split(" ");
        var geom = vrmlobj_id + ";" + parr[0] + "," + parr[1] + ";" + tname + ";" + vrmlobj_file;
        var coord = { x: parr[0], z: 0, y: parr[1] };
        points = coord;
    }
    openTab('tablink2', 'tabdiv2');
    openPanel('editobjectdiv', 'panelcontent');

    document.getElementById("editwrlfile").value = vrmlobj_file;
    document.getElementById("edittname").value = tname;
    document.getElementById("editdate").value = cdate;

    openPanel('editbldpanelv' , 'editpanelcontent');
    document.getElementById('editbldgrounddiv').style.display = "none";
    setEditVrml(vrmlobj_id, geom);
}

function updateTFM() {
    var dragonfly = parent.frames["dragonfmap"].dragonfly;

    var s = document.getElementById('vrmlscale').value / 100;
    var x = document.getElementById('vrmlxrot').value-0;
    var y = document.getElementById('vrmlyrot').value-0;
    var z = document.getElementById('vrmlzrot').value-0;

    var tx = document.getElementById('vrmlxmove').value-0;
    var ty = document.getElementById('vrmlymove').value-0;
    var tz = document.getElementById('vrmlzmove').value-0;
    var tfstr = "S:"+s+","+s+","+s+";R:"+x+",1,0,0;R:"+y+",0,1,0;R:"+z+",0,0,1;T:"+tx+","+ty+","+tz;

    parent.frames["dragonfmap"].dragonfly.setShapeLayerProperty("CREATED_0","DLTRANSFORMSHAPE",tfstr);
    vrmlobj_tfm = tfstr;
}

function sliderKeyUp(obj) {
    var v = obj.value;
    if(!isNaN(v)) {
        updateTFMSliders();
        updateTFM();
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

    var mmin = -1000;
    var mmax = 1000;

    document.getElementById('vrmlscalepuck').style.left = ((document.getElementById('vrmlscale').value-smin)*width/(smax-smin)-margin-cw/2) + "px";

    document.getElementById('vrmlrotxpuck').style.left = ((document.getElementById('vrmlxrot').value-rmin)*width/(rmax-rmin)-margin-cw/2) + "px";
    document.getElementById('vrmlrotypuck').style.left = ((document.getElementById('vrmlyrot').value-rmin)*width/(rmax-rmin)-margin-cw/2) + "px";
    document.getElementById('vrmlrotzpuck').style.left = ((document.getElementById('vrmlzrot').value-rmin)*width/(rmax-rmin)-margin-cw/2) + "px";

    document.getElementById('vrmlmovexpuck').style.left = ((document.getElementById('vrmlxmove').value-mmin)*width/(mmax-mmin)-margin-cw/2) + "px";
    document.getElementById('vrmlmoveypuck').style.left = ((document.getElementById('vrmlymove').value-mmin)*width/(mmax-mmin)-margin-cw/2) + "px";
    document.getElementById('vrmlmovezpuck').style.left = ((document.getElementById('vrmlzmove').value-mmin)*width/(mmax-mmin)-margin-cw/2) + "px";
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

function addVrmlChild() {
    if( vrmlobj_id >= 0 ) {
        let params = "scrollbars=yes,resizable=yes,status=no,location=no,toolbar=no,menubar=no,width=600,height=500,left=100,top=100";
        window.open('vrmlup?vrmlid=-1&gid='+vrmlobj_id , 'design',params);
    }
    else {
        alert("VRML OBJECT 選択してください。");
    }
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
                    document.getElementById("editenddate").value = objects[0]['end_date'];
            		document.getElementById("edittname").value = objects[0]['tname'];
                    document.getElementById("setfavoritebutton").disabled = false;

                    if ( layerid < 2000 ) {
                        editType = 1;
                        points = objects[0]['geom'];
                        setWallImage(objects[0]['wallid']);
                		document.getElementById("edit_design_id").value = objects[0]['designid'];
                		bld_ht = objects[0]['floorht'] * objects[0]['floornum'];
                        bld_ground = objects[0]['flground'] - 0;
    	            	var geomstr = objects[0]['geomstr'];
                        if( editlayer == "tatemono_1" ) {
                            openPanel('editbldpanel1' , 'editpanelcontent');
                            document.getElementById('editbldgrounddiv').style.display = "block";
                        }
                        else if( editlayer == "tatemono_2" ) {
                            openPanel('editbldpanel2' , 'editpanelcontent');
                            document.getElementById('editbldgrounddiv').style.display = "block";
                        }
                        setEditShape( id, geomstr );
                        document.getElementById("editbldground").value = bld_ground;
                        updateGround(document.getElementById("editbldground"));
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
                            document.getElementById('editbldgrounddiv').style.display = "none";
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

function setObjectFavorite() {
    var data = {
        id: edit_id,
        layer: editlayer
    }
    $.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/setobjectplace",        // POST送信先のURL
        //data: data,
        data:JSON.stringify(data),  // JSONデータ本体
        contentType: 'application/json', // リクエストの Content-Type
        dataType: "json",           // レスポンスをJSONとしてパースする
        success: function(json_data) {   // 200 OK時
            if (!json_data['list']) {    // サーバが失敗を返した場合
                alert("Transaction error. ");
                return;
            }
            else {
                alert("お気に入りに登録しました。");
                document.getElementById('searchplacename').value = "";
                document.getElementById('newplacename').value = "";
                var list = json_data['list'];
                reloadPlaces(list);
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
        enddate: document.getElementById("editenddate").value,
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
        enddate: document.getElementById("editenddate").value,
        coords: points,
        ht: bld_ht,
        ground : bld_ground,
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
    document.getElementById('vrmlxmove').value = 0;
    document.getElementById('vrmlymove').value = 0;
    document.getElementById('vrmlzmove').value = 0;
    updateTFMSliders();
}

function saveObject() {

	var data = {
        coords: points,
        ht: bld_ht,
        ground : bld_ground,
        date: document.getElementById("newdate").value,
        enddate: document.getElementById("newenddate").value,
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
    bld_ground = 0;
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
        cell1.setAttribute("style" ,"width:160px;outline-color:lightgrey;outline-style: solid;outline-width: 1px;");
        var span = document.createElement("span");
        span.id = "place" + obj['places_id'];
        span.setAttribute("style" ,"width:160px;height:30px;margin-left: 2px;margin-top: 2px;display: inline-block;padding: 3px;");
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

        var cell3 = row.insertCell(2);
        cell3.setAttribute("style" ,"width:40px;text-align: center;");
        var at2 = document.createElement("a");
        at2.setAttribute("onclick","deletePlace('"+obj['places_id']+"');");
        at2.setAttribute("style" ,"color:red;text-decoration: none;");
        at2.innerHTML = "削除";
        cell3.appendChild(at2);

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

function deletePlace(id) {
    var data = {
        places_id: id
    };

    $.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/deleteplace",        // POST送信先のURL
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
                document.getElementById('searchplacename').value = "";
                document.getElementById('newplacename').value = "";
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

function addPlace() {
    var pname = document.getElementById('newplacename').value;

    if(pname == '') {
        alert("名前を入力してください。");
        return;
    }

    var dragonfly = parent.frames["dragonfmap"].dragonfly;
    
    var data = {
        pname: pname,
        wx : dragonfly.getWorldX(),
        wy : dragonfly.getWorldY(),
        cx : dragonfly.getCenterX(),
        cy : dragonfly.getCenterY(),
        alt : dragonfly.getWorldALT(),
        pitch : dragonfly.getPitchDegrees(),
        dir : dragonfly.getDirectionDegrees(),
        out : dragonfly.getWorldOut(),
        lift : dragonfly.getCameraLift()
    };

    $.ajax({
        type:"post",                // method = "POST"
        url:"/api/city/addplace",        // POST送信先のURL
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
                document.getElementById('searchplacename').value = "";
                document.getElementById('newplacename').value = "";
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
        <td> 表示範囲：タイム
        </td>
    </tr>
    <tr>
        <td>
 <script>
  $( function() {
    $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 300,
      values: [ 0, 300 ],
      slide: function( event, ui ) {

        var fromdate = new Date(2000, (ui.values[ 0 ]), 1);
        var untildate = new Date(2000, (ui.values[ 1 ]), 1);

        var fyear = fromdate.getFullYear();
        var fmonth = fromdate.getMonth() - -1;
        var fmstr = fmonth + "";
        if( fmonth < 10 ) {
            fmstr = "0" + fmstr;
        }

        var tyear = untildate.getFullYear();
        var tmonth = untildate.getMonth() - -1;
        var tmstr = tmonth + "";
        if( tmonth < 10 ) {
            tmstr = "0" + tmstr;
        }

        document.getElementById("frombld").value = fyear + "-" + fmstr + "-01 to " + tyear + "-" + tmstr + "-01";
      }
    });
    $( "#slider-range" ).mouseup(function()
        {
            setDataRange();
        });
  } );
  </script>

<div id="slider-range"></div>

        </td>
    </tr>
    <tr>
        <td> 
            <input type="text" name="frombld" id="frombld" value="2000-01-01 to 2025-01-01"  style="width:200px;"/>
        </td>
    </tr>
</table>
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
        <tr>
            <td>
                終了日：
            </td>
            <td>
                <input type="text" id="newdate" name="newenddate" value="" />
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
    var example = flatpickr('#newenddate',{
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
            <td>地面調整</td>
            <td><input type="text" id="newbldground" name="newbldground" value="0"  onchange="updateGround(this)" onkeyup="updateGround(this)"/></td>
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
            <td>
                終了日：
            </td>
            <td>
                <input type="text" id="editenddate" name="editenddate" value="" />
<script>
    var example = flatpickr('#editenddate',{
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
        style="position:absolute;top:-15px;left:0px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
        <!--  -->
    
    </div> 
                            </td>
                            <td>
                                <input id="vrmlscale" value="100" type="text" style="width:30px;" onkeyup="sliderKeyUp(this)" onchange="updateTFM()"> %
                            </td>
                        </tr>
                        <tr>
                            <td>
                                X軸回転
                            </td>
                            <td>
    <div style="position:relative;width:150px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div id="vrmlrotxpuck" ontouchstart="dragslider(this,'vrmlxrot',150,0,360,0);" 
        onmousedown="dragslider(this,'vrmlxrot',150,0,360,0);" 
        style="position:absolute;top:-15px;left:0px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
    </div> 
                            </td>
                            <td>
                                <input type="text" style="width:30px;" id="vrmlxrot" value="0" onchange="updateTFM()" onkeyup="sliderKeyUp(this)"/>°
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Y軸回転
                            </td>
                            <td>
    <div style="position:relative;width:150px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div id="vrmlrotypuck" ontouchstart="dragslider(this,'vrmlyrot',150,0,360,0);" 
        onmousedown="dragslider(this,'vrmlyrot',150,0,360,0);" 
        style="position:absolute;top:-15px;left:0px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
    </div> 
                            </td>
                            <td><input type="text" style="width:30px;" id="vrmlyrot" value="0" onchange="updateTFM()" onkeyup="sliderKeyUp(this)"/>°
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Z軸回転
                            </td>
                            <td>
    <div style="position:relative;width:150px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div id="vrmlrotzpuck" ontouchstart="dragslider(this,'vrmlzrot',150,0,360,0);" 
        onmousedown="dragslider(this,'vrmlzrot',150,0,360,0);" 
        style="position:absolute;top:-15px;left:0px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
    </div> 
                            </td>
                            <td><input type="text" style="width:30px;" id="vrmlzrot" value="0" onchange="updateTFM()" onkeyup="sliderKeyUp(this)"/>°</td>
                        </tr>


                        <tr>
                            <td>
                                X軸移動
                            </td>
                            <td>
    <div style="position:relative;width:150px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div id="vrmlmovexpuck" ontouchstart="dragslider(this,'vrmlxmove',150,-1000,1000,0);" 
        onmousedown="dragslider(this,'vrmlxmove',150,-1000,1000,0);" 
        style="position:absolute;top:-15px;left:0px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
    </div> 
                            </td>
                            <td><input type="text" style="width:30px;" id="vrmlxmove" value="0" onchange="updateTFM()" onkeyup="sliderKeyUp(this)"/></td>
                        </tr>
                        <tr>
                            <td>
                                Y軸移動
                            </td>
                            <td>
    <div style="position:relative;width:150px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div id="vrmlmoveypuck" ontouchstart="dragslider(this,'vrmlymove',150,-1000,1000,0);" 
        onmousedown="dragslider(this,'vrmlymove',150,-1000,1000,0);" 
        style="position:absolute;top:-15px;left:0px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
    </div> 
                            </td>
                            <td><input type="text" style="width:30px;" id="vrmlymove" value="0" onchange="updateTFM()" onkeyup="sliderKeyUp(this)"/></td>
                        </tr>
                        <tr>
                            <td>
                                Z軸移動
                            </td>
                            <td>
    <div style="position:relative;width:150px;padding-bottom:20px;">
        <div style="border-left:solid 1px #b0b0b0;border-top:solid 1px #b0b0b0;margin-top:5px;font-size:1px;height:3px;">
            <div style="border-top:solid 2px #e7eaea;"></div>
        </div>
        <div id="vrmlmovezpuck" ontouchstart="dragslider(this,'vrmlzmove',150,-1000,1000,0);" 
        onmousedown="dragslider(this,'vrmlzmove',150,-1000,1000,0);" 
        style="position:absolute;top:-15px;left:0px;width:40px;height:30px;font-size:1px;background:transparent url(/img/slider.gif) no-repeat center center;"></div>
    </div> 
                            </td>
                            <td><input type="text" style="width:30px;" id="vrmlzmove" value="0" onchange="updateTFM()" onkeyup="sliderKeyUp(this)"/></td>
                        </tr>

                    </table>
                </div>
			</td>
		</tr>
        <tr>
            <td colspan="3">
                <div id="editbldgrounddiv" >
                <table>    
                    <tr>
                        <td>地面調整</td>
                        <td><input type="text" id="editbldground" name="editbldground" value="0" onchange="updateGround(this)" onkeyup="updateGround(this)"/></td>
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
				<input type="button" onclick="setEditTool('vw')" value="頂点編集開始" />
				<input type="button" onclick="endEditTool()" value="頂点編集終了" />
			</td>
		</tr>
        <tr>
            <td colspan="2">
                <input type="button" onclick="addVrmlChild()" value="子オブジェクト追加" />
            </td>
        </tr>
		<tr>
			<td colspan="2">&nbsp;
			</td>
		</tr>

        <tr>
            <td colspan="2">
                <input type="button" id="setfavoritebutton" onclick="setObjectFavorite()" value="お気に入り登録" />
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
    <hr />
    <h4>
    お気に入り場所登録</h4>
    <input type="textbox" value="" id="newplacename" style="width:150px">
    <input type="button" id="addPlaceButton" onclick="addPlace();" value="登録"><br>
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
                <td style="width:160px;outline-color:lightgrey;outline-style: solid;outline-width: 1px;">

                <span id="place<?= $place['places_id'] ?>" style="width:160px;height:30px;margin-left: 2px;margin-top: 2px;display: inline-block;padding: 3px;" 
            onclick="gotoPlace(<?= $place['lon']; ?>,<?= $place['lat']; ?>,<?= $place['center_lon']; ?>,<?= $place['center_lat']; ?>,<?= $place['alt']; ?>,<?= $place['pitch']; ?>,<?= $place['dir']; ?>, <?= $place['out']; ?>);"> <?= $place['pname'] ?> </span>

                </td>
                <td style="width:30px;text-align: center;">
                    <a style="color:green;text-decoration: none;" onclick="placeToCB('<?= $place['url']; ?>');">url</a>
                </td>
                <td style="width:40px;text-align: center;">
                    <a style="color:red;text-decoration: none;" onclick="deletePlace('<?= $place['places_id']; ?>');">削除</a>
                </td>
            </tr>
            <? } ?>
        </table>
	</div>
        <br>
        <input type="submit" value="CSVダウンロード" />
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