<?php 

/*
Template Name: wiki-request
*/

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			
			<?php request(); search();?>
			

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>

<?php
function request(){
echo("


	<html>

		<form method=\"post\">
			<b>食物搜索</b>: <input type=\"text\" name=\"key\">
			<input type=\"submit\" value=\"是什么？\">
		</form>
	</html>




");

}
?>

<?php



function search(){
	header("Content-Type: text/html;charset=utf-8");

	$keyword = $_POST["key"];
	//$keyword = "apple";
	$keyword = str_replace("\n", "", $keyword);
	$keyword = str_replace("\r", "", $keyword);
	$keyword = trim($keyword);
	
	$result = wikisearch($keyword);
	echo $result;
 
}	
	
function wikisearch($keyword){
	// Main function
	$keyword = urlencode($keyword);
	$url = 'http://en.wikipedia.org/w/api.php?action=opensearch&search='.$keyword.'&format=xml&limit=1';

	$ch = curl_init($url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt ($ch, CURLOPT_USERAGENT, "test");
	$content = curl_exec($ch);
	
	$result = "<br>" ;
	$result .= parse_content_en($content);
	return $result;
}

function parse_content_en($content){
	// Parse wiki search result
	$pattern_keyword = '#<Text xml:space=.preserve.>(.*)</Text>#Us';
	$text_en = "";
	$text_zh = "";
	$text_urls = "";
	$text_all = "";
	$content_keyword = "";
	if(preg_match($pattern_keyword, $content, $matches_keyword))
	{	   
		$content_keyword = strip_tags($matches_keyword[1]);
		
		$text_en = "<h2>".$content_keyword."</h2><br>";
	}
	
	// pattern for first match of a paragraph
	$pattern = '#<Description xml:space=.preserve.>(.*)</Description>#Us';
	if(preg_match($pattern, $content, $matches))
	{	   
		$content_desc = strip_tags($matches[1]);		
		$text_en .= $content_desc."<br>---------------------------<br>";
	}
	
	$text_zh = search_zh($content_keyword);
	$text_urls = wiki2image($content_keyword);
	$text_all = $text_en . $text_zh;// . $text_urls;
	return $text_all;
}

function search_zh($keyword){

	$keyword = urlencode($keyword);	
	$url = 'http://en.wikipedia.org/w/api.php?action=query&titles='.$keyword.'&prop=langlinks&lllimit=500&format=xml';
	
	$ch = curl_init($url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_USERAGENT, "test"); 
	$content = curl_exec($ch);
	
	$text_zh = parse_content_zh($content);
	return $text_zh;

}

function parse_content_zh($content){

	// pattern for first match of a paragraph
	$pattern_lang_wuu = '#<ll lang=.wuu. xml:space=.preserve.>(.*)</ll>#Us';
	$pattern_lang_zh = '#<ll lang=.zh. xml:space=.preserve.>(.*)</ll>#Us';
	$text_zh = "";
	if(preg_match($pattern_lang_wuu, $content, $matches_lang))
	{
	   
		$content_keyword_zh = strip_tags($matches_lang[1]);
		$text_zh = "<br><h2>".$content_keyword_zh."</h2><br>";
		$text_zh .= wikisearch_zh($content_keyword_zh);
	}
	else if(preg_match($pattern_lang_zh, $content, $matches_lang)){


		$content_keyword_zh = strip_tags($matches_lang[1]);
		$text_zh = "<br><h2>".$content_keyword_zh."</h2><br>";
		$text_zh .= wikisearch_zh($content_keyword_zh);


}
	return $text_zh;
}

function wikisearch_zh($keyword_zh){

	$keyword_zh = urlencode($keyword_zh);
	$url = 'http://zh.wikipedia.org/w/api.php?action=opensearch&search='.$keyword_zh.'&format=xml&limit=1';
	
	$ch = curl_init($url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_USERAGENT, "test"); // required by wikipedia.org server; use YOUR user agent with YOUR contact information. (otherwise your IP might get blocked)
	$content = curl_exec($ch);
	$content_desc = "";
	
	// pattern for first match of a paragraph
	$pattern = '#<Description xml:space=.preserve.>(.*)</Description>#Us';
	if(preg_match($pattern, $content, $matches))
	{	   
		$content_desc = strip_tags($matches[1]);
		
	}
	return $content_desc."<br><br>";
}



function wiki2image($keyword){

	$keyword = urlencode($keyword);
	$imageUrls = wikipediaImageUrls('http://en.wikipedia.org/wiki/'.$keyword);

	$counter = 0;
	$str_urls = "";
	
	foreach ($imageUrls as &$value) {
		
		if($counter == 4){
			break;
		}
		echo "<img src=\"".$value."\" width=\"100\"/>";
		$str_urls .= $value."<br>";
		
   		$counter++;
   	}
	
	return $str_urls;

}


function wikipediaImageUrls($url) {
    $imageUrls = array();
    $pathComponents = explode('/', parse_url($url, PHP_URL_PATH));
    $pageTitle = array_pop($pathComponents);
    $imagesQuery = "http://en.wikipedia.org/w/api.php?action=query&titles={$pageTitle}&prop=images&format=json";
    $jsonResponse = makeCall($imagesQuery);
    $response = json_decode($jsonResponse, true);
    $imagesKey = key($response['query']['pages']);
    foreach($response['query']['pages'][$imagesKey]['images'] as $imageArray) {
        if($imageArray['title'] != 'File:Commons-logo.svg' && $imageArray['title'] != 'File:P vip.svg') {
            $title = str_replace('File:', '', $imageArray['title']);
            $title = str_replace(' ', '_', $title);
            $imageUrlQuery = "http://en.wikipedia.org/w/api.php?action=query&titles=Image:{$title}&prop=imageinfo&iiprop=url&format=json";
            $jsonUrlQuery = makeCall($imageUrlQuery);
            $urlResponse = json_decode($jsonUrlQuery, true);
            $imageKey = key($urlResponse['query']['pages']);
            $imageUrls[] = $urlResponse['query']['pages'][$imageKey]['imageinfo'][0]['url'];
        }
    }
    return $imageUrls;
}

function makeCall($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    return curl_exec($curl);
}
?>