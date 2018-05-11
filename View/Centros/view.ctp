<?php echo $this->Html->script(array('acordeon', 'slider')); ?>
<?php echo $this->Html->css('slider.css'); ?>
<!-- start main -->
<div class="TituloSec"><?php echo ($centro['Centro']['sigla']); ?></div>
<div id="ContenidoSec">
    <div class="row">
    	<div class="col-md-8">
	 		<div class="unit">
 				<div class="row perfil">
			  	<!--<div class="col-md-4 col-sm-6 col-xs-12 thumbnail text-center">
			  		<img src="http://ipam.com.br/2012/fotos/image/facebook-silueta-perfil-300x203.jpg"/>
			  	</div>-->
   					<div class="col-md-6 col-sm-6 col-xs-8">
                      <div id="click_01" class="titulo_acordeon_datos">Datos Generales <span class="caret"></span></div>
                      <div id="acordeon_01">
                        <div class="unit">
							<b><?php echo __('Nombre: '); ?></b>
							<?php echo ($centro['Centro']['nombre']); ?></p>
							<b><?php echo __('Fecha de Fundación: '); ?></b>
							<?php echo ($centro['Centro']['fechaFundacion']); ?></p>
							<b><?php echo __('Sector: '); ?></b>
							<?php echo ($centro['Centro']['sector']); ?></p>
							<b><?php echo __('Nivel - Servicio: '); ?></b>
							<?php echo ($centro['Centro']['nivel_servicio']); ?></p>
							<b><?php echo __('Director: '); ?></b>
				    			<?php echo $centro['Centro']['equipoDirectivo']; ?></p>
							<b><?php echo __('Ámbito: '); ?></b>
								<?php echo $centro['Centro']['ambito']; ?></p>
				        </div>
	   				</div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-8">
                      	<div id="click_02" class="titulo_acordeon_datos">Datos de Contacto <span class="caret"></span></div>
                      	<div id="acordeon_02">
	                        <div class="unit">
	                        	<b><?php echo __('Domicilio: '); ?></b>
								<?php echo $centro['Centro']['direccion']; ?></p>
								<b><?php echo __('Barrio: '); ?></b>
									<?php echo $barrios[$centro['Centro']['barrio_id']]; ?></p>
								<b><?php echo __('Código Postal: '); ?></b>
									<?php echo $centro['Centro']['cp']; ?></p>
								<b><?php echo __('Código de Localidad: '); ?></b>
									<?php echo $centro['Centro']['codigo_localidad']; ?></p>
								<b><?php echo __('Ciudad: '); ?></b>
									<?php echo $ciudades[$centro['Centro']['ciudad_id']]; ?></p>
								<b><?php echo __('Departamento: '); ?></b>
								<?php echo $departamentos[$centro['Centro']['departamento_id']]; ?></p>
							    <b><?php echo __('Telefono: '); ?></b>
									<?php echo $centro['Centro']['telefono']; ?></p>
		                        <b><?php echo __('Email: '); ?></b>
									<?php echo ($this->Html->link($centro['Centro']['email'],'mailto:'.$centro['Centro']['email'])); ?></p>
		                        <b><?php echo __('URL: '); ?></b>
									<?php echo ($this->Html->link($centro['Centro']['url'],'href:'.$centro['Centro']['url'])); ?></p>
		          			</div>
 				        </div>
 					</div>
 				</div>
			</div>
		</div>
