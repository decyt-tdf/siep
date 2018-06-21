<?php echo $this->Html->script(array('acordeon', 'slider')); ?>
<?php echo $this->Html->css('slider.css'); ?>
<?php error_reporting (0);?> <!-- oculta los mensaje de error de indice (barrio o asentamiento) -->
<!-- start main -->
<div class="TituloSec"><?php echo ($persona['Persona']['apellidos']).' '.($persona['Persona']['nombres']); ?></div>
  <div id="ContenidoSec">
     <div class="row">
        <div class="col-md-8">
	       <div class="unit">
 		      <div class="row perfil">
                 <div class="col-md-4 col-sm-6 col-xs-12 thumbnail text-center">
					<!-- Sí no tiene foto presenta imagen de avatar según el sexo. -->
	                <?php if(($foto == 0) && ($persona['Persona']['sexo'] == 'MASCULINO')): ?>
	                         <?php echo $this->Html->image('../img/avatar-masculino.png', array('class' => 'img-thumbnail img-responsive img-rounded')); ?>
	                <?php endif; ?>
	                <?php if(($foto == 0) && ($persona['Persona']['sexo'] == 'FEMENINO')): ?>
	                         <?php echo $this->Html->image('../img/avatar-femenino.png', array('class' => 'img-thumbnail img-responsive img-rounded')); ?>
	                <?php endif; ?>
	                <!-- Sí tiene foto la presenta. -->
	                <?php if($foto == 1): ?>
	                         <?php echo $this->Html->image('../files/alumno/foto/' . $persona['Persona']['foto_dir'] . '/' . 'thumb_' .$persona['Persona']['foto'], array('class' => 'img-thumbnail img-responsive img-rounded')); ?>
	                <?php endif; ?>
  	             </div>
                 <div class="col-md-8 col-sm-6 col-xs-8">
                    <b><?php echo __('Nombres: '); ?></b>
                    <?php echo ($persona['Persona']['nombres']); ?></p>
                    <b><?php echo __('Apellidos: '); ?></b>
                    <?php echo ($persona['Persona']['apellidos']); ?></p>
                    <b><?php echo __('Documento: '); ?></b>
                    <?php echo ($persona['Persona']['documento_tipo']).' '.($persona['Persona']['documento_nro']); ?></p>
                    <b><?php echo __('Edad: '); ?></b>
                    <?php echo ($persona['Persona']['edad']); ?></p>
	                </div>
                    <div class="col-md-8 col-sm-6 col-xs-8">
                    <b><?php echo __('División Política: '); ?></b>
                    <?php echo ($persona['Persona']['division_politica']); ?></p>
                    <b><?php echo __('Lugar de Nacimiento: '); ?></b>
                    <?php echo ($persona['Persona']['pcia_nac']); ?></p>
                    <b><?php echo __('Domicilio: '); ?></b><br>
                    <?php echo ' Calle: '.$persona['Persona']['calle_nombre'].' N° '.$persona['Persona']['calle_nro'].' | Dto/Casa: '.$persona['Persona']['depto_casa'].' |  Tira/Edificio: '.$persona['Persona']['tira_edificio'].' | Ciudad: '.$ciudadNombre[$persona['Persona']['ciudad_id']]; ?></p>
                     <!-- Sí tiene barrio guardado lo muestra. -->
                    <?php if (($barrioNombre[$persona['Persona']['barrio_id']]) !== null):?>
                          <b><?php echo __('Barrio: '); ?></b>
                          <?php echo ($barrioNombre[$persona['Persona']['barrio_id']]); ?></p>
                    <?php else: ?> <!-- Sí no tiene barrio guardado consulta si tiene asentamiento. -->
                          <?php if (($AsentamientoNombre[$persona['Persona']['asentamiento_id']]) !== null):?>
                          <b><?php echo __('Asentamiento: '); ?></b>
                          <?php echo $AsentamientoNombre[$persona['Persona']['asentamiento_id']]; ?></p>
                          <?php endif; ?>
                    <?php endif; ?>
                    <b><?php echo __('Telefono: '); ?></b>
                    <?php echo $persona['Persona']['telefono_nro']; ?></p>
                    <b><?php echo __('Email: '); ?></b>
                    <?php echo ($this->Html->link($persona['Persona']['email'],'mailto:'.$persona['Persona']['email'])); ?></p>
                    <!--
                    <b><?php echo __('Ocupación: '); ?></b>
                    <?php echo ($persona['Persona']['ocupacion']); ?></p>
                    <b><?php echo __('Lugar de trabajo: '); ?></b>
                    <?php echo ($persona['Persona']['lugar_de_trabajo']); ?></p>
                    <b><?php echo __('Horario de trabajo: '); ?></b>
                    <?php echo ($persona['Persona']['horario_de_trabajo']); ?></p>
                    -->
                    <b><?php echo __('Observaciones: '); ?></b>
                    <?php echo $persona['Persona']['observaciones']; ?></p>
                 </div>
 	          </div>
         </div>
    </div>
