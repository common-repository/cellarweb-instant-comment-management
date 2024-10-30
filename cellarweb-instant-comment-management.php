<?php
/*
Plugin Name: CellarWeb Instant Comment Management
Contributors: rhellewellgmailcom
Donate link: https://cellarweb.com/
Author URI: https://www.cellarweb.com
Plugin URI: https://www.cellarweb.com/wordpress-plugins/
Requires at least: 4.9
Tested up to:      6.3
Requires PHP:      7.3
Description: Instantly manage comments from the front end, with quick spam/trash/delete links added next to the standard edit link.
Author: Rick Hellewell
Version: 1.01
Author URI: https://www.cellarweb.com/
 */

// ============================================================================

// So we can use the version number on the plugin info page
define("CWICM_VERSION_NUMBER", "1.01 (15 Jul 2022)");
// checkmark icon

// ============================================================================
// add spam/trash/delete links to comments for admins only
//      based on https://gist.github.com/5ally/88320109c9bb534d89e47231cf500c98
// --------------------------------------------------------------

add_action('wp_enqueue_scripts', 'CWICM_ajax_action');
function CWICM_ajax_action() {
	// Do nothing if we're not on the single post page, or if the post is
	// password-protected and a correct password has not been provided, or
	// if the current user is not allowed to moderate comments.
	if (!is_singular() || post_password_required(get_queried_object()) ||
		!current_user_can('moderate_comments')
	) {
		return;
	}

	wp_enqueue_script(
		'CWICM_comment_ajax_actions',
		plugin_dir_url(__FILE__) . '/js/cwicm_comment_ajax_actions.js',
		array('jquery', 'wp-api-request'),
		wp_get_theme()->get('Version'),
		true
	);
}

// --------------------------------------------------------------
// end of gist code
// --------------------------------------------------------------
// --------------------------------------------------------------

// ============================================================================
// Add settings link on plugin page
// ----------------------------------------------------------------------------
function CWICM_settings_link($links) {
	$settings_link = '<a href="options-general.php?page=CWICM_settings" title="CellarWeb Instant Comment Management">Instant Comment Management Info/Usage</a>';
	array_unshift($links, $settings_link);
	return $links;
}

// ============================================================================
// link to the settings page
// ----------------------------------------------------------------------------
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'CWICM_settings_link');

// ============================================================================
//  build the class for all of this
// ----------------------------------------------------------------------------
class CWICM_Settings_Page {

// start your engines!
	public function __construct() {
		add_action('admin_menu', array($this, 'CWICM_add_plugin_page'));
	}

// add options page
	public function CWICM_add_plugin_page() {
// This page will be under "Settings"
		add_options_page('Instant Comment Management', 'Instant Comment Management Usage', 'manage_options', 'CWICM_settings', array($this, 'CWICM_create_admin_page'));
	}

// options page callback
	public function CWICM_create_admin_page() {
		// Set class property
		$this->options = get_option('CWICM_options');
		?>

<div align='center' class = 'CWICM_header'>
     <img src="<?php echo plugin_dir_url(__FILE__); ?>assets/banner-1000x200.jpg" width="95%"  alt="" class='CWICM_shadow'>
</div>
    <p align='center'>Version: <?php echo CWICM_VERSION_NUMBER; ?></p>
<div >
    <div class="CWICM_options">
        <?php CWICM_info_top();?>
    </div>
    <div class='CWICM_sidebar'>
        <?php CWICM_sidebar();?>
    </div>
</div>
<!-- not sure why this one is needed ... -->
<div class="CWICM_footer">
    <?php CWICM_footer();?>
</div>
<?php }

// print the Section text
	public function CWICM_print_section_info() {
		print '<h3><strong>Information about Block Comment Spam Bots from CellarWeb.com</strong></h3>';
	}
}
// end of the class stuff
// ============================================================================

// ============================================================================
// if on the admin pages, set up the settings page
// ----------------------------------------------------------------------------

