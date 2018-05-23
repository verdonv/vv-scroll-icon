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


class VVSI_Display {

    /*--------------------------------------------*
     * Attributes
     *--------------------------------------------*/

    /** Refers to a single instance of this class. */
    private static $instance = null;

    /* Saved options */
    public $options;


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
		$this->options = (object)get_option( 'vvsi_settings' );

		// add the css
		add_action( 'wp_enqueue_scripts', array( $this, 'vvsi_que_css' ) );

		// add the html
		add_shortcode('vvscroll', array( $this, 'vvsi_print_scrollicon'));

    }


    /*--------------------------------------------*
     * Functions
     *--------------------------------------------*/

	// ADD THE BASE CSS
	public function vvsi_que_css () {
		$handle = 'vvsi_css';
		$src = VVSI__PLUGIN_URL . 'css/vvsi.css';
		wp_enqueue_style( $handle, $src );
	}

	// RENDER THE HTML
	public function vvsi_print_scrollicon() {
	?>

	<style type="text/css">
		.vvs_chevron {
			width: <?php echo $this->options->vvsi_chevw ?>px;
			height: <?php echo $this->options->vvsi_chevh ?>px;
		}
		.vvs_chevron:before,
		.vvs_chevron:after {
			background: <?php echo $this->options->vvsi_chevcol ?>;
		}
		.vvs_text {
			margin-top: <?php echo $this->options->vvsi_txtmargin ?>px;
			font-family: <?php echo $this->options->vvsi_txtfont ?>;
			font-size: <?php echo $this->options->vvsi_txtsz ?>px;
			color: <?php echo $this->options->vvsi_txtcol ?>;
			text-transform: <?php echo $this->options->vvsi_txttransform ?>;
		}
	</style>

	<div class="vvs_container">
		<div class="vvs_chevron"></div>
		<div class="vvs_chevron"></div>
		<div class="vvs_chevron"></div>
		<div class="vvs_text"><?php echo $this->options->vvsi_txtwords ?></div>
	</div>

	<?php

	}

}


VVSI_Display::get_instance();