<!-- star sidebar -->
		<div class="col-md-4">
 			<div class="unit">
 				<div class="subtitulo">Opciones</div>
				<div class="opcion"><?php echo $this->Html->link(__('Listar Instituciones'), array('controller' => 'centros', 'action' => 'index')); ?></div>
				<?php /*
				<!--<div class="opcion"><?php echo $this->Html->link(__('Listar Ciclos'), array('controller' => 'ciclos', 'action' => 'index')); ?></div>
		        <div class="opcion"><?php echo $this->Html->link(__('Listar Titulaciones'), array('controller' => 'titulacions', 'action' => 'index')); ?></div>
                <div class="opcion"><?php echo $this->Html->link(__('Listar Cursos'), array('controller' => 'cursos', 'action' => 'index')); ?></div>
		        <div class="opcion"><?php echo $this->Html->link(__('Listar Alumnos'), array('controller' => 'alumnos', 'action' => 'index')); ?></div>
		        <div class="opcion"><?php echo $this->Html->link(__('Listar Inscripciones'), array('controller' => 'inscripcions', 'action' => 'index')); ?></div>
                <!--<div class="opcion"><?php echo $this->Html->link(__('Listar Inasistencias'), array('controller' => 'inasistencias', 'action' => 'index')); ?></div>-->
			  	<!--<div class="opcion"><?php echo $this->Html->link(__('Exportar a PDF'), array('action' => 'view', $centro['Centro']['id'], 'ext' => 'pdf')); ?></div>-->
				*/ ?>
			  <?php if($current_user['role'] == 'superadmin'): ?>
				<div class="opcion"><?php echo $this->Html->link(__('Editar'), array('action' => 'edit', $centro['Centro']['id'])); ?></div>
		        <div class="opcion"><?php echo $this->Html->link(__('Borrar'), array('action' => 'delete', $centro['Centro']['id']), null, sprintf(__('Esta seguro de borrar el centro %s?'), $centro['Centro']['sigla'])); ?></div>
		      <?php endif; ?>
  			</div>
 		</div>
	</div>
