<?php 
set_time_limit(0);
include "include/bd.php";
include"include/simple_html_dom.php";
//mysql_query("REPAIR TABLE content");die;
function create_news($url,$type){
    $url = trim($url);
    $result = mysql_query("SELECT id FROM `post_urls` WHERE url LIKE '$url' LIMIT 1");
    if(mysql_num_rows($result) > 0){
        echo 'exist<br />';
        return false;
    }
    $html = file_get_html($url);
    $insert = array();
    $var = $html->find('div#right-col #item h1');
    $insert['title'] = trim($var[0]->plaintext);
    $paragraphs = $html->find('#i-content p');
    $text = '';
    foreach($paragraphs as $p){
        $text .= '<p>'.trim($p->plaintext).'</p>';
    }
    $insert['post'] = $text.'<p>Աղբյուր ՝ <a href="'.$url.'" rel="nofollow" target="_blank">tert.am</a></p>';
    $insert['metakey']=$insert['title'];
    $insert['created'] = date("Y-m-d H:i:s");
    $insert['published'] = $insert['created'];
    $insert['state'] = 1;
    $r = rand(1,4);
    if($r == 3){
        $insert['important'] = 1;
    }

    $imgs = $html->find('#i-content img');
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





$i=0;
$url = 'http://www.tert.am/am/news/armenia';
$type = 1;
if(isset($_GET['type'])){
    $type = $_GET['type'];
    switch($type){
        case 2:
            $url = 'http://www.tert.am/am/news/culture';
        break;
        case 3:
            $url = 'http://www.tert.am/am/news/lifestyle';
        break;
        case 4:
            $url = 'http://www.tert.am/am/news/Sports';
        break;
        case 5:
            $url = 'http://www.tert.am/am/news/press-digest';
        break;
        case 6:
            $url = 'http://www.tert.am/am/news/world';
        break;        
    }
}


//for($i=3;$i<20;$i++){
    $html = file_get_html($url);
    $links = $html->find('div#right-col div.news-blocks h4 a');
    foreach($links as $url){
        $i++;
        if($i>20){
            break;
        }
        create_news($url->href,$type);
        echo $url->href.'<br />';
    }
    if($type!=6){
        $r = $type+1;
        echo '
        <script type="text/javascript">
         setTimeout(function(){
            window.location.href = "http://newsroyal.com/parser_n.php?type='.$r.'";
         },5000);
        </script>
        ';
    }
    else{
        echo '
        <script type="text/javascript">
         setTimeout(function(){
            window.location.href = "http://newsroyal.com/parser_1in.php";
         },5000);
        </script>
        ';
    }
    
//}

?>