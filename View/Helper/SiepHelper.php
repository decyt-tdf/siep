<?php

App::uses('AppHelper', 'View/Helper');

class SiepHelper extends AppHelper
{
    public function isSuperAdmin()
    {
        return (AuthComponent::user('role') == 'superadmin') ? true : false;
    }

    public function isUsuario()
    {
        return (AuthComponent::user('role') == 'usuario') ? true : false;
    }

    public function isAdmin()
    {
        return (AuthComponent::user('role') == 'admin') ? true : false;
    }

    public function isViewer()
    {
        return (AuthComponent::user('role') == 'viewer') ? true : false;
    }

    public function logQuery($modelo)
    {
        $log = $modelo->getDataSource()->getLog(false, false);
        return $log['log'];
    }

    function logQuerySave($sql){
        //log error into a txt file and every day has unique file that contains the errors.
        //save file to tmp\logs\sql folder
        $log_dir_path = LOGS.'sql';
        $res1 = is_dir($log_dir_path);
        if($res1 != 1)
        {
            $res2= mkdir($log_dir_path, 0777, true);
        }
        $file = $log_dir_path.'/'.date('d-m-Y').".log";
        $message = date('Y-m-d G:i:s') . ' - ' . $sql;
        $handle = fopen($file, 'a+');
        if($handle !== false)
        {
            fwrite($handle, "\n\n".$message . "\n");
            fclose($handle);
        }
    }

    public function clearfix($counter,$rows=2)
    {
        if($counter%$rows==0)
        {
            echo '<div class="clearfix"></div>';
        }
        $counter++;
        return $counter;
    }
}