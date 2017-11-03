<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 28/08/17
 * Time: 08:49 PM
 */

class FPP_Form_Print_Pay_plugin
{

	/**
	 * @var string
	 */
	public $file;

	/**
	 * @var string
	 */
	public $version;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $plugin_path;

	/**
	 * @var string
	 */
	public $plugin_url;

	/**
	 * @var string
	 */
	public $includes_path;

	/**
	 * @var bool
	 */
	private $_bootstrapped = false;

	/**
	 * @var
	 */
	public $settings;

	/**
	 * @var array
	 */
	public $uploads;
	/**
	 * @var string
	 */
	public $uploads_dir;

	/**
	 * @var string
	 */
	public $uploads_url;

	public function __construct($file, $version, $name)
	{
		$this->file = $file;
		$this->version = $version;
		$this->name = $name;

		// Path.
		$this->plugin_path   = trailingslashit( plugin_dir_path( $this->file ) );
		$this->plugin_url    = trailingslashit( plugin_dir_url( $this->file ) );
		$this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
		$this->uploads = wp_upload_dir();
		$this->uploads_dir = $this->uploads['basedir'] . '/form-print-pay/';
		$this->uploads_url = $this->uploads['baseurl'] . '/form-print-pay/';

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action('fpp_form_print_pay', array($this, 'fpp_update_orders_form_print_pay'));
	}

	public function form_print_run()
	{
		add_action('init',array($this, 'manage_news_feed'));
		try{
			if ($this->_bootstrapped){
				throw new Exception( __( 'Form Print Pay can only be called once', 'form-print-pay' ));
			}
			$this->_run();
			$this->_bootstrapped = true;
		}catch (Exception $e){
			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				do_action('notices_action_tag', $e->getMessage());
			}
		}
	}


	protected function _run()
	{
		$this->_load_handlers();
	}

	protected function _load_handlers()
	{
		require_once ($this->includes_path . 'class-form-print-pay-curl.php');
		require_once ($this->includes_path . 'class-form-print-pay-admin.php');
		require_once ($this->includes_path . 'class-form-print-pay-admin-config.php');
		require_once ($this->includes_path . 'class-form-print-pay-admin-email.php');
		require_once ($this->includes_path . 'class-form-print-pay-admin-paypal.php');
		require_once ($this->includes_path . 'class-form-print-pay-admin-pdf.php');
		require_once ($this->includes_path . 'class-form-print-pay-admin-tabs.php');
		require_once ($this->includes_path . 'class-form-print-pay-shortcodes.php');
		require_once ($this->includes_path . 'class-form-print-pay-paypal.php');
		require_once ($this->includes_path . 'fpdf/fpdf.php');
		require_once ($this->includes_path . 'class-form-print-pay-pdf.php');

		$this->cURL = new Form_Print_Pay_Curl();
		$this->Admin = new Form_Print_Pay_Admin();
		$this->Config = new Form_Print_Pay_Admin_Config();
		$this->configEmail = new Form_Print_Pay_Admin_Email();
		$this->configPaypal = new Form_Print_Pay_admin_Paypal();
		$this->configPdf = new Form_Print_Pay_Admin_PDF();
		$this->tabs = new Form_Print_Pay_Admin_Tabs();
		$this->shortcodes = new Form_Print_Pay_Shortcodes();
		$this->Paypal = new Form_Print_Pay_Paypal();
		$this->fpdf = new FPDF();
		$this->form_fpdf = new Form_Print_Pay_PDF();

	}

	public function manage_news_feed()
	{
		register_post_type('fpp_form_print_pay', array(
				'labels' => array(
					'name' => 'shortcodes Form',
					'singular_name' => 'Shortcodes',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New Form',
					'edit' => 'Edit',
					'edit_item' => 'Edit Form',
					'new_item' => 'New Form',
					'view' => 'View',
					'view_item' => 'View Form',
					'search_items' => 'Search Form',
					'not_found' => 'No Form',
					'not_found_in_trash' => 'No Form found in Trash',
					'parent' => 'Parent News Forms'
				),
				'public' => true,
				'menu_position' => 100,
				'supports' => array('title'),
				'taxonomies' => array(''),
				'menu_icon' => $this->plugin_url . 'icon.jpg',
				'has_archive' => true,
				'rewrite' => true,
			)
		);
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script( 'form-print-pay', $this->plugin_url . 'assets/js/form-pay.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_style('frontend-form-print-pay', $this->plugin_url . 'assets/css/frontend-form-print-pay.css', array(), $this->version, null);
		wp_localize_script( 'form-print-pay', 'fpp_form_print_pay', array(
			'message_paypal' => __('Redireccionando a paypal...','form-print-pay'),
			'loading' => __('Recopilando la información...','form-print-pay'),
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );
	}

	public function createDirUploads($dir)
	{
		mkdir($dir,0755);
		mkdir($dir . 'img',0755);
		mkdir($dir . 'pdfs',0755);
	}

	public function fpp_update_orders_form_print_pay()
	{
		$events = get_posts( array ( 'post_type' => 'fpp_form_print_pay' ) );

		if ( isset($events) ) {

			foreach ( $events as $event ) {
				$custom_meta = get_post_meta($event->ID, 'fpp_form_print_pay_meta', true );

				if (isset($custom_meta['pending']) && array_key_exists(0,$custom_meta['pending'])){
					foreach ($custom_meta['pending'] as $pendiente){
						if (isset($pendiente['transactionid'])) {

							$paypal = fpp_form_print_pay()->Paypal;
							$paypal->DoExpressCheckoutPayment($pendiente['token'],$pendiente['payerid'],null,true,$event->ID,$pendiente['uniquid']);
						}
					}
				}else{
					return;
				}
			}
		}else{
			return;
		}
	}
}