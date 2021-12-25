<?php

/**
 * ギフトコントローラー
 *
 * @package  app
 * @extends  Controller
 */

class Controller_Api_City extends Controller_Apibase {
	public function before() {
        $this->auth = false;

        parent::before();
    }

    public function action_addobject() {
    	$post = Input::post();
        $get = Input::get();
        $post = array_merge($get, $post);

        $json = Input::json();

        $errors = array();

        $coords = $json['coords'];
        $ht = $json['ht'];
        $layer = $json['layer'];
        $wallid = $json['wallid'];
        $tname = $json['tname'];
        $designid = $json['designid'];
        if( $designid === "" ) {
            $designid = "0";
        }
        else if ( $designid + 0 > 0) {
            $wallid = "0";
        }
        $cstr = "";
        for($i=0;$i<count($coords);$i++) {
        	if ($i == 0) {
        		$cstr = $coords[$i]['x'] . " " . $coords[$i]['y'];
        	}
        	else {
        		$cstr = $cstr . "," . $coords[$i]['x'] . " " . $coords[$i]['y'];
        	}
        }

        if( ($coords[0]['x'] != $coords[count($coords)-1]['x'] ) || ($coords[0]['y'] != $coords[count($coords)-1]['y']) ) {
			$cstr = $cstr . "," . $coords[0]['x'] . " " . $coords[0]['y'];
		}

        $query = DB::select(db::expr('max(gid) max'));
        $query->from($layer);
        $result = $query->execute()->as_array();
        $mid = $result[0]['max'] + 0;

        $name = "BLDG_" . $mid;

        $floors = (int)($ht / 3.0);

        $query = DB::insert($layer);
        $query->set(array( 'tname' => $name , 
        	'floornum' => $floors , 
            'wallid' => $wallid,
            'tname' => $tname,
            'create_ts' => time(),
            'update_ts' => time(),
            'designid' => $designid,
        	'wkb_geometry' => db::expr("ST_GeomFromText('POLYGON(( ".$cstr." ))',4612)") ));
        $query->execute();

        $jresp = array();
        $jresp['result'] = "OK";
        $jresp['errors'] = $errors;
        // INSERT INTO tatemono_1 (tname,floornum,wkb_geometry) VALUES ('test1', 10,ST_GeomFromText('POLYGON((139.76 35.68,139.76 35.686,139.766 35.686,139.766 35.68,139.76 35.68))',4612));
        return Response::forge(json_encode($jresp) , 200);


    }

    public function action_getobject() {

        $post = Input::post();
        $get = Input::get();
        $post = array_merge($get, $post);

        $json = Input::json();

        $errors = array();

        $gid = $json['id'];
        $layer = $json['layer'];

        $query = DB::select('*' , db::expr("ST_AsGeoJSON(wkb_geometry) gjson"));
        $query->from($layer);
        $query -> where('gid' , $gid);
        $result = $query->execute()->as_array();

        $obj_arr = array();
        for($i=0;$i<count($result);$i++) {
            $geom = json_decode($result[$i]['gjson'], true);
            $coords = $geom['coordinates'];
            $coords = $coords[0]; // first part
            unset($result[$i]['gjson']);
            unset($result[$i]['wkb_geometry']);
            $coord_obj_arr = array();
            
            $coordstr = "";
            for($j=0;$j<count($coords);$j++) {
                $ptstr = "";
                for($k=0;$k<count($coords[$j]);$k++) {
                    if ( $k==0 ) {
                        $ptstr = $coords[$j][$k];
                    }
                    else {
                        $ptstr = $ptstr . "," . $coords[$j][$k];
                    }
                }

                if(( $j < (count($coords)-1) ) || ( $coords[0][0] != $coords[count($coords)-1][0] && $coords[0][1] != $coords[count($coords)-1][1]  )) {
                    if (count($coords[$j]) == 2) {
                        $coord_obj_arr[] = array("x" => $coords[$j][0], "y" => $coords[$j][1]);
                    }
                    else if (count($coords[$j]) >= 3){
                        $coord_obj_arr[] = array("x" => $coords[$j][0], "y" => $coords[$j][1] , "z" => $coords[$j][2]);
                    }
                }

                if ($coordstr === "") {
                    $coordstr = $ptstr;
                }
                else {
                    $coordstr = $coordstr . " " . $ptstr;
                }
            }
            $geomstr = $result[$i]['gid'] . ";1;" . ($result[$i]['floorht']*$result[$i]['floornum']) . ";" . $coordstr;
            // gid;numfl;flht;[x,y x,y ...]
            $result[$i]['geomstr'] = $geomstr;
            $result[$i]['geom'] = $coord_obj_arr;

            $obj_arr[] = $result[$i];
        }

        $jresp = array();
        $jresp["layer"] = $layer;
        $jresp["objects"] = $obj_arr;

        return Response::forge(json_encode($jresp) , 200);
    }

