<?php
/*
Plugin Name: owagu.com clickbank marketplace loader
Plugin URI: http://datafeeds.owagu.com
Description: This is the clickbank marketplace loader by owagu.com. This free spinoff from the massive datafeed loader 
will automatically retrieve the latest clickbank marketplace entries, unzip them, and create posts out of them with your ID embedded inside. Fully
SEO optimized with automated categories and readable by search engines. Some limitations in the free loader can be lifted with our commercial version
from http://datafeeds.owagu.com
Author: pete scheepens
Author URI: http://datafeeds.owagu.com
Version: 2.1
*/

add_action('admin_menu', 'owagu_clickbank_loader');

function owagu_clickbank_loader() {
  add_options_page('owagu Plugin Options', 'OWAGU clickbank Loader', 'manage_options', 'owagu_cb_loader', 'owagu_CB_options');
}
function owagu_CB_options() {
  if (!current_user_can('manage_options'))  {wp_die( __('You do not have sufficient permissions to access this page.') );}

$qwerty = "THIS IS VERSION 2.1 of the commercial CLICKBANK loader";?>
<div style="width:60%;text-align:CENTER;">
owagu.com massive datafeed loader fork - by: Pete Scheepens - <a href="http://datafeeds.owagu.com">datafeeds.owagu.com</a><hr>
<div style="float:left;width:100%;color:black;text-align:CENTER;background-color:#DAFFCC;">
<div style="color:white;text-align:left;background-color:#000000;">
Final preview & data-pumping  -  using owagu.com's CLICKBANK template and loader files  - 
</div>
	<img src="<? echo plugins_url('/owagu-sa-clickbank-loader/cb.jpg',_FILE_)?>">
	<br>This plugin already comes with a copy of the datafeed. 
	<br>If you wanted to retrieve the latest version of the datafeed you can push the "retrieve fresh feed" button below. We recommend you do NOT do this unless something does not work correctly.<br>
	<form method="post" action="<?php echo $PHP_SELF;?>">
	<input type="hidden" value="here" name="dlcb">
<input type="submit" value="retrieve fresh feed" name="submit"><br /> </form>
<?if ($_POST['dlcb'] == "here") {
echo "<br>Attempting to download the clickbank datafeed";
// first download attempt
$url  = 'http://www.clickbank.com/feeds/marketplace_feed_v1.xml.zip';
    $path = '../wp-content/plugins/owagu-sa-clickbank-loader/clickbankfeed.zip';
    $fp = fopen($path, 'w'); 
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FILE, $fp); 
    $data = curl_exec($ch);
    curl_close($ch);
    fclose($fp);
// second download attempt
if (is_file('../wp-content/plugins/owagu-sa-clickbank-loader/clickbankfeed.zip')) 
{ echo "<br><b>I have downloaded the clickbank datafeed</b>";} else {
echo "<br><b>I could not get the feed using curl, attempting another way</b>";	
$file = 'http://www.clickbank.com/feeds/marketplace_feed_v1.xml.zip';	
$newfile = '../wp-content/plugins/owagu-sa-clickbank-loader/clickbankfeed.zip';
if (!copy($file, $newfile)) {echo "failed to copy $file...\n";} 
}
if (is_file('../wp-content/plugins/owagu-sa-clickbank-loader/clickbankfeed.zip')) {
{echo "<br><b>Found zip file ! trying to unzip ...</b>"; exec("/usr/bin/unzip ../wp-content/plugins/owagu-sa-clickbank-loader/clickbankfeed.zip", $aOutput); 
     print_r($aOutput); 
	 

	require_once('pclzip.lib.php'); 
	echo "<br>before setting upload dire".getcwd() . "\n"; 
	$upload_dir = '../wp-content/plugins/owagu-sa-clickbank-loader/';
	$filename = 'clickbankfeed.zip';
	$zip_dir = basename($filename, ".zip");
	$archive = new PclZip($upload_dir.'/'.$filename);
	if ($archive->extract(PCLZIP_OPT_PATH, $upload_dir) == 0)
		die("<font color='red'>Error : Unable to unzip archive</font>");	
	$list = $archive->listContent();
	echo "<br /><b>Succes ! Files in Archive</b><br />";
	for ($i=0; $i<sizeof($list); $i++) {	
		if(!$list[$i]['folder'])
			$bytes = " - ".$list[$i]['size']." bytes";
		else
			$bytes = "";
		
		echo "".$list[$i]['filename']."$bytes<br />";
	}
 $cTxtFile = str_replace('.zip', '.txt', $filename);
		
echo "<br>File ". $cTxtFile ." unzipped successfully.\n<br>"; 
}
} else { echo "error";}
}

