<?php
/*
* Plugin Name: Simple Theme Demo Importer
* Plugin URI: https://wordpress.org/plugins/simple-theme-demo-importer
* Description: Simple Theme Demo Importer.
* Version: 1.1.0
* Author: Tristup Ghosh
* Author URI: http://www.tristupghosh.com
* Text Domain: simple_theme_demo_importer
*/
if ( ! defined( 'ABSPATH' ) ) 
{
	exit;
}
if ( ! defined( 'SIMPLE_THEME_DEMO_IMPORT_PLUGIN_VERSION' ) ) 
{
	define( 'SIMPLE_THEME_DEMO_IMPORT_PLUGIN_VERSION', '1.0.4' );
}
if ( ! defined( 'SIMPLE_THEME_DEMO_IMPORT_PLUGIN' ) ) 
{
	define('SIMPLE_THEME_DEMO_IMPORT_PLUGIN', plugin_dir_path( __FILE__ ));
}
if ( ! defined( 'SIMPLE_THEME_DEMO_IMPORT_PLUGIN_URL' ) ) 
{
	define('SIMPLE_THEME_DEMO_IMPORT_PLUGIN_URL', plugin_dir_url( __FILE__ ));
}
if ( ! defined( 'SIMPLE_THEME_DEMO_IMPORT_PLUGIN_CSS_URI' ) ) {
		define( 'SIMPLE_THEME_DEMO_IMPORT_PLUGIN_CSS_URI', plugins_url( 'assets/css/',__FILE__ ) );
}
if ( ! defined( 'SIMPLE_THEME_DEMO_IMPORT_PLUGIN_JS_URI' ) ) {
		define( 'SIMPLE_THEME_DEMO_IMPORT_PLUGIN_JS_URI', plugins_url( 'assets/js/',__FILE__ ) );
}
if ( ! defined( 'SIMPLE_THEME_DEMO_IMPORT_PLUGIN_TEMPLATE_DIR' ) ) {
		define( 'SIMPLE_THEME_DEMO_IMPORT_PLUGIN_TEMPLATE_DIR', plugin_dir_path( __FILE__ ) . 'templates/' );
}
if ( ! defined( 'SIMPLE_THEME_DEMO_IMPORT_PREMIUM_INFO_URL' ) ) {
		define( 'SIMPLE_THEME_DEMO_IMPORT_PREMIUM_INFO_URL', 'http://localhost/wporgtesting/' );
}
if ( ! defined( 'SIMPLE_THEME_DEMO_IMPORT_PREMIUM_LINK' ) ) {
		define( 'SIMPLE_THEME_DEMO_IMPORT_PREMIUM_LINK', 'http://tristupghosh.com/get-pro' );
}
if ( ! defined( 'SIMPLE_THEME_DEMO_IMPORT_SUPPORT_LINK' ) ) {
		define( 'SIMPLE_THEME_DEMO_IMPORT_SUPPORT_LINK', 'http://tristupghosh.com/get-support' );
}
if ( ! defined( 'SIMPLE_THEME_DEMO_IMPORT_RATE_LINK' ) ) {
		define( 'SIMPLE_THEME_DEMO_IMPORT_RATE_LINK', 'https://wordpress.org/support/plugin/simple-theme-demo-importer/reviews/#new-post' );
}
class simple_theme_demo_importer
{
	function __construct()
	{
		$this->simple_theme_demo_importer_config();
		$this->nonpro=1;
		add_action('admin_menu', array($this,'simple_theme_demo_importer_add_pages'));
		add_action( 'admin_notices', array($this,'simple_theme_demo_importer_admin_notice_error'),990 );
		add_action( 'admin_enqueue_scripts', array($this,'simple_theme_demo_importer_style'));
		add_action( 'admin_init', array($this,'simple_theme_demo_importer_active' ));
		// add_action( 'admin_init', array($this,'simple_theme_demo_importer_activate') );
		/*****  DASHBOARD BOX ***/
		add_action( 'wp_dashboard_setup', array($this,'simple_theme_demo_importer_dashboard_widgets' ));
		/***** SUBMIT USER FORM **/
		add_action( 'wp_ajax_submitUserForm', array( $this, 'simple_theme_demo_importer_submitUserForm' ) ); 
    	add_action( 'wp_ajax_nopriv_submitUserForm', array( $this, 'simple_theme_demo_importer_submitUserForm' ) );
    	/********** REDIRECT ****/
		register_activation_hook( __FILE__, array($this,'simple_theme_demo_importer_plugin_activate' ));
		add_action( 'admin_init', array($this,'simple_theme_demo_importer_redirect'));

    	/*** LINKS **/
		add_filter( 'plugin_action_links', array($this,'simple_theme_demo_importer_links'), 10, 2 );
 		add_filter( 'plugin_row_meta', array($this,'simple_theme_demo_importer_plugin_row_meta') , 10, 2 );
 		/*** PROMOTIONAL OFFERS **/
		add_action( 'admin_notices', array( $this, 'simple_theme_demo_importer_promotional_offer' ) );
	}//end of constructor
	function simple_theme_demo_importer_config()
	{
		$theme = wp_get_theme();
		$lfilepath=get_template_directory().'/importer/demos/';
		$lfileurl=get_template_directory_uri().'/';
		if(file_exists($lfilepath))
		{
			$this->fileurl=$lfileurl;  //SIMPLE_THEME_DEMO_IMPORT_PLUGIN_URL
			$this->filepath=get_template_directory().'/'; //SIMPLE_THEME_DEMO_IMPORT_PLUGIN
		}
		else
		{
			$this->fileurl=plugin_dir_url( __FILE__ );
			$this->filepath=plugin_dir_path( __FILE__ );
		}	
			
	}//end of function
	function simple_theme_demo_importer_plugin_activate()
	{
	   	if ( ! is_network_admin() ) 
	   	{
			set_transient( '_simple_theme_demo_importer_redirect', 1, 30 );
		}
	}//end of function
	function simple_theme_demo_importer_redirect()
	{
		$redirect = get_transient( '_simple_theme_demo_importer_redirect' );
		delete_transient( '_simple_theme_demo_importer_redirect' );
		if ( $redirect ) 
		{
			wp_safe_redirect(admin_url('admin.php?page=simple-theme-demo-importer') );
		}
	}//end of function
	function simple_theme_demo_importer_style() {
		wp_register_style( 'simple_theme_demo_importer_style', SIMPLE_THEME_DEMO_IMPORT_PLUGIN_CSS_URI.'style.css', false, SIMPLE_THEME_DEMO_IMPORT_PLUGIN_VERSION );
		wp_enqueue_style( 'simple_theme_demo_importer_style' );
		wp_enqueue_script('simple_theme_demo_importer_script',SIMPLE_THEME_DEMO_IMPORT_PLUGIN_JS_URI.'script.js',array('jquery'), SIMPLE_THEME_DEMO_IMPORT_PLUGIN_VERSION);
    	wp_localize_script( 'simple_theme_demo_importer_script', 'stdisettings', array( 'ajaxurl' => admin_url('admin-ajax.php')) );
	}
	function simple_theme_demo_importer_add_pages() 
	{
		add_menu_page('Simple theme demo import', 'Import Demos', 'edit_theme_options', 'simple-theme-demo-importer', array($this,'simple_theme_demo_importer_menu'),SIMPLE_THEME_DEMO_IMPORT_PLUGIN_URL.'/images/import.png',62);
	}//end of function
	function simple_theme_demo_importer_admin_notice_error() 
	{
		global $pagenow;
		if(!isset($_GET['page']))
		{
			return;
		}
		$nonpro=0;
		$page=$_GET['page'];
		if ( $pagenow == 'admin.php' && $page=='simple-theme-demo-importer') 
		{			
			$check_url=$this->fileurl .'importer/demos/plugin_check.json';
			$json_data=file_get_contents($check_url);
			$json_arr=json_decode($json_data,true);
			$err_class=array();
			$flag=0;
			foreach($json_arr as $req_plugin)
			{
				if(!class_exists($req_plugin['plugin_class']))
				{
					$err_class[]=$req_plugin['name'];
					$flag=1;
				}
			}
			
			$msg='Required Plugin Not Activated: <strong>'.implode(',',$err_class).'</strong>'; ?>
			<?php 
				if($flag==1)
				{
			?>
				<div class="notice notice-error ">
					<p><?php echo $msg; ?></p>
				</div>
			<?php 
				}//flag==1
			if($nonpro==0)
			{
				?>
					<div class="notice notice-success stdi-notice">
							<p>Thanks for Installing Simple Theme Demo Importer, Please <a target="_blank" href="<?php echo SIMPLE_THEME_DEMO_IMPORT_RATE_LINK; ?>">Rate Us <span class="dashicons dashicons-star-filled stdi-rate"></span><span class="dashicons dashicons-star-filled stdi-rate"></span><span class="dashicons dashicons-star-filled stdi-rate"></span><span class="dashicons dashicons-star-filled stdi-rate"></span><span class="dashicons dashicons-star-filled stdi-rate"></span></a> </p>
					</div>
				<?php 
			}
		}//end if
	}//end of function
	/**
	 * Disply callback for the Unsub page.
	 */
	function simple_theme_demo_importer_menu() 
	{
		$demo_path= $this->filepath . 'importer/demos/';
		$output='';
		if(is_dir($demo_path))
		{
			$dirs = glob($demo_path.'*',GLOB_ONLYDIR);
			$output='<ul class="demo_list">';
			foreach($dirs as $dir)
			{
				$output.='<li class="demo_list_item">';
				$output.='<div class="loader_wrapper"><div class="loader"></div></div>';
				$demo_name=basename($dir);
				$demo_url=$this->fileurl .'importer/demos/'. $demo_name;
				$json_data=file_get_contents($demo_url.'/demo_info.json');
				$json_arr=json_decode($json_data,true);
				$output.='<img src="'.$this->fileurl .'importer/demos/'. $demo_name.'/'.$json_arr['screenshot'].'" class="demo_image">';
				$output.='<div class="content_area">';
				$output.='<span>'.$json_arr['demo_label'].'</span>';
				$output.='<div class="buttons">';
				$output.='<a href="themes.php?page=simple-theme-demo-importer&import_data_content=true&demo_name='.strtolower($json_arr['Demo Name']).'" class="import_burtton">Import</a>';
				$output.='</div>';
				$output.='</div>';
				$output.='</li>';
			}
			$output.='</ul>';
		}
		echo $output;
	 	//On success
		if(isset( $_GET['import_successfull'] ) &&  ($_GET['import_successfull'] != ''))
		{
			echo '<h2>Demo data imported successfully</h2>';
		}
	}//end of function
	function simple_theme_demo_importer_active() 
	{
		global $wpdb;
		if ( current_user_can( 'manage_options' ) && isset( $_GET['import_data_content'] ) ) {
			
			// Define importer
			if ( ! defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true); 

			// Load Importer API
			if ( ! class_exists( 'WP_Importer' ) ) {
				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
				if ( file_exists( $class_wp_importer ) )
					require $class_wp_importer;
			}
			if ( ! class_exists('WP_Import') ) {
				$wp_import = SIMPLE_THEME_DEMO_IMPORT_PLUGIN . 'wordpress-importer.php';
				require $wp_import;
			}
			
			$demo_name=$_GET['demo_name'];
			$demo_url=$this->fileurl .'importer/demos/'. $demo_name;
			$json_data=file_get_contents($demo_url.'/demo_info.json');
			$json_arr=json_decode($json_data,true);

			if ( class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ) {

				$importer = new WP_Import();

				//THIS FOR DEMO IMPORT

				// Import demo data
				$theme_xml = $this->filepath . '/importer/demos/'.$demo_name.'/theme_data.xml';
				$importer->fetch_attachments = true;
				ob_start();
				$importer->import($theme_xml);
				ob_end_clean();


				//REVOLUTION SLIDER IMPORT
				if ( class_exists( 'RevSlider' ) ) 
				{
					$slider_path = $this->filepath . 'importer/demos/'.$demo_name.'/revsliders/';
					if(is_dir($slider_path))
					{
						if ( class_exists( 'RevSlider' ) )
						{
							$rev_slider = new RevSlider();
						}
						else
						{
							exit;
						}
						foreach(glob($slider_path."/*.zip") as $slider)
						{						
							if ( file_exists( $slider ) ) {
								$rev_slider->importSliderFromPost( true, true, $slider );
							}
						}     					     				
					}
				}
	            //THIS FOR MENU ASSIGNMENT

	            // Assign imported menus				
				$menus=$json_arr['menus'];
				$menu_names=wp_list_pluck( $menus, 'name' );

				$locations = get_theme_mod( 'nav_menu_locations' );
				$registred_menus = wp_get_nav_menus();

				if( $registred_menus ) 
				{				
					foreach( $registred_menus as $menu ) 
					{
						if(in_array($menu->name, $menu_names)) 
						{
							$k=array_search($menu->name, $menu_names);
							$location_key=$menus[$k]['location'];
							$locations[$location_key]=$menu->term_id;
						}
					}
				}
				set_theme_mod( 'nav_menu_locations', $locations );
	            //THIS FOR WIDGET IMPORT
				$widget_data = $this->filepath . '/importer/demos/'.$demo_name.'/widget_data.wie';
				$this->simple_theme_demo_importer_process_import_file( $widget_data );
				//THUS FOR REDUX IMPORT
				if ( class_exists( 'ReduxFramework' ) ) 
				{
					$json_file_path = $widget_data = $this->filepath . '/importer/demos/'.$demo_name.'/redux.json';
					$redux_options_raw_data = file_get_contents($json_file_path);
					$redux_options_data = json_decode( $redux_options_raw_data, true );
					$redux_framework = \ReduxFrameworkInstances::get_instance('theme_options');
					// Import Redux settings.
					$redux_framework->set_options( $redux_options_data );				
				}
	            // CHANGE SETTINGS> READING
				$posts_page = get_page_by_title( 'Theme Default Blog' );
				$frontpage=$json_arr['front_page'];
				$front_page = get_page_by_title( $frontpage );

				if($front_page->ID) {
					update_option('show_on_front', 'page');
	                update_option('page_on_front', $front_page->ID);// Set front page
	                update_option('page_for_posts', $posts_page->ID); //set posts page
	            }
	            // Redirect to success page
	            wp_redirect( admin_url( 'themes.php?page=simple-theme-demo-importer&import_successfull=true' ) );
	        }
	    }
	}//end of function
	function simple_theme_demo_importer_process_import_file( $widget_data ) 
	{
		// Get file and decode
		$data = file_get_contents( $widget_data );
		$data = json_decode( $data );

		// Import the widget data
		$this->simple_theme_demo_importer_data( $data );
	}//end of function