    function action_editgeom() {
        $post = Input::post();
        $get = Input::get();
        $post = array_merge($get, $post);

        $json = Input::json();
        $errors = array();

        $id = $json['id'];
        $coords = $json['coords'];
        $ht = $json['ht'];
        $layer = $json['layer'];
        $wallid = $json['wallid'];
        $tname = $json['tname'];
        $designid = $json['designid'];
        if( $designid === "" ) {
            $designid = "0";
        }
        else if ( $designid + 0 > 0) {
            $wallid = "0";
        }

        if( $wallid === "" ) {
            $wallid = "0";
        }

        $cstr = "";
        for($i=0;$i<count($coords);$i++) {
            if ($i == 0) {
                $cstr = $coords[$i]['x'] . " " . $coords[$i]['y'];
            }
            else {
                $cstr = $cstr . "," . $coords[$i]['x'] . " " . $coords[$i]['y'];
            }
        }
        if( ($coords[0]['x'] != $coords[count($coords)-1]['x'] ) || ($coords[0]['y'] != $coords[count($coords)-1]['y']) ) {
            $cstr = $cstr . "," . $coords[0]['x'] . " " . $coords[0]['y'];
        }

        $floors = (int)($ht / 3.0);

        $query = DB::update($layer);
        $query->set(array(
            'floornum' => $floors , 
            'wallid' => $wallid,
            'tname' => $tname,
            'update_ts' => time(),
            'designid' => $designid,
            'wkb_geometry' => db::expr("ST_GeomFromText('POLYGON(( ".$cstr." ))',4612)") ));
        $query -> where('gid' , $id);
        $query->execute();

        $jresp = array();
        $jresp['result'] = "OK";
        $jresp['errors'] = $errors;
        return Response::forge(json_encode($jresp) , 200);
    }

    public function action_deletegeom() {
        $post = Input::post();
        $get = Input::get();
        $post = array_merge($get, $post);

        $json = Input::json();
        $errors = array();

        $id = $json['id'];
        $layer = $json['layer'];

        $query = DB::select('*');
        $query->from($layer);
        $query -> where('gid' , $id);
        $result = $query->execute()->as_array();
        if( count( $result ) > 0 ) {
            // design?
            $designid = $result[0]["designid"]+0;
            if($designid > 0) {
                $query = db::delete('design_base');
                $query -> where('design_id' , $designid);
                $query -> execute();

                $query = db::delete('design_item');
                $query -> where('design_id' , $designid);
                $query -> execute();

                $dfolder = DOCROOT.'/assets/design/rc_' . $designid . '/';
                if( file_exists($dfolder) )
                {
                    File::delete_dir($dfolder);
                }
            }
        }

        $query = DB::delete($layer);
        $query -> where('gid' , $id);
        $query->execute();

        $jresp = array();
        $jresp['result'] = "OK";
        $jresp['errors'] = $errors;
        return Response::forge(json_encode($jresp) , 200);
    }

}