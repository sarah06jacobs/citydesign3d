<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.8.2
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2019 Fuel Development Team
 * @link       https://fuelphp.com
 */

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Hawkeye extends Controller
{
	/**
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
        $get = Input::get();

        $views = array();
        $views['wx'] = isset($get["wx"]) ? $get["wx"] : Config::get('top_lon');
        $views['wy'] = isset($get["wy"]) ? $get["wy"] : Config::get('top_lat');
        $views['cx'] = isset($get["cx"]) ? $get["cx"] : Config::get('top_clon');
        $views['cy'] = isset($get["cy"]) ? $get["cy"] : Config::get('top_clat');
        $views['alt'] = isset($get["alt"]) ? $get["alt"] : Config::get('top_alt');
        $views['pitch'] = isset($get["pitch"]) ? $get["pitch"] : Config::get('top_pitch');
        $views['dir'] = isset($get["dir"]) ? $get["dir"] : Config::get('top_dir');

        $views['out'] = isset($get["out"]) ? $get["out"] : Config::get('top_out');
        return Response::forge(View::forge('app/index' , $views));
	}

	public function action_map()
	{
            $get = Input::get();
            $views = array();
            $views['host'] = $_SERVER['SERVER_ADDR'];
            $views['wx'] = isset($get["wx"]) ? $get["wx"] : Config::get('top_lon');
            $views['wy'] = isset($get["wy"]) ? $get["wy"] : Config::get('top_lat');
            $views['cx'] = isset($get["cx"]) ? $get["cx"] : Config::get('top_clon');
            $views['cy'] = isset($get["cy"]) ? $get["cy"] : Config::get('top_clat');
            $views['alt'] = isset($get["alt"]) ? $get["alt"] : Config::get('top_alt');
            $views['pitch'] = isset($get["pitch"]) ? $get["pitch"] : Config::get('top_pitch');
            $views['dir'] = isset($get["dir"]) ? $get["dir"] : Config::get('top_dir');

            $views['out'] = isset($get["out"]) ? $get["out"] : Config::get('top_out');
            return Response::forge(View::forge('app/map' , $views));
	}

	public function action_lyrs()
	{
            $get = Input::get();
            $views = array();
            
            $query = DB::select('*');
	        $query->from('places');
	        $query->order_by('create_ts','desc');
	        $places = $query->execute()->as_array();
	        $views['places'] = $places;
            
            $query = DB::select('*');
            $query -> from('addr_pref');
            $query -> order_by('pref_code' , 'asc');
            $prefecture = $query->execute()->as_array();
            
            $views["prefecture"] = $prefecture;
            $views["wallcount"] = Config::get('wallcount') + 0;
            $views['host'] = $_SERVER['SERVER_ADDR'];
            return Response::forge(View::forge('app/lyrs', $views));
	}

	public function action_placescsv()
	{
		$get = Input::get();
        $views = array();

        $searchplacename = isset($post["searchplacename"]) ? $post["searchplacename"] : "";
        $query = DB::select('*');
        $query->from('places');
        //if( $searchplacename !== "" ) {
        //	$query->where('pname' , 'like' , "%$searchplacename%");
        //}
        $query->order_by('create_ts','desc');
        $places = $query->execute()->as_array();

        $views['filename'] = "places-" . date("Y-m-d") . ".csv";

        for($i=0;$i<count($places);$i++) {
        	$places[$i]['url'] = 'http://' . $_SERVER['SERVER_ADDR'] . '/hawkeye/' . $places[$i]['url'];
        	$places[$i]['url'] = str_replace("&amp;","&",$places[$i]['url']);
        }

        $views['list'] = $places;

        return Response::forge(View::forge('app/csvfile', $views));
	}
        
    public function action_design()
	{
        $post = Input::post();
        $get = Input::get();
        $post = array_merge($get, $post);
        $design_id = isset( $post["design_id"] ) ? ($post["design_id"] + 0) : -1;
        $action = isset( $post["action"] ) ? $post["action"] : "";
        $layer = isset( $post["layer"] ) ? $post["layer"] : "tatemono_2";
        $dname = "";
        $isnew = false;
        $result = "";

        $walls = array();

        if( $design_id + 0 == 0 ) {
        	$design_id = -1;
        }
        
        $webfolder = '/assets/design/rc_' . $design_id . '/';
        $default_wall = $this->getWallInfo("" , $webfolder , 'wall');
        $roof = $this->getWallInfo("" , $webfolder , 'roof');

        if ( $action == "save" ) {

        	$submitv = $post['submit'];

        	if( $submitv === "クリア" ) {
        		$query = db::delete('design_base');
				$query -> where('design_id' , $design_id);
				$query -> execute();

				$query = db::delete('design_item');
				$query -> where('design_id' , $design_id);
				$query -> execute();

				$query = DB::update($layer);
		        $query->set(array(
		            'designid' => "-1" )
		    			);
		        $query -> where('designid' , $design_id);
		        $query->execute();

				$dfolder = DOCROOT.'/assets/design/rc_' . $design_id . '/';
                if( file_exists($dfolder) )
                {
                    File::delete_dir($dfolder);
                }
                $result = "remove";
        	}
        	else {
	        	$config = array(
				    'path' => APPPATH.'data',
				    'ext_whitelist' => array('png','jpg','jpeg','gif')
				);
				Upload::process($config);
				Upload::save();
				// clear prev
				
	        	$dname = $post['dname'];
	            if ( $design_id == -1 ) {
	                // add to db
	                $query = DB::insert('design_base');
	                $query->set(array("dname" => $dname , "create_ts" => time()));
	                $design_obj = $query -> execute();
	                $design_id = $design_obj[0];
	                $isnew = true;
	            }
	            
	            $dfolder = DOCROOT.'/assets/design/rc_' . $design_id . '/';
	            $webfolder = '/assets/design/rc_' . $design_id . '/';
	            if( !file_exists($dfolder) )
	            {
	            	mkdir($dfolder , 0777, true);
	            }
	            // save files to fol
	            
	            $query = db::delete('design_item');
				$query -> where('design_id' , $design_id);
				$query -> execute();

	            $wtex_ix = 0;
	            while( 0==0 ) {
	                if(!isset( $post["wallw" .$wtex_ix] )) {
	                    break;
	                }

	                $wallw = $post["wallw" .$wtex_ix];
	                $wallh = $post["wallh" .$wtex_ix];
	                $file = Upload::get_files('walltex'.$wtex_ix);
	                if( count($file) > 0 ) {
	                    $filepath = APPPATH.'data/' . $file['name'];
	                    $info = getimagesize($filepath);
	                    if ($info === FALSE ) {
	                        unlink($filepath);
	                    } else {
	                        if( file_exists($dfolder . 'wall' . $wtex_ix . ".png") )
	                        {
	                            unlink($dfolder . 'wall' . $wtex_ix . ".png");
	                        }
	                        if( ($info[2] !== IMAGETYPE_PNG) ) {
	                    		$binary = imagecreatefromstring(file_get_contents($filepath));
								ImagePNG($binary, $dfolder . 'wall' . $wtex_ix . ".png", 0);
	                    	}
	                    	else {
	                        	File::copy( $filepath , $dfolder . 'wall' . $wtex_ix . ".png");
	                    	}

		                    // add to item
		                    $dstr = $wtex_ix . ":0";
		                    if ( $wallw !== "" && $wallh !== "" )
		                    {
		                    	$dstr = $dstr . ":" . $wallw . ":" . $wallh;
		                    }
		                    $query = DB::insert('design_item');
		                    $query->set(array("design_id" => $design_id , "dtype" => "0", "dvalue" => $dstr , "idx" => $wtex_ix));
		                    $query -> execute();

	                	}
	            	}
	            	else if ($isnew == false){
	            		// no file
	            		$wallidx = $post["wallidx" .$wtex_ix];
	            		if( $wallidx !== "" ) {
	            			// check for image
	            			if( file_exists($dfolder . 'wall' . $wtex_ix . ".png") )
	                        {
			            		$query = DB::insert('design_item');
			            		$dstr = $wtex_ix . ":0";
			                    if ( $wallw !== "" && $wallh !== "" )
			                    {
			                    	$dstr = $dstr . ":" . $wallw . ":" . $wallh;
			                    }
			                    $query->set(array("design_id" => $design_id , "dtype" => "0", "dvalue" => $dstr , "idx" => $wtex_ix));
			                    $query -> execute();
		                	}
	                	}

	                	if (isset($post["wallrem" . $wtex_ix]) ) {
	                		if( file_exists($dfolder . 'wall' . $wtex_ix . ".png") )
	                        {
	                            unlink($dfolder . 'wall' . $wtex_ix . ".png");
	                        }
	                        $query = DB::delete('design_item');
		                    $query->where("design_id" , $design_id);
		                    $query->where("dtype","0");
		                    $query->where("idx",$wtex_ix);
		                    $query -> execute();
	                	}
	            	}
	            	$wtex_ix = $wtex_ix + 1;
	            }
	                
	            $rooffile = Upload::get_files('rooftex');
	            
	        	$roofw = $post["roofw"];
	            $roofh = $post["roofh"];
	            $roofr = $post["roofh"];

	            if( count($rooffile) > 0 ) {
	                $filepath = APPPATH.'data/' . $rooffile['name'];
	                $info = getimagesize($filepath);
	                if ($info === FALSE) {
	                    unlink($filepath);
	                } else {
	                	if( file_exists($dfolder . "roof0.png") )
	                    {
	                        unlink($dfolder . "roof0.png");
	                    }
	                    if( ($info[2] !== IMAGETYPE_PNG) ) {
                    		$binary = imagecreatefromstring(file_get_contents($filepath));
							ImagePNG($binary, $dfolder . "roof0.png", 0);
                    	}
                    	else {
	                    	File::copy( $filepath , $dfolder . "roof0.png");
	                	}


	                    // add to item
	                    $dstr = "0:0";
	                    if ( $roofw !== "" && $roofh !== "" )
	                    {
	                    	$dstr = $dstr . ":" . $roofw . ":" . $roofh;
	                    }
	                    $query = DB::insert('design_item');
	                    $query->set(array("design_id" => $design_id , "dtype" => "2", "dvalue" => $dstr , "idx" => "0"));
	                    $query -> execute();
	                    //unlink($filepath);
	                }
	            }
	            else if ($isnew == false){
					// no file
	        		$roofidx = $post["roofidx"];
	        		if( $roofidx !== "" ) {
	        			if( file_exists($dfolder . "roof0.png") )
	                    {
		            		$query = DB::insert('design_item');
		            		$dstr = "0:0";
		                    if ( $roofw !== "" && $roofh !== "" )
		                    {
		                    	$dstr = $dstr . ":" . $roofw . ":" . $roofh;
		                    }
		                    $query = DB::insert('design_item');
		                    $query->set(array("design_id" => $design_id , "dtype" => "2", "dvalue" => $dstr , "idx" => "0"));
		                    $query -> execute();
	                	}
	            	}

	            	if (isset($post["roofrem"]) ) {
                		if( file_exists($dfolder . "roof0.png") )
	                    {
	                        unlink($dfolder . "roof0.png");
	                    }
                        $query = DB::delete('design_item');
	                    $query->where("design_id" , $design_id);
	                    $query->where("dtype","2");
	                    $query->where("idx","0");
	                    $query -> execute();
                	}
	            }
	            // now delete all temp files
	            foreach(Upload::get_files() as $file)
				{
					$filepath = APPPATH.'data/' . $file['name'];
					if( file_exists($filepath) )
	            	{
						unlink($filepath);
					}	
				}
	            $result = "complete";
            }
            
        }
        
        if($design_id >= 0) {
            $query = DB::select('*');
            $query -> from('design_base');
            $query -> where('design_id' , $design_id);
            $dresult = $query->execute()->as_array();
            if( count( $dresult ) > 0 ) {
                $dname = $dresult[0]['dname'];
                
                $query = DB::select('*');
                $query -> from('design_item');
                $query -> where('design_id' , $design_id);
                $query -> order_by('idx' , 'asc');
                $ditems = $query->execute()->as_array();
                // need to add up to n walls
                $lastwall = 0;
                if( count($ditems) > 0 ) {
                	$lastwall = $ditems[count($ditems)-1]["idx"] + 0;
                }
                for( $u=1; $u < $lastwall; $u++ ) {
                	$walls[] = array("img" => "" , "w" => "", "h" => "", "r" => "", "idx" => $u);
                }

                for( $u=0; $u<count($ditems); $u++ ) {
                    $di = $ditems[$u];
                    $dtype = $di['dtype'] + 0;
                    if( $dtype == 0 ) { // wall tex
                        if( $di['idx']+0 == 0 ) {
                            $default_wall = $this->getWallInfo($di['dvalue'] , $webfolder , 'wall');
                        }
                        else {
                        	$walls[$di['idx']-1] = $this->getWallInfo($di['dvalue'] , $webfolder , 'wall');
                            //$walls[] = $this->getWallInfo($di['dvalue'] , $webfolder , 'wall');
                        }
                    }
                    else if( $dtype == 2 ) {
                        $roof = $this->getWallInfo($di['dvalue'] , $webfolder , 'roof');
                    }
                }
            }
        }

        
        $views = array();
        $views["design_id"] = $design_id;
        $views["dname"] = $dname;
        $views['result'] = $result;
        $views['layer'] = $layer;

        $views["default_wall"] = $default_wall;
        $views["walls"] = $walls;
        $views["roof"] = $roof;
        return Response::forge(View::forge('app/design', $views));
    }

    public function action_vrmlup()
	{
        $post = Input::post();
        $get = Input::get();
        $post = array_merge($get, $post);
        $result = "";
        $layer = isset( $post["layer"] ) ? $post["layer"] : "tatemono_v";
        $action = isset( $post["action"] ) ? $post["action"] : "";
        $vrml_id = isset( $post["vrmlid"] ) ? $post["vrmlid"]+0 : -1;
        $skip = isset( $post["skip"] ) ? $post["skip"]+0 : 0;
        $wallct = isset( $post["wallct"] ) ? $post["wallct"]+0 : 0;
        $vfname = "";
        $points = "";
        $cdate = "";
        $tfm = "";
        $tname = "upload0";

        if ($action === "upload") {
        	$dfolder = DOCROOT.'/maps/shape/vrml/';
        	$config = array(
			    'path' => APPPATH.'data',
			    'ext_whitelist' => array('png','jpg','jpeg','gif','wrl','xml', 'csv')
			);
			Upload::process($config);
			Upload::save();

			$tengunfile = Upload::get_files('tengunfile');
			$vrmlfile = Upload::get_files('vrmlfile');
			$csvfile = Upload::get_files('csvfile');

            if( count($vrmlfile) > 0 || count($tengunfile) > 0 || count($csvfile) > 0 ) {
                if( $vrml_id >= 0 ) {
                	$query = DB::select('*' , db::expr("ST_AsGeoJSON(wkb_geometry) gjson"));
		            $query -> from($layer);
		            $query -> where('gid' , $vrml_id);
		            $result = $query->execute()->as_array();
		            if( count($result) > 0 ) {
		            	
		            	$geom = json_decode($result[0]['gjson'], true);
			            $type = $geom['type'];
			            $coords = $geom['coordinates'];
			            $points = $coords[0] . " " . $coords[1];
			            $tfm = $result[0]["tfm"];
			            $cdate = $result[0]["create_date"];
			            $tname = $result[0]["tname"];
		            }
		            else {
		            	$vrml_id = -1;
		            }
                }

                if( $vrml_id == -1 ) {
	              	$cdate = date('Y-m-d H:i');
	                $query = DB::insert($layer);
		            $query -> set(array( 'tname' => 'upload' ,
			            'create_date' => $cdate,
			            'create_ts' => strtotime($cdate),
			            'update_ts' => time(),
			            'imgcount' => $wallct,
			        	'wkb_geometry' => db::expr("ST_GeomFromText('POINT(100.0 5.0)',4612)") ));
		            $vrmlobj = $query->execute();
		            $vrml_id = $vrmlobj[0];
	        	}
	        	else {
	        		$query = DB::update($layer);
		            $query -> set(array( 
			            'update_ts' => time(),
			            'imgcount' => $wallct ) );
		            $vrmlobj = $query->execute();
	        	}

	            $vfname = "obj_" . $vrml_id . ".wrl";

                if( file_exists($dfolder . $vfname) )
                {
                    unlink($dfolder . $vfname);
                }
                if( file_exists($dfolder . $vfname . ".blob") )
                {
                    unlink($dfolder . $vfname . ".blob");
                }

                if( count($tengunfile) > 0 ) {
					// convert to vrml
					$this -> convertxmltovrml($tengunfile , $dfolder . $vfname, $skip);
				}
				else if ( count($csvfile) > 0 ) {
					$this -> convertcsvtovrml($csvfile , $dfolder . $vfname, $skip);
				}
				else if ( count($vrmlfile) > 0 ) {

					// attachments
					// upload images
					$replaceimg = array();
		            for( $q=1;$q<=$wallct;$q++  ) {
		                $file = Upload::get_files('vtex'.$q);
		                if( count($file) > 0 ) {
		                	$fname = $file['name'];

		                	//$destname = substr($fname, 0, strrpos($fname, ".")) . ".png";
		                	$destname = "obj_" . $vrml_id . "_img_" . $q . ".png";

		                    $filepath = APPPATH.'data/' . $fname;
		                    $info = getimagesize($filepath);
		                    if ($info === FALSE ) {
		                        unlink($filepath);
		                    } else {
		                        if( file_exists($dfolder . $destname) )
		                        {
		                            unlink($dfolder . $destname);
		                        }
		                        if( ($info[2] !== IMAGETYPE_PNG) ) {
		                    		$binary = imagecreatefromstring(file_get_contents($filepath));
									ImagePNG($binary, $dfolder . $destname, 0);
		                    	}
		                    	else {
		                        	File::copy( $filepath , $dfolder . $destname);
		                    	}
		                    	$replaceimg[] = array("orig" => $fname , "new" => $destname);
		                    }
		                    // delete temp
		                    unlink($filepath);
		                }
		            }

		            $filepath = APPPATH.'data/' . $vrmlfile['name'];

		            $fconts = file_get_contents($filepath);
		            if( count($replaceimg) > 0 ) {
		            	for($ll=0; $ll<count($replaceimg); $ll++) {
		            		$fconts = str_ireplace( $replaceimg[$ll]['orig'] , $replaceimg[$ll]['new'] , $fconts  );
		            	}
		            }
		            file_put_contents($dfolder . $vfname, $fconts);
		            // delete temp
		            unlink($filepath);
				}

                $query = DB::update($layer);
                $query -> set(array( 'wrl' => $vfname));
                $query -> where('gid' , $vrml_id);
                $query->execute();
            	$result = "complete";
            }
        }

        $views = array();
        $views['result'] = $result;
        $views['vrml_id'] = $vrml_id;
        $views['vfname'] = $vfname;
        $views['layer'] = $layer;

        $views['tname'] = $tname;
        $views['cdate'] = $cdate;
        $views['points'] = $points;
        $views['tfm'] = $tfm;
        return Response::forge(View::forge('app/vrmlup', $views));
    }

    public function getWallInfo($str , $dfolder, $imgname) {
    	$arr = explode(":" , $str);
    	if (count($arr) < 2) {
    		return array("img" => "" , "w" => "", "h" => "", "r" => "", "idx" => "");
    	}
    	$imw = "";
    	$imh = "";
    	$rot = "";
    	$img = $dfolder . $imgname . $arr[0] . ".png";
    	if (count($arr) >= 4) {
    		$imw = $arr[2];
			$imh = $arr[3];
    	}
    	if (count($arr) > 4) {
    		$rot = $arr[4];
    	}

    	$info = array("img" => $img , "w" => $imw, "h" => $imh,  "r" => $rot,"idx" => $arr[0]);
    	return $info;
    }

    public function action_overlay() {
    	$get = Input::get();
    	$views = array();
    	header("Content-type: text/plain");
    	$views['shaperoot'] = str_replace("\\","/",DOCROOT) . "maps/shape/";
    	$views['wallcount'] = Config::get('wallcount'); 
    	$views['walltexw'] = Config::get('walltexw');
    	return Response::forge(View::forge('app/overlay', $views));
    }
	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		return Response::forge(Presenter::forge('welcome/404'), 404);
	}

	public function convertcsvtovrml($csvfile, $vfname, $skip=0) {
		$filepath = APPPATH.'data/' . $csvfile['name'];
		$fhandle = fopen($filepath , 'r');

		if( file_exists($vfname) )
        {
            unlink($vfname);
        }

		$fp = fopen($vfname, 'w');
		fwrite($fp, '#VRML V2.0 utf8' . PHP_EOL);
		fwrite($fp, 'Shape {' . PHP_EOL);

		fwrite($fp, '  geometry PointSet {' . PHP_EOL);
		fwrite($fp, '    coord Coordinate {' . PHP_EOL);
		fwrite($fp, '    point [' . PHP_EOL);
		$pinx = 0;
		while(!feof($fhandle)) {
			$dat = fgetcsv($fhandle);
			// x,y,z,r,g,b
			if( $dat && count($dat) >= 3 ) {
				$x = $dat[0];
				$y = $dat[1];
				$z = $dat[2];

				if( ($skip == 0) || ($pinx % $skip ) == 0 ) {
					$point = $x . " " . $y . " " . $z;
					fwrite($fp,$point . "," . PHP_EOL);
				}
				$pinx = $pinx + 1;
			}	
		}

		fwrite($fp, '    ]' . PHP_EOL); // close point
		fwrite($fp, '    }' . PHP_EOL); // close Coordinate

		rewind($fhandle);
		fwrite($fp, '    color Color {' . PHP_EOL);
		fwrite($fp, '    color [' . PHP_EOL);

		while(!feof($fhandle)) {
			$dat = fgetcsv($fhandle);
			if( $dat && count($dat) >= 6 ) {
				// x,y,z,r,g,b
				$r = $dat[3];
				$g = $dat[4];
				$b = $dat[5];

				if( ($skip == 0) || ($pinx % $skip ) == 0 ) {
					$point = round(($r/255.0),2) . " " . round(($g/255.0),2) . " " . round(($b/255.0),2);
					fwrite($fp,$point . "," . PHP_EOL);
				}
				$pinx = $pinx + 1;
			}
		}
		fwrite($fp, '    ]' . PHP_EOL); // close color
		fwrite($fp, '    }' . PHP_EOL); // close Color

		fwrite($fp, '  }' . PHP_EOL); // close pointset
	
		fwrite($fp, '}' . PHP_EOL); // close shape
		fclose($fp);
		fclose($fhandle);

		unlink($filepath);
	}

	public function convertxmltovrml($tengunfile, $vfname, $skip=0) {
		$filepath = APPPATH.'data/' . $tengunfile['name'];
		$xml = simplexml_load_file($filepath);

		if( file_exists($vfname) )
        {
            unlink($vfname);
        }
		
		$fp = fopen($vfname, 'w');
		fwrite($fp, '#VRML V2.0 utf8' . PHP_EOL);
		fwrite($fp, 'Shape {' . PHP_EOL);

		foreach ( $xml->CgPoints as $pset ) {
			fwrite($fp, '  geometry PointSet {' . PHP_EOL);
			fwrite($fp, '    coord Coordinate {' . PHP_EOL);
			fwrite($fp, '    point [' . PHP_EOL);
			$pinx = 0;
			foreach ( $pset->CgPoint as $point ) {
				if( ($skip == 0) || ($pinx % $skip ) == 0 ) {
					fwrite($fp,$point . "," . PHP_EOL);
				}
				$pinx = $pinx + 1;
			}
			fwrite($fp, '    ]' . PHP_EOL); // close point
			fwrite($fp, '    }' . PHP_EOL); // close Coordinate

			//fwrite($fp, '    color Color {' . PHP_EOL);
			//fwrite($fp, '    color [' . PHP_EOL);
			//foreach ( $pset->CgPoint as $point ) {
			//	fwrite($fp,"1.0 0.0 0.0," . PHP_EOL);
			//}
			//fwrite($fp, '    ]' . PHP_EOL); // close color
			//fwrite($fp, '    }' . PHP_EOL); // close Color

			fwrite($fp, '  }' . PHP_EOL); // close pointset
		}
		fwrite($fp, '}' . PHP_EOL); // close shape
		fclose($fp);

		unlink($filepath);
	}
}
