<?
namespace message;

class message extends \Controller {  
    
    function default_method()
    {
        return $this->layout_show('index.html');
    }
    
    function captchaCreate(){
        $values     = array('apple','strawberry','lemon','cherry','pear'); // image names //   // array('house','folder','monitor','man','woman','lock','rss'); -> for general theme
        $imageExt   = 'jpg'; // image extensions //
        $imagePath  = '/source/images/s3icons/fruit/'; // image path //  // images/general/ -> for general theme
        $imageW     = '33'; // icon width // 35 -> for general theme
        $imageH     = '33'; // icon height // 35 -> for general theme

        $rand       = mt_rand(0,(sizeof($values)-1));
        shuffle($values);
        $s3Capcha = '<p>Verify that you are a human, please choose <strong>'.$values[$rand]."</strong></p>\n";
        for($i=0;$i<sizeof($values);$i++) {
            $value2[$i] = mt_rand();
            $s3Capcha .= '<div><span>'.$values[$i].' <input type="radio" name="s3capcha" value="'.$value2[$i].'"></span><div style="background: url('.$imagePath.$values[$i].'.'.$imageExt.') bottom left no-repeat; width:'.$imageW.'px; height:'.$imageH.'px;cursor:pointer;display:none;" class="img" /></div></div>'."\n";
        }
        $_SESSION['s3capcha'] = $value2[$rand];
        return $s3Capcha;
    }
    
    function captchaCheck(){
        $res = false;
        if($_POST['s3capcha'] == $_SESSION['s3capcha'] && $_POST['s3capcha'] != '') {
            unset($_SESSION['s3capcha']);
            $res = true;
        } else 
            $res = FALSE;
        return $res;
    }
    
}
?>