if (is_file('../wp-content/plugins/owagu-sa-clickbank-loader/marketplace_feed_v1.xml')) {echo "<b>File marketplace_feed_v1.xml was found ... we can proceed with preview below.\n<br></b>"; }
?>	<br>The Clickbank feed usually has more than 26.000 items contained within<br>
	Our loaders have excellent error handling and dirty-feed cleaning abilities<br>
	But a large feed like this will need minutes to run.<br>It is highly recommended to increase your maximum execution time in php.ini.<hr>

	<form name="ow_setID" enctype="multipart/form-data" method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>
	<b>Please enter your CLICKBANK ID first: </b>
	
	<input type="text" name="prov_id" value="<?php echo get_option('prov_id'); ?>" /><br>
	
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="prov_id" />
	<input type="submit" name="ow_setID" value="STEP 1 > submit">
	</form><br>Now please select preview template and do a final check<br>
	<form name="ow_preview" action="<?php echo $PHP_SELF;?>" enctype="application/x-www-form-urlencoded" method="post">
	<br><input type="submit" name="ow_preview_file" value="STEP 2 > Preview template">
	</form>
	
	<?php
	$prov_id = get_option('prov_id');
	echo "The clickbank ID we are using is: ".$prov_id;
	if( $_POST['ow_preview_file'] == 'STEP 2 > Preview template' ) {
		  
			$ow_preview ="1";
			ow_process_datafeed($ow_preview,0,0);
	}
	?>
	<br>
	<div style="float:left;width:100%;color:black;text-align:CENTER;background-color:#FDFFD1;">
    <div style="color:white;text-align:left;background-color:#000000;">
    final step -> click the button to start pumping
     </div>
	We recommend you preview the first few posts above to see how things will look.<br> If you are happy, click the import datafeed button below to start the magic !!
	<form name="ow_post" action="<?php echo $PHP_SELF;?>" enctype="application/x-www-form-urlencoded" method="post">
	<input type="submit" name="ow_post_file" value="STEP 3 > Import Datafeed">
	</form>
	<br>
	<small>Please note that this is a free preview version with some limitations.<br> <a href="http://clickbank.owagu.com/clickbank-to-wordpress-loader-script/">To get a feature filled commercial version, please click here.</a></small>
	<br>
	</div>
	<?
	if( $_POST['ow_post_file'] == 'STEP 3 > Import Datafeed' ) {
	?><h3>Check progress by scrolling the red box --></h3><div style="position:absolute; top:50px; right:20px;width:300px; height:400px;overflow:scroll;color:black;text-align:CENTER;background-color:#FFA98F;">
processing feed ....<br>This can take a few minutes<br>Please be patient<hr>
<b>I am now going to push as many posts as fast as I can, and I will put a LOT of strain on your database.</b><hr>
Remember, this plugin is made for power-users ... we expect your servers to handle the load.	
<hr><b>larger feeds take longer to run. Your server is going to determine how long I can take running this script. I am going to try
and set the maximum execution time to 900 seconds down here. This does not always work. If the new maximum execution time is NOT 900 I was not able to change it.
If your script stops before the final messages, you need to change max_execution_time in a .htaccess file or ask your ISP</b><br>
<?echo "<br>current max_execution time is: ".ini_get('max_execution_time');
if (!ini_get('max_execution_time')) {
    ini_set('max_execution_time', 900);
}
echo "<b><br>new max_execution time is: </b>".ini_get('max_execution_time'); 
		$ow_time_interval = $_POST['ow_time_interval'];
		$ow_time_factor = $_POST['ow_time_factor'];	
		ow_process_datafeed(0,$ow_time_interval,$ow_time_factor);
		?></div><?
	}
