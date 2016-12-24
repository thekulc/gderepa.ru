<?
require_once(ROOT."core/controller.php"); 
if(isset($_GET['debug']) AND isset($_GET['info']) ) debug(phpinfo());
$url4parse = $_SERVER['REQUEST_URI'];

if(strpos($_SERVER['REQUEST_URI'],'?'))
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'?'));

/*
 * CHECK ALIASES
 */

if (ALIAS)
    $_SERVER['REQUEST_URI'] = check_aliases($_SERVER['REQUEST_URI']);

$routeArray = explode('/', $_SERVER['REQUEST_URI']);

$route = array();
foreach ($routeArray as &$value) if (!empty($value)) $route[] = trim($value);

$admin = false;
$controller = null;

if ($route[0] == 'admin') {
    $admin = true;
    array_shift($route);
}

$excluded_folders = explode(",",EXCLUDED_FOLDERS);
if (in_array($route[0], $excluded_folders)) exit();
$id = "";

if ($route[0]) {
    $application = $route[0];
    if ($route[2]) {
        if (substr($route[1],0,2) == "id"){
            $id = substr($route[1], 2);
            array_splice($route, 1, 1);
        }
		elseif ( substr($route[2],0,2) == "id" ){
            $id = substr($route[2], 2);
            array_splice($route, 2, 1);
		}
		else{
			$id = $route[2];
		}
		
		if(intval($id) > 0){
			if (checkController($application, $application)) {
				$controller = $application;
				array_shift($route);
			}
		}
		else{
			if (checkController($application, $route[1])) {
				$controller = $route[1];
				array_shift($route[0]);
			}
			else{
				$controller = $route[0];
				$id = $route[1];
				array_shift($route);
				array_shift($route);
			}
		}
    }
    elseif ($route[1]) {
        if (substr($route[1],0,2) == "id") {
			$id = substr($route[1], 2);
		}
		else{
			$id = $route[1];
			$controller = $route[1];
		}
    }
    else {
        $id = $route[1];
    }    
}
else $application = "index";

$cr = get_controller($application, $controller, $admin, $id, $route);

if ($cr) {
    if ($admin) $cr->test_access();
    else {
        if(is_dir(ROOT.'globals'.DS)) {
            $handle = opendir(ROOT.'globals'.DS);
            while (false !== ($script = readdir($handle))) {
                $filename = ROOT.'globals'.DS.$script;
                $pathinfo = pathinfo($filename);
                if(is_file($filename) && $pathinfo['extension'] == 'php') include_once($filename);
            }
        }
    }
    $cr->default_method();    
}
else {
	debug("Контроллер {$controller} в модуле {$application} не найден",false);
    Controller::error_page(404);
}

function checkController($application, $controller, $admin_mode = false){
	$root_path = ROOT."applications".DS.$application.DS;
	
	if($admin_mode) 
        $path = $root_path . "controllers" . DS . "admin" . DS . $controller . ".php";
    else 
        $path = $root_path . "controllers" . DS . $controller . ".php";
	
	if(!file_exists($path) OR !is_file($path))
		return false;
	else 
		return true;
}

function get_controller($application, $controller=null, $admin_mode=false, $id=false, $more=null)
{    
    $root_path = ROOT."applications".DS.$application.DS;
    if (!$controller)
        $controller = $application;
     
    if($admin_mode) 
        $path = $root_path . "controllers".DS."admin".DS.$controller.".php";
    else 
        $path = $root_path . "controllers".DS.$controller.".php";
    
    if(!file_exists($path) OR !is_file($path)){
		if($admin_mode) 
			$path = $root_path . "controllers".DS."admin".DS.$application.".php";
		else 
			$path = $root_path . "controllers".DS.$application.".php";
		$class = $application."\\".$application;
		
    }
	else{
		//pr($id);
		$class = $application."\\".$controller;
	}
	
	include_once($path);
	
	if ($admin_mode) $class = "admin\\".$class;
	
	return new $class($application,$id,$more);
}

function check_aliases($route){
    $res = "";
    $rout = rtrim(strstr($route, "/"), "/");
    if ($rout){
        $alias = get_controller("aliases","aliases",true)->getAliasFromAlias($rout);
        
        if(isset($alias))
            $res = $alias[0]['address'];
        else $res = $route;
    }
    else {
        $alias = get_controller("aliases","aliases",true)->getAliasFromAlias("/");
        $res = $alias[0]['address'];
    }
    return $res;
}
?>
