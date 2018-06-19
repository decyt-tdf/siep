<div id='contenedor-personas'>
<!-- start main -->
<div class="TituloSec"> Personas</div>
<div id="ContenidoSec">
    <div id="main">
    <!-- start second nav -->
      <div class="row">
          <div class="col-xs-12 col-sm-8 col-md-8">
            <div id="second-nav">
			        <div class="unit text-center">
			  	      <?php// if(($current_user['role'] == 'superadmin') || ($current_user['role'] == 'usuario')) { ?>
                  <span class="link"><?php echo $this->Html->link('<i class= "glyphicon glyphicon-plus"></i> AGREGAR ', array('action' => 'add'), array('class' => 'btn btn-primary','escape' => false)); ?>
                  </span>
                <?php// } else if (($current_user['role'] == 'admin') && (($nivelCentroString == 'Común - Inicial') || ($nivelCentroString == 'Común - Primario'))) { ?>
                  <!--<span class="link"><?php// echo $this->Html->link('<i class= "glyphicon glyphicon-plus"></i> AGREGAR ', array('action' => 'add'), array('class' => 'btn btn-primary','escape' => false)); ?>
                  </span>-->
                <?php// } ?>
              </div>
            </div>
    <!-- end second nav -->
      	    <div class="row">
      			<?php foreach ($personas as $persona): ?>
                  <?php echo $this->element('persona',array( 'persona' => $persona )); ?>
                  <?php endforeach; ?>
      		  </div>
            <div class="unit text-center">
                <?php echo $this->element('pagination'); ?>
            </div>
	        </div>
    <!-- end main -->
          <div class="col-xs-12 col-sm-4 col-md-4">
      	    <div class="unit">
      		     <div class="subtitulo">Búsqueda</div>
    		        <br><?php echo $this->element('formsSearch/formSearch_persona'); ?>
      	         </div>
    		  </div>
	    </div>
  </div>
  <?php echo $this->Js->writeBuffer(); ?>
</div> <!-- contenedor-alumnos -->
