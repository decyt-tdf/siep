<?php echo $this->Html->script(array('acordeon', 'slider')); ?>
<?php echo $this->Html->css('slider.css'); ?>
<!-- start main -->
<div class="TituloSec">Sección <?php echo ($curso['Curso']['nombre_completo_curso']); ?></div>
<div id="ContenidoSec">
    <div class="row">
        <div class="col-md-8">	
	         <div class="unit">
 		        <div class="row perfil">
                    <div class="col-md-4 col-sm-6 col-xs-8">	
					<div id="click_01" class="titulo_acordeon_datos">Datos Generales <span class="caret"></span></div>
                      	<!--<div id="acordeon_01">-->
                         	<div class="unit">
								<b><?php echo __('Centro: '); ?></b>
									<?php echo ($this->Html->link($curso['Centro']['sigla'], array('controller' => 'centros', 'action' => 'view', $curso['Centro']['id']))); ?></p>
								<b><?php echo __('Año: '); ?></b>
									<?php echo ($curso['Curso']['anio']); ?></p>
								<b><?php echo __('División: '); ?></b>
									<?php echo ($curso['Curso']['division']); ?></p>
								<b><?php echo __('Turno: '); ?></b>
									<?php echo ($curso['Curso']['turno']); ?></p>		
								<b><?php echo __('Tipo: '); ?></b>
									<?php echo ($curso['Curso']['tipo']); ?></p>
								<!--<b><?php// echo __('Aula: '); ?></b>
									<?php// echo ($curso['Curso']['aula_nro']); ?></p>-->
								<!--<b><?php// echo __('Plazas: '); ?></b>--> 
									<!--<span class="badge"><?php// echo ($cursoPlazasString); ?></span></button></b><br/><br/>-->
								<!--<strong>CICLO 2018</strong><br/>-->
								<!--<b><?php// echo __('| Matriculados: '); ?></b>-->
									<!--<span class="badge"><?php// echo ($cursoMatriculaString); ?></span></button></b><br/>-->
								<!--<b><?php// echo __('| Vacantes: '); ?></b>-->
									<!--<span class="badge"><?php// echo ($vacantes); ?></span></button></b>-->	
								<!--<button class="btn btn-primary" type="button">Vacantes: 
									<span class="badge"><?php// echo ($vacantes); ?></span></button>-->
							<!--<div class="col-md-4 col-sm-6 col-xs-8">	
								<b><?php echo __('Organización de cursada: '); ?></b>
								<?php echo ($curso['Curso']['organizacion_cursada']); ?></p>
								<b><?php echo __('Titulación: '); ?></b>
								<?php echo ($this->Html->link($curso['Titulacion']['nombre'], array('controller' => 'titulacions', 'action' => 'view', $curso['Titulacion']['id']))); ?></p>
							</div>-->
							</div>
						<!--</div>-->
					</div>
					<div class="col-md-4 col-sm-6 col-xs-8">
						<div id="click_02" class="titulo_acordeon_datos">Datos Específicos <span class="caret"></span></div>
						<!--<div id="acordeon_02">-->
							<div class="unit">
								<b><?php echo __('Titulacion: '); ?></b>
									<?php echo ($this->Html->link($curso['Titulacion']['nombre'], array('controller' => 'titulacions', 'action' => 'view', $curso['Titulacion']['id']))); ?></p>
									<?php if (($current_user['role'] == 'superadmin') || ($current_user['puesto'] == 'Dirección Jardín') || ($current_user['puesto'] == 'Dirección Escuela Primaria') || ($current_user['puesto'] == 'Supervisión Inicial/Primaria')) : ?>
										<?php $ParejaPedagogica = ($curso['Curso']['pareja_pedagogica'] == '1') ? 'SI' : 'NO' ; ?>
										<b><?php echo 'Pareja Pedagógica: ';?></b><?php echo $ParejaPedagogica; ?></p>
										<?php $MaestraIntregradora = ($curso['Curso']['maestra_apoyo_inclusion'] == '1') ? 'SI' : 'NO' ; ?>
										<b><?php echo 'Maestra Integradora: ';?></b><?php echo $MaestraIntregradora; ?></p>
									<?php endif; ?>
									<?php if (($current_user['role'] == 'superadmin') || ($current_user['puesto'] == 'Dirección Colegio Secundario') || ($current_user['puesto'] == 'Supervisión Secundaria') || ($current_user['puesto'] == 'Dirección Instituto Superior')) : ?>
										<b><?php echo __('Hs Cátedras: '); ?></b>
											<?php echo ($curso['Curso']['hs_catedras']); ?></p>		
										<b><?php echo __('Resolución Presupuestaria: '); ?></b>
											<?php echo ($curso['Curso']['reso_presupuestaria']); ?></p>
									<?php endif; ?>
							</div>
						<!--</div>-->
					</div>
					<div class="col-md-4 col-sm-6 col-xs-8">
						<div id="click_02" class="titulo_acordeon_datos">Observaciones <span class="caret"></span></div>
						<!--<div id="acordeon_02">-->
							<div class="unit">
								<?php echo ($curso['Curso']['observaciones']); ?></p>
							</div>
						<!--</div>-->
					</div>
				</div>																																																												
            </div>
        </div>
		<div class="col-md-4">
		    <div class="unit">
				<?php

				$cicloDatoAlumno = $cicloActual['Ciclo']['nombre'];
				if(empty($cursoDivisionString))
				{
					$cicloDatoAlumno = $cicloPosterior['Ciclo']['nombre'];
				}

				?>
		 		<div class="subtitulo">Opciones</div>
				<div class="opcion"><?php echo $this->Html->link(__('Listar Secciones'), array('action' => 'index')); ?></div>
				<div class="opcion"><?php echo $this->Html->link(__('Datos de Alumnos'), array('action' => 'index','controller' => 'ListaAlumnos',
						'centro_id'=>$curso['Centro']['id'],
						'curso_id'=>$curso['Curso']['id'],
						'ciclo'=>$cicloDatoAlumno
					)); ?>
				</div>
				<?php
					// Por defecto se ocultan las siguientes opciones
					$showPromocion = false;
					$showEgreso = false;
					$showRepetir = true;

				if ($curso['Curso']['division'] != '') {
						/*  Los unicos 6to que promocionan
							940007700 CEPET
							940008300 EPET
							940015900 SABATO
							940015700 GUEVARA
						*/
						if($curso['Curso']['anio'] == '6to') {
							if(
								($curso['Centro']['cue'] == '940007700') ||
								($curso['Centro']['cue'] == '940008300') ||
								($curso['Centro']['cue'] == '940015900') ||
								($curso['Centro']['cue'] == '940015700') ||
								($curso['Centro']['nivel_servicio'] == 'Especial - Primario') ||
								($curso['Centro']['nivel_servicio'] != 'Especial - Integración')
							) {
								$showPromocion = true;
							}
						} else {
							// El resto de las secciones promocionan menos Sala de 5 años (Inicial), 7mo (Secundario Común) y 3ro (Primario/Secundario Adultos).
							if(
								($curso['Centro']['nivel_servicio'] == 'Común - Inicial' && $curso['Curso']['anio'] != 'Sala de 5 años') ||
								($curso['Centro']['nivel_servicio'] == 'Común - Primario') ||
								($curso['Centro']['nivel_servicio'] == 'Común - Secundario' && $curso['Curso']['anio'] != '7mo') ||
								($curso['Centro']['nivel_servicio'] == 'Adultos - Secundario' && $curso['Curso']['anio'] != '3ro') ||
								($curso['Centro']['nivel_servicio'] == 'Adultos - Primario' && $curso['Curso']['anio'] != '3ro') ||
								($curso['Centro']['nivel_servicio'] == 'Especial - Primario') ||
								($curso['Centro']['nivel_servicio'] == 'Especial - Integración') ||
								($curso['Centro']['nivel_servicio'] == 'Maternal - Inicial') ||
								($curso['Centro']['nivel_servicio'] == 'Común - Servicios complementarios')
							) {
								$showPromocion = true;
							}
						}
					}	
				?>

				<?php

					// Hardcode
					// Los centros con las siguientes ID
					$promocionCustomCentroId =[11,509,510];
					$promocionCustomCicloId = $cicloActual['Ciclo']['nombre'];
					if(in_array($curso['Centro']['id'],$promocionCustomCentroId))
					{
						$promocionCustomCicloId= 2018;
					}

					?>

					<?php  if($showPromocion) : ?>
						<div class="opcion"><?php echo $this->Html->link(__('Promocionar'), array('action' => 'index','controller' => 'Promocion',
								'centro_id'=>$curso['Centro']['id'],
								'curso_id'=>$curso['Curso']['id'],
								'ciclo' => $promocionCustomCicloId
							)); ?>
						</div>
					<?php endif; ?>

					<?php
						if($curso['Curso']['anio'] == '6to')
						{
							/*Deben mostrar la opción "EGRESAR" los 7mos de las secciones con orientación técnica y los 6tos de las secciones con orientación bachiller*/
							if(
								($curso['Centro']['cue'] == '940007700') ||
								($curso['Centro']['cue'] == '940008300')/* ||
								($curso['Centro']['cue'] == '940015900') ||
								($curso['Centro']['cue'] == '940015700')*/
							) {
								$showEgreso = false;
							} else {
								$showEgreso = true;
							}
						} else {
							// Egresos de Sala de 5 años (Inicial) y 3ros (Primario/Secundario Adultos).
							if(
								($curso['Centro']['nivel_servicio'] == 'Común - Inicial' && $curso['Curso']['anio'] == 'Sala de 5 años') || ($curso['Curso']['tipo'] == 'Múltiple' && $curso['Curso']['anio'] == 'Sala de 4 años') || ($curso['Centro']['nivel_servicio'] == 'Común - Secundario' && $curso['Curso']['anio'] == '7mo') ||
								($curso['Centro']['nivel_servicio'] == 'Adultos - Secundario' && $curso['Curso']['anio'] == '3ro') ||
								($curso['Centro']['nivel_servicio'] == 'Adultos - Primario' && $curso['Curso']['anio'] == '3ro')
							) {
								$showEgreso = true;
							}
						}
					?>

					<?php  if($showEgreso) : ?>
						<div class="opcion"><?php echo $this->Html->link(__('Egresar'), array('action' => 'index','controller' => 'Egreso',
								'centro_id'=>$curso['Centro']['id'],
								'curso_id'=>$curso['Curso']['id']
							)); ?>
						</div>
					<?php endif; ?>

				<?php  if($showRepetir) : ?>
					<div class="opcion"><?php echo $this->Html->link(__('Repitencia'), array('action' => 'index','controller' => 'Repitentes',
							'centro_id'=>$curso['Centro']['id'],
							'curso_id'=>$curso['Curso']['id'],
							'ciclo' =>$cicloActual['Ciclo']['nombre']
						)); ?>
					</div>
				<?php endif; ?>

					<div class="opcion"><?php echo $this->Html->link(__('Reubicar'), array('action' => 'index','controller' => 'Reubicacion',
							'centro_id'=>$curso['Centro']['id'],
							'curso_id'=>$curso['Curso']['id']
						)); ?>
					</div>


			  <?php if(($current_user['role'] == 'superadmin' && ($current_user['puesto'] == 'Sistemas' || $current_user['puesto'] == 'Atei')) || ($current_user['role'] == 'usuario' && ($current_user['puesto'] == 'Supervisión Secundaria'))): ?>
				<div class="opcion"><?php echo $this->Html->link(__('Editar'), array('action' => 'edit', $curso['Curso']['id'])); ?></div>
              <?php endif; ?>
              <?php if($current_user['role'] == 'superadmin' && $current_user['puesto'] == 'Sistemas'): ?>
				<div class="opcion"><?php echo $this->Html->link(__('Borrar'), array('action' => 'delete', $curso['Curso']['id']), null, sprintf(__('Esta seguro de borrar el curso %s?'), $curso['Curso']['division'])); ?></div>
			  <?php endif; ?>	
		 	</div>
		</div>
    </div>
