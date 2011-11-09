<?php
/*
Plugin Name: 2-Klicks-Button - Socialshareprivacy Plugin
Version: 1.2.0
Description: Das 2-Klicks-Buttons Socialshareprivacy-Plugin von heise.de für Wordpress. Update by Smeagol (SE = Smeagol Edition)
Plugin URI: http://wordpress.org/extend/plugins/socialshareprivacy/
Author: Michael Knaak, Smeagol
Author URI: http://sgr.cc
*/

define(SOCIALSHAREPRIVACY_VERSION, '1.2.0');

function socialshareprivacy_ausschluss($thepost) {
	$ssp_ausschlussarray = explode(';', get_option('ssp_ausschluss_cats'));
	if(in_category($ssp_ausschlussarray, $thepost)) return true;
	
	$ssp_ausschlussarray = explode(';', get_option('ssp_ausschluss_site'));
	foreach($ssp_ausschlussarray as $testpostid) {
		if($testpostid==$thepost->ID)
			return true;
	}
	return false;
}

function socialshareprivacy($content) {
  global $post;
  $ssp_content = '';
  $sspb_ausschluss = socialshareprivacy_ausschluss($post);
  if (!is_feed() && (is_page() || is_single()) && !$sspb_ausschluss) {
    $ssp_content = '<div id="socialshareprivacy"></div>';
  }
  return $content.$ssp_content;
}


function socialshareprivacy_header() {
  global $post;
  $dir = '/wp-content/plugins/'.basename(dirname(__FILE__));
  $sspb_ausschluss = socialshareprivacy_ausschluss($post);
  if (!is_feed() && (is_page() || is_single()) && !$sspb_ausschluss) {
    echo '<script type="text/javascript" src="'.$dir.'/jquery.socialshareprivacy.min.js"></script>';
    echo '<script type="text/javascript">jQuery(document).ready(function(){if(jQuery(\'#socialshareprivacy\').length > 0){jQuery(\'#socialshareprivacy\').socialSharePrivacy({services:{';
    $content = "";
    // Facebook widget
    $appid = get_option('ssp_facebookappid',false);
    $content .= "facebook:{";
    if ($appid) {
      $content .= "'status':'".(get_option('ssp_facebook','1')=='1'?'on':'off')."',
      'app_id':'$appid',
      'dummy_img':'$dir/images/dummy_facebook.png'";
      if (get_option('ssp_infolink',false)) $content .= ", 'txt_info':'".get_option('ssp_facebooktext',false)."'";
    } else $content .= "'status:'off'";
    $content .= "}";

    $content .= ", twitter:{";
      $content .= "'status':'".(get_option('ssp_twitter','1')=='1'?'on':'off')."',
      'dummy_img':'$dir/images/dummy_twitter.png'";
      if (get_option('ssp_infolink',false)) $content .= ", 'txt_info':'".get_option('ssp_twittertext',false)."'";
      $content .= "}";

    $content .= ", gplus:{";
      $content .= "'status':'".(get_option('ssp_gplus','1')=='1'?'on':'off')."',
      'dummy_img':'$dir/images/dummy_gplus.png'";
      if (get_option('ssp_infolink',false)) $content .= ", 'txt_info':'".get_option('ssp_gplustext',false)."'";
      $content .= "}";

    echo str_replace(array("\n","\r","  "), "", $content);

    echo "},'cookie_domain':'".get_option('siteurl',false)."'";
    if (get_option('ssp_infosettings',false)) echo ", 'settings_perma':'".str_replace("'", "\'", get_option('ssp_infosettings',false))."'";
    if (get_option('ssp_infotext',false)) echo ", 'txt_help':'".str_replace("'", "\'", get_option('ssp_infotext',false))."'";
    if (get_option('ssp_infolink',false)) echo ", 'info_link':'".get_option('ssp_infolink',false)."'";
    if (get_option('ssp_css_layout',false)) echo ", 'css_layout':'".get_option('ssp_css_layout',false)."'";
    echo '});}});</script>';
  }
}

