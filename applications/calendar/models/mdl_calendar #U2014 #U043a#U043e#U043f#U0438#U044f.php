<?
namespace calendar;

$opt['host'] = HOST;
$opt['user'] = USER;
$opt['pass'] = PASSWORD;
$opt['db'] = DASTABASE;

class mdl_calendar extends \Model {
    
    public function getData($tbl){
        return $this->getInd('id', "SELECT * FROM ?n", $tbl);
        //return $this->select($tbl);
    }

    function getRusMonthName(){
        return array(
            '1'=>'Январь',
            '2'=>'Февраль',
            '3'=>'Март',
            '4'=>'Апрель',
            '5'=>'Май',
            '6'=>'Июнь',
            '7'=>'Июль',
            '8'=>'Август',
            '9'=>'Сентябрь',
            '10'=>'Октябрь',
            '11'=>'Ноябрь',
            '12'=>'Декабрь');
    }

    function getArendators(){
        return $this->getInd("SELECT *, (select contacts from users_description where users_description.user_id = users.id) as contacts FROM `users` inner join user_role on (users.id = user_role.user_id) where user_role.role_id in (1,3)");
        //return $this->selectQuery("SELECT *, (select contacts from users_description where users_description.user_id = users.id) as contacts FROM `users` inner join user_role on (users.id = user_role.user_id) where user_role.role_id in (1,3)", true);
    }
    
    function getMonthEvents($date){
        $dtStart = $date . '-01 00:00:00';
        return $this->getInd("SELECT id, date, title, description, type_id, owner_id, (SELECT FIO FROM users WHERE id = owner_id LIMIT 1) as owner from events WHERE `date` BETWEEN date_add('?s', interval -1 month) AND date_add('?s', interval +2 month) ORDER BY date DESC",$dtStart, $dtStart);
        //return $this->select("events", "`date` BETWEEN date_add('{$dtStart}', interval -1 month) AND date_add('{$dtStart}', interval +2 month)", "id, date, title, description, type_id, owner_id, (SELECT FIO FROM users WHERE id = owner_id LIMIT 1) as owner", "date DESC");
    }
    
    function insert($event){
        $eventStr = $this->getStr($event);
        $sql = "INSERT INTO events SET " . $eventStr;
        if (!mysql_query($sql))
            return mysql_error ();
        else 
            return true;
    }
    
    private function getStr($event){
        $res = "";
        foreach ($event as $key => $value) {
            $res .= $key . " = '" . $value . "', ";
        }
        
        return substr($res, 0, -2);
    }



}
?>