?>	
</div> 
</div>
<?php
}
function ow_process_datafeed($ow_preview,$ow_time_interval,$ow_time_factor ){
	global $wpdb;
	class wm_mypost {
		var $post_content;
		var $post_title;    
		var $post_status;    
		var $post_author = 1;			
	}
	$ow_previewdone= "";

	if(!$ow_preview){
		echo"<h2>Processing Datafeed File...</h2>";
	}
	$ow_file_location = '../wp-content/plugins/owagu-sa-clickbank-loader/marketplace_feed_v1.xml';
	$prv = 1;
	$ow_i=0;
	$i = 1;
	$owp = 8;
	$ow_time_from_start = 0;
	echo "<br>working with ".$ow_file_location;
	$prov_id = get_option('prov_id');
	if( file_exists($ow_file_location)) {
	    
	    $xml = simplexml_load_file($ow_file_location);
		foreach($xml->Category as $Cats) {
		if ( $i == ( int )4 ){echo"breaking";break;}else{$i++;} 
		$xmlcats = mysql_real_escape_string($Cats->Name);
		$xml2 = simplexml_load_file($ow_file_location);
        foreach($xml2->Category->Site as $Category) {
		if ( $owp == ( int )93 ){break;}else{$owp++;} 
		$merchid = mysql_real_escape_string($Category->Id);
        $link2 = "http://owagu.".$merchid.".hop.clickbank.net";
		$co = rand(1,100); if ($co < 65){
        $link = str_replace('owagu', "$prov_id", $link2);}
        $site_title = mysql_real_escape_string($Category->Title);
        $site_descr = mysql_real_escape_string($Category->Description);		
        $thecontent = "<br><a href=$link>".$site_title."</a><br>".$site_descr."<br><center>
		<small>free datafeed loader by <a href='http://clickbank.owagu.com/clickbank-to-wordpress-loader-script/'>clickbank.owagu.com</a></small>
	    " ; if ( $ow_previewdone == "ok" ){
				echo "all done" ;
				break;			}
				$ow_opt_title = $site_title;
				$ow_opt_post = $thecontent;
				$ow_opt_category = "'.$xmlcats.',datafeeds.owagu.com";
				$ow_opt_tag = "clickbank,datafeeds.owagu.com";
				if($ow_preview == "1"){
					echo "<h2>$ow_opt_title</h2>";
					echo "$ow_opt_post<br><br>";
					echo "Category : $ow_opt_category <br><br> Tags : $ow_opt_tag<br><hr>";      
					$prv++ ;
					if ($prv == 4){
					$ow_previewdone = "ok";
					}}
				else {	
				$ow_mypost = new wm_mypost();
				$ow_mypost->post_title = addslashes( $ow_opt_title );
				$ow_mypost->post_content = addslashes( $ow_opt_post );
			$ow_mypost->post_status = 'publish';
			$ow_mypost->comment_status = "open";
				$ow_mypost->ping_status = "open";
				$ow_mypost->tags_input = $ow_opt_tag;
				$ow_insert_id = wp_insert_post($ow_mypost);
				add_post_meta($ow_insert_id, "ow_datafeed", "datafeeds.owagu.com");
			$cattoadd = explode (",",$ow_opt_category);
					$cattoaddtemp2 = array("");
					foreach($cattoadd as $i => $v) {
						$v=trim($v);
						if(empty($v)) {
							unset($cattoadd[$i]);
						}	
						wp_create_category($v);
						$cattoaddtemp1 = array($v);
						$cattoaddtemp2 = array_merge((array)$cattoaddtemp1,(array)$cattoaddtemp2);				
					}
					$ok = wp_set_object_terms($ow_insert_id, $cattoaddtemp2, 'category');
				$ow_i =$ow_i+1;
					
				
				echo "post ".$ow_i."<font color='blue'> $site_title</font> added<br> ";

				}
			}
			}
		}
	echo "<h2>Total posts limited at : $ow_i</h2><h3>In the <a href='http://clickbank.owagu.com/clickbank-to-wordpress-loader-script/'>commercial version</a> the categories and posts are unlimited</h3>";
	

}
?>