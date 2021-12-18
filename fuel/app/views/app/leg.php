

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Legend</title>

<script language="javascript">
var IE = (document.all)?true:false;

function showoutput(msg1,msg2){
	window.status = "message : " + msg1;
}
function plotPosition(lat,lon,alt){
}
function popUpWindow(url){
	//parent.list.openwin(url,500,400);
}
function showalert(msg){
	alert(msg);
}

var numMaps = 3;

var min_x = new Array();
var max_y = new Array();
var max_x = new Array();
var min_y = new Array();
var maxalt = new Array();
var map_w = new Array();
var map_h = new Array();
var map_px_w = new Array();
var map_px_h = new Array();
var stopAlt = new Array();
var legendMaps = new Array();

min_x[0] = 127084370;
max_y[0] = 45609760;
max_x[0] = 147084370;
min_y[0] = 25609760;
maxalt[0] = 100000000;
map_px_w[0] = 700;
map_px_h[0] = 700;
stopAlt[0] = 30000;
legendMaps[0] = new Image();
legendMaps[0].src = "img/japan.png";
map_w[0] = max_x[0] - min_x[0];
map_h[0] = max_y[0] - min_y[0];
min_x[1] = 139220727;
max_y[1] = 45561515;
max_x[1] = 149057444;
min_y[1] = 41284011;
maxalt[1] = 100000;
map_px_w[1] = 1454;
map_px_h[1] = 914;
stopAlt[1] = 5000;
legendMaps[1] = new Image();
legendMaps[1].src = "img/hokkaido.png";
map_w[1] = max_x[1] - min_x[1];
map_h[1] = max_y[1] - min_y[1];
min_x[2] = 141147682;
max_y[2] = 43187488;
max_x[2] = 141511853;
min_y[2] = 42914493;
maxalt[2] = 30000;
map_px_w[2] = 754;
map_px_h[2] = 742;
stopAlt[2] = 400;
legendMaps[2] = new Image();
legendMaps[2].src = "img/sapporo_small.png";
map_w[2] = max_x[2] - min_x[2];
map_h[2] = max_y[2] - min_y[2];

var framew = 0;
var frameh = 0;
var imgw = 30;
var imgh = 30;
var imgroot =  "img/";
var planeimg = new Array();
var overmap = false;
var current_x = 0;
var current_y = 0;
var current_theta = 0;
var current_alt = 0;
var current_fx = 0;
var current_fy = 0;
window.onresize = setMap;
var winW,winH;
var CurrentMap = 0;
var doSnap = true;
var y_mark,x_mark;
var doSetMark = false;
var isFlying = false;
var timeoutId = -1;

var initComplete = false;
var isPageLoaded = false;

function releaseSnap(){
	doSnap = false;
}

function init(){
	waitForInit();
	isPageLoaded = true;
}

function getRefmapObj(objname){
	if(document.all){
		return refmap.document.getElementById(objname);
	}else{
		return document.getElementById("refmap").contentDocument.getElementById(objname);
	}
}
function scrollRefMap(l,d){
	if(document.all){
		refmap.window.scrollTo(l,d);
	}
	else{
		window.frames[0].scrollTo(l,d);
	}
}

function centerframeOn(fx,fy){
	
	var goleft = (fx-min_x[CurrentMap]) * (map_px_w[CurrentMap]/map_w[CurrentMap])-(8);
	var godown = (max_y[CurrentMap]-fy) * (map_px_h[CurrentMap]/map_h[CurrentMap])-(8);
	scrleft = parseInt(goleft) - (framew/2) + (imgw/2);
	scrdown = parseInt(godown) - (frameh/2) + (imgh/2);
	scrollRefMap(scrleft,scrdown);
}

function centerframe(){
	//var dotobj = getRefmapObj("dot");
	var byeobj = getRefmapObj("blseye");
	scrleft = parseInt(byeobj.style.left) - (framew/2) + (imgw/2);
	scrdown = parseInt(byeobj.style.top) - (frameh/2) + (imgh/2);
	scrollRefMap(scrleft,scrdown);
}

