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
            return Response::forge(View::forge('app/lyrs'));
	}
        
        public function action_design()
	{
            $post = Input::post();
            $get = Input::get();
            $post = array_merge($get, $post);
            $design_id = isset( $post["design_id"] ) ? ($post["design_id"] + 0) : -1;
            $action = isset( $post["action"] ) ? $post["action"] : "";
            
            $dname = "";
            $default_wall = "";
            $roof = "";

            $walls = array();
            
            if ( $action == "save" ) {
            	$config = array(
				    'path' => APPPATH.'data',
				    'ext_whitelist' => array('png')
				);
				Upload::process($config);
				Upload::save();

            	$dname = $post['dname'];
                if ( $design_id == -1 ) {
                    // add to db
                    $query = DB::insert('design_base');
                    $query->set(array("dname" => $dname , "create_ts" => time()));
                    $design_obj = $query -> execute();
                    $design_id = $design_obj[0];
                }
                
                $dfolder = DOCROOT.'/assets/design/rc_' . $design_id . '/';
                mkdir($dfolder , 0777, true);
                // save files to fol
                
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
	                    if ($info === FALSE || ($info[2] !== IMAGETYPE_PNG)) {
	                        unlink($filepath);
	                    } else {
	                        if( file_exists($dfolder . 'wall' . $wtex_ix . ".png") )
	                        {
	                            unlink($dfolder . 'wall' . $wtex_ix . ".png");
	                        }
	                        File::rename( $filepath , $dfolder . 'wall' . $wtex_ix . ".png");
	                    
		                    $wtex_ix = $wtex_ix + 1;
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
                }
                    
                $rooffile = Upload::get_files('rooftex');
                if( count($rooffile) > 0 ) {
                	$roofw = $post["roofw"];
	                $roofh = $post["roofh"];
	                $roofr = $post["roofh"];
	                $filepath = APPPATH.'data/' . $rooffile['name'];
	                $info = getimagesize($filepath);
	                if ($info === FALSE || ($info[2] !== IMAGETYPE_PNG)) {
	                    unlink($filepath);
	                } else {
	                	if( file_exists($dfolder . "roof.png") )
	                    {
	                        unlink($dfolder . "roof.png");
	                    }
	                    File::rename( $filepath , $dfolder . "roof.png");
	                
	                    $wtex_ix = $wtex_ix + 1;
	                    // add to item
	                    $dstr = $wtex_ix . ":0";
	                    if ( $wallw !== "" && $wallh !== "" )
	                    {
	                    	$dstr = $dstr . ":" . $wallw . ":" . $wallh;
	                    }
	                    $query = DB::insert('design_item');
	                    $query->set(array("design_id" => $design_id , "dtype" => "2", "dvalue" => $dstr , "idx" => $wtex_ix));
	                    $query -> execute();
	                }
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
                    
                    for( $u=0; $u<count($ditems); $u++ ) {
                        $di = $ditems[$u];
                        $dtype = $di['dtype'] + 0;
                        if( $dtype == 0 ) { // wall tex
                            if( $di['idx']+0 == 0 ) {
                                $default_wall = $di['dvalue'];
                            }
                            else {
                                $walls[] = $di['dvalue'];
                            }
                        }
                        else if( $dtype == 2 ) {
                            $roof = $di['dvalue'];
                        }
                    }
                    
                }
            }
            
            $views = array();
            $views["design_id"] = $design_id;
            $views["dname"] = $dname;
            
            $views["default_wall"] = $default_wall;
            $views["walls"] = $walls;
            $views["roof"] = $roof;
            return Response::forge(View::forge('app/design', $views));
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
