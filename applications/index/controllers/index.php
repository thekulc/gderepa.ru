<?
namespace index;

class index extends \Controller {  
    
    function default_method()
    {
        return $this->layout_show('index.html',array('title'=>'Главная'));
    }
    
}
?>