function socialshareprivacy_options(){
  $dir = '/wp-content/plugins/'.basename(dirname(__FILE__));
?>
  <form method="post" action="options.php">
	<img src="<?php ECHO $dir; ?>/images/2-klick-logo.jpg" alt="2-Klicks-Button" width="100" height="100" style="float: left; margin: 0px 30px 0px 10px;" />
    <p>Um den neuen Datenschutzregeln werden mit Hilfe des 2-Klick-Button die Scripts der Like-, twitter- und Google+ Button erst durch ein gewollten Klick des Besuchers geladen. Hierdurch kann der Besucher selber entscheiden ob Daten an Dritte übertragen werden dürfen oder nicht.</p>
    <p>Das 2-Klick Wordpress-Plugin basiert auf dem <i>socialshareprivacy jQuery Script</i> von <a href="http://heise.de/" target="_blank">Heise.de</a> und wurde für die Nutzung auf Wordpress angepasst.</p>
    <p>Weitere Informationen zu dem 2-Klick-Buttons Projekt findet ihr auf: <a href="http://heise.de/-1333879" target="_blank">http://heise.de/-1333879</a></p>
    <div style="clear:both"></div>
    <h3>Welche Social-Communitys sollen angezeigt werden?</h3>
	<div style="padding:0px;">
		<fieldset style="width: 600px; border: 1px solid #999999; padding: 5px;">
			<legend style="padding: 0px 5px;">
				Facebook
			</legend>
			<label for="ssp_facebook" style="display: inline-block; width: 120px">
				Aktivieren
			</label>
			<input id="ssp_facebook" type="checkbox" size="20" name="ssp_facebook" value="1" <?php echo (get_option('ssp_facebook',true)==true?'checked':''); ?> /><br />
			<label for="ssp_facebookappid" style="display: inline-block; width: 120px">
				Facebook App-Id
			</label>
			<input id="ssp_facebookappid" type="text" size="20" name="ssp_facebookappid" value="<?php echo get_option('ssp_facebookappid'); ?>" />
			<div style="font-size: 11px; margin-left: 120px;">
				Um den Like-Button verwenden zu können benötigst du ein verifiziertes Facebook-Konto und eine gültige App-Id welche du dir <a href="http://developers.facebook.com/docs/reference/plugins/like/" target="_blank">hier erstellen</a> kannst.
			</div>
			<label for="ssp_facebooktext" style="display: inline-block; width: 120px">
				Infotext
			</label>
			<textarea id="ssp_facebooktext" name="ssp_facebooktext" style="width: 300px; height: 50px; vertical-align: top;"><?php
				echo get_option('ssp_facebooktext',''); 
			?></textarea>
			<div style="font-size: 11px; margin-left: 120px;">
				Leer lassen für Standard-Text
			</div>
		</fieldset>
		<fieldset style="width: 600px; border: 1px solid #999999; padding: 5px;">
			<legend style="padding: 0px 5px;">
				twitter
			</legend>
			<label for="ssp_twitter" style="display: inline-block; width: 120px">
				Aktivieren
			</label>
			<input id="ssp_twitter" type="checkbox" size="20" name="ssp_twitter" value="1" <?php echo (get_option('ssp_twitter',true)==true?'checked':''); ?> /><br />
			<label for="ssp_twittertext" style="display: inline-block; width: 120px">
				Infotext
			</label>
			<textarea id="ssp_twittertext" name="ssp_twittertext" style="width: 300px; height: 50px; vertical-align: top;"><?php 
				echo get_option('ssp_twittertext',''); 
			?></textarea>
			<div style="font-size: 11px; margin-left: 120px;">
				Leer lassen für Standard-Text
			</div>
		</fieldset>
		<fieldset style="width: 600px; border: 1px solid #999999; padding: 5px;">
			<legend style="padding: 0px 5px;">
				Google Plus
			</legend>
			<label for="ssp_gplus" style="display: inline-block; width: 120px">
				Aktivieren
			</label>
			<input id="ssp_gplus" type="checkbox" size="20" name="ssp_gplus" value="1" <?php echo (get_option('ssp_gplus',true)==true?'checked':''); ?> /><br />
			<label for="ssp_gplustext" style="display: inline-block; width: 120px">
				Infotext
			</label>
			<textarea id="ssp_gplustext" name="ssp_gplustext" style="width: 300px; height: 50px; vertical-align: top;"><?php
				echo get_option('ssp_gplustext','');
			?></textarea>
			<div style="font-size: 11px; margin-left: 120px;">
				Leer lassen für Standard-Text
			</div>
		</fieldset>
		<fieldset style="width: 600px; border: 1px solid #999999; padding: 5px;">
			<legend style="padding: 0px 5px;">
				Allgemeines
			</legend>
			<label for="ssp_infosettings" style="display: inline-block; width: 120px">
				Überschrift Infobox
			</label>
			<input type="text" id="ssp_infosettings" name="ssp_infosettings" style="width: 300px;" value="<?php echo get_option('ssp_infosettings',''); ?>" /><br />
			<label for="ssp_infotext" style="display: inline-block; width: 120px">
				Infotext
			</label>
			<textarea id="ssp_infotext" name="ssp_infotext" style="width: 300px; height: 50px; vertical-align: top;"><?php
				echo get_option('ssp_infotext','');
			?></textarea><br />
			<label for="ssp_infolink" style="display: inline-block; width: 120px">
				Infolink
			</label>
			<input type="text" id="ssp_infolink" name="ssp_infolink" style="width: 300px;" value="<?php echo get_option('ssp_infolink',''); ?>" /><br />
			<label for="ssp_css_layout" style="display: inline-block; width: 120px">
				Layout
			</label>
			<select id="ssp_css_layout" name="ssp_css_layout" style="width: 200px;">
				<option value="">Standard</option>
				<option value="nofloat"<?php echo (get_option('ssp_css_layout',true)==true?' selected':''); ?>>Untereinander</option>
			</select><br />
			<div style="font-size: 11px; margin-left: 120px;">
				Leer lassen für Standard-Werte
			</div>
		</fieldset>
		<fieldset style="width: 600px; border: 1px solid #999999; padding: 5px;">
			<legend style="padding: 0px 5px;">
				Ausschliessen
			</legend>
			<label for="ssp_ausschluss_ids" style="display: inline-block; width: 120px">
				Seitennummern
			</label>
			<input id="ssp_ausschluss_ids" type="text" size="20" name="ssp_ausschluss_site" value="<?php echo get_option('ssp_ausschluss_site'); ?>" />
			<div style="font-size: 11px; margin-left: 120px;">
				Verschiedene Seiten Ids ";" getrennt schreiben. Leer lassen um auf allen Seiten die 2-Klick-Buttons anzuzeigen.
			</div><br />
			<label for="ssp_ausschluss_catids" style="display: inline-block; width: 120px">
				Kategorien
			</label>
			<input id="ssp_ausschluss_catids" type="text" size="20" name="ssp_ausschluss_cats" value="<?php echo get_option('ssp_ausschluss_cats'); ?>" />
			<div style="font-size: 11px; margin-left: 120px;">
				Verschiedene Kategorien ";" getrennt schreiben. Leer lassen um in allen Kategorien die 2-Klick-Buttons anzuzeigen.<br />
				Kategorien können als Name und/oder ID angegeben werden.
			</div><br />
		</fieldset>
	</div>
	<div style="clear:both"></div>
	<p class="submit">
		<input type="submit" class="button-primary" value="Speichern" />
		<?php wp_nonce_field('update-options'); ?>
		<input type="hidden" name="page_options" value="ssp_facebook,ssp_facebookappid,ssp_facebooktext,ssp_twitter,ssp_twittertext,ssp_gplus,ssp_gplustext,ssp_cookies,ssp_infotext,ssp_infolink,ssp_infosettings,ssp_css_layout,ssp_ausschluss_site,ssp_ausschluss_cats" />
        <input type="hidden" name="action" value="update" />
	</form>
<?php
}

add_action('wp_head', 'socialshareprivacy_header');
add_filter('the_content', 'socialshareprivacy');
add_action('admin_menu', 'socialshareprivacy_addmenu');
add_action('wp_print_styles', 'socialshareprivacy_css');

function socialshareprivacy_addmenu() {
  add_options_page("2-Klicks-Buttons", "2-Klicks-Buttons", "administrator", "socialshareprivacy", "socialshareprivacy_options");
}
function socialshareprivacy_css() {
	wp_enqueue_style('socialshareprivacy', '/wp-content/plugins/'.basename(dirname(__FILE__)).'/socialshareprivacy.css', false, SOCIALSHAREPRIVACY_VERSION, 'screen');
}

?>