<!-- end sidebar -->
<?php /*
<!--<div class="related">
	<h3><?php echo __('Cargos Relacionados');?></h3>
	<?php if (!empty($centro['Cargo'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Nombre'); ?></th>
		<th><?php echo __('Tipo'); ?></th>
		<th><?php echo __('Resolucion Nro'); ?></th>
		<th><?php echo __('Hs Catedra'); ?></th>
		<th><?php echo __('Hs Reloj'); ?></th>
		<th><?php echo __('Area'); ?></th>
		<th><?php echo __('Puesto'); ?></th>
		<th><?php echo __('Descripcion'); ?></th>
		<th><?php echo __('Creacion'); ?></th>
		<th><?php echo __('Cierre'); ?></th>
		<th><?php echo __('Alta'); ?></th>
		<th><?php echo __('Baja'); ?></th>
		<th><?php echo __('Cambio Situacion'); ?></th>
		<th><?php echo __('Estado'); ?></th>
		<th><?php echo __('Centro Id'); ?></th>
		<th><?php echo __('Curso Id'); ?></th>
		<th><?php echo __('Materia Id'); ?></th>
		<th class="actions"><?php echo __('Opciones');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($centro['Cargo'] as $cargo):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $cargo['id'];?></td>
			<td><?php echo $cargo['nombre'];?></td>
			<td><?php echo $cargo['tipo'];?></td>
			<td><?php echo $cargo['resolucionNro'];?></td>
			<td><?php echo $cargo['hsCatedra'];?></td>
			<td><?php echo $cargo['hsReloj'];?></td>
			<td><?php echo $cargo['area'];?></td>
			<td><?php echo $cargo['puesto'];?></td>
			<td><?php echo $cargo['descricpion'];?></td>
			<td><?php echo $cargo['fechaCreacion'];?></td>
			<td><?php echo $cargo['fechaCierre'];?></td>
			<td><?php echo $cargo['fechaAltaPersona'];?></td>
			<td><?php echo $cargo['fechaBajaPersona'];?></td>
			<td><?php echo $cargo['fechaCambioSituacionPersona'];?></td>
		    <td><?php echo $cargo['estado'];?></td>
			<td><?php echo ($this->Html->link($cargo['centro_id'], array('controller' => 'centros', 'action' => 'view', $cargo['centro_id'])));?></td>
			<td><?php echo ($this->Html->link($cargo['curso_id'], array('controller' => 'centros', 'action' => 'view', $cargo['curso_id'])));?></td>
			<td><?php echo ($this->Html->link($cargo['materia_id'], array('controller' => 'centros', 'action' => 'view', $cargo['materia_id'])));?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('Ver'), array('controller' => 'cargos', 'action' => 'view', $cargo['id'])); ?>
				<?php echo $this->Html->link(__('Editar'), array('controller' => 'cargos', 'action' => 'edit', $cargo['id'])); ?>
				<?php echo $this->Html->link(__('Borrar'), array('controller' => 'cargos', 'action' => 'delete', $cargo['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $cargo['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('Nuevo Cargo'), array('controller' => 'cargos', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>-->
*/ ?>
<?php /*
<!-- Titulaciones Relacionadas -->
<div id="click_03" class="titulo_acordeon">Titulaciones Relacionadas <span class="caret"></span></div>
<div id="acordeon_03">
	<div class="row">
	<?php if (!empty($centro['Titulacion'])):?>
		<div class="col-xs-12 col-sm-6 col-md-8">
		<?php foreach ($centro['Titulacion'] as $titulacion): ?>
            <div class="col-md-4">
                <div class="unit">
					<?php echo '<b>Nombre:</b> '.$titulacion['nombre'];?><br>
					<?php echo '<b>Orientación:</b> '.$titulacion['orientacion'];?><br>
		            <?php echo '<b>Organización del plan:</b> '.$titulacion['organizacion_plan'];?><br>
					<!--<?php echo '<b>Plan de estudio:</b> '.$titulacion['plan_estudio'];?><br>-->
					<!--<?php echo '<b>Organización de la cursada:</b> '.$titulacion['organizacion_cursada'];?><br>-->
		            <?php echo '<b>Forma del dictado:</b> '.$titulacion['forma_dictado'];?><br>
			        <hr>
                    <div class="text-right">
                        <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'titulacions', 'action' => 'view', $titulacion['id']), array('class' => 'btn btn-success','escape' => false)); ?>
                        <?php if($current_user['role'] == 'superadmin'): ?>
                        <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-edit"></i>'), array('controller' => 'titulacions', 'action' => 'edit', $titulacion['id']), array('class' => 'btn btn-warning','escape' => false)); ?>
                        <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'titulacions', 'action' => 'delete', $titulacion['id']), array('class' => 'btn btn-danger','escape' => false)); ?>
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
<!-- end Titulaciones Relacionadas -->
*/ ?>
<?php /*
<!-- Cursos Relacionados -->
<div id="click_04" class="titulo_acordeon">Secciones Relacionadas <span class="caret"></span></div>
<div id="acordeon_04">
	<div class="row">
	<?php if (!empty($centro['Curso'])):?>
		<div class="col-xs-12 col-sm-6 col-md-8">
		<?php foreach ($centro['Curso'] as $curso): ?>
            <div class="col-md-4">
                <div class="unit">
                    <?php echo '<b>Año:</b> '.$curso['anio'];?><br>
                    <?php echo '<b>División:</b> '.$curso['division'];?><br>
                    <?php echo '<b>Turno:</b> '.$curso['turno'];?><br>
                    <!--<?php echo '<b>Tipo:</b> '.$curso['tipo'];?><br>
                    <?php echo '<b>Cursada:</b> '.$curso['organizacion_cursada'];?><br>
                    <?php echo '<b>Titulación:</b> '.($this->Html->link($curso['titulacion_id'], array('controller' => 'titulacions', 'action' => 'view', $curso['titulacion_id'])));?><br>-->
                    <hr>
                    <div class="text-right">
                        <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'cursos', 'action' => 'view', $curso['id']), array('class' => 'btn btn-success','escape' => false)); ?>
                       <?php if($current_user['role'] == 'superadmin'): ?> 
                        <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-edit"></i>'), array('controller' => 'cursos', 'action' => 'edit', $curso['id']), array('class' => 'btn btn-warning','escape' => false)); ?>
                        <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'cursos', 'action' => 'delete', $curso['id']), array('class' => 'btn btn-danger','escape' => false)); ?>
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
<!-- end Cursos Relacionados -->
<?php /*
<!-- Inscripciones Relacionadas
	<div id="click_02" class="titulo_acordeon">Inscripciones Relacionadas <span class="caret"></span></div>
	<div id="acordeon_02">
		<div class="row">
			<?php if (!empty($centro['Inscripcion'])):?>
  			<div class="col-xs-12 col-sm-6 col-md-8">
	<?php foreach ($centro['Inscripcion'] as $inscripcion):	?>
	<div class="col-md-4">
		<div class="unit">
			<!--<?php echo '<b>Ciclo id:</b> '.($this->Html->link($inscripcion['ciclo_id'], array('controller' => 'ciclos', 'action' => 'view', $inscripcion['ciclo_id'])));?><br>
			<?php echo '<b>Código:</b> '.$inscripcion['legajo_nro'];?><br>
			<!--<?php echo '<b>Tipo de alta:</b> '.$inscripcion['tipo_alta'];?><br>
			<?php echo '<b>Fecha de alta:</b> '.$this->Html->formatTime($inscripcion['fecha_alta']);?><br>
			<!--<?php echo '<b>Cursa:</b> '.$inscripcion['cursa'];?><br>
            <?php echo '<b>Fecha de baja:</b> '.$this->Html->formatTime($inscripcion['fecha_baja']);?><br>
			<?php echo '<b>Tipo de baja:</b> '.$inscripcion['tipo_baja'];?><br>
            <?php echo '<b>Fecha de egreso:</b> '.$this->Html->formatTime($inscripcion['fecha_egreso']);?><br>
            <!--<?php echo '<b>Nota:</b> '.$inscripcion['nota'];?><br>
            <b>Estado:</b> <?php if($inscripcion['estado'] == "COMPLETA"){; ?><span class="label label-success"><?php echo $inscripcion['estado']; ?></span><?php } else{; ?><span class="label label-danger"><?php echo $inscripcion['estado']; ?></span><?php } ?>
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
*/ ?>
<?php if($current_user['role'] == 'superadmin'): ?>
<!-- Empleados Relacionados -->
<div id="click_05" class="titulo_acordeon">Usuarios Relacionados <span class="caret"></span></div>
<div id="acordeon_05">
	<div class="row">
	<?php if (!empty($centro['User'])):?>
	<div class="col-xs-12 col-sm-6 col-md-8">
	<?php foreach ($centro['User'] as $user): ?>
		<div class="col-md-4">
			<div class="unit">
				<?php echo '<b>Nombre de Usuario:</b> '.$this->Html->link($user['username'], array('controller' => 'users', 'action' => 'view', $user['id']));?><br>
				<?php echo '<b>Puesto:</b> '.$user['puesto'];?><br>
	            <!--<?php echo '<b>Centro:</b> '.$user['centro_id'];?><br>-->
	            <hr>
	            <div class="text-right">
	            <?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-edit"></i>'), array('controller' => 'users', 'action' => 'edit', $user['id']), array('class' => 'btn btn-warning', 'escape' => false)); ?>
				<?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-eye-open"></i>'), array('controller' => 'users', 'action' => 'view', $user['id']), array('class' => 'btn btn-success','escape' => false)); ?>
				<?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-trash"></i>'), array('controller' => 'users', 'action' => 'delete', $user['id']), array('class' => 'btn btn-danger','escape' => false)); ?>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<?php else: echo '<div class="col-md-12"><div class="unit text-center">No se encuentran relaciones.</div></div>'; ?>
	<?php endif; ?>
</div>
</div>
<!-- end Agentes Relacionados -->
<?php endif; ?>
