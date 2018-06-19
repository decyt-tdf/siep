<div class="col-md-6">
	<div class="unit">
        <!--<div class="col-md-4 col-sm-6 col-xs-12 thumbnail text-center">
            <?php if($inscripcion['Inscripcion']['estado_documentacion'] == "COMPLETA"): ?>
            <span class="checked"></span><?php echo $this->Html->image('../img/inscription_image.png', array('class' => 'img-thumbnail img-responsive')); ?>
            <?php endif; ?>
            <?php if($inscripcion['Inscripcion']['estado_documentacion'] == "PENDIENTE"): ?>
            <span class="error"></span><?php echo $this->Html->image('../img/inscription_image.png', array('class' => 'img-thumbnail img-responsive')); ?>
                <?php endif; ?>
        </div>-->
        <span class="name"><span class="glyphicon glyphicon-home"></span> <b>Centro:</b> <?php echo $this->Html->link($centros[$inscripcion['Inscripcion']['centro_id']], array('controller' => 'centros', 'action' => 'view', $inscripcion['Inscripcion']['centro_id'])); ?></span><br/>
        <span class="name"><span class="glyphicon glyphicon-info-sign"></span> <b>Código:</b> <?php echo $inscripcion['Inscripcion']['legajo_nro']; ?></span><br/>
        <span class="name"><span class="glyphicon glyphicon-user"></span> <b>Alumno:</b> <?php echo $this->Html->link($inscripcion['Alumno']['Persona']['nombre_completo_persona'], array('controller' => 'alumnos', 'action' => 'view', $inscripcion['Inscripcion']['alumno_id'])); ?></span><br/>
        <span class="name"><span class="glyphicon glyphicon-info-sign"></span> <b>Documentación:</b> <?php echo $inscripcion['Inscripcion']['estado_documentacion']; ?></span><br/>
        <span class="name"><span class="glyphicon glyphicon-info-sign"></span> <b>Estado:</b> <?php echo $inscripcion['Inscripcion']['estado_inscripcion']; ?></span><br/>
        <!--<span class="name"><span class="glyphicon glyphicon-calendar"></span> <b>Alta:</b> <?php echo $this->Html->formatTime($inscripcion['Inscripcion']['fecha_alta']);?></span><br/>-->
        <!--<span class="name"><span class="glyphicon glyphicon-calendar"></span> <b>Baja:</b> <?php echo $this->Html->formatTime($inscripcion['Inscripcion']['fecha_baja']);?></span><br/>--> 		   
        <!--<span class="name"><span class="glyphicon glyphicon-calendar"></span> <b>Egreso:</b> <?php echo $this->Html->formatTime($inscripcion['Inscripcion']['fecha_egreso']);?></span><br/>-->       
	    <hr />
        <div class="text-right">
            <span class="link"><?php echo $this->Html->link('<i class="glyphicon glyphicon-eye-open"></i>', array('controller' => 'inscripcions', 'action' => 'view', $inscripcion['Inscripcion']['id']), array('class' => 'btn btn-success','escape' => false)); ?></span>
            <span class="link"><?php echo $this->Html->link('<i class= "glyphicon glyphicon-edit"></i>', array('controller' => 'inscripcions', 'action' => 'edit', $inscripcion['Inscripcion']['id']), array('class' => 'btn btn-warning','escape' => false)); ?></span>
          <?php if($current_user['role'] == 'superadmin'): ?>  
            <span class="link"><?php echo $this->Html->link('<i class= "glyphicon glyphicon-trash"></i>', array('controller' => 'inscripcions', 'action' => 'delete', $inscripcion['Inscripcion']['id']), array('confirm' => 'Está seguro de borrar la inscripción nro.'.$inscripcion['Inscripcion']['id'], 'class' => 'btn btn-danger','escape' => false)); ?></span>
          <?php endif; ?>   
		</div>
	</div>
</div>
