
<HTML>
<HEAD>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<TITLE>City Design</TITLE>
<script language="javascript">

function init(){
	  var pstr = "";
	  pstr += " -dserversrc ";
	  //pstr += "127.0.0.1 /maps/jsat15m.glm ";
	  pstr += "<?= $host; ?> /maps/legacy/kkc.glm ";
	  pstr += " -shapeserversrc <?= $host; ?> /hawkeye/overlay/ ";
	  
	  pstr += " -basescale 3200 ";
	  pstr += " -numlayers 7 ";
	  pstr += " -fov 60 ";
	  pstr += " -units dd ";
	  //pstr += " -notools -nosettings -noprintmode ";
	  //pstr += " -terrain -kankyou ";
	  pstr += " -maxalt 10000000";
	  pstr += " -jscript -Smapoutputcoords -Shandleaction -Sitemclicked";
	  //pstr += " -vextents 126 25 149 46 ";
	  // pstr += " -debugfile C:\\Users\\carls\\Documents\\dfly.txt ";
	  pstr += " -mapjoho -earthmodel -nostencil ";
 	  //pstr += " -mapjoho ";
	  //pstr += " -keepalive 0 ";
	  
	  dragonfly.sendParameterString(pstr);
	  dragonfly.sendMessage("StartDataThreads");
	  //dragonfly.setCameraMapPos(139.774883522,35.6426525309,20000,-90,0);

	  dragonfly.setCameraMapPos(139.766,35.68,10000,-90,0);
}

function handleAction(val,shp,lyr) {
	parent.frames["contents"].handleAction(val,shp,lyr);

}

function itemClicked(id,layerid,inclusive) {
	parent.frames["contents"].itemClicked(id,layerid,inclusive);
}

function mapOutputCoords(outx,  alt, outy, li, si, pi, isadd, iscw) {
	parent.frames["contents"].mapOutputCoords(outx,  alt, outy, li, si, pi, isadd, iscw);
}

function setObjSize(){
 if (parseInt(navigator.appVersion)>3) {
 if (navigator.appName=="Netscape") {
  winW = window.innerWidth;
  winH = window.innerHeight;
 }
 if (navigator.appName.indexOf("Microsoft")!=-1) {
  winW = document.body.offsetWidth;
  winH = document.body.offsetHeight;
 }
}

  if(winH > 65){
  		dragonfly.width = (winW);
  		dragonfly.height = (winH);
  }
  rskey = -1;
}
var rskey = -1;
window.onresize = function(){
	
	if(rskey != -1)
		clearTimeout(rskey);
	rskey = setTimeout("setObjSize();",500);
}


function mapSet(fx,fy,wx,wy,alt,dir,pitch){
  window.status = fx + " " + fy + " " + alt + " " + dir + " " + pitch;
  return;
}


</script>
</HEAD>
<BODY leftmargin="1" rightmargin="1" topmargin="1" bottommargin="1" onload="init();">
<script language="javascript">
 var stwinW,stwinH;
 
 if (parseInt(navigator.appVersion)>3) {
 if (navigator.appName=="Netscape") {
  stwinW = window.innerWidth;
  stwinH = window.innerHeight;
 }
 if (navigator.appName.indexOf("Microsoft")!=-1) {
  stwinW = document.body.offsetWidth;
  stwinH = document.body.offsetHeight;
 }
}
 var plugintext = "<object width=\""+(stwinW)+"\" height=\""+(stwinH)+"\" id=\"dragonfly\" name=\"dragonfly\" codebase=\"dragonfly.cab#Version=<%=DragonflyVersion%>\" classid=\"clsid:99F34F1F-A0FE-43D1-843A-C83F938E9558\">";
	plugintext += "</object>";
 document.write(plugintext);

</script>

</BODY>
</HTML>
