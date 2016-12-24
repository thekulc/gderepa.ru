<?
namespace taxonomy;

class taxonomy extends \Controller {  
    
    function default_method()
    {
        if (isset($this->id)) {
            $obj['term'] = $this->get($this->id, true, $err);
        }
        else {
            $obj['message'] = 'Вы находитесь на странице медиа материалов. Пожалуйста выберите раздел.';
        }

        return $this->layout_show('index.html', $obj);
    }
    
    var $term;
    
    function get($id, $mObjects = false, &$err = null){
        $id = (int) mysql_real_escape_string($id);
        
        $query = "SELECT id, name, description, parent_id as pID,(select alias from aliases where address=concat('/taxonomy/id', taxonomy.id)) as alias FROM taxonomy WHERE id = {$id}";
        
        $termsQ = mysql_query($query);
        if (mysql_errno() == 0){
            if (mysql_num_rows($termsQ)>0){
                $term = mysql_fetch_object($termsQ);
                $term->childs = $this->getChilds($term);
                $term->parents = $this->getParents($term);
                $term->branch = array_reverse($this->branch, true);
                $term->mObjects = $mObjects == true ? $this->getObjectsByTerm($id) : null;
                unset ($this->branch);
            }
        }
        else
            $err = mysql_error();
        return $this->term = $term;
    }
    /*select tax.*, o.*, a.* from taxonomy_object as t 
LEFT JOIN objects as o ON t.object_id = o.id 
LEFT JOIN attachments as a ON o.id=a.object_id
LEFT JOIN taxonomy as tax ON tax.id = t.taxonomy_id*/
    function getObjectsByTerm($term_id, &$err){
        $res = null;
        if(isset($term_id)){
            $rooturi = $this->get_controller("attachment", "attachment", true)->getUri();
            //$query = "SELECT *, (SELECT CONCAT('{$rooturi}', filename)) as uri, (SELECT type_id from objects WHERE id = object_id) as type_id FROM attachments WHERE object_id in (SELECT object_id FROM taxonomy_object WHERE taxonomy_id = {$term_id})";
            $query = "select o.*, (SELECT CONCAT('{$rooturi}', a.filename)) as uri from taxonomy_object as t LEFT JOIN objects as o ON t.object_id = o.id LEFT JOIN attachments as a ON o.id=a.object_id where t.taxonomy_id = " . $term_id;
            if ($mObjects = mysql_query($query)){
                while ($mObject = mysql_fetch_object($mObjects)) {
                    $res[$mObject->id] = $mObject;
                }
            }
            else{
                $err = mysql_error();
            }
        }
        return $res;
        
    }
    
    private $branch;
    function getChilds($term){
        $res = null;
        $query = "SELECT id, name, parent_id as pID, (select alias from aliases where address=concat('/taxonomy/id', taxonomy.id)) as alias FROM taxonomy WHERE parent_id = {$term->id} ORDER BY sort_order ASC, name ASC";
        $query = mysql_query($query);
        if (mysql_num_rows($query)>0){
            while ($lTerm = mysql_fetch_object($query)) {
                $lTerm->active = $this->isTermInBranch($lTerm, $this->term);
                $lTerm->childs = $this->getChilds($lTerm);
                $res[$lTerm->id] = $lTerm;
                unset($lTerm);
            }
        }
        return $res;
    }
    
    
    function getParents($term){
        $res = null;
        if ($term->pID){
            $query = "SELECT id, name, parent_id as pID, (select alias from aliases where address=concat('/taxonomy/id', taxonomy.id)) as alias FROM taxonomy WHERE id = {$term->pID} ORDER BY sort_order ASC, name ASC";
            $query = mysql_query($query);
            if (mysql_num_rows($query)){
                while ($lTerm = mysql_fetch_object($query)) {
                    $this->constructBranchObj($lTerm);
                    $lTerm->parents = $this->getParents($lTerm);
                    $res[$lTerm->id] = $lTerm;
                    unset($lTerm);
                }
            }
        }
        return $res;
    }
    
    function constructBranchObj($term){
        $this->branch[$term->id]->id = $term->id;
        $this->branch[$term->id]->name = $term->name;
        $this->branch[$term->id]->alias = $term->alias != "" ? $term->alias : "/taxonomy/id".$term->id;
    }
            
    function getTree($activeTermId = null){
        $res = null;
        $query = "SELECT id, name, parent_id as pID, (select alias from aliases where address=concat('/taxonomy/id', taxonomy.id)) as alias FROM taxonomy WHERE isNULL(parent_id) ORDER BY name ASC, sort_order ASC";
        $query = mysql_query($query);
        if (mysql_num_rows($query) > 0){
            if ((int) $activeTermId)
                $activeTerm = $this->get($activeTermId);
            while ($term = mysql_fetch_object($query)) {
                $term->active = $this->isTermInBranch($term, $activeTerm);
                $term->childs = $this->getChilds($term);
                $res[$term->id] = $term;
            }
        }
        return $res;
    }
    
    private function isTermInBranch($term, $activeTerm){
        $res = false;
        if (!$activeTerm)
            return $res;
        
        if ($activeTerm->branch && $term->id){
            $id = $term->id;
            $branch = array_keys($activeTerm->branch);
            if ($term->id == $activeTerm->id)
                $res = true;
            else
                $res = in_array($id, $branch);
        }
        elseif($term->id == $activeTerm->id){
            $res = true;
        }
        return $res;
    }
    
}
?>
