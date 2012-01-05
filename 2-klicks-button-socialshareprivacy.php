<?php
/*
Plugin Name: 2-Klicks-Button - Socialshareprivacy Plugin
Plugin URI: http://wordpress.org/extend/plugins/2-klicks-button-socialshareprivacy-plugin/
Version: 1.4.0
Description: Das 2-Klicks-Buttons Socialshareprivacy-Plugin von heise.de für Wordpress. Bearbeitet von Smeagol. Grundlage ist das heise.de Plugin Version 1.3
Author: Smeagol45
Author URI: http://sgr.cc/?p=1251
License: GPL v2
*/

require_once 'includes/k2bssp_admin.php';
define(K2BSSP_PREFIX, 'k2bssp');
define(BASE_URL, plugins_url('/2-klicks-button-socialshareprivacy-plugin/'));
$K2BSSP_NUMBER = 1;

$Default_options = array(
	info_link      => 'http://heise.de/-1333879',
	txt_help       => 'Wenn Sie diese Felder durch einen Klick aktivieren, werden Informationen an Facebook, Twitter oder Google in die USA &uuml;bertragen und unter Umst&auml;nden auch dort gespeichert. N&auml;heres erfahren Sie durch einen Klick auf das <em>i</em>.',
	settings_perma => 'Dauerhaft aktivieren und Daten&uuml;ber&shy;tragung zustimmen:',
	cookie_path    => '/',
	cookie_expire  => 365,
	cookie_domain  => '',
	css_path       => BASE_URL . 'socialshareprivacy.css',
	oben		   => 'nein',
	overall		   => 'nein',
	ausschluss_private => 'nein',
	services       => array(
		facebook => array(
			status          => 'on',
			//app_id          => '',
			dummy_img       => BASE_URL . 'images/dummy_facebook.png',
			txt_info        => '2 Klicks f&uuml;r mehr Datenschutz: Erst wenn Sie hier klicken, wird der Button aktiv und Sie k&ouml;nnen Ihre Empfehlung an Facebook senden. Schon beim Aktivieren werden Daten an Dritte &uuml;bertragen &ndash; siehe <em>i</em>.',
			txt_fb_off      => 'nicht mit Facebook verbunden',
			txt_fb_on       => 'mit Facebook verbunden',
			display_name    => 'Facebook',
			referrer_track  => '',
			language        => 'de_DE'
		),
		twitter  => array(
			status          => 'on',
			dummy_img       => BASE_URL . 'images/dummy_twitter.png',
			txt_info        => '2 Klicks f&uuml;r mehr Datenschutz: Erst wenn Sie hier klicken, wird der Button aktiv und Sie k&ouml;nnen Ihre Empfehlung an Twitter senden. Schon beim Aktivieren werden Daten an Dritte &uuml;bertragen &ndash; siehe <em>i</em>.',
			txt_twitter_off => 'nicht mit Twitter verbunden',
			txt_twitter_on  => 'mit Twitter verbunden',
			display_name    => 'Twitter',
			referrer_track  => '',
			tweet_text      => '%title% '
		),
		gplus    => array(
			status          => 'on',
			dummy_img       => BASE_URL . 'images/dummy_gplus.png',
			txt_info        => '2 Klicks f&uuml;r mehr Datenschutz: Erst wenn Sie hier klicken, wird der Button aktiv und Sie k&ouml;nnen Ihre Empfehlung an Google+ senden. Schon beim Aktivieren werden Daten an Dritte &uuml;bertragen &ndash; siehe <em>i</em>.',
			txt_gplus_off   => 'nicht mit Google+ verbunden',
			txt_gplus_on    => 'mit Google+ verbunden',
			display_name    => 'Google+',
			referrer_track  => '',
			language        => 'de'
		)
	)
);

function k2bssp_myausschluss($setting_options) {
	global $post;
	
	if($post->post_status=='private')
		if(!(strtolower(substr($setting_options['ausschluss_private'], 0, 1))!='j'))
			return true; // ja. Private ausschließen.

	$k2bssp_ausschlussarray = explode(';', $setting_options['ausschluss_cats']);
	if(in_category($k2bssp_ausschlussarray, $post)) return true; // Ist in Ausschluss Kathegorie
	
	$k2bssp_ausschlussarray = explode(';', $setting_options['ausschluss_site']);
	foreach($k2bssp_ausschlussarray as $testpostid) {
		if($testpostid==$post->ID)
			return true;	// Ist ausgeschlossene Id
	}
	return false; // anzeigen.
}

