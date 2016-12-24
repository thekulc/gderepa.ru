<?
require_once(ROOT.'libraries/Twig/Autoloader.php');
require_once (ROOT."libraries/Mobile-Detect-2.8.24/Mobile_Detect.php");

class layout {
    
    static private function pre_layout($path, $vars=0, $cached=CACHING)
    {
        if(file_exists(ROOT.$path) && is_file(ROOT.$path))
        {
            $t = self::run($path, $cached);
            if(!$vars) $vars = array();  
            return array($t,$vars);
        } 
        else return debug("не найден шаблон ".$path);    
    }
    
    static function layout_show($path, $vars=0, $cached=CACHING)
    {
		$vars['device'] = new \Mobile_Detect;
        if ($t = self::pre_layout($path,$vars,$cached)) $t[0]->display($t[1]);
    }

    static function layout_get($path, $vars=0, $cached=CACHING)
    {
        if ($t = self::pre_layout($path,$vars,$cached)) return $t[0]->render($t[1]);
    }

    private static function run($path, $cached=CACHING){
        $settings = array();
        if($cached) $settings['cache'] = ROOT.'data'.DS.'layouts_cache';
        $settings['autoescape'] = false;
        
        Twig_Autoloader::register();
        $loader=new Twig_Loader_Filesystem(ROOT);
        $twig=new Twig_Environment($loader,$settings);
       // $twig->addExtension(new Twig_Extension_I18n());
        return $twig->loadTemplate($path);
    }
}
?>
