<?php

class GWFO_Admin_Page extends scbAdminPage {
	function setup() {
		$this->args = array(
			'page_title' => 'GWF Optimizer',
		);
	}


	function page_content() {
		echo html( 'h3', 'Select how you want Google Web Fonts to be loaded' );
		echo $this->form_table( array(
			array(
				'title' => 'Enable or Disable GWFO?',
				'type' => 'radio',
				'name' => 'gwfo_enabled',
				'value' => array(
					'enabled' => 'Enable',
					'disabled' => 'Disable'
				),
			),
			array(
				'title' => 'Select your font loader',
				'type' => 'radio',
				'name' => 'import_type',
				'value' => array(
					'html_link' => 'HTML links in the header (no FOUT)',
					'webfont_script' => 'Javascript Web Font Loader (higher PageSpeed Score)'
				),
			),
			array(
				'title' => 'Custom font family names<br/><span style="font-weight: normal; font-size: smaller">separate multiple values with commas</span>',
				'type' => 'text',
				'name' => 'custom_font_names',
                'value' => NULL,
			),
			array(
				'title' => 'Font family URLs<br/><span style="font-weight: normal; font-size: smaller">see <a href="https://github.com/typekit/webfontloader#custom" target="_blank">font naming conventions here</a><br/>separate multiple values with commas</span>',
				'type' => 'text',
				'name' => 'custom_font_urls',
                'value' => NULL,
			),
		) );
	}
}

