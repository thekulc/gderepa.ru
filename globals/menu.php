<?php
/*
function _genMenu()
{
    $tree = Controller::get_controller('menu')->getTree();
    $res = $tree->getStructure();
    $childs = $tree->getAllChilds();
    Controller::set_global('childs',$childs);
    
    Controller::set_global('menu_top',$res);
    $breadcrumb = findActive(&$tree);
    Controller::set_global('breadcrumb',$breadcrumb);
    
}

function findActive(&$tree){
    $routestr = substr($_SERVER['REQUEST_URI'], stripos("/", $_SERVER['REQUEST_URI']));
    $menu = $tree->_items;
    $breadcrumb = array();
    foreach ($menu as $menuItem) {
        if ($menuItem['data']['address']==$routestr) {
            $menuItem['active'] = true;
            
            $tree->_items[$menuItem['id']]['active'] = 1;
            
            if ($tree->_items[$menuItem['parent']])
                $tree->_items[$menuItem['parent']]['active'] = 1;
            
            $breadcrumb = $tree->getBranch($menuItem['id']);
            
            break;
        }
        else $tree->_items[$menuItem['id']]['active'] = 0;
    }
    //pr($breadcrumb);
    return $breadcrumb;
}

_genMenu();
*/

Controller::set_global("menu", Controller::get_controller("menu")->getList());
//Controller::set_global("taxonomy", Controller::get_controller("taxonomy")->getTree($GLOBALS['id']));

?>
