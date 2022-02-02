<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://maatio.fi
 * @since      1.0.0
 *
 * @package    Woo_Inventaario
 * @subpackage Woo_Inventaario/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Inventaario
 * @subpackage Woo_Inventaario/admin
 * @author     Nader Gam / Maatio Oy <dev@myyntimaatio.fi>
 */
class Woo_Inventaario_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		// Setup admin settings page.
		add_action( 'admin_menu', array($this, 'inventaario_page_settings') );
		
		// Add filter for classes
		add_filter( 'admin_body_class', array($this, 'add_body_classes') );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Inventaario_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Inventaario_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-inventaario-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Inventaario_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Inventaario_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-inventaario-admin.js', array( 'jquery' ), $this->version, false );

	}

	/*
	*
	* THIS IS WHERE THE CUSTOM CODE STARTS 
	*
	*/
	
	// Filter for classes
	function add_body_classes($classes) {
		// Add class to Admin Body
		return "$classes no-print";
	}

	// Register page settings
	function inventaario_page_settings() {
		add_menu_page(
			'Verkkokaupan inventaario',     // page title
			'Inventaario',     // menu title
			'edit_posts',   // capability
			'inventaario_woocommerce',     // menu slug
			array($this, 'inventaario_render'), // callback function
			'dashicons-table-col-before', // icon
			140
		);
	}

	// Render the page
	function inventaario_render() {		

		?>
		<div class = "inventaario-header">
			<h1>Verkkokaupan inventaario</h1>
			<button id = "windowprintBtn" onclick = "windowprint()">Siirry lataamaan PDF</button>	
		</div>
		<?php
		// Args
		$args = array(
			'post_type'			=>	'product',
			'posts_per_page'	=>	-1,
		);
		
		// Create query with args
		$tuotteet = new WP_Query($args);
		
		// Loop with query
		if ( $tuotteet->have_posts() ) :
		
		// Muuttujat
		$erilaisetVarastossa = 0; // Lasketaan erilaisten tuotteiden määrä (joita varastossa) 
		$tuotteitaYhteensa = 0; // Lasketaan tuotteet yhteensä
		
		$alvitonHinnatYhteensa = 0; // Lasketaan tuotteiden ALViton hinta yhteensä
		$alvillinenHinnatYhteensa = 0; // Lasketaan tuotteiden ALVillinen hinta yhteensä
		?>

		<table class = "mmAdmin-inventaarioTaulu">
			<tr class = "mmAdmin-inventaario-TuoteOtsikot">
				<th>Tuotteen SKU</th>
				<th>ID (WP)</th>
				<th>Tuote</th>
				<th>Määrä</th>
				<th>Hankintahinta (ALV)</th>
				<th>Hankintahinta (ei-ALV)</th>
				<th>Hinta yhteensä (ei-ALV)</th>
			</tr>
			<?php while ( $tuotteet->have_posts() ) : $tuotteet->the_post();
			$product_item = get_product($tuotteet->post); // Alusta muuttuja
			
			// ACF
			$hankintahinta_kentta = get_field('hankintahinta'); 	// Hankintahinta
			$alv_kentta = get_field('alv-hankinta');				// ALV %
		
			// Laskutoimitukset
			$alvillinenHankinta = $hankintahinta_kentta ? $hankintahinta_kentta : '';
			$alvillinenHankintaYht = $hankintahinta_kentta * $product_item->get_stock_quantity();
		
			$alvitonHankinta = $hankintahinta_kentta && $alv_kentta ? round( ($hankintahinta_kentta / $alv_kentta), 2) : '';
			$alvitonHankintaYht = $alvitonHankinta * $product_item->get_stock_quantity();
			
			?>
			<tr class = "mmAdmin-inventaario-Tuote">
				<td class = "mmAdmin-inventaario-Tuote-ID"><?php echo $product_item->get_sku(); ?></td>
				<td class = "mmAdmin-inventaario-Tuote-ID"><?php echo get_the_id(); ?></td>
				<td class = "mmAdmin-inventaario-Tuote-Nimi"><?php echo get_the_title(); ?></td>
				<td class = "mmAdmin-inventaario-Tuote-Maara"><?php echo $product_item->get_stock_quantity(); ?></td>
				<td class = "mmAdmin-inventaario-Tuote-Hinta"><?php echo $hankintahinta_kentta; ?></td>
				<td class = "mmAdmin-inventaario-Tuote-Hinta"><?php echo $alvitonHankinta; ?></td>
				<td class = "mmAdmin-inventaario-Tuote-Maara"><?php echo $alvitonHankintaYht; ?></td>
			</tr>
			<?php
		
			// Lisää laskutoimituksia
			$erilaisetVarastossa++; // +1 per tuote -> lasketaan kuinka monta erilaista tuotetta

			$tuotteitaYhteensa = $tuotteitaYhteensa + $product_item->get_stock_quantity(); // Kaikki tuotteet yhteensä
			
			$alvillinenHinnatYhteensa = $alvillinenHinnatYhteensa + $alvillinenHankintaYht; // ALVillinen hinta yhteensä
			$alvitonHinnatYhteensa = $alvitonHinnatYhteensa + $alvitonHankintaYht; // ALViton hinta yhteensä
		
			endwhile; ?>
			<tr class = "mmAdmin-inventaario-hinnatYhteensa">
				<th></th>
				<th>Inventaariossa eri tuotteita</th>
				<th></th>
				<th>Inventaariossa tuotteita yhteensä</th>
				<th>Inventaarion arvo yhteensä (ALV)</th>
				<th></th>
				<th>Inventaarion arvo yhteensä (ei-ALV)</th>
			</tr>
			<tr>
				<td></td>
				<td><?php echo $erilaisetVarastossa; ?></td>
				<td></td>
				<td><?php echo $tuotteitaYhteensa; ?></td>
				<td><?php echo $alvillinenHinnatYhteensa; ?></td>
				<td></td>
				<td><?php echo $alvitonHinnatYhteensa; ?></td>
			</tr>
		</table>
		
		<script>
		function windowprint() {
			window.print();
		}
		</script>
		<?php endif;

		wp_reset_postdata();
	}

}
