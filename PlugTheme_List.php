<?php
/*
Plugin Name: PlugTheme_List
Plugin URI:  http://link to your plugin homepage
Description: This plugin gives list of plugin and themes installed in your wordpress Site.Not only that it present you a 
             simple interface to look which plugins are activated and need update. This functionality goes with theme as well as.
Version:     1.0
Author:      Abhinav Kumar
License:     GPL2 etc
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2017 Abhinav Kumar (email : abby37kumar@outlook.com)
(PlugTheme_List) is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
(PlugTheme_List) is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with (PlugTheme_List). If not, see (http://link to your plugin license).
*/

/*
$reflector = new ReflectionClass('WP_List_Table');
die($reflector->getFileName());
echo $reflector->getStartLine();

*/

require_once 'class-abhi-list-table.php';

  if ( ! function_exists( 'get_plugins' ) ) {	
  require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

class Theme_List extends Abhi_List_Table {   
    function __construct(){
        global $status, $page;
        parent::__construct( array(
            'singular'  => 'theme',     //singular name of the listed records
            'plural'    => 'themes',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

function column_default($item,$column_name)
 {
    switch($column_name) {
        case 'Name':
        case 'ThemeURI':
        case 'Version':
        case 'Update':
        return $item[$column_name];
        default:
        return print_r($item,true);
    }
 }

         function get_columns() {
            $columns = array(
               'Name' => 'Theme Name',
               'Version' => 'Theme Version',
              // 'Url' => ' Theme Url',
               'Update' => 'Theme Update'
                  );
                return $columns;
         }

         function prepare_items(){
             $columns = $this->get_columns();
             $hidden = array();
             $sortable = array();
             $this->_column_headers = array($columns, $hidden, $sortable);
             //$this->items =$this->get_theme_list(); 
             $data = $this->get_theme_list();
             $per_page = 10;
             $current_page = $this->get_pagenum();
             $total_items = count($data);

              $this->set_pagination_args(array(
              	'total_items' =>$total_items,
              	'per_page' => $per_page,
              	'item' => 'Theme',
              	'items' => 'Themes'));

              	$data = array_slice($data,(($current_page -1)*$per_page),$per_page);
              	$this->items = $data;    
                 }

         function get_theme_list()
            {
              $ar = array();
              $all_themes = $this->theme_list();
              foreach ($all_themes as $theme)
              {
             $nw = array(
            'Name' => $theme['Name'],
            'Version' => $theme['Version'],
            'Update' => $theme['Update'],
            //'Url' => $theme['Url']
                   );
              array_push($ar,$nw);
              }                 
             return $ar; 
        }

        public  function theme_list() {
      	$arr_themes = array();
      	$all_themes = wp_get_themes();
        $update = $this->updatenote_theme();
      	if(isset($all_themes))
      	{
          $msg =" ";
      		foreach ($all_themes as $key => $theme) {  
                     if(in_array($key, $update))
                   { $msg ="Update Available";}
                  else 
                    $msg = " ";
                  $nw_arr = array();
                  $nw_arr['Name']=$theme['Name'];
                  $nw_arr['Version'] = $theme['Version'];
                  //$nw_arr['Url'] = $theme['url'];
                  $nw_arr['Update'] = $msg;
                  array_push($arr_themes, $nw_arr);
      		}	      		
      	}
      	return $arr_themes;
      }

       public function current_theme() {
        $theme = wp_get_theme();
        return $theme['Name'];
      }

           public function updatenote_theme( ) {
             $update_theme = array();
             $update_themes = get_site_transient( 'update_themes' );
             if ( ! empty($update_themes->response) ) {
             $themes_needupdate = $update_themes->response;
              foreach ( $themes_needupdate as $key => $value ) {
               array_push($update_theme,$key);
      }
    }

  //return $update_themes;
    return $update_theme;
  /*
  output of updata_themes
  stdClass Object ( [last_checked] => 1492627126 [checked] => Array ( [apex] => 1.17 [exclusive] => 1.0.35 [newspro] => 1.0.0 [twentyfifteen] => 1.7 [twentyseventeen] => 1.1 [twentysixteen] => 1.3 ) [response] => Array ( [apex] => Array ( [theme] => apex [new_version] => 1.23 [url] => https://wordpress.org/themes/apex/ [package] => http://downloads.wordpress.org/theme/apex.1.23.zip ) ) [translations] => Array ( ) )
  */
}

    }



class Plugin_List extends Abhi_List_Table {   
    function __construct(){
        global $status, $page;
        parent::__construct( array(
            'singular'  => 'plugin',     //singular name of the listed records
            'plural'    => 'plugins',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

function column_default($item,$column_name)
 {
    switch($column_name) {
        case 'Name':
        case 'PluginURI':
        case 'Version':
        case 'Activated':
        case 'Update':
        return $item[$column_name];
        default:
        return print_r($item,true);
    }
 }

        function get_columns() {
          $columns = array(
         'Name' => 'Plugin Name',
         'PluginURI' => 'Plugin URL',
         'Version' => 'Plugin Version',
         'Activated' => 'Plugin Activate',
         'Update' => 'Plugin Update'
           );
            return $columns;
        }

         function prepare_items() {
           $columns = $this->get_columns();
           $hidden = array();
           $sortable = array();
           $this->_column_headers = array($columns, $hidden, $sortable);
           //$this->items =$this->get_plugin_list();
           $data = $this->get_plugin_list();
             $per_page = 10;
             $current_page = $this->get_pagenum();
             $total_items = count($data);

              $this->set_pagination_args(array(
              	'total_items' =>$total_items,
              	'per_page' => $per_page,
              	 'item' => 'Plugin',
              	'items' => 'Plugins'));  

              	$data = array_slice($data,(($current_page -1)*$per_page),$per_page); 

              	$this->items = $data;     
            }

            function get_plugin_list()
            {
              $ar = array();
              $all_plugins = get_plugins();
              $active_plug = $this->list_activated_plugins();
              $upgrade_plug = $this->list_upgrade_plugin();
              $msg = "Activated";    
              $msg1 = " ";
           foreach ($all_plugins as $key => $value) {
             $active = in_array($key, $active_plug);
             $upgrade = in_array($key, $upgrade_plug);
             if($active)
              $msg = "Activated";
             else
              $msg = " ";
             if($upgrade)
              $msg1 = "Update Available";
             else
              $msg1 = "";
             $nw = array(
            'Name' => $value['Name'],
            'PluginURI' => $value['PluginURI'],
            'Version' => $value['Version'],
            'Activated' => $msg,
            'Update' => $msg1
                   );
              array_push($ar, $nw);
             }    
             return $ar; 

        }



function list_activated_plugins() {
    $plugins = get_option('active_plugins', array () );
    return $plugins;
}

function list_upgrade_plugin() {
  $update_plug = array();
  $update_plugins = get_site_transient( 'update_plugins' );
  //var_dump($update_plugins->response);
  if ( ! empty($update_plugins->response) ) {
    $plugins_needupdate = $update_plugins->response;
    foreach ( $plugins_needupdate as $key => $plugin )
       array_push($update_plug,$key);
  }
  return $update_plug;
}

}

 class MyPlugin{
  
      private $my_plugin_screen_name;
      private static $instance;
  
      static function GetInstance()
      {
          
          if (!isset(self::$instance))
          {
              self::$instance = new self();
          }
          return self::$instance;
      }
      

     public function plugin_top_menu()
      {
      	add_menu_page('PlugTheme List','PlugTheme','manage_options',__FILE__,array($this,'render_plugin_page'),'dashicons-screenoptions');
      	add_submenu_page(__FILE__,'Plugin List','Plugin','manage_options',__FILE__,array($this,'render_plugin_page'));
      	add_submenu_page(__FILE__,'Theme List','Theme','manage_options',__FILE__.'/custom',array($this,'render_theme_page'));
      //	remove_submenu_page(__FILE__,__FILE__);
      }

    public function render_page(){
  ?>
   <div class='wrap'>
    <h2></h2>
   </div>
  <?php
 }

 public function render_theme_page(){
  
          $themeTable = new Theme_List();
          $themeTable->prepare_items();
          $themeTable->display();
          ?>
          <script>
          var mtable = document.getElementsByClassName("wp-list-table widefat fixed striped themes");
          var wpbody = document.getElementById("the-list");
          var wprow = wpbody.getElementsByTagName("tr");
          var current = '<?php echo $themeTable->current_theme();?>';
          //console.log(current);
          for (var i =0; i < wprow.length; i++)
          {
          	var wpcell = wprow[i].cells;
          	var str = wpcell[0].innerHTML;
          	var pos = str.indexOf("<");
          	var name = str.substring(0,pos);
          	//console.log(name);
          	if (current.match(name))
          		{
          			wprow[i].setAttribute("style", "background-color: #A9A9A9;");
          			console.log(name);
          			break;
          		}
          }

          </script>

          <?php
          
          /*
          foreach ($res as $key => $values) {
            if($key == 'response')
            {
              print_r($values);
              //echo $value['theme'];
              //echo $value['new_version'];
              foreach ($values as $key1 => $value) {
                echo $value['theme'];
                echo $value['new_version'];
              }
            }
          }
          */

 }

 public function render_plugin_page(){
   
     $PTable = new Plugin_List();
     $PTable->prepare_items();
     $PTable->display();

   /*
   if ( !class_exists( 'Plugin_List' ) ) 
   {
    define( 'Abhi_CORE_PATH', trailingslashit( dirname( __FILE__ ) ) );
    // die(Abhi_CORE_PATH);
    require_once Abhi_CORE_PATH.'Plugin-List.php';
   }
   */
 }
      

      public function InitPlugin()
      {
       add_action('admin_menu',array($this,'plugin_top_menu'));
      }
  
 }

$MyPlugin = MyPlugin::GetInstance();
$MyPlugin->InitPlugin();