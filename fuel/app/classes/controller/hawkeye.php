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
            return Response::forge(View::forge('app/map'));
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
            $design_id = isset( $post["design_id"] ) ? ($post["design_id"] + 0) : "1";
            $action = isset( $post["action"] ) ? $post["action"] : "";
            
            $dname = "";
            $default_wall = "";
            $roof = "";
            
            if ( $action == "save" ) {
                if ( $design_id == -1 ) {
                    // add to db
                    $query = DB::insert('design_base');
                    $query->set(array("dname" => ''));
                    $design_id = $query -> execute();
                    
                }
                
                $dfolder = DOCROOT.'/assets/design/rc_' . $design_id . '/';
                // save files to fol
                
                $wtex_ix = 0;
                
                while( 0==0 ) {
                    if(!isset( $post["wallw" .$wtex_ix] )) {
                        break;
                    }
                    $wallw = $post["wallw" .$wtex_ix];
                    $wallh = $post["wallh" .$wtex_ix];
                    $file = $_FILES['walltex'.$wtex_ix]['name'];
                    echo "file : $file <br>";
                    /*
                    $filepath = APPPATH.'data/images/' . $file;

                    $info = getimagesize($filepath);
                    if ($info === FALSE || ($info[2] !== IMAGETYPE_PNG)) {
                        unlink($filepath);
                    } else {
                        if( file_exists($dfolder . 'wall' . $wtex_ix . ".png") )
                        {
                            unlink($dfolder . 'wall' . $wtex_ix . ".png");
                        }
                        File::rename( $filepath , $dfolder . 'wall' . $wtex_ix . ".png");
                    }
                     */
                    $wtex_ix = $wtex_ix + 1;
                }
                    
                $rooffiles =  $_FILES['rooftex']['name'];
                echo "roof : $rooffiles <br>";
            }
            
            if($design_id >= 0) {
                $query = DB::select('*');
                $query -> from('design_base');
                $query -> where('design_id' , $design_id);
                $dresult = $query->execute()->as_array();
                if( count( $dresult ) > 0 ) {
                    $dname = $dresult[0]['dname'];
                    
                    $query = DB::select('*');
                    $query -> from('design_items');
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
            
            return Response::forge(View::forge('app/design') , $views);
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