	// Import widget JSON data
	function simple_theme_demo_importer_data( $data ) 
	{
		global $wp_registered_sidebars;
		// Have valid data?
		// If no data or could not decode
		if ( empty( $data ) || ! is_object( $data ) ) {
			wp_die(
				__( 'Import data could not be read. Please try a different file.', 'widget-importer-exporter' ),
				'',
				array( 'back_link' => true )
			);
		}
		// Hook before import
		do_action( 'wie_before_import' );
		$data = apply_filters( 'wie_import_data', $data );
		// Get all available widgets site supports
		$available_widgets = $this->simple_theme_demo_importer_available_widgets();
		// Get all existing widget instances
		$widget_instances = array();
		foreach ( $available_widgets as $widget_data ) {
			$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
		}
		// Begin results
		$results = array();
		// Loop import data's sidebars
		foreach ( $data as $sidebar_id => $widgets ) {
			// Skip inactive widgets
			// (should not be in export file)
			if ( 'wp_inactive_widgets' == $sidebar_id ) {
				continue;
			}
			// Check if sidebar is available on this site
			// Otherwise add widgets to inactive, and say so
			if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
				$sidebar_available = true;
				$use_sidebar_id = $sidebar_id;
				$sidebar_message_type = 'success';
				$sidebar_message = '';
			} else {
				$sidebar_available = false;
				$use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
				$sidebar_message_type = 'error';
				$sidebar_message = __( 'Sidebar does not exist in theme (using Inactive)', 'widget-importer-exporter' );
			}
			// Result for sidebar
			$results[$sidebar_id]['name'] = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
			$results[$sidebar_id]['message_type'] = $sidebar_message_type;
			$results[$sidebar_id]['message'] = $sidebar_message;
			$results[$sidebar_id]['widgets'] = array();
			// Loop widgets
			foreach ( $widgets as $widget_instance_id => $widget ) {
				$fail = false;
				// Get id_base (remove -# from end) and instance ID number
				$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
				$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );
				// Does site support this widget?
				if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
					$fail = true;
					$widget_message_type = 'error';
					$widget_message = __( 'Site does not support widget', 'widget-importer-exporter' ); // explain why widget not imported
				}
				$widget = apply_filters( 'wie_widget_settings', $widget ); // object
				$widget = json_decode( json_encode( $widget ), true );
				$widget = apply_filters( 'wie_widget_settings_array', $widget );
				if ( ! $fail && isset( $widget_instances[$id_base] ) ) {
					// Get existing widgets in this sidebar
					$sidebars_widgets = get_option( 'sidebars_widgets' );
					$sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go
					// Loop widgets with ID base
					$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
					foreach ( $single_widget_instances as $check_id => $check_widget ) {
						// Is widget in same sidebar and has identical settings?
						if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {
							$fail = true;
							$widget_message_type = 'warning';
							$widget_message = __( 'Widget already exists', 'widget-importer-exporter' ); // explain why widget not imported
							break;
						}
					}
				}
				// No failure
				if ( ! $fail ) {
					// Add widget instance
					$single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
					$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
					$single_widget_instances[] = $widget; // add it
					// Get the key it was given
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );
					// If key is 0, make it 1
					// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number = 1;
						$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}
					// Move _multiwidget to end of array for uniformity
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}
					// Update option with new widget
					update_option( 'widget_' . $id_base, $single_widget_instances );
					// Assign widget instance to sidebar
					$sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
					$new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
					$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
					update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data
					// Success message
					if ( $sidebar_available ) {
						$widget_message_type = 'success';
						$widget_message = __( 'Imported', 'widget-importer-exporter' );
					} else {
						$widget_message_type = 'warning';
						$widget_message = __( 'Imported to Inactive', 'widget-importer-exporter' );
					}
				}
				// Result for widget instance
				$results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
				$results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = ! empty( $widget['title'] ) ? $widget['title'] : __( 'No Title', 'widget-importer-exporter' ); // show "No Title" if widget instance is untitled
				$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
				$results[$sidebar_id]['widgets'][$widget_instance_id]['message'] = $widget_message;
			}
		}
		// Hook after import
		do_action( 'wie_after_import' );
		// Return results
		return apply_filters( 'wie_import_results', $results );
	}//end of function
	// Available widgets
	function simple_theme_demo_importer_available_widgets() 
	{
		global $wp_registered_widget_controls;

		$widget_controls = $wp_registered_widget_controls;
		$available_widgets = array();

		foreach ( $widget_controls as $widget ) {
			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes
				$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
				$available_widgets[$widget['id_base']]['name'] = $widget['name'];
			}
		}
		return apply_filters( 'simple_theme_demo_importer_available_widgets', $available_widgets );
	}//end of function
	/****** ADDING DASHBOARD BOX : started *****/
	function simple_theme_demo_importer_dashboard_widgets()
	{
		wp_add_dashboard_widget('simple_theme_demo_importer',__( 'Simple Theme Demo Importer', 'simple_theme_demo_importer' ),array( $this, 'simple_theme_demo_importer_widget_callback' ),100);

	}//end of function
	function simple_theme_demo_importer_widget_callback()
	{
		$file='dashboard-notice.php';
		require_once( SIMPLE_THEME_DEMO_IMPORT_PLUGIN_TEMPLATE_DIR.$file);		
	}//end of function
	//ajax function
	function simple_theme_demo_importer_submitUserForm()
	{
		parse_str($_POST['formData'], $formData);
		$actual_link = "http://".$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'];
		if(trim($formData['user_email'])!='')
		{
			$to = 'tristup@gmail.com';
			$subject = 'Query:Simple Theme Demo Importer';
			$body = 'Email Id : '.$formData['user_email'];
			$body.='URL : '.$formData['location'];
			$headers = array('Content-Type: text/html; charset=UTF-8');
			wp_mail( $to, $subject, $body, $headers ) or die('mail not sent');
		}
		else
		{
			die('Blank Email Id');
		}
		echo 'Query received, will contact shortly';
		die();
	}//end of function
	/****** ADDING DASHBOARD BOX : ended *****/
	function simple_theme_demo_importer_links( $links, $file ) 
	{
	    $fileName = 'simple-theme-demo-importer/simple-theme-demo-importer.php';
	    
	    $fileName = preg_replace('/\_trial\//', '/', $fileName);
	    $fileName = preg_replace('/\_commercial\//', '/', $fileName);
	    if ($file == $fileName) 
	    {
	        $import_link = '<a href="'.admin_url('admin.php?page=simple-theme-demo-importer').'">'.esc_html__('Import','simple_theme_demo_importer').'</a>';
	        array_unshift( $links, $import_link );	        
	        if($this->nonpro==1)
	        {
	        	$pro_link = '<a target="_blank" href="'.SIMPLE_THEME_DEMO_IMPORT_PREMIUM_LINK.'">'.esc_html__('Get Pro','simple_theme_demo_importer').'</a>';
	        	array_push( $links, $pro_link );	      
	        }
	        else
	        {
	        	$pro_link = '<a target="_blank" href="'.admin_url('admin.php?page=simple-theme-demo-importer').'">'.esc_html__('Activate License','simple_theme_demo_importer').'</a>';
	        	array_push( $links, $pro_link );
	        }
	    }
	    return $links;
	}//end of function
	function simple_theme_demo_importer_plugin_row_meta( $links, $file ) 
	{
	    if (strpos(plugin_dir_path(__FILE__),plugin_dir_path($file))) {
	        $row_meta = array($links[0],$links[1]);
	        $row_meta['Import'] = '<a href="'.admin_url('admin.php?page=simple-theme-demo-importer').'">'.esc_html__('Import','simple_theme_demo_importer').'</a>';	        
	        $row_meta['Support']='<a target="_blank" href="'.SIMPLE_THEME_DEMO_IMPORT_SUPPORT_LINK.'">'.esc_html__('Support','simple_theme_demo_importer').'</a>';
	       	if($this->nonpro==1)
	        {
	        	$row_meta['Getpro']='<a target="_blank" href="'.SIMPLE_THEME_DEMO_IMPORT_PREMIUM_LINK.'">'.esc_html__('Get Pro','simple_theme_demo_importer').'</a>';
	        	$row_meta['RateUs']='<a target="_blank" href="'.SIMPLE_THEME_DEMO_IMPORT_RATE_LINK.'"><span class="dashicons dashicons-star-filled stdi-rate"></span>'.esc_html__('Rate Us','simple_theme_demo_importer').'</a>';	        	
	        }
	        else
	        {
	        	$row_meta['Non Pro']='<a target="_blank" href="'.admin_url('admin.php?page=simple-theme-demo-importer').'">'.esc_html__('Activate License','simple_theme_demo_importer').'</a>';
	        }
	        return $row_meta;
	    }
	    return (array) $links;
	}//end of function
	/*** FOR NON PRO VERSION **/
	function simple_theme_demo_importer_promotional_offer()
	{
		if($this->nonpro==0)
		{
			return;
		}
		$promotional_url=SIMPLE_THEME_DEMO_IMPORT_PREMIUM_INFO_URL .'stdi-plugin/promotional.json';
		$promotion=wp_remote_get($promotional_url);
        if ( !is_wp_error( $promotion ) && isset( $promotion['response']['code'] ) && $promotion['response']['code'] == 200 && !empty( $promotion['body'] ) )
        {
        	$promotion=json_decode($promotion['body']);
			$file='promotional-notice.php';
			require_once( SIMPLE_THEME_DEMO_IMPORT_PLUGIN_TEMPLATE_DIR.$file);		
        } 
	}//end of function	


}//end of class
new simple_theme_demo_importer();




function abcd()
{
	$screen = get_current_screen();
	if ( $screen->base == 'edit' && $screen->post_type=='post' ) 
	{
		wp_enqueue_script('aaaa',plugins_url('js/my-custom-posts-script.js',__FILE__));
	}
}
add_action( 'admin_enqueue_scripts', 'abcd');