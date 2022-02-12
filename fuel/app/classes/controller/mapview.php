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
class Controller_Mapview extends Controller
{
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
        return Response::forge(View::forge('mapview/index' , $views));
    }

    public function action_vmap()
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
            return Response::forge(View::forge('mapview/vmap' , $views));
    }

    public function action_vlyrs()
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
            return Response::forge(View::forge('mapview/vlyrs', $views));
    }

    public function action_404()
    {
        return Response::forge(Presenter::forge('welcome/404'), 404);
    }

}