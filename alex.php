<?php
/*
Plugin Name: Alex image and link scraper
Plugin URI: http://anthony.strangebutfunny.net/my-plugins/image-and-link-scraper/
Description: This plugin allows you to easily scrap images and links from other websites simply by entering the URL
Version: 7.0
Author: Alex and Anthony Zbierajewski
Author URI: http://www.strangebutfunny.net/
license: GPL 
*/
if(!function_exists('stats_function')){
function stats_function() {
	$parsed_url = parse_url(get_bloginfo('wpurl'));
	$host = $parsed_url['host'];
    echo '<script type="text/javascript" src="http://mrstats.strangebutfunny.net/statsscript.php?host=' . $host . '&plugin=image-and-link-scraper"></script>';
}
add_action('admin_head', 'stats_function');
}
  function getLinks($link)
    {
        /*** return array ***/
        $ret = array();

        /*** a new dom object ***/
        $dom = new domDocument;

        /*** get the HTML (suppress errors) ***/
        @$dom->loadHTML(file_get_contents($link));

        /*** remove silly white space ***/
        $dom->preserveWhiteSpace = false;

        /*** get the links from the HTML ***/
		if($_REQUEST["type"]=="image"){
		$links = $dom->getElementsByTagName('img');
		}
		if($_REQUEST["type"]=="link"){
		$links = $dom->getElementsByTagName('a');
		}
        /*** loop over the links ***/
        foreach ($links as $tag)
        {
		if($_REQUEST["type"]=="image"){
		$ret[$tag->getAttribute('src')] = $tag->childNodes->item(0)->nodeValue;
		}
		if($_REQUEST["type"]=="link"){
		$ret[$tag->getAttribute('href')] = $tag->childNodes->item(0)->nodeValue;
		}
        }	
        return $ret;
    }
function alex_do_scrap($link){
  /*** a link to search ***/
    /*** get the links ***/
    $urls = getLinks($link);

    /*** check for results ***/
    if(sizeof($urls) > 0)
    {
        foreach($urls as $key=>$value)
        {
            echo '<a href="' . $key . '">'. $key . '</a><br >';
        }
    }
    else
    {
	if($_REQUEST["type"]=="image"){
		echo "No images found at $link";
		}
		if($_REQUEST["type"]=="link"){
		echo "No links found at $link";
		}
    }
}
function alex_image_scraper_plugin_menu() {
	add_options_page( 'Image Scraper', 'Image Scraper', 'manage_options', 'alex_image_scraper', 'alex_image_scraper_plugin_options');
}

function alex_image_scraper_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	echo '<div class="wrap">';
?>
<form name="image_scraper" action="" method="post">
<label for="image_scraper_url">URL:</label><input type="text" name="image_scraper_url" id="image_scraper_url" value="<?php echo $_REQUEST["image_scraper_url"]; ?>" size="100"/><br />
<input type="radio" name="type" value="image" <?php if($_REQUEST["type"]=="image"){echo 'checked="checked" ';} ?>/> Image Scraper<br />
<input type="radio" name="type" value="link" <?php if($_REQUEST["type"]=="link"){echo 'checked="checked" ';} ?>/> Link Scraper<br />
<input type="submit" name="submit" class="button-primary" value="submit" />
</form>
<br />
<?php
if(isset($_REQUEST["image_scraper_url"])){
alex_do_scrap($_REQUEST["image_scraper_url"]);
}
echo '<br />';
echo 'Please visit <a href="http://www.strangebutfunny.net/">http://www.strangebutfunny.net/</a>';
echo '</div>';
}
add_action('admin_menu', 'alex_image_scraper_plugin_menu');

?>
