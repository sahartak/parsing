<?php 
set_time_limit(0);
function create_news($url,$type){
    $url = trim($url);
    $result = mysql_query("SELECT id FROM `post_urls` WHERE url LIKE '$url' LIMIT 1");
    if(mysql_num_rows($result) > 0){
        echo 'exist<br />';
        return false;
    }
    $html = file_get_html($url);
    $insert = array();
    $var = $html->find('div.single-post-main span.news-title');
    $insert['title'] = trim($var[0]->plaintext);
    $paragraphs = $html->find('span.news-content p');
    $text = '';
    foreach($paragraphs as $p){
        $text .= '<p>'.trim($p->plaintext).'</p>';
    }
    $insert['post'] = $text.'<p>Աղբյուր ՝ <a href="'.$url.'" rel="nofollow" target="_blank">www.1in.am</a></p>';
    $insert['metakey']=$insert['title'];
    $insert['created'] = date("Y-m-d H:i:s");
    $insert['published'] = $insert['created'];
    $insert['state'] = 1;
    $r = rand(1,4);
    if($r == 3){
        $insert['important'] = 1;
    }
    $imgs = $html->find('div.single-post-main span.news-photo img');
    $image = $imgs[0];
    
    $imageSrc = $image->src;
    $id = insert('content',$insert);
    if($id){
        $insert = array('id'=>$id, 'cat_id'=>$type);
        insert('category_rel',$insert);
        $imageData = file_get_contents($imageSrc);
        file_put_contents('upload/'.$id.'.png', $imageData);
        $insert = array('url'=>$url);
        insert('post_urls',$insert);
        echo 'ok<br />';
    }  
    
}




include "include/bd.php";
include"include/simple_html_dom.php";
$i=0;
    $url = 'http://www.1in.am/section/newsfeed/armenia/politics';
    $type = 1;

    $html = file_get_html($url);
    $links = $html->find('div.maincol-big div.maincol-wide div.top-featured-wrapper div.category-post-main div.category-post-item');
    foreach($links as $url){
        $v = $url->find('span.video');
        if($v){
            $i--;
            continue;
        }
        $a = $url->find('a');
        $i++;
        if($i>10){
            break;
        }
        create_news($a[0]->href,$type);
        echo $a[0]->href.'<br />';
    }
    
    $url = 'http://www.1in.am/section/newsfeed/regional';
    $type = 6;
$i=0;
    $html = file_get_html($url);
    $links = $html->find('div.maincol-big div.maincol-wide div.top-featured-wrapper div.category-post-main div.category-post-item');
    foreach($links as $url){
        $v = $url->find('span.video');
        if($v){
            $i--;
            continue;
        }
        $a = $url->find('a');
        $i++;
        if($i>10){
            break;
        }
        create_news($a[0]->href,$type);
        echo $a[0]->href.'<br />';
    }
    
    $url = 'http://www.1in.am/section/newsfeed/press';
    $type = 5;
$i=0;
    $html = file_get_html($url);
    $links = $html->find('div.maincol-big div.maincol-wide div.top-featured-wrapper div.category-post-main div.category-post-item');
    foreach($links as $url){
        $v = $url->find('span.video');
        if($v){
            $i--;
            continue;
        }
        $a = $url->find('a');
        $i++;
        if($i>10){
            break;
        }
        create_news($a[0]->href,$type);
        echo $a[0]->href.'<br />';
    }
    
    echo '
        <script type="text/javascript">
         setTimeout(function(){
            window.location.href = "http://newsroyal.com/parser_armsport.php";
         },5000);
        </script>
        ';

?>