<?php
/**
 * WordPress Widget Boilerplate
 *
 * The WordPress Widget Boilerplate is an organized, maintainable boilerplate for building widgets using WordPress best practices.
 *
 * @package   Extra_Widget_Links_Icon
 * @author    Vincent Saïsset <vs@extralagence.com>
 * @license   GPL-2.0+
 * @link      www.extralagence.com
 * @copyright 2014 Extra
 *
 * @wordpress-plugin
 * Plugin Name:       Extra Widget Menu Page
 * Plugin URI:        https://github.com/extralagence/extra-widget-links-icon
 * Description:       Extra plugin. Links with icon
 * Version:           1.0.0
 * Author:            Vincent SAISSET
 * Author URI:        www.extralagence.com
 * Text Domain:       extra-widget-links-icon
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/extralagence/extra-widget-links-icon<repo>
 */

class Extra_Widget_Links_Icon extends WP_Widget {

    /**
     *
     * Unique identifier for your widget.
     *
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * widget file.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $widget_slug = 'extra-widget-links-icon';

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			$this->get_widget_slug(),
			__( 'Liens avec icône (Extra)', $this->get_widget_slug() ),
			array(
				'classname'  => $this->get_widget_slug().'-class',
				'description' => __( "Liste de liens représentés par des icônes", $this->get_widget_slug() )
			)
		);

		// Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

		// Refreshing the widget's cached output with each new post
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

	} // end constructor


    /**
     * Return the widget slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_widget_slug() {
        return $this->widget_slug;
    }

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		
		// Check if there is a cached output
		$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset ( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset ( $cache[ $args['widget_id'] ] ) )
			return print $cache[ $args['widget_id'] ];
		
		// go on with your widget logic, put everything into a string and …

		extract( $args, EXTR_SKIP );

		$show_widget = true;

		$widget_string = $before_widget;
		ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/widget.php' );
		$widget_string .= ob_get_clean();
		$widget_string .= $after_widget;


		$cache[ $args['widget_id'] ] = $widget_string;

		wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

		if ($show_widget) {
			print $widget_string;
		}
	} // end widget
	
	
	public function flush_widget_cache() 
	{
    	wp_cache_delete( $this->get_widget_slug(), 'widget' );
	}
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 */
	public function update($new_instance, $old_instance) {
		$old_instance['title'] = strip_tags($new_instance['title']);

		$links_by_id = array();
		foreach ($new_instance as $key => $value) {
			if ($key == 'title') {
				$old_instance['title'] = $value;
			} else if($key != 'link_title' && $key != 'link_url' && !empty($value)) {
				$matches = array();

				preg_match('([\\(].*[\\)])', $key, $matches);
				$match = $matches[0];

				$link_id = substr($match, 1, strlen($match)-2);
				$link_key = str_replace($match, '', $key);

				$link = array();
				if (array_key_exists($link_id, $links_by_id)) {
					$link = $links_by_id[$link_id];
				}
				$link[$link_key] = $value;

				$links_by_id[$link_id] = $link;
			}
		}

		$links = array();
		foreach($links_by_id as $link) {
			$links[] = $link;
		}
		$old_instance['links'] = $links;

		return $old_instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title' => '',
			)
		);

		// Display the admin form
		include( plugin_dir_path(__FILE__) . 'views/admin.php' );

	} // end form

	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		load_plugin_textdomain( $this->get_widget_slug(), false, plugin_dir_path( __FILE__ ) . 'lang/' );

	} // end widget_textdomain

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param  boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
	} // end deactivate

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {

		wp_enqueue_style( $this->get_widget_slug().'-admin-styles', plugins_url( 'css/admin.less', __FILE__ ) );

	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {

		wp_enqueue_script( $this->get_widget_slug().'-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array('jquery') );

	} // end register_admin_scripts

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {

		wp_enqueue_style( $this->get_widget_slug().'-widget-styles', plugins_url( 'css/widget.less', __FILE__ ) );

	} // end register_widget_styles

	/**
	 * Registers and enqueues widget-specific scripts.
	 */
	public function register_widget_scripts() {

		wp_enqueue_script( $this->get_widget_slug().'-script', plugins_url( 'js/widget.js', __FILE__ ), array('jquery') );

	} // end register_widget_scripts


	public function admin_item_template($link_id = null, $link = null) {
		$field_id_suffix = ($link_id === null) ? '' : '-'.$link_id;
		$fields_name_suffix = ($link_id === null) ? '' : '('.$link_id.')';


		$icons =  array(
			array(
				'value' => 'icon-link-facebook',
				'label' => 'Facebook',
			),
			array(
				'value' => 'icon-link-twitter',
				'label' => 'Twitter',
			),
			array(
				'value' => 'icon-link-youtube',
				'label' => 'Youtube',
			),
			array(
				'value' => 'icon-link-googleplus',
				'label' => 'Google +',
			),
			array(
				'value' => 'icon-link-vimeo',
				'label' => 'Vimeo',
			),
			array(
				'value' => 'icon-link-skype',
				'label' => 'Skype',
			),
			array(
				'value' => 'icon-link-flickr',
				'label' => 'Flickr',
			),
			array(
				'value' => 'icon-link-forrst',
				'label' => 'Forrst',
			),
			array(
				'value' => 'icon-link-dribble',
				'label' => 'Dribble',
			),
			array(
				'value' => 'icon-link-digg',
				'label' => 'Digg',
			),
			array(
				'value' => 'icon-link-share',
				'label' => 'Share',
			),
			array(
				'value' => 'icon-link-rss',
				'label' => 'Rss',
			)
		);
		$icons = apply_filters('extra-widget-links-icon-list', $icons);

		?>
		<div class="extra-widget-links-icon-item extra-bloc<?php echo ($link == null) ? ' extra-widget-links-icon-template' : ''; ?>">
			<div class="extra-widget-links-icon-list-handle"><span class="icon-admin icon-admin-grip"></span></div>
			<button class="button button-primary right extra-widget-links-icon-remove-button"><span class="icon-admin icon-admin-remove"></span></button>
			<p>
				<label for="<?php echo $this->get_field_id('link_icon'.$field_id_suffix); ?>"><?php _e("Icône :", "extra-widget-links-icon"); ?></label>
				<select id="<?php echo $this->get_field_id('link_icon'.$field_id_suffix); ?>" name="<?php echo $this->get_field_name('link_icon'.$fields_name_suffix); ?>">
					<option value=""><?php _e("Sélectionner une icône", "extra-widget-links-icon"); ?></option>
					<?php foreach($icons as $icon) : ?>
						<option value="<?php echo $icon['value']; ?>"<?php echo ($link !== null && $link['link_icon'] == $icon['value']) ? ' selected' : ''; ?>><?php echo $icon['label']; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('link_url'.$field_id_suffix); ?>"><?php _e("Url :", "extra-widget-links-icon"); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('link_url'.$field_id_suffix); ?>" type="text" name="<?php echo $this->get_field_name('link_url'.$fields_name_suffix); ?>" value="<?php echo ($link !== null) ? $link['link_url'] : ''; ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('link_title'.$field_id_suffix); ?>"><?php _e("Titre :", "extra-widget-links-icon"); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('link_title'.$field_id_suffix); ?>" type="text" name="<?php echo $this->get_field_name('link_title'.$fields_name_suffix); ?>" value="<?php echo ($link !== null) ? $link['link_title'] : ''; ?>"/>
			</p>
		</div>
		<?php
	}

} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("Extra_Widget_Links_Icon");' ) );