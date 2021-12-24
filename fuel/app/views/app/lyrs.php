<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<TITLE>City Design</TITLE>
<LINK REL="StyleSheet" HREF="/css/style.css" TYPE="text/css">
<script type="text/javascript" src="/js/jquery.js" ></script>
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
				lyr_1002: "tatemono_3" ,
				lyr_1003: "tatemono_4" ,
			};

function setMapStatus(lid){
	var lobj = document.getElementById("mlayer"+lid);
	var setstr = (lobj.checked) ? "ON" : "OFF";
	lid = lid -1;
	var lyrswitch = [["top_annotation","middle_annotation","city_annotation_0","city_annotation_1"],
	["top_line","aza_polygon", "oaza_polygon"] ,
	["middle_road_line","city_road_0","city_road_1","city_road_2"] ,
	["middle_railway","city_railway"],
	["tatemono_1" , "tatemono_2"] ];

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

function setDesign( design_id ) {
	document.getElementById('new_design_id').value=design_id;
	document.getElementById('edit_design_id').value=design_id;
}

function newGeom(lyr) {
        editlayer = lyr;
	openPanel('newobjectdiv', 'panelcontent');
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

            		points = objects[0]['geom'];
            		setWallImage(objects[0]['wallid']);
            		document.getElementById("edittname").value = objects[0]['tname'];
            		document.getElementById("edit_design_id").value = objects[0]['designid'];
            		bld_ht = objects[0]['floorht'] * objects[0]['floornum'];
	            	var geomstr = objects[0]['geomstr'];
	            	editlayer = json_data['layer'];
                        
                        if( editlayer == "tatemono_1" ) {
                            openPanel('editbldpanel1' , 'editpanelcontent');
                        }
                        else {
                            openPanel('editbldpanel2' , 'editpanelcontent');
                        }
        
	            	setEditShape( id, geomstr );
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

function saveObject() {

	var data = {
        coords: points,
        ht: bld_ht,
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
	if( clickmode > 0 ) {
		dragonfly.setShapeLayerProperty(editlayer,"RELOADLAYER", "1");
	}
	clickmode = 0;
	points = [];
	bld_ht = 3;
	edit_id = -1;
	editlayer = "";
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
    window.open('design?design_id=' + document.getElementById(editid).value , 'design', params);
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
		<td colspan="9999">
		<input type="button" onclick="newGeom('tatemono_1')" value="New Regular" />
                <input type="button" onclick="newGeom('tatemono_2')" value="New Design" /><br>
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
		<td colspan="9999">
		<tr>
			<td>
				壁画像：
			</td>
			<td>
                            
                <div id="newbldpanel1" class="newpanelcontent" >
		<select id="newwallidselect" name="newwallidselect" onchange="changeWallImg(this,'newwallimage');">
			<? for ($r=1;$r<=$wallcount;$r++) { ?>
				<option value=<?= $r; ?>>wall <?= $r; ?></option>
			<? } ?>
		</select>
		<img id="newwallimage" src="/assets/walls/dm2_1.png" width="20" height="20"/>
                </div>
                <div id="newbldpanel2" class="newpanelcontent" >
                    <input type="text" name="new_design_id" id="new_design_id" value="" readonly="readonly"/>
                    <input type="button" onclick="createNewDesign();" value="create new design" />
                </div>
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
				壁画像：
			</td>
			<td>
                            <div id="editbldpanel1" class="editpanelcontent" >
				<select id="wallidselect" name="wallidselect" onchange="changeWallImg(this,'wallimage');">
					<? for ($r=1;$r<=$wallcount;$r++) { ?>
						<option value=<?= $r; ?>>wall <?= $r; ?></option>
					<? } ?>
				</select>
				<img id="wallimage" src="/assets/walls/dm2_1.png" width="20" height="20"/>
                            </div>
                            <div id="editbldpanel2" class="editpanelcontent" >
                                <input type="text" name="edit_design_id" id="edit_design_id" value="" readonly="readonly"/>
                                <input type="button" onclick="editDesign('edit_design_id');" value="create new design" />
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
		Tokyo
</div>

</form>
</BODY>
</HTML>