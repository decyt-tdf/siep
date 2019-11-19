<?php echo $this->Html->script(array('acordeon', 'slider')); ?>
<div class="TituloSec">Familiares </div>
<div id="ContenidoSec">	
    <div class="row"><div class="col-md-8">
        <div class="unit perfil">
		    <div class="subtitulo"><?php echo ($familiar['Familiar']['vinculo']); ?></div>
	        <div class="row">
	            <div class="col-md-6 col-sm-6">
                    <div class="subtitulo"><?php echo 'Datos Personales'; ?></div>
                        <b>Nombre completo:</b>		
                        <?php echo ($familiarNombre); ?><br>
                        <b>Documento Nº:</b>       
                        <?php echo ($familiarDNI); ?><br>
                        <b>Nacionalidad:</b>        
                        <?php echo ($familiarNacionalidad); ?><br>
                        <!--<b>Ocupación:</b>       
                        <?php// echo ($familiarOcupacion); ?><br>
                        <b>Lugar de trabajo:</b>        
                        <?php// echo ($familiarLugarTrabaja); ?>-->
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="subtitulo"><?php echo 'Datos de Contacto'; ?></div>
                        <b>Ciudad:</b>
                        <?php echo ($ciudadNombre); ?><br>
                        <b>Barrio:</b>
                        <?php echo ($barrioNombre); ?><br>
                        <b>Domicilio:</b>
                        <b><?php echo '| Calle: '?></b><?php echo $familiarCalleNombre;?><b><?php echo ' | N°: ';?></b><?php echo $familiarCalleNumero;?><b><?php echo ' | Dto/Casa: ';?></b><?php echo $familiarDeptoCasa;?><b><?php echo ' | Tira/Edificio: ';?></b><?php echo $familiarTiraEdificio;?><br>
                        <b>Telefono:</b>
                        <?php echo ($familiarTelefono); ?><br>
                        <b>Email:</b>
                        <?php echo ($familiarEmail); ?><br>                                
                </div>
            </div>
            <div class="subtitulo"><?php echo 'Datos Generales'; ?></div>
            <b>Convive con el alumno:</b> <?php echo $familiarConvivienteRta; ?><br>
            <b>Autorizado a retirar el alumno:</b> <?php echo $familiarAutorizadoRetirarRta; ?><hr>
	        <b>Observaciones:</b>
            <?php echo ($familiar['Familiar']['observaciones']); ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="unit">
            <div class="subtitulo">Opciones</div>
            <div class="opcion"><?php echo $this->Html->link(__('Listar Alumnos'), array('action' => 'index', 'controller' => 'alumnos')); ?></div>
          <?php if(($current_user['role'] == 'superadmin' && $current_user['puesto'] == 'Sistemas') || ($current_user['role'] == 'usuario') || ($clave != '')): ?>  
            <div class="opcion"><?php echo $this->Html->link(__('Editar'), array('action' => 'edit', $familiar['Familiar']['id'])); ?></div>
          <?php endif; ?>
          <?php if($current_user['role'] == 'superadmin' && $current_user['puesto'] == 'Sistemas'): ?>  <div class="opcion"><?php echo $this->Html->link(__('Borrar'), array('action' => 'delete', $familiar['Familiar']['id'] ), null, sprintf(__('Esta seguro de borrar el familiar "'.$familiar['Familiar']['id'].'"'), $this->Form->value('Familiar.id'))); ?>
            </div>
          <?php endif; ?>  
        </div>
    </div>	
</div>
<!-- Alumnos Relacionados -->
<?php /* 
<div id="click_01" class="titulo_acordeon">Alumnos Relacionados <span class="caret"></span></div>
<div id="acordeon_01">
    <div class="row">
    <?php if (!empty($familiar['Alumno'])):?>
    <div class="col-xs-12 col-sm-6 col-md-8">
    <?php foreach ($familiar['Alumno'] as $alumno): ?>
        <div class="col-md-4">
            <div class="unit">
                <?php echo '<b>Documento:</b> '.$alumnoDocumentoTipo[$alumno['id']]. ' '.$$alumnoPersonaId[$alumno['id']];?><br>
                <?php echo '<b>Nombre Completo:</b> '.$alumnoNombre[$alumno['id']];?><br>
                <hr>
                <div class="text-right">
                <?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-edit"></i>'), array('controller' => 'alumnos', 'action' => 'edit', $alumno['id']), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                <?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-eye-open"></i>'), array('controller' => 'alumnos', 'action' => 'view', $alumno['id']), array('class' => 'btn btn-success','escape' => false)); ?>
              <?php if($current_user['role'] == 'superadmin'): ?>  
                <?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-trash"></i>'), array('controller' => 'alumnos', 'action' => 'delete', $alumno['id']), array('class' => 'btn btn-danger','escape' => false)); ?>
              <?php endif; ?>  
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: echo '<div class="col-md-12"><div class="unit text-center">No se encuentran relaciones.</div></div>'; ?>
    <?php endif; ?>
    </div>
</div>
*/ ?>  
<!-- end Alumnos Relacionados -->