if (is_admin()) { // display info on the admin pages  only
	$my_settings_page = new CWICM_Settings_Page();
	// ----------------------------------------------------------------------------
	// supporting functions
	// ----------------------------------------------------------------------------
	//  display the top info part of the settings page
	// ----------------------------------------------------------------------------
	function CWICM_info_top() {
		?>
<h1 align='center'>CellarWeb Instant Comment Management</h1>
<p>Adds quick links to your comment list on the front end to manage the comments. There are links for <b>Spam</b> / <b>Trash</b> / <b>Delete</b> that are placed next to the <b>Edit</b> link that you will see as administrator. Clicking the link will immediately perform that action, and that comment will disappear from the comment display. Only users with the "Manage Comment" role (usually just Administrators) will see those links.</p>
<p>This is a fast and easy way to handle comments on a busy site with many comments.</p>
<p>The links that are shown next to the <b>Edit</b> link perform these action:
<ul class="CWICM_list_disc">
    <li><b>Spam</b> - mark the comment as spam.</li>
    <li><b>Trash</b> - mark the comment as trash.</li>
    <li><b>Delete</b> - Permanently delete the comment (after a confirmation popup). This action is not reversable.</li>
    </ul></p>
    <p>You are able to restore the <b>Spam</b> and <b>Trash</b> comments via the Admin Comment interface. Only the <b>Delete</b> action will permanently remove the comment.</p>
<p>As you click the link, the action is done, and the comment is removed from the display. Since you don't have to go to the intermediate Admin screen, you don't have to reposition or scroll the screen to get to the next comment in the list. Just click the link and it's done.</p>
<p>Note that the comment count at the top of the list is not updated in this version. </p>
<p>Here is an example screenshot of the comment as seen on the 'front end' (publicly viewed page) that shows the additional links available to the administrator only:</p>
<p align='center'><img src="<?php echo plugin_dir_url(__FILE__); ?>/assets/screenshot-1.jpg" alt="" style="width:80%;"></p>
<p>There are no settings for this plugin. If you do not want this capability, just deactivate the plugin.</p>
<hr />
<p>Tell us how theplugin works for you - leave a review or rating on our plugin page. You can get support or ask questions via the plugin support page.</p>
<p><b>Checkout the sidebar</b> for info on our other plugins - including quick and easy ways to block contact form and comment spam, and more!</p>
<hr>

<?php CWICM_footer();
		return;}

// ============================================================================
	// display the copyright info part of the admin  page
	// ----------------------------------------------------------------------------
	function CWICM_info_bottom() {
		// print copyright with current year, never needs updating
?>
		<hr><div style="background-color:#9FE8FF;padding-left:15px;padding:10px 0 10px 0;margin:15px 0 15px 0;">
<p align="center">Copyright &copy; 2026 - <?php echo date("Y");?>  by Rick Hellewell and <a href="https://CellarWeb.com" title="CellarWeb" >CellarWeb.com</a>;, All Rights Reserved. Released under GPL2 license.</p></div><hr>
 <?php
		return;
	}

	// end  copyright ---------------------------------------------------------

	// ----------------------------------------------------------------------------
	// ``end of admin area
	//here's the closing bracket for the is_admin thing
}

// ============================================================================
// add the css to the settings page
// ----------------------------------------------------------------------------
function CWICM_init() {
	wp_register_style('CWICM_namespace', plugins_url('/css/settings.css', __FILE__), array(), time());
	wp_enqueue_style('CWICM_namespace'); // gets the above css file in the proper spot
}

add_action('init', 'CWICM_init');

