<?php
/*
function news($count)
{
    
    $res = null;
    $query = "SELECT * from news where isPub=true order by date asc limit ".$count;
    $query = mysql_query($query);
    if (mysql_num_rows($query)>0)
        while($news = mysql_fetch_assoc($query)){
                $res[] = $news;
        }
        
    $news = $res;
    Controller::set_global('news', $news);
}

function blocks()
{
    $blocks = Controller::get_controller('blocks')->getList();
    Controller::set_global('blocks', $blocks);
}

blocks();*/
//news(3);
?>