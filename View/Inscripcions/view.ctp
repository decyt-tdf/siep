<?php echo $this->Html->script(array('acordeon', 'slider')); ?>
<?php echo $this->Html->css('slider'); ?>
<!-- start main -->
<div class="TituloSec">Inscripción: <?php echo ($inscripcion['legajo_nro']); ?></div>
<div id="ContenidoSec">
    <div class="row">
        <div class="col-md-8">	
	        <div class="unit">
 		        <div class="row perfil">
                <!--<h3>Datos del Alumno</h3>-->
                    <div class="col-md-4 col-sm-4 col-xs-12">	
                        <b><?php echo __('Ciclo:'); ?></b>
                        <?php echo ($this->Html->link($inscripcion['ciclo']['nombre'], array('controller' => 'ciclos', 'action' => 'view', $inscripcion['ciclo_id']))); ?></p>
                        <b><?php echo __('Institución:'); ?></b>
                        <?php echo($this->Html->link($inscripcion['centro']['sigla'], array('controller' => 'centros', 'action' => 'view', $inscripcion['centro_id']))); ?></p>
                        <b><?php echo __('Alumno:'); ?></b>
                        <?php echo ($this->Html->link("{$inscripcion['alumno']['persona']['nombres']} {$inscripcion['alumno']['persona']['apellidos']}", array('controller' => 'alumnos', 'action' => 'view', $inscripcion['alumno_id']))); ?></p>
                        <b><?php echo __('| Inscripción - Características:'); ?></b></p>
                        <b><?php echo __('| Tipo:'); ?></b>
                        <?php echo $inscripcion['tipo_inscripcion']; ?></p>
                        <b><?php echo __('| Estado:'); ?></b>
                        <?php if($inscripcion['estado_inscripcion'] == "ANULADA") {; ?>
                            <span class="label label-danger"><?php echo $inscripcion['estado_inscripcion']; ?></span></p>
                        <?php } else if ($inscripcion['estado_inscripcion'] == "CONFIRMADA") {; ?>
                            <span class="label label-success"><?php echo $inscripcion['estado_inscripcion']; ?></span>
                        <?php } else if ($inscripcion['estado_inscripcion'] == "NO CONFIRMADA") {?>
                            <span class="label label-warning"><?php echo $inscripcion['estado_inscripcion']; ?></span>
                        <?php } else if ($inscripcion['estado_inscripcion'] == "BAJA" || $inscripcion['estado_inscripcion'] == "EGRESO") {?>
                            <span class="label label-info"><?php echo $inscripcion['estado_inscripcion']; ?></span>
                        <?php } ?></p>
                        <b><?php echo __('Documentación:'); ?></b>
                            <span class="opcion"><?php echo $inscripcion['estado_documentacion']; ?></span>
                        </p>
                        </p>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">

                        <?php
                        /* ==== DESHABILITADO CON PHP  =====

                        <!--<h3>Datos previos</h3>-->
                        <!--<div id="click_03" class="titulo_acordeon_datos">Datos previos <span class="caret"</span></div>
                            <div id="acordeon_03">
                                 <div class="unit">
                                    <b><?php echo __('Cursa:'); ?></b>
                                    <?php echo $inscripcion['cursa']; ?></p>
                                    <b><?php echo __('Fines:'); ?></b>
                                    <?php echo $inscripcion['fines']; ?></p>
                                    <b><?php echo __('Recursante:'); ?></b>
                                        <?php if($inscripcion['recursante'] == 1): ?>
                                        <?php echo "SI"; ?>
                                        <?php endif; ?>
                                        <?php echo "No"; ?></p>
                                    <b><?php echo __('Condición de aprobación:'); ?></b>
                                    <?php echo $inscripcion['condicion_aprobacion']; ?></p>
                                 </div>
                            </div>
                            <!--<h3>Datos del alta baja y egreso del Alumno</h3>-->
                            */
                        ?>
                        <div id="click_04" class="titulo_acordeon_datos">Datos del Alta <span class="caret"</span></div>
                        <div id="acordeon_04">
                            <div class="unit">
                                <b><?php echo __('Fecha:'); ?></b>
                                <?php echo $this->Html->formatTime($inscripcion['fecha_alta']);?></p>
                                <?php  if($inscripcion['hermano_id']): ?>
                                    <b><?php echo __('Hermano de:'); ?></b></p>
                                    <b><?php echo ($this->Html->link($inscripcion['hermano']['persona']['nombre_completo'], array('controller' => 'alumnos', 'action' => 'view', $inscripcion['hermano_id']))); ?></b>
                                <?php endif; ?></p>
                                <?php  if($inscripcion['tipo_inscripcion'] === 'Pase'): ?>
                                    <b><?php echo __('Centro de Origen:'); ?></b></p>
                                    <b><?php echo $inscripcion['pase']['sigla']; ?></b>
                                <?php endif; ?></p>
                                <b><?php echo __('Documentación faltante:'); ?></b>
                                  <ul>
                                  <?php if($inscripcion['cud_estado'] == 'Desactualizado' || $inscripcion['cud_estado'] == 'No tiene'): ?>
                                    <li><span class="label label-danger"><?php echo 'Falta CUD'; ?></span></li>
                                   <?php endif; ?>
                                   <?php if(!$inscripcion['fotocopia_dni'] == 1): ?>
                                    <li><span class="label label-danger"><?php echo 'Falta Fotocopia DNI'; ?></span></li>
                                   <?php endif; ?>
                                   <?php if($userCentroNivel != 'Adultos - Secundario' && $userCentroNivel != 'Adultos - Primario') : ?>
                                    <?php if(!$inscripcion['partida_nacimiento_alumno'] == 1): ?>
                                        <li><span class="label label-danger"><?php echo 'Falta Partida Alumno'; ?></span></li>
                                    <?php endif; ?>
                                    <?php if(!$inscripcion['certificado_vacunas'] == 1): ?>
                                        <li><span class="label label-danger"><?php echo 'Certificado vacunación'; ?></span></li>
                                    <?php endif; ?>
                                   <?php endif; ?> 
                                    <?php if(($current_user['role'] == 'superadmin' || $current_user['puesto'] == 'Dirección Colegio Secundario' || $current_user['puesto'] == 'Supervisión Secundaria' || $userCentroNivel == 'Adultos - Secundario' || $userCentroNivel == 'Adultos - Primario') && ($inscripcion['certificado_septimo'] == 0)): ?>
                                    <li><span class="label label-danger"><?php echo 'Falta Certificado Primaria'; ?></span></li><?php endif; ?>
                                  </ul>
                            </div>
                        </div>
                        <div id="click_05" class="titulo_acordeon_datos">Datos de Baja <span class="caret"</span></div>
                        <div id="acordeon_05">
                            <div class="unit">
                                <b><?php echo __('Tipo:'); ?></b>
                                <?php echo ($inscripcion['tipo_baja']); ?></p>
                                <b><?php echo __('Fecha:'); ?></b>
                                <?php echo ($this->Html->formatTime($inscripcion['fecha_baja'])); ?></p>
                                <b><?php echo __('Motivo:'); ?></b>
                                <?php echo $inscripcion['motivo_baja']; ?></p>
                             </div>
                             <!--<h3>Datos del egreso</h3>-->
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <div id="click_06" class="titulo_acordeon_datos">Egreso <span class="caret"</span></div>
                            <div id="acordeon_06">
                                <div class="unit">
                                    <b><?php echo __('Fecha:'); ?></b>
                                    <?php echo ($this->Html->formatTime($inscripcion['fecha_egreso'])); ?></p>
                                    <b><?php echo __('Acta Nº:'); ?></b>
                                    <?php echo $inscripcion['acta_nro']; ?></p>
                                    <b><?php echo __('Libro Matriz Nº:'); ?></b>
                                    <?php echo $inscripcion['libro_nro']; ?></p>
                                    <b><?php echo __('Folio Nº:'); ?></b>
                                    <?php echo $inscripcion['folio_nro']; ?></p>

                                    <b><?php echo __('Título Nº:'); ?></b>
                                    <?php echo $inscripcion['titulo_nro']; ?></p>
                                </div>
                            </div>

                        <?php
                    /* ==== DESHABILITADO CON PHP  =====

                    <!--<h3>Datos de la titulación</h3>
                    <div id="click_07" class="titulo_acordeon_datos">Titulación <span class="caret"</span></div>
                        <div id="acordeon_07">
                           <div class="unit">
                                <b><?php echo __('Emitido el:'); ?></b>
                                <?php echo ($this->Html->formatTime($inscripcion['fecha_emision_titulo'])); ?></p>
                                <b><?php echo __('Nota:'); ?></b>
                                <?php ($inscripcion['nota']); ?></p>
                                <b><?php echo __('Fecha Nota:'); ?></b>
                                <?php echo ($inscripcion['fecha_nota']); ?></p>
                                <b><?php echo __('Agente: '); ?></b>
                                <?php echo ($this->Html->link($inscripcion['Empleado']['apellidos'], array('controller' => 'empleados', 'action' => 'view', $inscripcion['Empleado']['apellidos']))
                                    ." ".($this->Html->link($inscripcion['Empleado']['nombres'], array('controller' => 'empleados', 'action' => 'view', $inscripcion['Empleado']['nombres']))));
                                ?></p>
                           </div>
                        </div>
                    <!--<h3>Observaciones</h3>-->
                        */
                    ?>
                    <div id="click_08" class="titulo_acordeon_datos">Observaciones <span class="caret"</span></div>
                        <div id="acordeon_08">
                           <div class="unit">
                                <b><?php echo __('Observaciones:'); ?></b>                                
                                <?php echo ($inscripcion['observaciones']); ?></p>                                
                           </div>
                        </div>
                    </div>
                 </div>
              </div>
          </div>
          <div class="col-md-4">
              <div class="unit">
 			      <div class="subtitulo">Opciones</div>
                  <div class="opcion"><?php echo $this->Html->link(__('Listar Inscripciones'), array('action' => 'index')); ?></div>
                <?php
                //Se visualiza solo sí se trata de un "superusuario", "usuario" o "admin" del mismo centro que la inscripción. 
                  if($current_user['role'] == 'superadmin' || $current_user['role'] == 'usuario' || $userCentroId == $inscripcion['centro_id']):
                    // y sí la inscripción del alumno tiene estado CONFIRMADA y es del ciclo actual. 
                    //if(($inscripcion['estado_inscripcion'] === 'CONFIRMADA' || $inscripcion['estado_inscripcion'] === 'EGRESO') ): ?>
                        <div class="opcion"><a href="<?php echo "/gateway/constancia/id:".$inscripcion['id'];?>">Constancia de Inscripción</a></div>
                        <div class="opcion"><a href="<?php echo "/gateway/constancia_regular/id:".$inscripcion['id'];?>">Constancia de Alumno Regular</a></div>
                    <?php //endif; ?>
                    <?php if ($inscripcion['estado_inscripcion'] != 'ANULADA') : ?>
                    <div class="opcion"><?php echo $this->Html->link(__('Editar'), array('action' => 'edit', $inscripcion['id'])); ?> </div>
                    <?php endif; ?>
                <?php endif; ?>  
                <?php if(($inscripcion['ciclo_id'] == 7) && ($current_user['role'] == 'usuario' || $current_user['id'] == 438 || $current_user['id'] == 326 || $current_user['id'] == 325 || $current_user['id'] == 338 || $current_user['id'] == 582)): ?> 
                    <?php if ($inscripcion['estado_inscripcion'] != 'ANULADA') { ?>
                        <div class="opcion"><?php echo $this->Html->link(__('Anular'), array('action' => 'delete', $inscripcion['id']), null, sprintf(__('Esta seguro de ANULAR la inscripción con legajo Nº: %s?'), $inscripcion['legajo_nro'])); ?></div>
                        <?php } ?>
                <?php endif; ?>  
              </div>
          </div>
     </div>
