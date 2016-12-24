<?
namespace admin\index;

class logout extends \Admin {

    function default_method()
    {
        unset($_SESSION['user']);
        $this->redirect('/admin/');
    }
}
?>
