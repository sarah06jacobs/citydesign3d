<?php

/**
 * API親コントローラー
 *
 * @package  app
 * @extends  Controller
 */

class Controller_Apibase extends Controller_Base {
    /*
     * 認証有無
     */
    public $BIG_ENDIAN = false;
    public $auth = false;
    /**
     * 前処理
     *
     * @access  public
     * @return  Response
     */
    public function before() {
        // FuelPHPのバグ対策
        // 'log_threshold' > Fuel::L_WARNING の時 Class 'Log' not found となる
        !class_exists('Log') and \Package::load('log');

        $this -> BIG_ENDIAN =  (pack('L', 1) === pack('N', 1));

        parent::before();
    }

    public function pack_int32s_be($n) {
        if ( $this -> BIG_ENDIAN) {
            return pack('l', $n); // that's a lower case L
        }
        return strrev(pack('l', $n));
    }

    public function pack_int32s_le($n) {
        if ( $this -> BIG_ENDIAN) {
            return strrev(pack('l', $n));
        }
        return pack('l', $n); // that's a lower case L
    }
    public function pack_double_be($n) {
        if ( $this -> BIG_ENDIAN) {
            return pack('d', $n);
        }
        return strrev(pack('d', $n));
    }
    public function pack_double_le($n) {
        if ( $this -> BIG_ENDIAN) {
            return strrev(pack('d', $n));
        }
        return pack('d', $n);
    }
    public function pack_byte($c) {
      return pack('c', $c);
    }

    public function pack_str($str , $len) {
      return pack('A' . $len , $str);
    }

    public function getShapeType($n) {
      $n = strtoupper($n);
      //1 Point
//3 PolyLine
//5 Polygon
//8 MultiPoint
//11 PointZ
//13 PolyLineZ
//15 PolygonZ
//18 MultiPointZ
//21 PointM
//23 PolyLineM
//25 PolygonM
//28 MultiPointM
        if($n === "POLYGON") {
          return 5;
        }
        else if($n === "POLYLINE") {
          return 3;
        }
        else if($n === "POINT") {
          return 1;
        }
        else if($n === "MULTIPOINT") {
          return 8;
        }
        else if($n === "POINTZ") {
          return 11;
        }
        else if($n === "POLYLINEZ") {
          return 13;
        }
        else if($n === "POLYGONZ") {
          return 15;
        }
        else if($n === "MULTIPOINTZ") {
          return 18;
        }
        return 1;
    }

    public function countShapePoints($coords) {
      $pct = 0;
      for($i=0;$i<count($coords);$i++) {
        $pct = $pct + count($coords[$i]);
      }
      return $pct;
    }

    public function getShapeBounds($coords) {
      if( count($coords) == 0) {
        return array(0,0,0,0);
      }
      $bounds = array( $coords[0][0][0]  , $coords[0][0][1]  , $coords[0][0][0] , $coords[0][0][1] );


      for($i=0;$i<count($coords);$i++) {
        for($j=1;$j<count($coords[$i]);$j++) {
          if($coords[$i][$j][0] < $bounds[0] ) {
            $bounds[0] = $coords[$i][$j][0];
          }
          if($coords[$i][$j][1] < $bounds[1] ) {
            $bounds[1] = $coords[$i][$j][1];
          }
          if($coords[$i][$j][0] > $bounds[2] ) {
            $bounds[2] = $coords[$i][$j][0];
          }
          if($coords[$i][$j][1] > $bounds[3] ) {
            $bounds[3] = $coords[$i][$j][1];
          }
        }
      }
      return $bounds;
    }

}
