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
            return Response::forge(View::forge('app/index'));
	}

	public function action_map()
	{
            $get = Input::get();
            $views = array();
            $views['host'] = $_SERVER['SERVER_ADDR'];
            return Response::forge(View::forge('app/map' , $views));
	}

	public function action_lyrs()
	{
            $get = Input::get();
            $views = array();
            
            
            $query = DB::select('*');
            $query -> from('addr_pref');
            $query -> order_by('pref_code' , 'asc');
            $prefecture = $query->execute()->as_array();
            
            $views["prefecture"] = $prefecture;
            $views["wallcount"] = Config::get('wallcount') + 0;
            return Response::forge(View::forge('app/lyrs', $views));
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
        $vrml_id = isset( $post["vrmlid"] ) ? $post["vrmlid"] : "-1";
        $vfname = "";
        $points = "";
        $cdate = "";
        $tname = "upload0";

        if ($action === "upload") {
        	$config = array(
			    'path' => APPPATH.'data',
			    'ext_whitelist' => array('png','jpg','jpeg','gif','wrl')
			);
			Upload::process($config);
			Upload::save();

			$vrmlfile = Upload::get_files('vrmlfile');
	            
            if( count($vrmlfile) > 0 ) {
                $filepath = APPPATH.'data/' . $vrmlfile['name'];
                $dfolder = DOCROOT.'/maps/shape/vrml/';

              	$cdate = date('Y-m-d H:i');
                $query = DB::insert($layer);
	            $query -> set(array( 'tname' => 'upload' ,
		            'create_date' => $cdate,
		            'create_ts' => strtotime($cdate),
		            'update_ts' => time(),
		        	'wkb_geometry' => db::expr("ST_GeomFromText('POINT(100.0 5.0)',4612)") ));
	            $vrmlobj = $query->execute();
	            $vrml_id = $vrmlobj[0];

	            $vfname = "obj_" . $vrml_id . ".wrl";

            	if( file_exists($dfolder . $vfname) )
                {
                    unlink($dfolder . $vfname);
                }
                File::copy( $filepath , $dfolder . $vfname);

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
}
