<?php
/*
Author: Verdon Vaillancourt
Author URI: http://verdon.ca/
License: GPLv2 or later
Text Domain: vv-scroll-icon
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class VVSI_Settings {

    /*--------------------------------------------*
     * Attributes
     *--------------------------------------------*/

    /** Refers to a single instance of this class. */
    private static $instance = null;

    /* Saved options */
    public $options;

    private static $defaults = array(
		'vvsi_chevcol'				=> '#000000',
		'vvsi_txtcol'				=> '#000000',
		'vvsi_chevw'				=> '28',
		'vvsi_chevh'				=> '8',
		'vvsi_txtsz'				=> '12',
		'vvsi_txtmargin'			=> '75',
		'vvsi_txttransform'			=> 'uppercase',
		'vvsi_txtfont'				=> '"Helvetica Neue", "Helvetica", Arial, sans-serif',
		'vvsi_txtwords'				=> 'Scroll down',
		'vvsi_reset'				=> '0',
		'vvsi_clearout'				=> '0'
	);


    /*--------------------------------------------*
     * Constructor
     *--------------------------------------------*/

    // CREATE OR RETURN AN INSTANCE OF THE CLASS
    public static function get_instance() {

        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    } // end get_instance;


	// INITIALIZE THE CLASS
    private function __construct() {

		// get our settings
		$this->options = (object) get_option( 'vvsi_settings', self::$defaults );

		// add page to admin menu
		add_action( 'admin_menu', array( $this, 'vvsi_add_admin_page' ) );

		// register page options
		add_action( 'admin_init', array( $this, 'vvsi_settings_init' ) );
		
		// print any errors
		add_action( 'admin_notices', array( $this, 'vvsi_admin_notices' ));
    }


    /*--------------------------------------------*
     * Functions
     *--------------------------------------------*/

    // ADD PAGE UNDER SETTINGS MENU
	public function vvsi_add_admin_page() {
		$page = add_theme_page(
			'VV Scroll Icon', // Page title
			'Scroll Icon', // Menu title
			'edit_theme_options', // capability
			'vvsi_options', // menu slug
			array( $this, 'vvsi_options_page' ) // Callback
		);

		add_action( "load-{$page}", array( $this, 'vvsi_enqueue_admin_js') );
	}

    // RENDER THE ADMIN PAGE
	public function vvsi_options_page() {
		?>
        <div class="wrap">
			<h2><?php echo __( 'VV Scroll Icon Settings', 'vv-scroll-icon' ); ?></h2>
			<p><strong><?php echo __( 'Place the shortcode [vvscroll] where you would like the icon', 'vv-scroll-icon' );?></strong></p>
			<form action='options.php' method='post'>
			<?php
				add_action( 'admin_notices', array( $this, 'vvsi_admin_notices' ));
				settings_fields('vvsi_settings_group');
				do_settings_sections('vvsi_options');
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	public function vvsi_admin_notices() {
		 settings_errors( 'vvsi_settings' );
	}

    // REGISTER ADMIN PAGE OPTIONS
	public function vvsi_settings_init() {

		register_setting(
			'vvsi_settings_group', // option group
			'vvsi_settings', // option name
			array( $this, 'vvsi_validate_options' ) // sanitize
		);

		add_settings_section(
			'vvsi_options_section', // ID
			__( 'Choose Icon Options', 'vv-scroll-icon' ), // Title
			array( $this, 'vvsi_settings_section_callback' ), // Callback
			'vvsi_options' // page
		);

		add_settings_section(
			'vvsi_admin_section', // ID
			__( 'Administrative Options', 'vv-scroll-icon' ), // Title
			array( $this, 'vvsi_admin_section_callback' ), // Callback
			'vvsi_options' // page
		);

		add_settings_field( // chevron colour
			'vvsi_chevcol',
			__( 'Chevron colour', 'vv-scroll-icon' ),
			array( $this, 'vvsi_chevcol_render' ),
			'vvsi_options',
			'vvsi_options_section'
		);

		add_settings_field( // text colour
			'vvsi_txtcol',
			__( 'Text colour', 'vv-scroll-icon' ),
			array( $this, 'vvsi_txtcol_render' ),
			'vvsi_options',
			'vvsi_options_section'
		);

		add_settings_field( // width
			'vvsi_chevw',
			__( 'Chevron width', 'vv-scroll-icon' ),
			array( $this, 'vvsi_chevw_render' ),
			'vvsi_options',
			'vvsi_options_section'
		);

		add_settings_field( // height
			'vvsi_chevh',
			__( 'Chevron height', 'vv-scroll-icon' ),
			array( $this, 'vvsi_chevh_render' ),
			'vvsi_options',
			'vvsi_options_section'
		);

		add_settings_field( // text size
			'vvsi_txtsz',
			__( 'Text size', 'vv-scroll-icon' ),
			array( $this, 'vvsi_txtsz_render' ),
			'vvsi_options',
			'vvsi_options_section'
		);

		add_settings_field( // margin
			'vvsi_txtmargin',
			__( 'Text top-margin', 'vv-scroll-icon' ),
			array( $this, 'vvsi_txtmargin_render' ),
			'vvsi_options',
			'vvsi_options_section'
		);

		add_settings_field( // transform
			'vvsi_txttransform',
			__( 'Text transformation', 'vv-scroll-icon' ),
			array( $this, 'vvsi_txttransform_render' ),
			'vvsi_options',
			'vvsi_options_section'
		);

		add_settings_field( // font face
			'vvsi_txtfont',
			__( 'Font face', 'vv-scroll-icon' ),
			array( $this, 'vvsi_txtfont_render' ),
			'vvsi_options',
			'vvsi_options_section'
		);

		add_settings_field( // the text
			'vvsi_txtwords',
			__( 'Scroll words', 'vv-scroll-icon' ),
			array( $this, 'vvsi_txtwords_render' ),
			'vvsi_options',
			'vvsi_options_section'
		);



		add_settings_field( // reset defaults
			'vvsi_reset',
			__( 'RESET ALL TO DEFAULT', 'vv-scroll-icon' ),
			array( $this, 'vvsi_reset_render' ),
			'vvsi_options',
			'vvsi_admin_section'
		);

		add_settings_field( // delete settings on deactivate
			'vvsi_clearout',
			__( 'Clear stored settings from database when deactivating this plugin', 'vv-scroll-icon' ),
			array( $this, 'vvsi_clearout_render' ),
			'vvsi_options',
			'vvsi_admin_section'
		);

	}

	// RENDER THE CHEVRON COLOUR DISPLAY OPTIONS
	public function vvsi_chevcol_render(  ) {
		?>
		<input type='text' name='vvsi_settings[vvsi_chevcol]' id='vvsi_settings[vvsi_chevcol]' value='<?php echo $this->options->vvsi_chevcol ?>' class='vvsi-color-picker' />
		<?php
	}

	// RENDER THE TEXT COLOUR DISPLAY OPTIONS
	public function vvsi_txtcol_render(  ) {
		?>
		<input type='text' name='vvsi_settings[vvsi_txtcol]' id='vvsi_settings[vvsi_txtcol]' value='<?php echo $this->options->vvsi_txtcol ?>' class='vvsi-color-picker' />
		<?php
	}

	// RENDER THE WIDTH DISPLAY OPTIONS
	public function vvsi_chevw_render(  ) {
		?>
		<input type='text' name='vvsi_settings[vvsi_chevw]' id='vvsi_settings[vvsi_chevw]' value='<?php echo $this->options->vvsi_chevw ?>' size='4' maxlength='5' />
		<?php
	}

	// RENDER THE HEIGHT DISPLAY OPTIONS
	public function vvsi_chevh_render(  ) {
		?>
		<input type='text' name='vvsi_settings[vvsi_chevh]' id='vvsi_settings[vvsi_chevh]' value='<?php echo $this->options->vvsi_chevh ?>' size='4' maxlength='5' />
		<?php
	}

	// RENDER THE TEXT SIZE DISPLAY OPTIONS
	public function vvsi_txtsz_render(  ) {
		?>
		<input type='text' name='vvsi_settings[vvsi_txtsz]' id='vvsi_settings[vvsi_txtsz]' value='<?php echo $this->options->vvsi_txtsz ?>' size='4' maxlength='5' />
		<?php
	}

	// RENDER THE TEXT MARGIN DISPLAY OPTIONS
	public function vvsi_txtmargin_render(  ) {
		?>
		<input type='text' name='vvsi_settings[vvsi_txtmargin]' id='vvsi_settings[vvsi_txtmargin]' value='<?php echo $this->options->vvsi_txtmargin ?>' size='4' maxlength='5' />
		<?php
	}

	// RENDER THE TEXT TRANSFORM OPTION FIELD
	public function vvsi_txttransform_render(  ) {
		?>
		<select name='vvsi_settings[vvsi_txttransform]' id='vvsi_settings[vvsi_txttransform]'>
			<option value='capitalize' <?php selected( $this->options->vvsi_txttransform, 'capitalize' ); ?>>Capitalize</option>
			<option value='uppercase' <?php selected( $this->options->vvsi_txttransform, 'uppercase' ); ?>>Uppercase</option>
			<option value='lowercase' <?php selected( $this->options->vvsi_txttransform, 'lowercase' ); ?>>Lowercase</option>
		</select>
		<?php
	}

	// RENDER THE FONT FACE OPTIONS
	public function vvsi_txtfont_render(  ) {
		?>
		<input type='text' name='vvsi_settings[vvsi_txtfont]' id='vvsi_settings[vvsi_txtfont]' value='<?php echo $this->options->vvsi_txtfont ?>' size='50' maxlength='100' />
		<?php
	}

	// RENDER THE WORDS OPTIONS
	public function vvsi_txtwords_render(  ) {
		?>
		<input type='text' name='vvsi_settings[vvsi_txtwords]' id='vvsi_settings[vvsi_txtwords]' value='<?php echo $this->options->vvsi_txtwords ?>' size='30' maxlength='100' />
		<?php
	}






	// RENDER THE RESET TO DEFAULT DISPLAY OPTIONS
	public function vvsi_reset_render(  ) {
		?>
		<input type='checkbox' name='vvsi_settings[vvsi_reset]' id='vvsi_settings[vvsi_reset]' <?php checked( $this->options->vvsi_reset, 1 ); ?> value='1' />
		<?php
	}

	// RENDER THE RESET TO DEFAULT DISPLAY OPTIONS
	public function vvsi_clearout_render(  ) {
		?>
		<input type='checkbox' name='vvsi_settings[vvsi_clearout]' id='vvsi_settings[vvsi_clearout]' <?php checked( $this->options->vvsi_clearout, 1 ); ?> value='1' />
		<?php
	}

    // ADD JAVASCRIPT FOR THE COLOUR PICKER
	public function vvsi_enqueue_admin_js() {
		// Make sure to add the wp-color-picker dependecy to js file

		// add the css for the colour picker
		wp_enqueue_style( 'wp-color-picker' );

		// add the custom script
		wp_enqueue_script( 'vvsi_custom_js', plugins_url( 'js/vvsi-jquery.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '', true  );
	}



    // VALIDATE THE FIELDS
    public function vvsi_validate_options( $fields ) {
		$valid_fields = array();

		if ($fields['vvsi_reset'] == 1) {
			$valid_fields = self::$defaults;
			return $valid_fields;
		}

		// just passing these right through as they are fixed input values
		$valid_fields['vvsi_txttransform'] 			= $fields['vvsi_txttransform'];

		// sanitize these
		$valid_fields['vvsi_txtfont'] 				= sanitize_text_field( $fields['vvsi_txtfont'] );
		$valid_fields['vvsi_txtwords'] 				= sanitize_text_field( $fields['vvsi_txtwords'] );

		// always zero this one back out
		$valid_fields['vvsi_reset'] 	= 0;
		$valid_fields['vvsi_clearout'] = $fields['vvsi_clearout'];

		// validate chevron color
		$chevron = trim( $fields['vvsi_chevcol'] );
		$chevron = strip_tags( stripslashes( $chevron ) );

		// check if it is a valid hex color
		if( FALSE === $this->check_color( $chevron ) ) {

			// set the error message
			add_settings_error( 'vvsi_settings', 'vvsi_chevron_error', 'Insert a valid color for Chevron', 'error' ); // $setting, $code, $message, $type

			// get the previous valid value
			$valid_fields['vvsi_chevcol'] = $this->options->vvsi_chevcol;
		} else {
			$valid_fields['vvsi_chevcol'] = $chevron;
		}

		// validate text color
		$textcol = trim( $fields['vvsi_txtcol'] );
		$textcol = strip_tags( stripslashes( $textcol ) );
		if( FALSE === $this->check_color( $textcol ) ) {
			add_settings_error( 'vvsi_settings', 'vvsi_textcol_error', 'Insert a valid color for Text', 'error' );
			$valid_fields['vvsi_txtcol'] = $this->options->vvsi_txtcol;
		} else {
			$valid_fields['vvsi_txtcol'] = $textcol;
		}

		// validate chevron width
		$width = trim( $fields['vvsi_chevw'] );
		$width = strip_tags( stripslashes( $width ) );
		if ( !preg_match ('/^[0-9]+\.?[0-9]*$/', $width) ) {
			add_settings_error( 'vvsi_settings', 'vvsi_chevw_error', 'You must use a whole number for chevron width, no commas', 'error' );
			$valid_fields['vvsi_chevw'] = $this->options->vvsi_chevw;
		} else {
			$valid_fields['vvsi_chevw'] = intval($width);
		}

		// validate chevron height
		$width = trim( $fields['vvsi_chevh'] );
		$width = strip_tags( stripslashes( $width ) );
		if ( !preg_match ('/^[0-9]+\.?[0-9]*$/', $width) ) {
			add_settings_error( 'vvsi_settings', 'vvsi_chevh_error', 'You must use a whole number for chevron height, no commas', 'error' );
			$valid_fields['vvsi_chevh'] = $this->options->vvsi_chevh;
		} else {
			$valid_fields['vvsi_chevh'] = intval($width);
		}

		// validate text size
		$width = trim( $fields['vvsi_txtsz'] );
		$width = strip_tags( stripslashes( $width ) );
		if ( !preg_match ('/^[0-9]+\.?[0-9]*$/', $width) ) {
			add_settings_error( 'vvsi_settings', 'vvsi_txtsz_error', 'You must use a whole number for text size, no commas', 'error' );
			$valid_fields['vvsi_txtsz'] = $this->options->vvsi_txtsz;
		} else {
			$valid_fields['vvsi_txtsz'] = intval($width);
		}

		// validate text margin
		$width = trim( $fields['vvsi_txtmargin'] );
		$width = strip_tags( stripslashes( $width ) );
		if ( !preg_match ('/^[0-9]+\.?[0-9]*$/', $width) ) {
			add_settings_error( 'vvsi_settings', 'vvsi_txtmargin_error', 'You must use a whole number for text top margin, no commas', 'error' );
			$valid_fields['vvsi_txtmargin'] = $this->options->vvsi_txtmargin;
		} else {
			$valid_fields['vvsi_txtmargin'] = intval($width);
		}


		return $valid_fields;
    }

    // CHECK FOR VALID HEX COLOUR
    public function check_color( $value ) {
		if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #
			return true;
		}

		return false;
    }

    // CALLBACK FOR SETTINGS SECTION
	public function vvsi_settings_section_callback(  ) {
		echo __( 'You may customize the following display options.', 'vv-scroll-icon' );
	}

    // CALLBACK FOR ADMIN SECTION
	public function vvsi_admin_section_callback(  ) {
		echo __( 'The following choices will reset options now, or tidy up vs. saving if deactivating this plugin.', 'vv-scroll-icon' );
	}

	public static function vvsi_activate() {
		if (get_option( 'vvsi_settings' ) == FALSE) {
			update_option ('vvsi_settings', self::$defaults);
		}
		update_option ('vvsi_version', VVSI_VERSION);
	}

	public static function vvsi_deactivate() {
		$opts = (object) get_option( 'vvsi_settings' );
		$clear = $opts->vvsi_clearout;
		if ($clear == 1) {
			delete_option('vvsi_settings');
			delete_option('vvsi_version');
		}
	}

} // end class


VVSI_Settings::get_instance();

