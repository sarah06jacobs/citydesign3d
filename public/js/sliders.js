
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
		}
		d.ontouchmove=d.onmousemove;
		
		d.onmouseup=function(){
			d.onmousemove=null;d.ontouchmove=null;	
			document.onmousemove=null; document.onmouseup=null;
		}
		document.onmousemove=d.onmousemove; document.onmouseup=d.onmouseup;
		d.ontouchend=d.onmouseup;
}