// ============================================================================
//  settings page sidebar content
// ----------------------------------------------------------------------------
function CWICM_sidebar() {
	?>
    <h3 align="center">But wait, there's more!</h3>
    <p><b>Need to stop Comment spam?</b> We've got a plugin that does that - install it and comment spam will stop immediately. Very effective. Does not require any other actions or configuration, and your comment form will look the same. It just works! See details here: <a href="https://wordpress.org/plugins/block-comment-spam-bots/" target="_blank" title="Block Comment Spam Bots">Block Comment Spam Bots</a> .</p>
<p><b>Plus these Contact Form spam-blocking plugins:</b></p>
<p><b>If you want to block bots from your contact form</b>, head over to our <a href="https://www.FormSpammerTrap.com" target="_blank" title="FormSpammerTrap - blocks Contact form spammers">FormSpammerTrap</a> site. <i>Works on WP and non-WP sites.</i> Takes a bit of programming, but we can help with that. Full docs and implementation instructions. Just request the free code via the site's Contact form. It's the most powerful and effective way to block Contact Form spammers!</p>
    <p><a href="https://wordpress.org/plugins/formspammertrap-for-comments/" target="_blank"><strong>FormSpammerTrap for Comments</strong></a>: reduces spam without captchas, silly questions, or hidden fields - which don't always work. You can also customize the look of your comment form. Uses the same techniques as our Block Comment Spam Bots plugin. </p>
    <p><a href="https://wordpress.org/plugins/formspammertrap-for-contact-form-7/" target="_blank"><strong>FormSpammerTrap for Contact Form 7</strong></a>: reduces spam when you use Contact Form 7 forms. All you do is add a little shortcode to the contact form.</p>
    <hr />
   <p>For <strong>multisites</strong>, we've got:
    <ul>
        <li><strong><a href="https://wordpress.org/plugins/multisite-comment-display/" target="_blank">Multisite Comment Display</a></strong> to show all comments from all subsites.</li>
        <li><strong><a href="https://wordpress.org/plugins/multisite-post-reader/" target="_blank">Multisite Post Reader</a></strong> to show all posts from all subsites.</li>
        <li><strong><a href="https://wordpress.org/plugins/multisite-media-display/" target="_blank">Multisite Media Display</a></strong> shows all media from all subsites with a simple shortcode. You can click on an item to edit that item. </li>
    </ul>
    </p>
    <hr />
    <p><b>Other Plugins</b></p>
     <p>We've got a <a href="https://wordpress.org/plugins/simple-gdpr/" target="_blank"><strong>CellarWeb Simple GDPR</strong></a> plugin that displays a GDPR banner ('cookie consent') for the user to acknowledge. And it creates a generic Privacy page, and will put that Privacy Page link at the bottom of all pages.</p>
<p><b>An option will do server-side Google Analytics (GA).</b> Did you know what GA is blocked by many ad-blockers - diluting the accuracy of your analytics? This will ensure that every visitor access is captured by GA - and with anonymized information that doesn't store personal information.</p>
<P>For just Google Server Side Analytics - using Googles "UA" analytics code - check out our new <a href="https://wordpress.org/plugins/cellarweb-server-side-analytics/" target="_blank" title="CellarWeb Server Side Analytics"><b>CellarWeb Server Side Analytics</b></a> plugin. Simple and effective - and analytics that are not blocked by ad blockers.</P>
    <p>How about our <strong><a href="https://wordpress.org/plugins/url-smasher/" target="_blank">URL Smasher</a></strong> which automatically shortens URLs in pages/posts/comments?</p>
<p><b>Or one that automatically adds your Affiliate link to all Amazon links - in posts and comments?</b> That's what our Amazolinkentor plugin does, and you can get it <a href="https://wordpress.org/plugins/amazolinkenator/" target="_blank" title="Automatic Affiliate Code Insertion">here</a>.</p>
    <hr />
    <p><strong>They are all free and fully featured!</strong></p>
    <hr />
    <p>I don't drink coffee, but if you are inclined to donate any amount because you like my WordPress plugins, go right ahead! I'll grab a nice hot chocolate, and maybe a blueberry muffin.  Maybe another bag of Cherry Jelly Belliees. And a nice <a href="https://wordpress.org/plugins/simple-cwssa/#reviews" target="_blank" title="CellarWeb Server Side Analytics Plugin Reviews page">review on the plugin page</a> is always appreciated! Or just contact us at <a href="https://cellarweb.com/contactus/" target="_blank">https://cellarweb.com/contactus/</a>. </p>
    <div align="center">
        <?php CWICM_donate_button();?>
    </div>
    <p align='center'><b>Thanks!  <a href="https://www.RichardHellewell.com" target="_blank" title="Richard Hellewell author site">Richard Hellewell</a>, somewhere opposite Mutiny Bay WA.</b></p>
    <hr />
    <p><strong>Privacy Notice</strong>: This plugin does not store or use any personal information or cookies.</p>

<?php
CWICM_cellarweb_logo();
	return;
}

// ============================================================================
// show the logo in the sidebar
// ----------------------------------------------------------------------------
function CWICM_cellarweb_logo() {
	?>
 <p align="center"><a href="https://www.cellarweb.com" target="_blank" title="CellarWeb.com site"><img src="<?php echo plugin_dir_url(__FILE__); ?>assets/cellarweb-logo-2022.jpg"  width="90%" class="CWICM_shadow" ></a></p>
 <?php
return;
}

// ============================================================================
// show the footer
// ----------------------------------------------------------------------------
function CWICM_footer() {
	?>
<p align="center"><strong>Copyright &copy; 2016- <?php echo date('Y'); ?> by Rick Hellewell and <a href="https://CellarWeb.com" title="CellarWeb" >CellarWeb.com</a> , All Rights Reserved. Released under GPL2 license. <a href="https://cellarweb.com/contact-us/" target="_blank" title="Contact Us">Contact us page</a>.</strong></p>
<?php
return;
}
// ============================================================================
// PayPal donation button for settings sidebar (as of 25 Jan 2022)
// ----------------------------------------------------------------------------
function CWICM_donate_button() {
	?>
<form action="https://www.paypal.com/donate" method="post" target="_top">
<input type="hidden" name="hosted_button_id" value="TT8CUV7DJ2SRN" />
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
</form>

<?php
return;
}

// ============================================================================
// end of file
// ============================================================================
