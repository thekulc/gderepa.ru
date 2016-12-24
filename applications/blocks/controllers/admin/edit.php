<?
namespace admin\blocks;

class edit extends \Admin {

    function default_method()
    {
        if ($_POST && isset($this->id)){
            $_POST['id'] = substr($this->id,2);
            $message = $this->edit($_POST);
        }
        $message['href']='/admin/blocks/';
        $this->layout = "/source/";
        $object = $this->layout_get("message.html", array('message'=>$message));
        $this->layout = "/applications/blocks/layouts/";
        return $this->layout_show('admin/index.html', 
                array('object'=>$object));
    }
    
    function edit($block){
        $block['body'] = mysql_real_escape_string($block['body']);
        if (isset($block['isPub']))
                $block['isPub'] = TRUE;
        else $block['isPub'] = FALSE;
        
        $query = "UPDATE blocks SET name = '{$block['name']}',
            body='{$block['body']}',
            isPub='{$block['isPub']}'
            WHERE id ='{$block['id']}'";
        $query = mysql_query($query);
        if ($query!=true)
            $message['text'] = "Произошла ошибка: ".mysql_error();
        else $message['text'] .= "Блок <b>'{$block['name']}'</b> успешно изменен";
        return $message;
    }
    
}
?>