function toggleMap(n){
	setTabs(n);
	if((current_x > max_x[n]) || (current_x < min_x[n]) || (current_y > max_y[n]) || (current_y < min_y[n])){
		getRefmapObj("legendMap").src = legendMaps[n].src;
		CurrentMap = n;
		updatePosition(current_x,current_y,current_theta,current_alt);
		scrollRefMap((map_px_w[n]/2),(map_px_h[n]/2));
	}
	else{
		if(CurrentMap != n){ // switch map
			getRefmapObj("legendMap").src = legendMaps[n].src;
			if(doSetMark) setXMark((x_mark-min_x[n]) * (map_px_w[n]/map_w[n]),(max_y[n]-y_mark) * (map_px_h[n]/map_h[n]));
		}
		CurrentMap = n;
		updatePosition(current_x,current_y,current_theta,current_alt,current_fx,current_fy);
		centerframe();
	}
}

function setTabs(n){
for(var i=0;i<numMaps;i++){
	var obj = document.getElementById("mtab" + i);
	var sty = "";
	if(n == i){
		sty = "tabon";
	}
	else{
		sty = "taboff";	
	}
	obj.className = sty;
}


}

function updatePosition(x,y,theta,alt,fx,fy){
		current_x = x;
		current_y = y;
		current_theta = theta;
		current_alt = alt;
		current_fx = fx;
		current_fy = fy;
		
		if((current_x > max_x[CurrentMap]) || (current_x < min_x[CurrentMap]) || (current_y > max_y[CurrentMap]) || (current_y < min_y[CurrentMap])){
			doSnap = false;
			
			var dotobj = getRefmapObj("dot");
			dotobj.style.left = -imgw;
			dotobj.style.top = -imgh;
			
			var byeobj = getRefmapObj("blseye");
			byeobj.style.left = -imgw;
			byeobj.style.top = -imgh;
		}
		else{
		
			rotateplane(theta);
			var dotobj = getRefmapObj("dot");
			dotobj.style.left = (x-min_x[CurrentMap]) * (map_px_w[CurrentMap]/map_w[CurrentMap])-(imgw/2);
			dotobj.style.top = (max_y[CurrentMap]-y) * (map_px_h[CurrentMap]/map_h[CurrentMap])-(imgh/2);
			
			var byeobj = getRefmapObj("blseye");
			byeobj.style.left = (fx-min_x[CurrentMap]) * (map_px_w[CurrentMap]/map_w[CurrentMap])-(8);
			byeobj.style.top = (max_y[CurrentMap]-fy) * (map_px_h[CurrentMap]/map_h[CurrentMap])-(8);
			
			if(doSnap)
				centerframe();
	
		}
}

function goToPoint(x,y,altitude){
	var inMap = true;
	var arcangle = 35;
	var vang = "35";
	if(!isNaN(vang)) arcangle = vang;
	
	setXMark((x-min_x[CurrentMap]) * (map_px_w[CurrentMap]/map_w[CurrentMap]),(max_y[CurrentMap]-y) * (map_px_h[CurrentMap]/map_h[CurrentMap]));

	parent.frames["dragonfmap"].legendGoToObject(x,y,altitude,arcangle);
}


function rotateplane(theta){
	var pl = getRefmapObj("plane");
	if((theta > 0) && (theta <= 22.5)){
		pl.src = planeimg[0].src;
	} else if((theta > 22.5) && (theta <= 67.5)){
		pl.src = planeimg[1].src;
	} else if((theta > 67.5) && (theta <= 112.5)){
		pl.src = planeimg[2].src;
	} else if((theta > 112.5) && (theta <= 157.5)){
		pl.src = planeimg[3].src;
	} else if((theta > 157.5) && (theta <= 202.5)){
		pl.src = planeimg[4].src;
	} else if((theta > 202.5) && (theta <= 247.5)){
		pl.src = planeimg[5].src;
	} else if((theta > 247.5) && (theta <= 292.5)){
		pl.src = planeimg[6].src;
	} else if((theta > 292.5) && (theta <= 337.5)){
		pl.src = planeimg[7].src;
	} else{
		pl.src = planeimg[0].src;
	}
}
function mapon(e){
	overmap = true;
}
function mapoff(e){
	overmap = false;
}
function mapover(e){
	if(overmap){
	var tempX,tempY;
	if (IE) { // grab the x-y pos.s if browser is IE
    	tempX = refmap.event.clientX + refmap.document.body.scrollLeft
    	tempY = refmap.event.clientY + refmap.document.body.scrollTop
  	} else {  // grab the x-y pos.s if browser is NS
   		tempX = e.pageX
    	tempY = e.pageY
  	}

	var lon = min_x[CurrentMap] + (tempX / (map_px_w[CurrentMap]/map_w[CurrentMap]))
	var lat = max_y[CurrentMap] - (tempY / (map_px_h[CurrentMap]/map_h[CurrentMap]));

	window.status = "x: " + lon + " y: " + lat;
	}
}
function mapdown(e){
	doSnap = false;
	var tempX,tempY;
	if (IE) { // grab the x-y pos.s if browser is IE
    	tempX = refmap.event.clientX + refmap.document.body.scrollLeft;
    	tempY = refmap.event.clientY + refmap.document.body.scrollTop;
  	} else {  // grab the x-y pos.s if browser is NS
   		tempX = e.pageX;
    	tempY = e.pageY;
  	}
	setXMark(tempX,tempY);

	var lon = min_x[CurrentMap] + (tempX / (map_px_w[CurrentMap]/map_w[CurrentMap]))
	var lat = max_y[CurrentMap] - (tempY / (map_px_h[CurrentMap]/map_h[CurrentMap]));
	
	goToPoint(lon,lat,stopAlt[CurrentMap]);
}
function setXMark(x,y){
	var xobj = getRefmapObj("xmark");
	xobj.style.left = x-10;
	xobj.style.top = y-10;
	doSetMark = false;
}

