<?php 

function create_news($url,$type){
    $url = trim($url);
    $result = mysql_query("SELECT id FROM `post_urls` WHERE url LIKE '$url' LIMIT 1");
    if(mysql_num_rows($result) > 0){
        echo 'exist<br />';
        return false;
    }
    $html = file_get_html($url);
    $insert = array();
    $var = $html->find('div#inner-col1 h2.ariticle-title');
    $insert['title'] = trim($var[0]->plaintext);
    $paragraphs = $html->find('div#article-content');
    
    $text = $paragraphs[0]->innertext;
    $insert['post'] = $text.'<p>Աղբյուր ՝ <a href="'.$url.'" rel="nofollow" target="_blank">armsport.am</a></p>';
    $insert['metakey']=$insert['title'];
    $insert['created'] = date("Y-m-d H:i:s");
    $insert['published'] = $insert['created'];
    $insert['state'] = 1;
    $r = rand(1,4);
    if($r == 3){
        $insert['important'] = 1;
    }
    $imgs = $html->find('div#photo-set div#photo-set-main img');
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
$url = 'http://www.armsport.am/hy/news';
$type = 4;



//for($i=3;$i<20;$i++){
    $html = file_get_html($url);
    $links = $html->find('div#inner-col1 div.list-news div.pic a');
    foreach($links as $url){
        $i++;
        if($i>20){
            break;
        }
        create_news($url->href,$type);
        echo $url->href.'<br />';
    }
    echo '
        <script type="text/javascript">
         setTimeout(function(){
            window.location.href = "http://newsroyal.com/parser_blog.php";
         },5000);
        </script>
        ';
//}

?>