function k2bssp_doreplaceoptions($Options) {
	global $post;
	$Options['services']['twitter']['tweet_text'] = str_replace(array(	'%title%',
																		'%content%',
																		'%author%'
															),	array(	preg_replace('/<.+>/', '', $post->post_title),
																		trim(preg_replace('/<.+>/', '', $post->post_content)),
																		get_the_author_meta('display_name', $post->post_author)
															), $Options['services']['twitter']['tweet_text']);
	return $Options;
}

function add_content($content = '') {
	global $Default_options;
	global $K2BSSP_NUMBER;
	global $post;
	
	$myContent = '';
	$setting_options = get_option('k2bssp_options');
	
	if ( !is_singular() ) {
		if(strtolower(substr($setting_options['overall'], 0, 1))!='j')
			return $content;
		else
			$setting_options['uri']=get_permalink($post->id);
	}
	if ( $setting_options ) {
		foreach ( array_keys($Default_options[services]) as $service ) {
			if ( !isset($setting_options['services_' . $service . '_status']) ) {
				$setting_options['services_' . $service . '_status'] = 'off';
			}
		}
		foreach ( $setting_options as $key => $value) {
			if ( isset($value) && strlen($value) ) {
				if ( preg_match('/^services_(facebook|twitter|gplus)_(.*)$/', $key, $matches ) ) {
					$Default_options[services][$matches[1]][$matches[2]] = $value;
				}
				else {
					$Default_options[$key] = $value;
				}
			} 
		}
	}
	$myContent .= '<!-- Beginn von `social share privacy by smeagol.de´ -->';
	if(!k2bssp_myausschluss($setting_options)) {
		$Default_options = k2bssp_doreplaceoptions($Default_options);
		$myContent .= '<div id="socialshareprivacy' . $K2BSSP_NUMBER . '"></div>';
		$myContent .= "
			<script type=\"text/javascript\">
			(function(\$){
				var options = " . json_encode($Default_options) . ";
				options.cookie_domain = document.location.host;
				$(document).ready(function(){
					\$('#socialshareprivacy" . $K2BSSP_NUMBER . "').socialSharePrivacy(options);
				});
			})(jQuery);
			</script>
		";
		$K2BSSP_NUMBER++;
	}
	$myContent .= '<!-- Ende von `social share privacy by smeagol.de´ -->';
	
	if(strtolower(substr($setting_options['oben'], 0, 1))!='j')
		$content .= $myContent;
	else
		$content = $myContent . $content;

	return $content;
}

function enqueue_styles() {
	wp_enqueue_style('2-klicks-button-socialshareprivacy-plugin', '/wp-content/plugins/2-klicks-button-socialshareprivacy-plugin/socialshareprivacy.css');
}
 
function enqueue_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('2-klicks-button-socialshareprivacy-plugin', '/wp-content/plugins/2-klicks-button-socialshareprivacy-plugin/jquery.socialshareprivacy.min.js');
}

function create_admin_menu() {
	
	// page_title, menu_title, capability, menu_slug, callback?
	add_options_page(
		'2 Klicks Buttons Social Share Privacy Settings',
		'2 Klicks Buttons',
		'administrator',
		'2-klicks-button-socialshareprivacy-plugin',
		'k2bssp_settings_page'
	);

	add_action( 'admin_init', array( 'k2bsspAdminPage', 'init' ) );
}

function k2bssp_settings_page() {
	?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"></div>
		<h2>2 Klicks Buttons</h2>
		<form action="options.php" method="post">
		<?php settings_fields('k2bssp-settings-group'); ?>
		<?php do_settings_sections('k2bssp'); ?>
		
	    <p class="submit">
	    <input type="submit" class="button-primary" value="<?php _e('Änderungen übernehmen') ?>" />
	    </p>
		</form>
		<script type="text/javascript">
		(function($){
			$(document).ready(function() {
				$('input[id*="status"]').change(status_watcher).each(status_watcher);
			});
			function status_watcher() {
				var checkbox = $(this);
				checkbox.parents('table.form-table').find('tr:not(:has(input[id*=status]))').each(function() {
					if ( checkbox.attr('checked') ) {
						$(this).show("normal");
					}
					else {
						$(this).hide("normal");
					}
				});
			}
		})(jQuery);
		</script>
	</div>
	
	<?php
}

function start2klicksspbutton() {
	wp_enqueue_script("jquery");
	add_filter('the_content', 'add_content');
	add_action( 'wp_print_scripts', 'enqueue_scripts' );
	add_action( 'wp_print_styles', 'enqueue_styles' );
	// admin seiten adds.
	if ( is_admin() ) {
		add_action('admin_menu', 'create_admin_menu');
	}
}

	start2klicksspbutton();

?>