<?php echo $this->Html->script(array('acordeon', 'slider')); ?>
<?php echo $this->Html->css('slider'); 
$sesion = $this->Session->read('inscripcion');
$inscripcion = $sesion['inscripcion'];
$curso = $sesion["curso"];
?>
<style>
    .preview{
        background-color:lightgray;
    }
</style>
<!-- start main -->
<div id="ContenidoSec">
    <div class="row">
        <div class="col-md-8">	
	        <div class="unit">
 		        <div class="row perfil">
                <!--<h3>Datos del Alumno</h3>-->
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p><i>Vista preliminar de Impresión de Constancia de Alumno Regular: <strong><?php echo ($inscripcion['legajo_nro']); ?></strong></i></p>
                        <hr>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="unit">
                            <p class="text-center preview"><?php echo($inscripcion['centro']['sigla'] ." C.U.E. N° ".$inscripcion['centro']['cue']); ?></p>
                            <p style="padding:10px; font-size:12px;font-weight: bold;" class="text-center preview">
                                <?php echo($inscripcion['centro']['direccion'] ." - ".strtoupper($inscripcion['centro']['ciudad']["nombre"])); ?>
                            </p>
                            <p class="text-center preview">
                                Se hace constar que <b><?php  echo strtoupper($inscripcion["alumno"]["persona"]["apellidos"]); ?>, <?php echo strtoupper($inscripcion["alumno"]["persona"]["nombres"]); ?></b>,
                                documento tipo: <b><?php echo strtoupper($inscripcion["alumno"]["persona"]["documento_tipo"]); ?></b>, N°
                                <b><?php echo strtoupper($inscripcion["alumno"]["persona"]["documento_nro"]); ?></b>
                                es alumno regular de este establecimiento y se encuentra cursando año <b><?php echo $curso["anio"] ?></b>

                                <?php 
                                    if($inscripcion["centro"]["nivel_servicio"] == 'Común - Inicial' && $curso["tipo"] == 'Múltiple' )
                                    {
                                        switch($curso["anio"])
                                        {
                                            case "Sala de 3 años":
                                                echo "Múltiple (3 y 4 años)" ;
                                            break;
                                            case "Sala de 4 años":
                                                echo "Múltiple (4 y 5 años)";
                                            break;
                                        }
                                            
                                    }
                                ?>

                                , división <b><?php echo $curso["division"] ?></b>
                                del servicio y nivel <b><?php echo $inscripcion["centro"]["nivel_servicio"] ?></b>
                            </p>
                            <hr>
                            <div class="alumnos form">
                                <!-- <form action="/alumnos/editConstancia/<?php echo $inscripcion['alumno']['id'];?>" method="post" id="AlumnoRegularEditForm" enctype="multipart/form-data">
                                    <p style="text-align:center;">            
                                        <h4>Observaciones:</h4>
                                        <textarea id="observaciones" name="observaciones" cols="50" rows="5">
                                                <?php echo $inscripcion["alumno"]["observaciones"] ?>
                                        </textarea>
                                        <input type="hidden" id="inscripcion_id" name="inscripcion_id" value="<?php echo $inscripcion['id'];?>">
                                    </p>
                                    <div>
                                        <input type="submit" class="btn btn-success pull-right" value="Guardar Cambios">
                                    </div>
                                </form> -->
                                <?php
                                echo $this->Form->create('Alumno',array('method'=>'POST','url'=>'/alumnos/editConstancia/'.$inscripcion["alumno"]["id"]));
                                echo $this->Form->input('observaciones',array('label'=>'Observaciones del Alumno', 'type' => 'textarea', 'between' => '<br>', 'class' => 'form-control', 'value' => $inscripcion["alumno"]["observaciones"]));
                                echo $this->Form->input('id', array('value' => $inscripcion["id"],'type' => 'hidden'));
                                echo $this->Form->input('persona_id', array('value' => $inscripcion["alumno"]["persona_id"],'type' => 'hidden'));
                                echo '<br>';
                                echo $this->Form->submit(__('Guardar Cambios', true), array('name' => 'submit', 'div' => false, 'between' => '<br>', 'class' => 'btn btn-success pull-right'));
                                echo $this->Form->end();
                                ?>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        
                    </div>
                 </div>
              </div>
          </div>
          <div class="col-md-4">
              <div class="unit">
 			      <div class="subtitulo">Acciones</div>
                    <div class="opcion"><a href="<?php echo "/gateway/constancia_regular/id:".$inscripcion['id'];?>">Imprimir Constancia de Alumno Regular</a></div>
                <?php
                // Se quita reestricción
                //Se visualiza solo sí se trata de un "superusuario", "usuario" o "admin" del mismo centro que la inscripción. 
                //   if($current_user['role'] == 'superadmin' || $current_user['role'] == 'usuario' || $userCentroId == $inscripcion['centro_id']):
                    // y sí la inscripción del alumno tiene estado CONFIRMADA y es del ciclo actual. 
                    //if(($inscripcion['estado_inscripcion'] === 'CONFIRMADA' || $inscripcion['estado_inscripcion'] === 'EGRESO') ): ?>
                    <?php //endif; ?>
                <?php //endif; ?>  
              </div>
          </div>
     </div>
<!-- end main -->
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