<!-- star sidebar -->
<div class="col-md-4">
 <div class="unit">
        <div class="subtitulo">Opciones</div>
        <div class="opcion"><?php echo $this->Html->link(__('Listar Personas'), array('action' => 'index')); ?></div>
      	<div class="opcion"><?php echo $this->Html->link(__('Editar'), array('action' => 'edit', $persona['Persona']['id'])); ?></div>
       	<?php if($current_user['role'] == 'superadmin'): ?>
              <div class="opcion"><?php echo $this->Html->link(__('Borrar'), array('action' => 'delete', $persona['Persona']['id']), null, sprintf(__('Esta seguro de borrar al alumno %s?'), $persona['Persona']['nombre_completo_persona'])); ?></div>
      <?php endif; ?>
	</div>
  </div>
</div>
<!-- end main -->
<?php /*
<!-- Familiares Relacionados
<div id="click_01" class="titulo_acordeon">Familiares Relacionadas <span class="caret"></span></div>
<div id="acordeon_01">
	<div class="row">
	<?php if (!empty($persona['Familiar'])):?>
		<div class="col-xs-12 col-sm-6 col-md-8">
			<?php foreach ($persona['Familiar'] as $familiar): ?>
            <div class="col-md-6">
                <div class="unit">
                    <?php echo '<b>Vinculo:</b> '.$familiar['vinculo'];?><br>
                    <?php echo '<b>Nombre:</b> '.$personaNombre[$familiar['persona_id']];?><br>
                    <?php echo '<b>Cuil/Cuit:</b> '.$personaCuilCuit[$familiar['persona_id']];?><br>
                    <?php echo '<b>Telefono:</b> '.$personaTelefono[$familiar['persona_id']];?><br>
                    <?php echo '<b>Email:</b> '.$personaEmail[$familiar['persona_id']];?><br>
                    <!--<?php echo '<b>Domicilio:</b> '.$familiar['domicilio'];?><br>
                <div class="text-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-edit"></i>'), array('controller' => 'familiars', 'action' => 'edit', $familiar['id']), array('class' => 'btn btn-warning','escape' => false)); ?>
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'familiars', 'action' => 'view', $familiar['id']), array('class' => 'btn btn-success','escape' => false)); ?>
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'familiars', 'action' => 'delete', $familiar['id']), array('class' => 'btn btn-danger','escape' => false)); ?>
                </div>
		    </div>
	    </div>
	    <?php endforeach; ?>
    </div>
	<?php else: echo '<div class="col-md-12"><div class="unit text-center">No se encuentran relaciones.</div></div>'; ?>
    <?php endif; ?>
  </div>
</div>
<!-- end Familiares Relacionados -->
<!-- Inscripciones Relacionadas
	<div id="click_03" class="titulo_acordeon">Inscripciones Relacionadas <span class="caret"></span></div>
	<div id="acordeon_03">
		<div class="row">
			<?php if (!empty($persona['Inscripcion'])):?>
  			<div class="col-xs-12 col-sm-6 col-md-8">
				<?php foreach ($persona['Inscripcion'] as $inscripcion):	?>
			<div class="col-md-4">
				<div class="unit">
					<!--<?php echo '<b>Ciclo id:</b> '.($this->Html->link($inscripcion['ciclo_id'], array('controller' => 'ciclos', 'action' => 'view', $inscripcion['ciclo_id'])));?><br>
					<?php echo '<b>Centro:</b> '.($this->Html->link($centroNombre[$inscripcion['centro_id']], array('controller' => 'centros', 'action' => 'view', $inscripcion['centro_id'])));?><br>
					<?php echo '<b>Código:</b> '.$inscripcion['legajo_nro'];?><br>
					<!--<?php echo '<b>Tipo de alta:</b> '.$inscripcion['tipo_alta'];?><br>
					<?php echo '<b>Fecha de alta:</b> '.$this->Html->formatTime($inscripcion['fecha_alta']);?><br>
					<!--<?php echo '<b>Cursa:</b> '.$inscripcion['cursa'];?><br>
		            <?php echo '<b>Fecha de baja:</b> '.$this->Html->formatTime($inscripcion['fecha_baja']);?><br>
					<?php echo '<b>Tipo de baja:</b> '.$inscripcion['tipo_baja'];?><br>
		            <?php echo '<b>Fecha de egreso:</b> '.$this->Html->formatTime($inscripcion['fecha_egreso']);?><br>
		            <!--<?php echo '<b>Nota:</b> '.$inscripcion['nota'];?><br>
		            <b>Estado:</b> <?php if($inscripcion['estado'] == "COMPLETA"){; ?><span class="label label-success"><?php echo $inscripcion['estado']; ?></span><?php } else{; ?><span class="label label-danger"><?php echo $inscripcion['estado']; ?></span><?php } ?></br>
		            <hr>
		            <div class="text-right">
			            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'inscripcions', 'action' => 'view', $inscripcion['id']), array('class' => 'btn btn-success','escape' => false)); ?>
			            <?php if(($current_user['role'] == 'superadmin') || ($current_user['role'] == 'admin')): ?>
			            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-edit"></i>'), array('controller' => 'inscripcions', 'action' => 'edit', $inscripcion['id']), array('class' => 'btn btn-warning','escape' => false)); ?>
						<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'inscripcions', 'action' => 'delete', $inscripcion['id']), array('class' => 'btn btn-danger','escape' => false)); ?>
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
<!-- end Inscripciones Relacionadas -->
<!-- Integraciones Relacionadas
	<div id="click_04" class="titulo_acordeon">Integraciones Relacionadas <span class="caret"></span></div>
	<div id="acordeon_04">
		<div class="row">
		<?php if (!empty($persona['Integracion'])):?>
  			<div class="col-xs-12 col-sm-6 col-md-8">
	<?php foreach ($persona['Integracion'] as $integracion):	?>
	<div class="col-md-6">
		<div class="unit">
			<?php echo '<b>Ciclo ID:</b> '.($this->Html->link($integracion['ciclo_id'], array('controller' => 'ciclos', 'action' => 'view', $integracion['ciclo_id'])));?><br>
			<?php echo '<b>Centro integrador:</b> '.($this->Html->link($integracion['centro_id'], array('controller' => 'centros', 'action' => 'view', $integracion['centro_id'])));?><br>
			<?php echo '<b>Docente integrador:</b> '.$integracion['docente_nombre_completo'];?><br>
			<?php echo '<b>Fecha de inicio:</b> '.$this->Html->formatTime($integracion['fecha_inicio']);?><br>
            <?php echo '<b>Fecha de fin:</b> '.$this->Html->formatTime($integracion['fecha_fin']);?><br>
            <div class="text-right">
            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-edit"></i>'), array('controller' => 'integracions', 'action' => 'edit', $integracion['id']), array('class' => 'btn btn-warning','escape' => false)); ?>
			<?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-eye-open"></i>'), array('controller' => 'integracions', 'action' => 'view', $integracion['id']), array('class' => 'btn btn-success','escape' => false)); ?>
			<?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-trash"></i>'), array('controller' => 'integracions', 'action' => 'delete', $integracion['id']), array('class' => 'btn btn-danger','escape' => false)); ?>
			</div>
		</div>
	</div>
		<?php endforeach; ?>
			</div>
		<?php else: echo '<div class="col-md-12"><div class="unit text-center">No se encuentran relaciones.</div></div>'; ?>
		<?php endif; ?>
	</div>
</div>
<!-- end Integraciones Relacionadas -->
<!-- Servicios Complementarios Relacionadas
	<div id="click_05" class="titulo_acordeon">Servicios Complementarios Relacionadas <span class="caret"></span></div>
	<div id="acordeon_05">
		<div class="row">
			<?php if (!empty($persona['Servicio'])):?>
  			<div class="col-xs-12 col-sm-6 col-md-8">
	<?php foreach ($persona['Servicio'] as $servicio): ?>
	<div class="col-md-6">
		<div class="unit">
			<?php echo '<b>Ciclo ID:</b> '.($this->Html->link($servicio['ciclo_id'], array('controller' => 'ciclos', 'action' => 'view', $servicio['ciclo_id'])));?><br>
			<?php echo '<b>Tipo:</b> '.$servicio['tipo_servicio'];?><br>
			<?php echo '<b>Estado:</b> '.$servicio['estado'];?><br>
			<?php echo '<b>Docente/Profesional a cargo:</b> '.$servicio['docente_profesional_acargo'];?><br>
            <?php echo '<b>Fecha de alta:</b> '.$this->Html->formatTime($servicio['fecha_alta']);?><br>
            <?php echo '<b>Fecha de baja:</b> '.$this->Html->formatTime($servicio['fecha_baja']);?><br>
            <div class="text-right">
            <?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-edit"></i>'), array('controller' => 'servicios', 'action' => 'edit', $servicio['id']), array('class' => 'btn btn-warning','escape' => false)); ?>
			<?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-eye-open"></i>'), array('controller' => 'servicios', 'action' => 'view', $servicio['id']), array('class' => 'btn btn-success','escape' => false)); ?>
			<?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-trash"></i>'), array('controller' => 'integracions', 'action' => 'delete', $servicio['id']), array('class' => 'btn btn-danger','escape' =>false)); ?>
			</div>
		</div>
	</div>
		<?php endforeach; ?>
			</div>
		<?php else: echo '<div class="col-md-12"><div class="unit text-center">No se encuentran relaciones.</div></div>'; ?>
		<?php endif; ?>
	</div>
</div>
<!-- end Servicios Complementarios Relacionadas -->
<!-- Inasistencias Relacionadas
	<div id="click_06" class="titulo_acordeon">Inasistencias Relacionadas <span class="caret"></span></div>
	<div id="acordeon_06">
		<div class="row">
		<?php if (!empty($persona['Inasistencia'])):?>
  			<div class="col-xs-12 col-sm-6 col-md-8">
	<?php foreach ($persona['Inasistencia'] as $inasistencia): ?>
	<div class="col-md-6">
		<div class="unit">
			<?php echo '<b>Ciclo Id:</b> '.($this->Html->link($inasistencia['ciclo_id'], array('controller' => 'ciclos', 'action' => 'view', $inasistencia['ciclo_id'])));?><br>
			<?php echo '<b>Tipo:</b> '. $inasistencia['tipo'];?><br>
			<?php echo '<b>Causa:</b> '.$inasistencia['causa'];?><br>
            <?php echo '<b>Justificado:</b> '.$inasistencia['justificado'];?><br>
            <?php echo '<b>Fecha:</b> '.$this->Html->formatTime($inasistencia['created']);?><br>
            <div class="text-right">
	            <?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-edit"></I>'), array('controller' => 'inasistencias', 'action' => 'edit', $inasistencia['id']), array('class' => 'btn btn-warning','escape' => false)); ?>
				<?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-eye-open"></i>'), array('controller' => 'inasistencias', 'action' => 'view', $inasistencia['id']), array('class' => 'btn btn-success','escape' => false)); ?>
				<?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-trash"></i>'), array('controller' => 'inasistencias', 'action' => 'delete', $inasistencia['id']), array('class' => 'btn btn-danger','escape' => false)); ?>
			</div>
		</div>
	</div>
		<?php endforeach; ?>
			</div>
		<?php else: echo '<div class="col-md-12"><div class="unit text-center">No se encuentran relaciones.</div></div>'; ?>
		<?php endif; ?>
	</div>
</div>
<!-- end Inasistencias Relacionadas -->
<!-- Calificaciones Relacionadas
<div id="click_07" class="titulo_acordeon">Calificaciones Relacionadas
	<span class="caret"></span>
</div>
<div id="acordeon_07">
	<div class="row">
		<?php if (!empty($persona['Nota'])):?>
  	<!-- Swiper
	    <div class="swiper-container" style="height: 200px;">
    	    <div class="swiper-wrapper" >
				<?php foreach ($persona['Nota'] as $nota): ?>
				<div class="swiper-slide">
					<div class="col-md-6">
						<div class="unit">
							<?php echo '<b>Ciclo:</b> '. ($this->Html->link($cicloNombre[$nota['ciclo_id']], array('controller' => 'ciclos', 'action' => 'view', $nota['ciclo_id'])));?><br>
							<?php echo '<b>Espacio:</b> '. ($this->Html->link($materiaAlia[$nota['materia_id']], array('controller' => 'materias', 'action' => 'view', $nota['materia_id'])));?><br>
							<?php echo '<b>Estado:</b>'?> <?php if($nota['estado'] == "En curso"){; ?><span class="label label-default"><?php echo $nota['estado']; ?></span><?php } else if($nota['estado'] == "Abandonada"){; ?><span class="label label-info"><?php echo $nota['estado']; ?></span><?php } else if($nota['estado'] == "Regularizada"){; ?><span class="label label-warning"><?php echo $nota['estado']; ?><?php } else if($nota['estado'] == "Desaprobada"){; ?><span class="label label-danger"><?php echo $nota['estado']; }?></span></br>
				            <div class="text-right">
				            <?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-edit"></i>'), array('controller' => 'notas', 'action' => 'edit', $nota['id']), array('class' => 'btn btn-warning','escape' => false )); ?>
							<?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-eye-open"></i>'), array('controller' => 'notas', 'action' => 'view', $nota['id']), array('class' => 'btn btn-success','escape' => false)); ?>
							<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'notas', 'action' => 'delete', $nota['id']), array('class' => 'btn btn-danger','escape' => false )); ?>
				        </div>
			        </div>
	            </div>
            </div>
		<?php endforeach; ?>
	</div>
	<!-- Add Pagination
    <div class="swiper-pagination"></div>
    <!-- Include plugin after Swiper
	<?php else: echo '<div class="col-md-12"><div class="unit text-center">No se encuentran relaciones.</div></div>'; ?>
	<?php endif; ?>
    </div>
  </div>
<!-- end Calificaciones Relacionadas -->
*/ ?>
</div>
<!-- Initialize Swiper -->
    <script>
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
    });
    </script>
