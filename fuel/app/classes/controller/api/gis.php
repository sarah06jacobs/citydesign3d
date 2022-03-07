<?php

/**
 * ギフトコントローラー
 *
 * @package  app
 * @extends  Controller
 */

// http://127.0.0.1/api/gis/getlayer?pool=hawk&stores=C:/ms4w/ms4w/Apache/htdocs/nigata/public/maps/shape/&layers=tatemono_1:floornum:floorht&bbox=139.740000000,35.660000000,139.760000000,35.680000000&gz=0
// http://127.0.0.1/api/gis/getlayer?pool=hawk&stores=C:/ms4w/ms4w/Apache/htdocs/nigata/public/maps/shape/&layers=tatemono_v&bbox=139.740000000,35.660000000,139.760000000,35.680000000&gz=0&gty=POINT

// http://127.0.0.1/cgi-bin/DFCgi.exe?vrml=apartment_4.wrl
class Controller_Api_Gis extends Controller_Apibase {
    public function before() {
        $this->auth = false;

        parent::before();
    }

    public function action_getlayer() {
    	$post = Input::post();
        $get = Input::get();
        $post = array_merge($get, $post);

        $layers = isset($post['layers']) ? $post['layers'] : "tatemono_1:floornum:floorht";
        $bbox = isset($post['bbox']) ? $post['bbox'] : "139.7,35.6,139.8,35.7";
        
        $fromts = isset($post['fromts']) ? $post['fromts']+0 : 0;
        $tots = isset($post['tots']) ? $post['tots']+0 : 0;
        $geomtype = isset($post['gty']) ? $post['gty'] : "POLYGON";
        //$bbox = "139.7,35.6,139.8,35.7";

        $ATTRIBUTE_LENGTH = 64;

        $layer_arr = explode(':' , $layers);
        $bbox_arr = explode("," , $bbox);

        // lyr->datalen = 9;// shaptype clen numshapes
        $content_length = 1;
        $out_content_size = 0;

        $att_count = count($layer_arr) - 1;

        $query = DB::select('*' , db::expr("ST_AsGeoJSON(wkb_geometry) gjson"));
        $query->from($layer_arr[0]);
        
        if( $fromts > 0 && $tots > 0 ) {
            $query->where('create_ts' , '<=' , $tots);
            $query->and_where_open();
            $query->where('end_ts' , '>=' , $fromts);
            $query->or_where('end_ts' , '0');
            $query->and_where_close();
        }
        
        $query->where('wkb_geometry','&&' ,db::expr(' ST_MakeEnvelope ('.$bbox_arr[0].', '.$bbox_arr[1].','.$bbox_arr[2].', '.$bbox_arr[3].',4612)'));
        $result = $query->execute()->as_array();

        $rcount = count($result);
        if( $rcount == 0 ) {
        	$content_length = 9; // no data
        }
        else {
        	$content_length = 9;// shaptype clen numshapes
        }

        $sig = $this->pack_int32s_le(9850);
        $out_content_size = $out_content_size + 4;
        $numlayers = $this->pack_int32s_le(1);
        $out_content_size = $out_content_size + 4;

        $shape_type = 1;
        if ( $rcount > 0 ) {
        	$geom_obj = json_decode($result[0]['gjson'], true);
        	$shape_type = $this -> getShapeType($geomtype);
        }

        $out_stype = $this->pack_byte($shape_type);
        $out_content_size = $out_content_size + 1;
        $out_clen = $this->pack_int32s_le($content_length); 
        $out_content_size = $out_content_size + 4;
        
        $out_numshape = $this->pack_int32s_le($rcount);
        $out_content_size = $out_content_size + 4;
        $out_att_count = $this->pack_int32s_le($att_count);
        $out_content_size = $out_content_size + 4;
        $out_att_sizes = array();
        for($j=0;$j<$att_count;$j++){
        	$out_att_sizes[] = $this->pack_int32s_le($ATTRIBUTE_LENGTH);
        	$out_content_size = $out_content_size + 4;
        }
		
        $shpbin = array();
        if ( $shape_type == 1 || $shape_type == 11 ) {
            for($j=0;$j<$rcount;$j++){
                $shpbin[] = $this->pack_int32s_le($result[$j]['gid'] + 0);
                $out_content_size = $out_content_size + 4;

                $geom_obj = json_decode($result[$j]['gjson'], true);
                $coords = $geom_obj['coordinates'];
                for($k=0;$k<count($coords);$k++) {
                    $shpbin[] = $this->pack_double_le($coords[$k]);
                    $out_content_size = $out_content_size + 8;
                }
                for( $k=0;$k<$att_count; $k++ ) {
                    $shpbin[] = $this -> pack_str( $result[$j][$layer_arr[$k+1]] , $ATTRIBUTE_LENGTH );
                    $out_content_size = $out_content_size + $ATTRIBUTE_LENGTH;
                }
            }
        }
        else {
    		for($j=0;$j<$rcount;$j++){
    			//	_shapeIds[j] = reader->readLongLSB();
    			$shpbin[] = $this->pack_int32s_le($result[$j]['gid'] + 0);
    			$out_content_size = $out_content_size + 4;

    			$geom_obj = json_decode($result[$j]['gjson'], true);
    			$coords = $geom_obj['coordinates'];
    			$bounds = $this -> getShapeBounds($coords);
    			for($k=0;$k<count($bounds);$k++){
    				$shpbin[] = $this->pack_double_le($bounds[$k]);
    				$out_content_size = $out_content_size + 8;
    			}

    			$shpbin[] = $this->pack_int32s_le(count($coords));
    			$out_content_size = $out_content_size + 4;

    			$nPoints = $this->countShapePoints($coords);
    			$shpbin[] = $this->pack_int32s_le($nPoints);
    			$out_content_size = $out_content_size + 4;

    			$partn = 0;
    			for($k=0;$k<count($coords);$k++){
    				$shpbin[] = $this->pack_int32s_le($partn);
    				$partn = count($coords[$k]); // start of next part
    				$out_content_size = $out_content_size + 4;
    			}


    			for($a=0;$a<count($coords);$a++) {
            		for($b=0;$b<count($coords[$a]);$b++) {
            			//		tmpx = reader->readDoubleLSB();
    					//		tmpy = reader->readDoubleLSB();
            			$shpbin[] = $this->pack_double_le($coords[$a][$b][0]);
            			$shpbin[] = $this->pack_double_le($coords[$a][$b][1]);
            			$out_content_size = $out_content_size + 16;
            		}
            	}
                for( $k=0;$k<$att_count; $k++ ) {
                    $shpbin[] = $this -> pack_str( mb_convert_encoding($result[$j][$layer_arr[$k+1]],"sjis","utf-8") , $ATTRIBUTE_LENGTH );
                    $out_content_size = $out_content_size + $ATTRIBUTE_LENGTH;
                }
    		}
        }


		header("Content-type: application/esrishp");
		header("Content-length: " . $out_content_size );

		echo $sig;
		echo $numlayers;
		echo $out_stype;
		echo $out_clen;
		echo $out_numshape;
		echo $out_att_count;
		for($j=0;$j<$att_count;$j++){
        	echo $out_att_sizes[$j];
        }
        for($j=0;$j<count($shpbin);$j++) {
        	echo $shpbin[$j];
        }
    }
    