<!-- end main -->
<!-- Cursos Relacionados -->
<div id="click_01" class="titulo_acordeon">Secciones Relacionadas <span class="caret"</span></div>
<div id="acordeon_01">
		<div class="row">
	        <?php if (count($cursos)):?>
                <?php foreach($cursos as $curso) : ?>
  			<div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="unit">
                        <?php echo '<b>Año/Gpo:</b> '.$curso['anio'];?><br>
                        <?php echo '<b>División:</b> '.$curso['division'];?><br>
                        <?php echo '<b>Turno:</b> '.$curso['turno'];?><br>
                        <?php echo '<b>Tipo:</b> '.$curso['tipo'];?><br>
                        <!--<?php echo '<b>Cursada:</b> '.$curso['organizacion_cursada'];?><br>
                        <?php echo '<b>Titulación:</b> '.($this->Html->link($curso['titulacion_id'], array('controller' => 'titulacions', 'action' => 'view', $curso['titulacion_id'])));?><br>-->
                        <hr>
                        <div class="text-right">
                            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('controller' => 'cursos', 'action' => 'view', $curso['id']), array('class' => 'btn btn-success','escape' => false)); ?>
                          <?php if($current_user['role'] == 'superadmin' && $current_user['puesto'] == 'Sistemas'): ?>
                            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-edit"></i>'), array('controller' => 'cursos', 'action' => 'edit', $curso['id']), array('class' => 'btn btn-warning','escape'  => false)); ?>
                            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-trash"></i>'), array('controller' => 'cursos', 'action' => 'delete', $curso['id']), array('class' => 'btn btn-danger','escape' => false)); ?>
                          <?php endif; ?>  
                        </div>
                    </div>
			</div>
                <?php endforeach; ?>
			<?php else: echo '<div class="col-md-12"><div class="unit text-center">No se encuentran relaciones.</div></div>'; ?>
            <?php endif; ?>
	  </div>
