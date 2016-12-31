<?php
require_once(ROOT."core/layout.php");

class Controller {

    protected $layout; 
    protected $user;
    protected $application;
    protected $model;
	private $allow = array("All");
	private $disallow = array("execute");

    function __construct($app, $id=false, $more=false,$admin_mode)
    {
        if($id) $this->id = $id;
        if ($more) $this->more = $more;

        $layoutPath = "/templates".'/'.LAYOUT;
        $this->set_layout_path($layoutPath);
        
        $layout_path = ROOT . "templates" . DS . LAYOUT;
        $this->set_global('layout_path', $layout_path);

        $this->set_application($app);
		
		$this->model = $this->model($admin_mode);
		
        $GLOBALS['app']['layout'] = $layoutPath."/";
        $GLOBALS['app']['css'] = $layoutPath."/"."css"."/";
        $GLOBALS['app']['js'] = $layoutPath."/"."js"."/";
        $GLOBALS['app']['images'] = $layoutPath."/"."images"."/";
    }
	
	function getMethodName($method = "", $disallowArray = array()){
		if (is_string($method)){
			$metods = get_class_methods( get_called_class() );
			if(
				!in_array( $method, $metods) OR 
				in_array($method, array_merge($this->disallow, $disallowArray))
			){
				$method = "";
			}
		}
		else{
			$method = "";
		}
		
		return $method;
	}

    public function __toString() {
        pr(get_object_vars($this));
        return get_object_vars($this);
    }

    function set_layout_path($path){
        $this->layout = $path.'/';
        $this->set_global('layoutPath', $path.'/');
    }

    function get_layout_path(){
        return $this->layout;
    }
    
    function set_application($application){
        $this->application = $application;
    }       

    function get_application(){
        return $this->application;
    }
    
    static function get_controller($application, $controller=null, $admin_mode=false, $id=false, $more=null)
    {    
        $path = ROOT."applications".DS.$application.DS;
        if (!$controller)
            $controller = $application;
         
        if($admin_mode) 
            $path .= "controllers".DS."admin".DS.$controller.".php";
        else 
            $path.= "controllers".DS.$controller.".php";
        
        if(file_exists($path) && is_file($path)){
            include_once($path);
            $class = $application."\\".$controller;
            if ($admin_mode) $class = "admin\\".$class;
            return new $class($application,$id,$more,$admin_mode);
        }
        else return debug("Контроллер {$controller} в модуле {$application} не найден");
    }

    private function model($admin_mode=false){
        $application = $this->get_application();
        
        if (!$controller)
            $modelName = 'mdl_'.$application;
        else
            $modelName = 'mdl_'.$controller;
        
        $path = ROOT."applications".DS.$application.DS;

        if($admin_mode) $path .= "models".DS."admin".DS.$modelName.".php";
        else $path.= "models".DS.$modelName.".php";
        
        if(file_exists($path) && is_file($path))
        {
            require_once('model.php');
            require_once($path);
            $class = $application."\\".$modelName;
            if ($admin_mode) $class = "admin\\".$class;

            return new $class($application);
        } 
        else {
            //return debug("Модель в модуле {$application} не найдена",false);
            return null;
        };

    }

    function layout_show($layout,$values=null)
    {
        if ($GLOBALS['globals']) $values['globals'] = $GLOBALS['globals'];
        if ($GLOBALS['app']) $values['app'] = $GLOBALS['app'];
		$values['app']['session'] = $_SESSION;
        $this->debug($this->layout.$layout);
        layout::layout_show($this->layout.$layout,$values);
    }

    function layout_get($layout,$values=null)
    {
        if ($GLOBALS['globals']) $values['globals'] = $GLOBALS['globals'];
        if ($GLOBALS['app']) $values['app'] = $GLOBALS['app'];
		$values['app']['session'] = $_SESSION;
        $this->debug($this->layout.$layout);
        return layout::layout_get($this->layout.$layout,$values);
    }

    static function error_page($num=404, $values='')
    {
        switch ($num)
        {
            case '404':
                header("HTTP/1.1 404 Not Found");
                break;
        }
        if ($GLOBALS['app']) $values['app'] = $GLOBALS['app'];
        if ($GLOBALS['globals']) $values['globals'] = $GLOBALS['globals'];
        layout::layout_show(DS."error".DS.$num.".html",$values);
	exit();
    }

    function redirect($url='', $delay=0)
    {
        $delay = (int) $delay;
        if(!$delay && !headers_sent()) {
            header('Location: '.$url);
            exit();
        } 
        else echo "<meta http-equiv='refresh' content='{$delay}; url={$url}'>";
    }

    static function set_global($key, $val)
    {
        $GLOBALS['globals'][$key] = $val;
        return true;
    }

    function get_global($key)
    {
        return $GLOBALS['globals'][$key];
    }

    function debug($args = null){
        if ((defined('DEV_MODE') && DEV_MODE==1) OR isset($_GET['debug'])){
            if (gettype($args) == "array" AND count($args)>0)
                foreach ($args as $arg) {
                    pr($arg);
                }
            elseif (isset($args))
                pr($args);
        }
    }

}

class Admin extends Controller{
    
    function test_access()
    {
        if (!$_SESSION['user']['admin'] && $this->application != "index") parent::redirect('/admin/');
    }
}
?>
