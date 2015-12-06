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
    $var = $html->find('div.blockcont h1');
    $insert['title'] = trim($var[0]->plaintext);
    $paragraphs = $html->find('div#opennewstext');
    $text = $paragraphs[0]->innertext;
    if(isset($_GET['v'])){
        $iframe = $paragraphs[0]->find('iframe');
        if($iframe){
            $iframe = $iframe[0]->src;
            echo $iframe.'<br />';
            if(strpos($iframe,'yout')){
                $youtube = substr($iframe,strrpos($iframe,'/')+1);
                if($youtube){
                    $text = $paragraphs[0]->plaintext;
                    $insert['video'] = 'https://www.youtube.com/watch?v='.$youtube;
                }
                
            }         
        }
    }
    
    $text = str_replace('style="display:none;visibility:hidden;" data-cfsrc','src',$text);
    $text = str_replace('"/static','"http://blognews.am/static',$text);
    
    $insert['post'] = $text.'<p>Աղբյուր ՝ <a href="'.$url.'" rel="nofollow" target="_blank">blognews.am</a></p>';
    $insert['metakey']=$insert['title'];
    $insert['created'] = date("Y-m-d H:i:s");
    $insert['published'] = $insert['created'];
    $insert['state'] = 1;
    $r = rand(1,4);
    if($r == 3){
        $insert['important'] = 1;
    }
    $imgs = $html->find('div#opennewstext img');
    $image = $imgs[0];
    $imageSrcarr = $image->attr;
    $imageSrc = ($imageSrcarr['data-cfsrc'])? $imageSrcarr['data-cfsrc']: $imageSrcarr['src'];
    if(!$imageSrc){
        $insert['important'] = 0;
        $imageSrc = 'http://newsroyal.com/img/royal.jpg';
    } 
    if(strpos($imageSrc,'http') === FALSE){
        $imageSrc = 'http://blognews.am'.$imageSrc;
    }
    else {
        echo strpos($imageSrc,'http').' -zzz -';
    }
    $imageSrc = str_replace('https','http',$imageSrc);
    echo $imageSrc.'<br />';
    $imageData = file_get_contents($imageSrc);
        if(!$imageData){
            $imageSrc = 'http://newsroyal.com/img/royal.jpg';
            $imageData = file_get_contents($imageSrc);
            $insert['important'] = 0;
        } 
    $id = insert('content',$insert);
    if($id){
        $insert = array('id'=>$id, 'cat_id'=>$type);
        insert('category_rel',$insert); 
        file_put_contents('upload/'.$id.'.png', $imageData);
        $insert = array('url'=>$url);
        insert('post_urls',$insert);
        echo 'ok<br />';
    }  
    
}




include "include/bd.php";
include"include/simple_html_dom.php";
$i=0;

if(isset($_GET['p'])){
    $url = 'http://blognews.am/arm/news/photo/';
    $type = 3;
    $red = 'q';
}
elseif(isset($_GET['q'])){
    $url = 'http://blognews.am/arm/news/politics/';
    $type = 1;
    $red = 'j';
}
elseif(isset($_GET['j'])){
    $url = 'http://blognews.am/arm/news/society/';
    $type = 3;
    $red = 'v';
}
elseif(isset($_GET['v'])){
    $url = 'http://blognews.am/arm/news/video/';
    $type = 3;
}
else{
    $url = 'http://blognews.am/arm/news/culture/';
    $type = 2;
    $red = 'p';
}
//for($i=3;$i<20;$i++){
    $html = file_get_html($url);
    $links = $html->find('div.newsbycatitem h3 a');
    foreach($links as $url){
        $i++;
        if($i>20){
            break;
        }
        create_news('http://blognews.am'.$url->href,$type);
        echo $url->href.'<br />';
    }
    if($red)
    echo '
        <script type="text/javascript">
         setTimeout(function(){
            window.location.href = "http://newsroyal.com/parser_blog.php?'.$red.'";
         },5000);
        </script>
        ';
    else
      echo  '
        <script type="text/javascript">
         setTimeout(function(){
            window.location.href = "http://newsroyal.com/parser_armfootball.php";
         },5000);
        </script>
        ';   
//}

?>