    // images will be upladed to design folder,  DESIGNFOLDER
    // %s/rc_%d/%s%d.%s\0",symname,objid,ot,number,symext
    // ot is wall or roof, fileext is png
    // objid is the design_id from design_attribute
    
    // table,  design_base -> design_id,dname , create_date
    // design_item -> dtype, dvalue, idx
    public function action_getd() {
            return "OK";
    }
    
    public function action_getdesign() {
        $post = Input::post();
        $get = Input::get();
        $post = array_merge($get, $post);

        $design_id = isset($post['design_id']) ? $post['design_id'] : "0";
        
        $query = DB::select('design_item.*');
        $query->from('design_base');
        $query->join('design_item' , 'left') -> on('design_base.design_id' , '=' , 'design_item.design_id');
        $query->where('design_base.design_id',$design_id);
        $designs = $query->execute()->as_array();
        
        header("Content-type: text/plain");
        
        echo "TATEMONO\n";
        echo "    WALLS\n";
        echo "        TEXTURES\n";
        for($i=0;$i<count($designs);$i++) {
            if( $designs[$i]['dtype'] == 0 ) {
        echo "            TEX ".$designs[$i]['dvalue']."\n";
            }
        }
        echo "        END\n";
        echo "        COLORS\n";
        for($i=0;$i<count($designs);$i++) {
            if( $designs[$i]['dtype'] == 1 ) {
        echo "            COL ".$designs[$i]['dvalue']."\n";
            }
        }
        echo "        END\n";
        echo "    END\n";
        echo "    ROOF\n";
        echo "        COLORS\n";
        for($i=0;$i<count($designs);$i++) {
            if( $designs[$i]['dtype'] == 3 ) {
        echo "            COL ".$designs[$i]['dvalue']."\n";
            }
        }
        echo "        END\n";
        echo "        TEXTURES\n";
        for($i=0;$i<count($designs);$i++) {
            if( $designs[$i]['dtype'] == 2 ) {
        echo "            TEX ".$designs[$i]['dvalue']."\n";
            }
        }
        echo "        END\n";
        echo "    END\n";
        echo "END\n";
        return "";
    }

    public function action_overlay() {

        
    }

    
}