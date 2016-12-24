<?
require_once(ROOT."core/controller.php"); 

$url4parse = $_SERVER['REQUEST_URI'];
if(strpos($_SERVER['REQUEST_URI'],'?'))
$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'?'));

$routeArray = explode('/', $_SERVER['REQUEST_URI']);

$route = array();
foreach ($routeArray as &$value) if (!empty($value)) $route[] = trim($value);
     
if ($route[0] == 'admin') {
    $admin = true;
    array_shift($route);
}


$excluded_folders = explode(",",EXCLUDED_FOLDERS);
if (in_array($route[0], $excluded_folders)) exit();

$data = array();
if ($route[0]) {
    if (substr($route[0],0,1)=='~')
    {
        $application = 'alias';
        $data['alias'] = substr($route[0], 1);
    }
    else
    {
        $application = $route[0];
    }
    if ($application!='alias'){
        
        if ($route[1] && $route[2]) {
            if (substr($route[1],0,1) == "~") {
                $data['alias'] = substr($route[1], 1);
            }
            if (substr($route[1],0,2) == "id") 
            {
                //$controller = $route[1];
                $id = substr($route[1], 2);
            }
            else{
                $controller = $route[1];
                $id = substr($route[2], 2);
                unset($route[2]);
            }
        }
        else if ($route[1]) {
            if (substr($route[1],0,2) == "id") {
                $id = substr($route[1], 2);
            }
            else if (substr($route[1],0,1) == "~") $data['alias'] = substr($route[1], 1);
            else $controller = $route[1];
        }
        else {
            $id = substr($route[1], 2);
        }
    }
    else {
        if ($route[1]) {
            if (substr($route[1],0,2) == "id") $id = substr($route[1], 2);
            else {
                $controller = $route[1];
                $id = substr($route[2], 2);
                unset($route[2]);
            }
        }
        else if ($route[1]) {
            if (substr($route[1],0,2) == "id") $id = substr($route[1], 2);
            else if (substr($route[1],0,1) == "~") $data['alias'] = substr($route[1], 1);
            else $controller = $route[1];
        }
        else {
            $id = substr($route[1], 2);
        }
    }
}
else $application = "index";


unset($route[0]);
unset($route[1]);


$cr = Controller::get_controller($application, $controller, $admin, $id, $data);

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
else Controller::error_page(404);

?>