var delay = 10;
var movespeed = 2;
var timer;
var stillDown;

function setMap(){
	readBound();
	
	document.getElementById("panmap").style.width = winW - 4 - 4;
	document.getElementById("panmap").style.height = winH - 32 - 4;
	if(document.all){
		document.all.refmap.style.width = framew = winW - 4 - 4;
		document.all.refmap.style.height = frameh = winH - 32 - 4;
	}
	else{
		document.getElementById("refmap").style.width = framew = winW - 4 - 4;
		document.getElementById("refmap").style.height = framew = winH - 32 - 4;
	}
}
function readBound(){
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
}

function waitForInit(){
	if(numMaps > 0)
	getRefmapObj("legendMap").src = legendMaps[0].src;
	setTabs(0);
	var dotobj = getRefmapObj("maplayer");
	dotobj.onmouseout=mapoff;
	dotobj.onmouseover=mapon;
	dotobj.onmousemove=mapover;
	dotobj.onmousedown=mapdown;
	document.getElementById("panmap").onmouseover=releaseSnap;
	planeimg.size = 8;
	planeimg[0] = new Image();
	planeimg[0].src = imgroot + "f14_N.png";
	planeimg[1] = new Image();
	planeimg[1].src = imgroot + "f14_NW.png";
	planeimg[2] = new Image();
	planeimg[2].src = imgroot + "f14_W.png";
	planeimg[3] = new Image();
	planeimg[3].src = imgroot + "f14_SW.png";
	planeimg[4] = new Image();
	planeimg[4].src = imgroot + "f14_S.png";
	planeimg[5] = new Image();
	planeimg[5].src = imgroot + "f14_SE.png";
	planeimg[6] = new Image();
	planeimg[6].src = imgroot + "f14_E.png";
	planeimg[7] = new Image();
	planeimg[7].src = imgroot + "f14_NE.png";
	setMap();
	document.getElementById("panmap").style.visibility = "visible";
	parent.frames["dragonfmap"].SLegend = this;
	doSnap = true;
}

</script>
<LINK REL="StyleSheet" HREF="css/style.css" TYPE="text/css">
</head>

<body onload="init();"  bottommargin="0" topmargin="0" leftmargin="0" rightmargin="0" bgcolor="#788880"  background="">

<div id="maptabs" style="top:5px;height:24px;position:absolute;">
<table cellpadding=1 cellspacing="0">
<tr>

<td id="mtab0" onclick="toggleMap(0);" class="taboff">&nbsp;<a onclick="toggleMap(0);">日本&nbsp;</a></td>
<!--
<td id="mtab1" onclick="toggleMap(1);" class="taboff">&nbsp;<a onclick="toggleMap(1);">北海道&nbsp;</a></td>

<td id="mtab2" onclick="toggleMap(2);" class="taboff">&nbsp;<a onclick="toggleMap(2);">札幌&nbsp;</a></td>
-->
</tr>
</table>
</div>

<div id="panmap" style="visibility:visible;position:absolute;top:30px;left:2px;">
	<iframe style="scrollbar-track-color:#132885;scrollbar-base-color:#B4C9DE;scrollbar-arrow-color:#0D3965;" id="refmap" scrolling=on src="mapimg.html"></iframe>
</div>
</body>
</html>


