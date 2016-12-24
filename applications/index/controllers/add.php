<?
namespace index;

class add extends \Controller {  
    
    function default_method()
    {
        $this->get_controller("news","add")->default_method();
    }
}
?>
