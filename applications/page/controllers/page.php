<?
namespace page;

class page extends \Controller {  
    
    function default_method()
    {
        $page = $this->get($this->id);
        $page['blocks']=$this->get_blocks($this->id);
        $address = $_SERVER["REQUEST_URI"];
        $layout = $this->getTemplate($address);
        return $this->layout_show('page/'.$layout, array('page'=>$page));
    }
    
    function get($id){
        $query = "select * from pages where id = '{$id}'";
        $query = mysql_query($query);
        if (mysql_num_rows($query)>0)
            $res = mysql_fetch_assoc ($query);
        else $res = null;
        return $res;
    }
    
    function get_blocks($id = null){
        $query =  "select * from blocks where (id in (select block_id from blocks_page where page_id='{$id}') and isPub = true)";
        $query = mysql_query($query);
        $result = null;
        if (mysql_num_rows($query)>0)
            while ($block = mysql_fetch_assoc($query)) {
                $result[$block['id']] = $block;
            }
        $result['captcha'] = $this->get_controller("message")->captchaCreate();
        return $result;
    }
    
    function getTemplate($address){
        $res = "index.html";
        
        $alias = $this->get_controller("aliases", "aliases", true)->getAliasFromAddress($address);
        
        $path = $this->get_global('layout_path').DS;
        
        
        if ($alias[0]['alias']){
            if($alias[0]['alias'] == "/")
                $templateName = $res;
            else 
                $templateName = "page_" . $alias[0]['alias'];
			
            $templateName = $templateName . ".html";
            if (file_exists($path.$templateName))
                $res = $templateName;
        }
        return $res;
    }
    
    
}
?>
