<?php echo $this->Html->css(array('/js/select2/select2.min')); ?>
<?php echo $this->Html->script(array('tooltip', 'datepicker', 'moment', 'bootstrap-datetimepicker','select2/select2.min')); ?>
<script>
    $(function(){
        $('.s2_general').select2();
        $('.s2_centro').select2();
        $('.s2_seccion').select2();
        $('.s2_centro').on("change", function(){
            getSeccionDependiente($(this).val());
        });
        // Previo a la carga, se completan las secciones dependientes al centro
        getSeccionDependiente(<?php echo $alumno['Alumno']['centro_id']; ?>);
        function getSeccionDependiente(centro_id) {
            // Remueve datos de secciones
            $(".s2_seccion").empty();
            // Obtener secciones dependientes al centro
            $.ajax({
                type:"GET",
                url: "<?php echo "/gateway/cursos/por_pagina:all/centro_id:"?>"  + centro_id,
                success: function(response){
                    $(".s2_centro").append('<option value="' +''+ '"> ' + 'Seleccione una sección'+ '</option>');

                    // Valores retorandos por el api
                    var data = response.data;
                    for (var index in data) {
                        var el = data[index];

                        if(el.id == <?php echo $cursoInscripcion['Curso']['id']; ?>) {
                            $(".s2_seccion").append('<option value="' +el.id+ '" selected="selected"> ' + el.nombre_completo + '</option>');
                        } else {
                            $(".s2_seccion").append('<option value="' +el.id+ '"> ' + el.nombre_completo + '</option>');
                        }
                    }
                }
            });
        }
    });
