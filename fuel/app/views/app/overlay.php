NAME vecs
STORE <?= $shaperoot; ?>

KEEPALIVE ON
SRID 4612
  
  LAYER
    NAME top_line
    DATA ipc/top/top_line
    DISPLAY3D ON
    TYPE LINE
    STATUS OFF
    LINEWIDTH 3
    DRAWSTYLE DASHED
    LIFTHT 50
    STROKE 190 190 200 120
    CGIREQUEST /cgi-bin/DFCgi.exe
    SRID 4612
    TILEWIDTH 2000000
    MAXSCALE 10000000
    MINSCALE 10000
    TRAIL 7
  END

  LAYER
    NAME top_annotation
    DATA ipc/top/top_annotation
    LABELATTRIBUTE name_str1
    FONTTEXTCOLOR 255 255 255 255
    FONTOUTLINECOLOR 20 20 20 255
    LABELSCALE 40
    MAXLABELS 30
    CENTERMAGNIFY 0.5:4.0:0.3
    DISPLAY3D ON
    TYPE POINT
    STATUS OFF
    POINTSIZE 0
    LIFTHT 20
    STROKE 220 200 20 255
    CGIREQUEST /cgi-bin/DFCgi.exe
    SRID 4612
    TILEWIDTH 200000
    MAXSCALE 10000000
    MINSCALE 10000
    TRAIL 3
  END

  LAYER
    NAME middle_annotation
    DATA ipc/middle/middle_annotation
    LABELATTRIBUTE name_str1
    FONTTEXTCOLOR 255 255 255 255
    FONTOUTLINECOLOR 20 20 20 255
    LABELSCALE 40
    MAXLABELS 300
    DISPLAY3D ON
    TYPE POINT
    STATUS OFF
    POINTSIZE 0
    LIFTHT 20
    STROKE 220 200 20 255
    CGIREQUEST /cgi-bin/DFCgi.exe
    SRID 4612
    TILEWIDTH 50000
    MAXSCALE 70000
    MINSCALE 5000
    TRAIL 7
  END

  LAYER
    NAME middle_road_line
    DATA ipc/middle/middle_road_line
    DISPLAY3D ON
    TYPE LINE
    STATUS OFF
    LINEWIDTH 2
    LIFTHT 15
    STROKE 250 250 70 150
    CGIREQUEST /cgi-bin/DFCgi.exe
    SRID 4612
    TILEWIDTH 5000000
    MAXSCALE 10000000
    MINSCALE 10000
    TRAIL 7
  END

  LAYER
    NAME middle_railway
    DATA ipc/middle/middle_railway
    DISPLAY3D ON
    TYPE LINE
    STATUS OFF
    LINEWIDTH 3
    LIFTHT 10
    STROKE 230 230 230 200
    CGIREQUEST /cgi-bin/DFCgi.exe
    SRID 4612
    TILEWIDTH 5000000
    MAXSCALE 10000000
    MINSCALE 10000
    TRAIL 7
  END

  LAYER
    NAME oaza_polygon
    DATA ipc/bottom/oaza_polygon
    DISPLAY3D ON
    TYPE POLYGON
    DRAWSTYLE DASHED
    LIFTHT 50
    STATUS OFF
    LINEWIDTH 2
    LIFTHT 15
    STROKE 120 200 120 170
    FILL 80 80 80 0
    CGIREQUEST /cgi-bin/DFCgi.exe
    SRID 4612
    TILEWIDTH 100000
    MAXSCALE 100000
    MINSCALE 10000
    TRAIL 8
  END

  LAYER
    NAME aza_polygon
    DATA ipc/bottom/aza_polygon
    DISPLAY3D ON
    TYPE POLYGON
    DRAWSTYLE DASHED
    LIFTHT 50
    STATUS OFF
    LINEWIDTH 3
    LIFTHT 5
    STROKE 120 250 120 170
    FILL 80 80 80 0
    CGIREQUEST /cgi-bin/DFCgi.exe
    SRID 4612
    TILEWIDTH 10000
    MAXSCALE 10000
    MINSCALE 0
    TRAIL 7
  END

  LAYER
    NAME city_railway
    DATA ipc/city/city_railway
    DISPLAY3D ON
    TYPE LINE
    STATUS OFF
    LINEWIDTH 4
    LIFTHT 5
    STROKE 200 20 200 200
    CGIREQUEST /cgi-bin/DFCgi.exe
    SRID 4612
    TILEWIDTH 20000
    MAXSCALE 10000
    MINSCALE 0
    TRAIL 7
  END

  LAYER
    NAME city_road_0
    DATA ipc/city/city_road_0
    DISPLAY3D ON
    TYPE POLYGON
    STATUS OFF
    LINEWIDTH 1
    LIFTHT 5
    STROKE 200 200 200 200
    FILL 180 180 180 0
    CGIREQUEST /cgi-bin/DFCgi.exe
    SRID 4612
    TILEWIDTH 20000
    MAXSCALE 10000
    MINSCALE 0
    TRAIL 7
  END

  LAYER
    NAME city_road_1
    DATA ipc/city/city_road_1
    DISPLAY3D ON
    TYPE POLYGON
    STATUS OFF
    LINEWIDTH 1
    LIFTHT 5
    STROKE 200 200 200 200
    FILL 180 180 180 0
    CGIREQUEST /cgi-bin/DFCgi.exe
    SRID 4612
    TILEWIDTH 10000
    MAXSCALE 10000
    MINSCALE 0
    TRAIL 7
  END

  LAYER
    NAME city_road_2
    DATA ipc/city/city_road_2
    DISPLAY3D ON
    TYPE POLYGON
    STATUS OFF
    LINEWIDTH 1
    LIFTHT 5
    STROKE 200 200 200 200
    FILL 180 180 180 0
    CGIREQUEST /cgi-bin/DFCgi.exe
    SRID 4612
    TILEWIDTH 10000
    MAXSCALE 10000
    MINSCALE 0
    TRAIL 7
  END

  LAYER
	NAME tatemono_1
	LAYERID 1000
	TYPE BUILDING
	DISPLAY3D ON
  DEPTH ON
	CGIREQUEST /api/gis/getlayer?pool=hawk
	STATUS ON
	DATA tatemono_1:floornum:floorht:wallid:designid:flground:tname
	SRID 4612
	TILEWIDTH 30000
	STROKE 173 173 185
	MAXSCALE 10000
	TRAIL 9
	MINSCALE 0
  SHADOWS ON
	BWALLS /assets/walls/dm2_
	BWALLFLNUM floornum
	BWALLFLHT floorht
	BWALLATTRIBUTE wallid
  <? if (Config::get('groundRoofTex1') == 1) { ?>
  BROOFTEXTURE ON
  <? } ?>
  
  BWALLFLGROUND flground
	BWALLCOUNT <?= $wallcount; ?>

	BWALLTEXW <?= $walltexw; ?>
  MAXLABELS 200
  LABELATTRIBUTE tname
  LABELSCALE 50
  LABELVISIBLE OFF
  FONTTEXTCOLOR 255 255 0 255
  FONTOUTLINECOLOR 20 20 20 255
  END

  LAYER
	NAME tatemono_2
	LAYERID 1001
  DEPTH ON
	TYPE BUILDING
	DISPLAY3D ON
	CGIREQUEST /api/gis/getlayer?pool=hawk
	STATUS ON
	DATA tatemono_2:floornum:floorht:wallid:designid:flground:tname
	SRID 4612
	TILEWIDTH 30000
	STROKE 173 173 185
	MAXSCALE 10000
  <? if (Config::get('groundRoofTex2') == 1) { ?>
  BROOFTEXTURE ON
  <? } ?>

	TRAIL 9
	MINSCALE 0
	BWALLS /assets/walls/dm2_
	BWALLFLNUM floornum
	BWALLFLHT floorht
	BWALLATTRIBUTE wallid
  BWALLFLGROUND flground
	BWALLCOUNT 1
	BWALLTEXW <?= $walltexw; ?>
  
  SHADOWS ON
	DESIGNATTRIBUTE designid
	DESIGNFOLDER /assets/design
	DESIGNSERVLET /api/gis/getdesign?design_id=
	DESIGNOBJECTMAX 200
	DESIGNTEXTUREMAX 2000
  MAXLABELS 200
  LABELATTRIBUTE tname
  LABELSCALE 50
  LABELVISIBLE OFF
  FONTTEXTCOLOR 255 255 0 255
  FONTOUTLINECOLOR 20 20 20 255
  END

  LAYER
  NAME tatemono_v
  LAYERID 2000
  DEPTH ON
  TRAIL 5
  SRID 4612
  DISPLAY3D ON
  STATUS ON
  TYPE POINT
  MAXSCALE 10000
  TILEWIDTH 20000
  MINSCALE 0
  SYMBOLS /cgi-bin/DFCgi.exe?vrml=
  SYMBOLFILETYPE VRML
  SYMBOLATTDESC NAME
  DATA tatemono_v:wrl:tfm:tname
  TRANSFORMATTRIBUTE tfm
  SYMBOLATTRIBUTE wrl
  MAXLABELS 200
  LABELATTRIBUTE tname
    FONTTEXTCOLOR 255 255 255 255
    FONTOUTLINECOLOR 20 20 20 255
    LABELSCALE 50
    LABELVISIBLE OFF
  CGIREQUEST /api/gis/getlayer?pool=hawk&gty=POINT
  END
END