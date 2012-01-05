<?php
/*
 * @author Smeagol45
 * @homepage http://sgr.cc/?p=1251
 * @version 1.1.0
 * @license: G.P.L. 2.0
 * @pluginname: 2-Klicks-Button - Socialshareprivacy Plugin
 */

class k2bsspAdminPage {
	
	private $input_fields = array();
	private $sections     = array();
	public static $page   = K2BSSP_PREFIX;
	
	public static function init() {
		new k2bsspAdminPage();
	}

	public function get_settings_group() {
		return K2BSSP_PREFIX . '_options';
	}
	
	public function add_section($section) {
		array_push($this->sections, $section);
	}

	public function __construct() {
		
		register_setting( 'k2bssp-settings-group', $this->get_settings_group() );

		//  $id, $title, $callback, $page
		$this->add_section( new k2bsspSection('g', 'Allgemeines', array(__CLASS__, 'admin_general_description'), k2bsspAdminPage::$page));
		$this->add_section( new k2bsspSection('fb', 'Facebook', array(__CLASS__, 'admin_facebook_description'), k2bsspAdminPage::$page));
		$this->add_section( new k2bsspSection('tw', 'Twitter', array(__CLASS__, 'admin_twitter_description'), k2bsspAdminPage::$page));
		$this->add_section( new k2bsspSection('gp', 'Google+', array(__CLASS__, 'admin_gplus_description'), k2bsspAdminPage::$page));
		$this->add_section( new k2bsspSection('ot', '', array(__CLASS__, 'admin_hinweise_description'), k2bsspAdminPage::$page));
		
	}
	
	function admin_general_description() {
		?><p><b>Wenn Optionen leergelassen werden, wird der Standardwert aus dem Heise.de Plugin verwendet.</b></p><?php
	}
	
	function admin_facebook_description() {
		?><p>Einstellungen für Facebook. Es ist keine App-ID mehr notwendig.</p><?php
	}
	
	function admin_twitter_description() {
		?><p>Einstellungen für Twitter.</p><?php
	}
	
	function admin_gplus_description() {
		?><p>Einstellungen für Google+.</p><?php
	}
	
	function admin_hinweise_description() {
		?><br /><p class="description">* dieser Text wird angezeigt wenn keine Bilder angezeigt werden können.</p><?php
	}
}

class k2bsspSection {
	
	private $name;
	private $caption;
	private $description_callback;
	
	public function __construct($name, $caption, $description_callback ) {
		$this->name = $name;
		$this->caption = $caption;
		$this->description_callback = $description_callback;
		add_settings_section( K2BSSP_PREFIX . '_' . $name, $caption, $description_callback, k2bsspAdminPage::$page);
		$this->create_content();
	}
	