<!-- end main -->
<?php /*
<!-- Cargos Relacionados -->
<!--<div class="related">
	<h3><?php echo __('Cargos Relacionados');?></h3>
	<?php if (!empty($curso['Cargo'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Nombre'); ?></th>
		<th><?php echo __('Tipo'); ?></th>
		<th><?php echo __('Resolucion Nro'); ?></th>
		<th><?php echo __('HsCatedra'); ?></th>
		<th><?php echo __('HsReloj'); ?></th>
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
		foreach ($curso['Cargo'] as $cargo):
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
				<?php echo $this->Html->link(__('Borrar'), array('controller' => 'cargos', 'action' => 'delete', $cargo['id']), null, sprintf(__('Esta seguro de borrar el cargo %s?'), $cargo['id'])); ?>
			
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Cargo'), array('controller' => 'cargos', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>-->
<!-- end Cargos Relacionados -->
<!-- Materias Relacionadas -->
<div id="click_02" class="titulo_acordeon">Unidades Curriculares Relacionadas <span class="caret"></span></div>
<div id="acordeon_02">
		<div class="row">
	<?php if (!empty($curso['Materia'])):?>
  	<!-- Swiper --> 
    <div class="swiper-container" style="height: 200px;">
        <div class="swiper-wrapper" >
	<?php foreach ($curso['Materia'] as $materia): ?>
	<div class="swiper-slide">
	<div class="col-md-6">
		<div class="unit">
			<?php echo '<b>Nombre:</b> '.$materia['nombre'];?><br>
			<?php echo '<b>Alia:</b> '.($this->Html->link($materia['alia'], array('controller' => 'materias', 'action' => 'view', $materia['id'])));?><br>
			<?php echo '<b>Carga horaria en:</b> '.$materia['carga_horaria_en'];?><br>
			<?php echo '<b>Carga horaria semanal:</b> '.$materia['carga_horaria_semanal'];?><br>
        <div class="text-right">
            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'materias', 'action' => 'view', $materia['id']), array('class' => 'btn btn-success','escape' => false)); ?>
            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-edit"></i>'), array('controller' => 'materias', 'action' => 'edit', $materia['id']), array('class' => 'btn btn-warning','escape' => false)); ?>	
		   <?php if($current_user['role'] == 'superadmin'): ?>	
			<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-trash"></i>'), array('cont""roller' => 'materias', 'action' => 'delete', $materia['id']), array('class' => 'btn btn-danger','escape' => false)); ?>
          <?php endif; ?>  
            </div>
		</div>
	</div>
</div>
		<?php endforeach; ?>
			</div>
			        <!-- Add Pagination --> 
        <div class="swiper-pagination"></div>
    </div>
    <!-- Include plugin after Swiper -->
		<?php else: echo '<div class="col-md-12"><div class="unit text-center">No se encuentran relaciones.</div></div>'; ?>
		<?php endif; ?>
    </div>
</div>
<!-- end Materias Relacionadas -->
<!-- Inscripciones Relacionadas -->
<div id="click_01" class="titulo_acordeon">Inscripciones Relacionadas <span class="caret"></span></div>
<div id="acordeon_01">
		<div class="row">
	<?php if (!empty($curso['Inscripcion'])):?>
  	<!-- Swiper -->
    <div class="swiper-container" style="height: 200px;">
        <div class="swiper-wrapper" >
	<?php foreach ($curso['Inscripcion'] as $inscripcion): ?>
	<!-- Sólo visualiza las inscripciones relacionadas del ciclo actual y con estado CONFIRMADAS -->
	<?php// if ((($inscripcion['ciclo_id'] == $cicloIdActualString) || $curso['Curso']['division'] == '')) { ?>
	<div class="swiper-slide">
	  <div class="col-md-6">
		<div class="unit">
			<?php echo '<b>Inscripción:</b> '.($this->Html->link($inscripcion['legajo_nro'], array('controller' => 'inscripcions', 'action' => 'view', $inscripcion['id'])));?><br>
			<?php echo '<b>Alumno:</b> '.($this->Html->link($personaNombre[$personaId[$inscripcion['alumno_id']]], array('controller' => 'personas', 'action' => 'view', $inscripcion['alumno_id'])));?><br>
            <!--<?php echo '<b>Fecha_alta:</b> '.($this->Html->formatTime($inscripcion['fecha_alta']));?><br>-->
			<!--<?php echo '<b>Fecha_baja:</b> '.($this->Html->formatTime($inscripcion['fecha_baja']));?><br>-->
            <!--<?php echo '<b>Fecha_egreso:</b> '.($this->Html->formatTime($inscripcion['fecha_egreso']));?><br>-->
            <?php echo '<b>Estado:</b> '.$inscripcion['estado_inscripcion'];?><br>
            <div class="text-right">
	            <?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-eye-open"></i>'), array('controller' => 'inscripcions', 'action' => 'view', $inscripcion['id']), array('class' => 'btn btn-success','escape' => false)); ?>
              <?php if(($current_user['role'] == 'superadmin') || ($current_user['role'] == 'admin')): ?>
	            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-edit"></i>'), array('controller' => 'inscripcions', 'action' => 'edit', $inscripcion['id']), array('class' => 'btn btn-warning','escape' => false)); ?>
	          <?php endif; ?>  
			  <?php if($current_user['role'] == 'superadmin'): ?>	
				<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'inscripcions', 'action' => 'delete', $inscripcion['id']), array('class' => 'btn btn-danger','escape' => false)); ?>
			  <?php endif; ?>	
            </div>
		</div>
	 </div>
  </div>		
		<?php// } ?>
		<?php endforeach; ?>
  </div>
  <!-- Add Pagination -->
       <div class="swiper-pagination"></div>
    </div>
    <!-- Include plugin after Swiper -->
		<?php else: echo '<div class="col-md-12"><div class="unit text-center">No se encuentran relaciones.</div></div>'; ?>
		<?php endif; ?>
    </div>
</div>
<!-- end Inscripciones Relacionadas -->
<!-- Ciclos Relacionadas -->
<!--<div class="related">
	<h3><?php echo __('Ciclos Relacionados');?></h3>
	<?php if (!empty($curso['Ciclo'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Ciclo'); ?></th>
		<th><?php echo __('Inicio'); ?></th>
		<th><?php echo __('Final'); ?></th>
		<th><?php echo __('PrimerCuatrimestre'); ?></th>
		<th><?php echo __('SegundoCuatrimestre'); ?></th>
		<th><?php echo __('Observaciones'); ?></th>
		<th class="actions"><?php echo __('Opciones');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($curso['Ciclo'] as $ciclo):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $ciclo['id'];?></td>
			<td><?php echo $ciclo['ciclo'];?></td>
			<td><?php echo $ciclo['fechaInicio'];?></td>
			<td><?php echo $ciclo['fechaFinal'];?></td>
			<td><?php echo $ciclo['primerCuatrimestre'];?></td>
			<td><?php echo $ciclo['segundoCuatrimestre'];?></td>
			<td><?php echo $ciclo['observaciones'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('Ver'), array('controller' => 'ciclos', 'action' => 'view', $ciclo['id'])); ?>
				<?php echo $this->Html->link(__('Editar'), array('controller' => 'ciclos', 'action' => 'edit', $ciclo['id'])); ?>
				<?php echo $this->Html->link(__('Borrar'), array('controller' => 'ciclos', 'action' => 'delete', $ciclo['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $ciclo['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Ciclo'), array('controller' => 'ciclos', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>-->
<!-- end Ciclos Relacionadas -->
<!-- Initialize Swiper -->
    <!--<script>
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
    });
    </script>-->
*/?>    