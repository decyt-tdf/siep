<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <?php echo $this->Html->charset(); ?>
    <!-- Smartphones, tablet -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,  minimum-scale=1.0, maximum-scale=1.0, initial-scale=no">
    <!-- ******************* -->
    <!-- ************ Css *********** -->
    <?php echo $this->Html->css(array('bootstrap', 'bootstrap.min', 'bootstrap-theme.min', 'jquery-ui.min', 'fileinput.min')); ?>
    <!-- **************************************** -->
    <!-- ************* Js *******************-->
    <?php echo $this->Html->script(array('jquery.min', 'jquery-ui.min', 'bootstrap.min', 'fastclick', 'fileinput.min')); ?>
    <!-- **************************************** -->
    <!-- ************ Sigedu Css *********** -->
    <?php echo $this->Html->css('custom', 'stylesheet', array("media"=>"all" )); ?>
    <?php echo $this->Html->css('animate', 'stylesheet', array("media"=>"all" )); ?> 
    <!-- ************************************** -->
    <script type="text/javascript">
         $("#foto").fileinput();
    	 var basePath = "<?php echo Router::url('/'); ?>"
    </script>
    		<title>SIEP</title>
    </head>
    <body>
    	<div class="content">
    <!-- ******* menu principal ******* -->
    	<?php 
            if($this->Html->loggedIn()) { 
                $userRole = $current_user['role'];
                if ($userRole == 'superadmin') {
                    echo $this->element('menues/menu-sa');
                } elseif ($userRole == 'admin') {
                    $userPuesto = $current_user['puesto'];
                    switch ($userPuesto) {
                        case 'Dirección Jardín':
                            echo $this->element('menues/menu-aip');
                            break;
                        case 'Dirección Escuela Primaria':
                            echo $this->element('menues/menu-aip');
                            break;
                        case 'Dirección Colegio Secundario':
                            echo $this->element('menues/menu-a');
                            break;                    
                        default:
                            //Dirección Instituto Superior.
                            echo $this->element('menues/menu-a');
                            break;
                    }
                } elseif ($userRole == 'usuario') {
                    $userPuesto = $current_user['puesto'];
                    switch ($userPuesto) {
                        case 'Supervisión Inicial/Primaria':
                            echo $this->element('menues/menu-aip');
                            break;
                        case 'Supervisión Secundaria':
                            echo $this->element('menues/menu-a');
                            break;                    
                        default:
                            //Dirección Provincial de Superior.
                            echo $this->element('menues/menu-a');
                            break;
                    }
                } elseif ($userRole == 'viewer') {
                    $userPuesto = $current_user['puesto'];
                    switch ($userPuesto) {
                        case 'Unidad de Estadística Educativa':
                            echo $this->element('menues/menu-e');
                            break;
                        default:
                            echo $this->element('menues/menu-a');
                            break;
                    }
                }            
            }    
        ?>
    <!-- ******************************* -->
    	<script>
            $(function() {
                FastClick.attach(document.body);
            });
        </script>
        <?php echo $scripts_for_layout;?>
        <div id="bg" class="animated fadeIn"></div>			
    		<?php echo $this->Session->flash(); ?>
            <?php echo $content_for_layout; ?>
        </div>
        <br>
        <div class="footer">
            <p><?php echo $this->Html->link('License  Creative Commons: by-nc-sa', 'http://creativecommons.org/licenses/by-nc-sa/3.0'); ?> </p>
        </div>
    </body>
</html>
