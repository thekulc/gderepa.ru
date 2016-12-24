<?
namespace admin\taxonomy;

class taxonomy extends \Admin {

    function default_method()
    {
        $terms = $terms = $this->get();
        if ($this->id) {
            $obj['term'] = $this->get($this->id);
            $obj['types'] = $this->get_controller("mObject", "add", true)->get_types();
            $obj['terms'] = $terms;
            $obj['options']['size'] = 10;
            return $this->layout_show('admin/term.html', $obj);
        }
        else{
            $obj = array();
            $obj['terms'] = $this->get();
            return $this->layout_show('admin/index.html', $obj);
        }
        
    }
    
    function get($id = null){
        $terms = array();
        
        $query = "SELECT id, name, description, type_id, parent_id as pID, sort_order, (SELECT name FROM taxonomy WHERE id = pID) as parent_name, (SELECT description FROM types WHERE types.id=taxonomy.type_id) as type_description";
        
        if ($id)
            $query .= ", (SELECT alias FROM aliases WHERE address = '/taxonomy/id".$id."') as alias FROM taxonomy WHERE id = ".$id;
        else 
            $query .= ", (select alias from aliases where address=concat('/taxonomy/id', taxonomy.id)) as alias FROM taxonomy";
        $termsQ = mysql_query($query);
        if (mysql_num_rows($termsQ)>0){
            while ($term = mysql_fetch_assoc($termsQ)) {
                $terms[] = $term;
            }
        }
        return $terms;
    }
    
    
}
?>