	public function create_content() {
		new InputField( 'info_link', 'Info-Link', 'k2bssp_g', 'Link zu detaillierter Datenschutz-Info', 'code' );
		new InputField( 'txt_help', 'Hilfetext', 'k2bssp_g', 'Info Text des <em>i</em>-Icons' );
		new InputField( 'settings_perma', 'Info-Text', 'k2bssp_g', 'Überschrift des Einstellungsmenüs' );
		new InputField( 'cookie_domain', 'Cookie-Domain', 'k2bssp_g', 'Domain, für die das Cookie gültig ist. Standard (nicht ausgefüllt): aktuelle Domain', 'code' );
		new InputField( 'cookie_path', 'Cookie-Path', 'k2bssp_g', 'Pfad der Gültigkeit des Cookies', 'code' );
		new InputField( 'cookie_expire', 'Cookie-Expire-Time', 'k2bssp_g', 'Dauer, die das Cookie gültig ist, in Tagen', 'code' );
		new InputField( 'oben', 'Buttons oberhalb des Artikels anzeigen', 'k2bssp_g', 'Ja/Nein - Die "Share Buttons" werden wenn "Ja" oberhalb der Artikel angezeigt. Ansonsten werden sie weiterhin unterhalb der Artikel angezeigt.', 'code');
		new InputField( 'overall', 'Buttons auch auf der Startseite anzeigen', 'k2bssp_g', 'Ja/Nein - Die "Share Buttons" werden wenn "Ja" auch auf der Startseite und nicht nur auf einzelnen Seiten/Artikeln angezeigt.', 'code');
		new InputField( 'ausschluss_cats', 'Kategorien ausschließen', 'k2bssp_g', 'Hier können sie ";"-getrennt verschiedene Kategorien ausschließen. ', 'code' );
		new InputField( 'ausschluss_site', 'Seiten ausschließen', 'k2bssp_g', 'Hier können sie ";"-getrennt verschiedene Ids von Seiten ausschließen. ', 'code' );
		new InputField( 'ausschluss_private', 'Private Artikel/Seiten ausschließen', 'k2bssp_g', 'Ja/Nein - Die Share Buttons werden wenn "Ja" nicht auf Privaten Artikeln/Seiten angezeigt.', 'code' );
		new InputField( 'services_facebook_status', '<b>Facebook Button anzeigen</b>', 'k2bssp_fb', 'Ja/Nein', '', 'checkbox');
		new InputField( 'services_facebook_display_name', 'Anzeigename', 'k2bssp_fb', 'Schreibweise des Service in den Optionen' );
		new InputField( 'services_facebook_txt_info', 'Info Text', 'k2bssp_fb', 'Info Text für den  Facebook Empfehlen Button' );
		new InputField( 'services_facebook_txt_fb_on', 'Statusmeldung <code>on</code>', 'k2bssp_fb', 'Text* der Schalter-Grafik im eingeschalteten Zustand, in der Regel nicht sichtbar für den Benutzer');
		new InputField( 'services_facebook_txt_fb_off', 'Statusmeldung <code>off</code>', 'k2bssp_fb', 'Text* der Schalter-Grafik im ausgeschalteten Zustand, in der Regel nicht sichtbar für den Benutzer');
		new InputField( 'services_facebook_referrer_track', 'Referrer Track', 'k2bssp_fb', 'Wird ans Ende der URL gehängt, kann zum Tracken des Referrers genutzt werden', 'code' );
		new InputField( 'services_facebook_language', 'Sprache', 'k2bssp_fb', 'Spracheinstellung, etwa "de_DE"', 'code');
		new InputField( 'services_twitter_status', '<b>Twitter Button anzeigen</b>', 'k2bssp_tw', 'Ja/Nein', '', 'checkbox' );
		new InputField( 'services_twitter_tweet_text', 'Tweet-Text', 'k2bssp_tw', 'Der Text welcher Getwittert wird. Ersetzt wird <code>%title%</code>, <code>%content%</code> und <code>%author%</code> mit dem jeweiligen Post Inhalt. Bei überschreitung von 140 Zeichen wird beim letzten Leerzeichen abgeschnitten und <code>...</code> angehängt. Am ende jedes Tweets wird immer der Link zum Post angefügt.');
		new InputField( 'services_twitter_display_name', 'Anzeigename', 'k2bssp_tw', 'Schreibweise des Service in den Optionen' );
		new InputField( 'services_twitter_txt_info', 'Info-Text', 'k2bssp_tw', 'Info Text des Twitter Buttons' );
		new InputField( 'services_twitter_txt_twitter_on', 'Statusmeldung <code>on</code>', 'k2bssp_tw', 'Text* der Schalter-Grafik im eingeschalteten Zustand, in der Regel nicht sichtbar für den Benutzer');
		new InputField( 'services_twitter_txt_twitter_off', 'Statusmeldung <code>off</code>', 'k2bssp_tw', 'Text* der Schalter-Grafik im ausgeschalteten Zustand, in der Regel nicht sichtbar für den Benutzer');
		new InputField( 'services_twitter_referrer_track', 'Referrer Track', 'k2bssp_tw', 'Wird ans Ende der URL gehängt, kann zum Tracken des Referrers genutzt werden', 'code' );
		new InputField( 'services_gplus_status', '<b>Google+-Button anzeigen</b>', 'k2bssp_gp', 'Ja/Nein', '', 'checkbox');
		new InputField( 'services_gplus_display_name', 'Anzeigename', 'k2bssp_gp', 'Schreibweise des Service in den Optionen' );
		new InputField( 'services_gplus_txt_info', 'Info-Text', 'k2bssp_gp', 'Info Text des Google+ Buttons' );
		new InputField( 'services_gplus_txt_gplus_on', 'Statusmeldung <code>on</code>', 'k2bssp_gp', 'Text* der Schalter-Grafik im eingeschalteten Zustand, in der Regel nicht sichtbar für den User');
		new InputField( 'services_gplus_txt_gplus_off', 'Statusmeldung <code>off</code>', 'k2bssp_gp', 'Text* der Schalter-Grafik im ausgeschalteten Zustand, in der Regel nicht sichtbar für den User');
		new InputField( 'services_gplus_referrer_track', 'Referrer Track', 'k2bssp_gp', 'Wird ans Ende der URL gehängt, kann zum Tracken des Referrers genutzt werden', 'code' );
		new InputField( 'services_gplus_language', 'Sprache', 'k2bssp_gp', 'Spracheinstellung, etwa "de"', 'code');
	}
}

class InputField {
	private $name;
	private $caption;
	private $section;
	private $description;
	private $style_class;
	private $type;
	public $value;

	public function __construct($name, $caption, $section, $description, $style_class = '', $type = 'text') {
		$this->name        = $name;
		$this->caption     = $caption;
		$this->section     = $section;
		$this->description = $description;
		$this->style_class = $style_class;
		$this->type        = $type;

		add_settings_field(
			$this->get_id(),
			$this->caption,
			array(__CLASS__, 'create_field'),
			k2bsspAdminPage::$page,
			$this->section,
			array ( label_for => $this->name, input_field => $this )
		);
	}

	public function get_input_element() {
		$options = get_option('k2bssp_options');
		return sprintf(
			'<input id="%s" name="%s_options[%s]" type="%s" value="%s" class="%s %s" %s/> <span class="description">%s</span>',
			$this->name,
			K2BSSP_PREFIX,
			$this->name,
			$this->type,
			$this->value,
			($this->type == "checkbox" ? '' : 'regular-text'),
			$this->style_class,
			($this->type == "checkbox" ? checked( $options[$this->name], 'on', false ) : ''),
			$this->description
		);
	}

	public function get_id() {
		return K2BSSP_PREFIX . $this->name;
	}
	
	public function create_field($args) {
		global $Default_options;
		$field   = $args[input_field];
		$options = get_option('k2bssp_options');
		$value   = $options[$field->name];
		if ( !$value ) {
			$value = $Default_options[$field->name];
			if ( !isset($value) ) {
				if ( preg_match('/^services_(facebook|twitter|gplus)_(.*)$/', $field->name, $matches ) ) {
					$value = $Default_options[services][$matches[1]][$matches[2]];
				}
			}
		}
		$field->value = $value;
		echo $field->get_input_element();
	}
}
?>