</script>
<div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="unit"><h3>PASO 1: Datos Generales</h3><hr />
            <div>
                <p><strong>Nombre y apellidos del alumno (*Obligatorio)</strong></p>
                <input class="form-control" disabled="disabled" label= "Nombre y apellidos del alumno (*Obligatorio)" data-toggle="tooltip" data-placemente="bottom" placeholder="Ingrese el nombre completo" value="<?php echo $alumno['Persona']['nombre_completo_persona'];?>">
            </div><br>
            <?php
                /*  echo $this->Form->input('alumno_id', array('label'=>'Nombres y apellidos del Alumno*', 'disabled' => true, 'options'=>$, 'between' => '<br>', 'class' => 'form-control'));*/
            ?>
        <div>
        <p><strong>Ciclo lectivo (*Obligatorio)</strong></p>
        <input class="form-control" label="Ciclo lectivo (*Obligatorio)" disabled="disabled" data-toggle="tooltip" data-placemente="bottom" value="<?php echo $cicloInscripcionNombreString; ?>">
        <?php $this->Form->input('ciclo_id', array('type' => 'hidden', 'default'=>$cicloInscripcionIdString)); ?> 
    </div><br>
    <?php
        if (($current_user['role'] == 'superadmin') || ($current_user['role'] == 'usuario')) {
            echo $this->Form->input('centro_id', array('default'=>$alumno['Alumno']['centro_id'],'label'=>'Institución (*Obligatorio)',/* 'empty' => 'Ingrese una institución...',*/ 'disabled'=>true, 'class' => 's2_centro form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
            echo '<br>';
            echo $this->Form->input('centro_id', array('type' => 'hidden', 'default'=>$alumno['Alumno']['centro_id']));
        }
    ?>
    <?php
        echo $this->Form->input('Curso', array('multiple' => true, 'label'=>'Sección (*Obligatorio)', 'empty' => 'Ingrese una sección...', 'between' => '<br>', 'class' => 's2_seccion form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
        /*
        if (($current_user['role'] == 'superadmin') || ($current_user['puesto'] == 'Dirección Colegio Secundario') || ($current_user['puesto'] == 'Supervisión Secundaria') || ($current_user['puesto'] == 'Dirección Instituto Superior') || ($current_user['puesto'] == 'Supervisión Secundaria')) {
            echo $this->Form->input('Inscripcion.Materia', array('multiple' => true, 'label'=>'Unidades Curriculares*', 'empty' => 'Ingrese una unidad...', 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
        }
        */
        echo $this->Form->input('legajo_nro', array('type' => 'hidden'));
    ?><br>
    <?php
        $estados_inscripcion = array('CONFIRMADA'=>'CONFIRMADA','NO CONFIRMADA'=>'NO CONFIRMADA','BAJA'=>'BAJA','EGRESO'=>'EGRESO','SIN TERMINALIDAD'=>'SIN TERMINALIDAD');
        //Si el número de legajo tiene la denominación "SINVACANTE", deshabilita la modificación del estado de inscripción.
        if ($sinVacante === 'SINVACANTE') {
        echo $this->Form->input('estado_inscripcion', array('default'=>$estadoInscripcionAnteriorArray['Inscripcion']['estado_inscripcion'],'label'=>'Estado de la inscripción (*Obligatorio)', 'disabled' =>true, 'empty' => 'Ingrese un estado de inscripción...', 'options'=>$estados_inscripcion, 'class' => 's2_general form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
        echo $this->Form->input('estado_inscripcion', array('type' => 'hidden', 'default'=>$estadoInscripcionAnteriorArray['Inscripcion']['estado_inscripcion']));
        } else {
        echo $this->Form->input('estado_inscripcion', array('default'=>$estadoInscripcionAnteriorArray['Inscripcion']['estado_inscripcion'],'label'=>'Estado de la inscripción (*Obligatorio)', 'empty' => 'Ingrese un estado de inscripción...', 'options'=>$estados_inscripcion, 'class' => 's2_general form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
        }  
    ?>
    <?php echo $this->Form->input('observaciones', array('default' => $obs, 'label'=>'Observaciones', 'type' => 'textarea', 'between' => '<br>', 'class' => 'form-control')); ?>
    <?php echo $this->Form->input('usuario_id', array('type' => 'hidden')); ?>
</div>
<?php /*
    <div class="unit"><strong><h3>Datos del Alta</h3></strong><hr />
      <?php
            $tipos_inscripcion = array('Común'=>'Común','Hermano de alumno regular'=>'Hermano de alumno regular','Pase'=>'Pase','Situación social'=>'Situación social', 'Integración'=>'Integración');
            echo $this->Form->input('tipo_inscripcion', array('label'=>'Tipo de inscripción*', 'empty' => 'Ingrese un tipo de inscripción...', 'options'=>$tipos_inscripcion, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
      ?>
    <div><hr />
    <span class="input-group-addon"><h4>Indique según tipo de inscripción seleccionado:</h4></span><hr />
    <strong><h5>Hermano de Alumno Regular</h5></strong>
    <input id="AutocompelteAlumno" class="form-control" placeholder="Indique Alumno por DNI, nombre y/o apellido">
    <div class="alert alert-danger" role="alert" id="AutocompleteAlumnoError" style="display:none;">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>
        No se encontraron resultados de busqueda
    </div>
    </div> 
    <hr />
    <script>
        $( function() {
            $( "#AutocompelteAlumno" ).autocomplete({
                source: "<?php echo $this->Html->url(array('controller'=>'Alumnos', 'action'=>'autocompleteNombreAlumno'));?>",
                minLength: 2,
                select: function( event, ui ) {
                    var nombre_completo = ui.item.Persona.apellidos +" "+ ui.item.Persona.nombres +' - ' +ui.item.Persona.documento_nro;
                    $("#AutocompelteAlumno").val( nombre_completo );
                    window.location.href = "<?php echo $this->Html->url(array('controller'=>'alumnos','action'=>'view'));?>/"+ui.item.Alumno.id;
                    return false;
                },
                response: function(event, ui) {
                    if (ui.content.length === 0)
                    {
                        $("#AutocompleteAlumnoError").show();
                    } else {
                        $("#AutocompleteAlumnoError").hide();
                    }
                }
            }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                var nombre_completo = item.Persona.apellidos +" "+ item.Persona.nombres +' - ' +item.Persona.documento_nro;
                return $( "<li>" )
                    .append( "<div>" +nombre_completo+ "</div>" )
                    .appendTo( ul );
            };
        });
    </script>
    <!-- End Autocomplete -->
    <div>
      <strong><h5>Pase</h5></strong>
      <input id="AutocompleteForm" class="form-control" placeholder="Indique institucion origen por nombre o CUE">
      <input id="centroId" type="hidden" name="Centro.id">
      </div><hr />
      <script>
         $( function() {
            $( "#AutocompleteForm" ).autocomplete({
               source: "<?php echo $this->Html->url(array('controller'=>'centros', 'action'=>'autocompleteCentro'));?>",
               minLength: 2,
               select: function( event, ui ) {
                  $("#AutocompleteForm").val( ui.item.Centro.sigla );
                  $('#centroId').val(ui.item.Centro.id);
                  return false;
               }
            }).autocomplete( "instance" )._renderItem = function( ul, item ) {
               return $( "<li>" )
                   .append( "<div>" +item.Centro.sigla + "</div>" )
                   .appendTo( ul );
            };
            $("#formularioBusqueda").submit(function(e){
               e.preventDefault();

               var centroId = $('#centroId').val();
               window.location.href = "<?php echo $this->Html->url(array('controller'=>'centros','action'=>'view'));?>/"+centroId;
            });
         });
      </script>
      <!-- End Autocomplete -->
      <?php
            $situaciones_sociales = array('Mudanza de la familia' => 'Mudanza de la familia', 'Pasó a educación de jóvenes y adultos' => 'Pasó a educación de jóvenes y adultos', 'Pasó a educación especial' => 'Pasó a educación especial', 'No le gustaba la escuela' => 'No le gustaba la escuela', 'Tenía muchas materias previas' => 'Tenía muchas materias previas', 'Problemas disciplinarios' => 'Problemas disciplinarios',  'Decisión de la escuela' => 'Decisión de la escuela', 'Problemas de salud' =>  'Problemas de salud', 'Cambio en la situación económica' => 'Cambio en la situación económica', 'Comenzó a trabajar' => 'Comenzó a trabajar', 'Quedó embarazada' => 'Quedó embarazada', 'Debe colaborar en la casa' => 'Debe colaborar en la casa');
            echo $this->Form->input('situacion_social', array('label' => 'Situación social', 'empty' => 'Ingrese una opción...', 'options' => $situaciones_sociales, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
      ?>
    </div>
    */ ?>  
    <?php echo '</div><div class="col-md-4 col-sm-6 col-xs-12">'; ?>
        <div class="unit"><h3>PASO 2: Datos de la BAJA</h3><hr />
            <?php
                echo $this->Form->input('fecha_baja', array('default' => $fechaBaja, 'label' => 'Fecha de Baja', 'type' => 'text', 'between' => '<br>', 'empty' => ' ','class' => 'datepicker form-control', 'Placeholder' => 'Ingrese una fecha...'));
                $tipos_baja = array('Salido con pase' => 'Salido con pase', 'Salido sin pase' => 'Salido sin pase', 'Pérdida de regularidad' => 'Pérdida de regularidad', 'Fallecimiento' => 'Fallecimiento', 'Sin especificar' => 'Sin especificar');
                echo $this->Form->input('tipo_baja', array('default' => $bajaTipo, 'label' => 'Baja tipo', 'empty' => 'Ingrese una opción...', 'options' => $tipos_baja, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
                if (($current_user['role'] == 'superadmin') || ($current_user['puesto'] == 'Dirección Colegio Secundario') || ($current_user['puesto'] == 'Supervisión Secundaria')) {
                    $motivos_baja = array('Pasó a educación especial' => 'Pasó a educación especial', 'Mudanza de la familia' => 'Mudanza de la familia', 'Problemas de adaptación' => 'Problemas de adaptación', 'Problemas disciplinarios' => 'Problemas disciplinarios', 'Decisión de la institución' => 'Decisión de la institución', 'Problemas de salud' => 'Problemas de salud', 'Dificultad de transporte' => 'Dificultad de transporte', 'Cambio en la situación económica' => 'Cambio en la situación económica', 'No especifica' => 'No especifica', 'Otro' => 'Otro', 'Tenía muchas materias previas' => 'Tenía muchas materias previas', 'Pasó a educación de jóvenes y adultos' => 'Pasó a educación de jóvenes y adultos', 'Comenzó a trabajar' => 'Comenzó a trabajar', 'Quedó embarazada' => 'Quedó embarazada', 'Debe colaborar en la casa' => 'Debe colaborar en la casa');
                } else {
                    $motivos_baja = array('Pasó a educación especial' => 'Pasó a educación especial', 'Mudanza de la familia' => 'Mudanza de la familia', 'Problemas de adaptación' => 'Problemas de adaptación', 'Problemas disciplinarios' => 'Problemas disciplinarios', 'Decisión de la institución' => 'Decisión de la institución', 'Problemas de salud' => 'Problemas de salud', 'Dificultad de transporte' => 'Dificultad de transporte', 'Cambio en la situación económica' => 'Cambio en la situación económica', 'No especifica' => 'No especifica', 'Otro' => 'Otro');
                }   
                echo $this->Form->input('motivo_baja', array('default' => $bajaMotivo, 'label' => 'Motivo de baja', 'empty' => 'Ingrese una opción...', 'options' => $motivos_baja, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
            ?>
        </div>  
        <div class="unit"><h3>PASO 3: Datos del EGRESO</h3><hr />
            <?php
                echo $this->Form->input('fecha_egreso', array('default' => $fechaEgreso, 'label' => 'Fecha de egreso', 'type' => 'text', 'between' => '<br>', 'empty' => ' ','class' => 'datepicker form-control', 'Placeholder' => 'Ingrese una fecha...'));
                if (($current_user['role'] == 'superadmin') || ($current_user['puesto'] == 'Dirección Colegio Secundario') || ($current_user['puesto'] == 'Supervisión Secundaria') || ($current_user['puesto'] == 'Dirección Instituto Superior') || ($current_user['puesto'] == 'Supervisión Secundaria')) {
                    echo $this->Form->input('fecha_emision_titulo', array('default' => $fechaEmisionTitulo, 'label' => 'Fecha de emisión del título', 'type' => 'text', 'between' => '<br>', 'empty' => ' ','class' => 'datepicker form-control', 'Placeholder' => 'Ingrese una fecha...'));
                    echo $this->Form->input('nota', array('default' => $notaFinal, 'label' => 'Nota Final', 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Ingrese la nota final', 'Placeholder' => 'Ingrese una nota...'));
                    echo $this->Form->input('acta_nro', array('default' => $actaNro, 'label' => 'Acta Nº', 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Ingrese un nº de acta', 'Placeholder' => 'Ingrese un nº de acta...'));
                    echo $this->Form->input('libro_nro', array('default' => $libroNro, 'label' => 'Libro Nº', 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Ingrese un nº de libro', 'Placeholder' => 'Ingrese un nº de libro...'));
                    echo $this->Form->input('folio_nro', array('default' => $folioNro, 'label' => 'Folio Nº', 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Ingrese un nº de folio', 'Placeholder' => 'Ingrese un nº de folio...'));
                    echo $this->Form->input('titulo_nro', array('default' => $tituloNro, 'label' => 'Título Nº', 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Ingrese un nº de título', 'Placeholder' => 'Ingrese un nº de título...'));
                }
            ?>
        </div>
        <?php echo '</div><div class="col-md-4 col-sm-6 col-xs-12">'; ?>
        <div class="unit"><h3>PASO 4: Documentación Presentada</h3><hr />
        <!--<?php
            $tipos_alta = array('Regular' => 'Regular', 'Equivalencia'=>'Equivalencia');
            // $this->Form->input('tipo_alta', array('label' => 'Alta tipo*', 'default' => 'Ingrese un tipo...', 'options' => $tipos_alta, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
        ?><br>-->
        <div class="row">
            <br>
            <?php/* if ($current_user['centro_id'] == 23 || $current_user['centro_id'] == 73
                        || $current_user['centro_id'] == 81 || $current_user['centro_id'] == 180
                        || $current_user['centro_id'] == 181 || $current_user['centro_id'] == 513) {
            */?>  
            <div class="input-group">
                <span class="input-group-addon">
                <?php
                    if ($userCentroNivel != 'Común - Servicios complementarios') :
                        $cud_estados = array('Actualizado'=>'Actualizado','Desactualizado'=>'Desactualizado','No tiene'=>'No tiene','No corresponde'=>'No corresponde'); 
                        echo $this->Form->input('cud_estado', array('label'=>'CERTIFICADO ÚNICO DISCAPACIDAD [CUD](*Obligatorio)', 'default' => $cudEstado, 'options' => $cud_estados, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
                    endif;
                ?>
                </span>
            </div>
            <?php/* } */?>
            <div class="input-group">
                <span class="input-group-addon">
                    <?php echo $this->Form->input('fotocopia_dni', array('default'=>$tildeDocumentoString, 'between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Fotocopia DNI</label>'));?>
                </span>
            </div>
            <?php if ($userCentroNivel != 'Adultos - Secundario' && $userCentroNivel != 'Adultos - Primario' && $userCentroNivel != 'Común - Servicios complementarios') : ?>  
            <div class="input-group">
                <span class="input-group-addon">
                    <?php echo $this->Form->input('partida_nacimiento_alumno', array('default'=>$tildePartidaString, 'between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Partida de Nacimiento Alumno</label>'));?>
                </span>
            </div>
            <div class="input-group">
                <span class="input-group-addon">
                    <?php echo $this->Form->input('certificado_vacunas', array('default'=>$tildeVacunasString, 'between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Certificado Vacunación</label>'));?>
                </span>
            </div>
            <?php endif; ?>  
            <!--<div class="input-group">
                <span class="input-group-addon">
                    <?php// echo $this->Form->input('partida_nacimiento_tutor', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Partida de Nacimiento Tutor</label>'));?>
                </span>
            </div>-->
            <?php 
                if (($current_user['role'] == 'superadmin') || ($current_user['puesto'] == 'Dirección Colegio Secundario') || ($current_user['puesto'] == 'Supervisión Secundaria') || ($userCentroNivel == 'Adultos - Secundario') || ($userCentroNivel == 'Adultos - Primario')) {
            ?>  
            <div class="input-group">
                <span class="input-group-addon">
                    <?php echo $this->Form->input('certificado_septimo', array('default'=>$tildeSeptimoString, 'between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Certificado Primario Completo</label>'));?>
                </span>
            </div>
            <!--
            <div class="input-group">
                <span class="input-group-addon">
                    <?php// echo $this->Form->input('analitico', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Certificado Analítico</label>'));?>
                </span>
            </div>
            -->
            <?php } ?>
        </div><br>
        <?php
            if (($current_user['role'] == 'superadmin') || ($current_user['puesto'] == 'Dirección Colegio Secundario') || ($current_user['puesto'] == 'Supervisión Secundaria') || ($current_user['puesto'] == 'Dirección Instituto Superior') || ($current_user['puesto'] == 'Supervisión Secundaria')) {
                // $this->Form->input('estado', array('type' => 'hidden'));
                $condiciones_aprobacion = array('Promocion directa' => 'Promocion directa', 'Examen regular' => 'Examen regular', 'Examen libre' => 'Examen libre', 'Examen de reválida' => 'Examen de reválida', 'Equivalencia' => 'Equivalencia', 'Saberes adquiridos' => 'Saberes adquiridos', 'Examen regular y Equivalencia' => 'Examen regular y equivalencia');
                // $this->Form->input('condicion_aprobacion', array('label' => 'Condición de aprobación*', 'options' => $condiciones_aprobacion, 'empty' => 'Ingrese una opción...', 'between' => '<br>', 'class' => 'form-control'));?><br>
            <?php
            /*
            ?>
            <div class="row">
                <div class="input-group">
                <span class="input-group-addon">
                    <?php  echo $this->Form->input('recursante', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Recursante</label>'));?>
                </span>
                </div>
            </div>
                //  $tipos_cursa = array('Cursa algun espacio curricular'=>'Cursa algun espacio curricular', 'Sólo se inscribe a rendir final' =>'Sólo se inscribe a rendir final', 'Cursa espacio curricular y rinde final'=>'Cursa espacio curricular y rinde final');
                // $this->Form->input('cursa', array('label' => 'Cursa*', 'empty' => 'Ingrese una opción...', 'options' => $tipos_cursa, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
                $tipos_fines = array('No' => 'No', 'Sí línea deudores de materias.' => 'Sí línea deudores de materias.', 'Sí línea trayectos educativos.' => 'Sí línea   trayectos educativos.');
                // $this->Form->input('fines', array('label' => 'Fines*', 'empty' => 'Ingrese una opción...', 'options' => $tipos_fines, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
            */
        } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
        $('#datetimepicker1').datetimepicker({
        useCurrent: true, //this is important as the functions sets the default date value to the current value
        format: 'YYYY-MM-DD hh:mm',
        }).on('dp.change', function (e) {
              var specifiedDate = new Date(e.date);
              if (specifiedDate.getMinutes() == 0)
              {
                  specifiedDate.setMinutes(1);
                  $(this).data('DateTimePicker').date(specifiedDate);
              }
           });
</script>
</div>