</div>
<!-- end Cursos Relacionados -->
<!-- Materias Relacionados  
<div id="click_02" class="titulo_acordeon">Unidades Curriculares Relacionadas <span class="caret"</span></div>
<div id="acordeon_02">
		<div class="row">
	        <?php if (!empty($inscripcion['Materia'])):?>
  	  	    <!-- Swiper 
            <div class="swiper-container" style="height: 200px;">
                <div class="swiper-wrapper" >
	                <?php foreach ($inscripcion['Materia'] as $materia): ?>
                    <div class="swiper-slide">
                        <div class="col-md-12">
                            <div class="unit" >
                                <?php echo '<b>Nombre:</b> '.$materia['nombre'];?><br>
                                <?php echo '<b>Alia:</b> '.$materia['alia'];?><br>
                                <?php echo '<b>Carga horaria:</b> '.$materia['carga_horaria_semanal'].' '.$materia['carga_horaria_en'];?>
                            <br>
                              <div class="text-right">
                                <?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-eye-open"></i>'), array('controller' => 'materias', 'action' => 'view', $materia['id']), array('class' => 'btn btn-success','escape' => false)); ?>
                               <?php if(($current_user['role'] == 'superadmin') || ($current_user['role'] == 'admin')): ?>
                                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-edit"></i>'), array('controller' => 'materias', 'action' => 'edit', $materia['id']), array('class' => 'btn btn-warning','escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-trash"></i>'), array('controller' => 'materias', 'action' => 'delete', $materia['id']), array('class' => 'btn btn-danger','escape' => false)); ?>

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
<!-- end Materias Relacionados -->
</div>
<!-- Initialize Swiper -->
    <script>
		  var swiper = new Swiper('.swiper-container', {
			  pagination: '.swiper-pagination',
			  paginationClickable: true,
		  });
    </script>