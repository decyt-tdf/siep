<?php echo $this->Html->css(array('/js/select2/select2.min')); ?>
<?php echo $this->Html->script(array('tooltip', 'datepicker', 'moment', 'bootstrap-datetimepicker','select2/select2.min')); ?>
<script>
    $(function(){
        $('.s2_centro').select2();
        $('.s2_seccion').select2();

        $('.s2_centro').on("change", function(){
            // Remueve datos de secciones
            $(".s2_seccion").empty();
            // Obtener secciones dependientes al centro
            $.ajax({
                type:"GET",
                url: "<?php echo "/gateway/cursos/por_pagina:all/centro_id:"?>" + $(this).val(),
                success: function(response){
                    var data = response.data;
                    $(".s2_centro").append('<option value="' +''+ '"> ' + 'Seleccione una sección'+ '</option>');
                    // Valores retorandos por el api
                    for (var index in data) {
                        var el = data[index];
                        $(".s2_seccion").append('<option value="' +el.id+ '"> ' + el.nombre_completo + '</option>');
                    }
                }
            });
        });

    });
</script>
<div class="row">
  <!-- Datos generales -->
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="unit"><strong><h3>PASO 1: Datos Generales</h3></strong><hr />
        <!-- Autocomplete para nombre de Personas -->
            <div>
                <h5><strong>Nombre y apellidos del alumno a inscribir (*Obligatorio)</strong></h5>
                <input id="PersonaNombreCompleto" class="form-control" data-toggle="tooltip" data-placemente="bottom" placeholder="Ingrese el nombre completo">
                <input id="PersonaId" name="data[Persona][persona_id]" type="text" style="display:none;">
                <div class="alert alert-danger" role="alert" id="AutocompleteError" style="display:none;">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only">Error:</span>
                        La persona no fue localizada.
                        <?php echo $this->Html->link("Crear persona",array('controller'=>'personas','action'=>'add'));?>
            </div>
            </div><br>
            <script>
                    $( function() {
                        $( "#PersonaNombreCompleto" ).autocomplete({
                            source: "<?php echo $this->Html->url(array('controller'=>'Alumnos','action'=>'autocompleteNombrePersona'));?>",
                            minLength: 2,
                            // Evento: se ejecuta al seleccionar el resultado
                            select: function( event, ui ) {
                                // Elimina ID de persona previo a establecer la seleccion
                                $("#PersonaId").val("");
                                if(ui.item != undefined) {
                                    var nombre_completo = ui.item.Persona.nombre_completo_persona;
                                    $("#PersonaNombreCompleto").val(nombre_completo);
                                    $("#PersonaId").val(ui.item.Persona.id);
                                    return false;
                                }
                            },
                            response: function(event, ui) {
                                // Elimina ID de persona al obtener respuesta
                                $("#PersonaId").val("");
                                if (ui.content.length === 0) {
                                    $("#AutocompleteError").show();
                                    $("#PersonaId").val("");
                                } else {
                                    $("#AutocompleteError").hide();
                                }
                            }
                        }).autocomplete("instance")._renderItem = function( ul, item ) {
                            // Renderiza el resultado de la respuesta
                            var nombre_completo = item.Persona.nombre_completo_persona + " - "+item.Persona.documento_nro;
                            return $( "<li>" )
                                .append( "<div>" +nombre_completo+ "</div>" )
                                .appendTo( ul );
                        };
                    });
            </script>
            <!-- End Autocomplete -->
            <?php
                echo $this->Form->input('ciclo_id', array('label'=>'Ciclo lectivo (*Obligatorio)', 'default'=>$cicloIdActual, 'empty' => 'Ingrese un ciclo lectivo...', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
            ?>
            <?php echo $this->Form->input('usuario_id', array('type' => 'hidden')); ?>
            <br>
            <?php
                if (($current_user['role'] == 'superadmin') || ($current_user['role'] == 'usuario')) {
                    echo $this->Form->input('centro_id', array('label'=>'Institución destino (*Obligatorio)', 'empty' => 'Ingrese una institución...', 'class' => 's2_centro form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
                    echo '<br>';
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
            ?>
        </div>
    </div>
    <!-- End Datos generales -->
    <!-- Datos de alta -->
   <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="unit">
        <h3>PASO 2: Datos del Alta</h3>
        <hr />
        <?php
            $estados_inscripcion = array('CONFIRMADA'=>'CONFIRMADA','NO CONFIRMADA'=>'NO CONFIRMADA');
            echo $this->Form->input('estado_inscripcion', array('label'=>'Estado de la inscripción (*Obligatorio)', 'default'=>'CONFIRMADA', 'empty' => 'Ingrese un estado de la inscripción...', 'options'=>$estados_inscripcion, 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
        ?>
        <br>
        <?php
            // Reestricción provisoria de inscripciones por hermanos.
            switch ($current_user['puesto']) {
                case 'Dirección Colegio Secundario':
                    if ($current_user['centro_id'] == 92) {
                        $tipos_inscripcion = array('Común'=>'Común','Hermano de alumno regular'=>'Hermano de alumno regular','Pase'=>'Pase','Situación social'=>'Situación social', 'Integración'=>'Integración');
                    } else {
                        $tipos_inscripcion = array('Común'=>'Común', 'Pase'=>'Pase', 'Situación social'=>'Situación social', 'Integración'=>'Integración');
                    }
                    break;
                default:
                    $tipos_inscripcion = array('Común'=>'Común','Hermano de alumno regular'=>'Hermano de alumno regular','Pase'=>'Pase','Situación social'=>'Situación social', 'Integración'=>'Integración', 'Estudiante de Intercambio'=>'Estudiante de Intercambio');
                    break;
            }
            echo $this->Form->input('tipo_inscripcion', array('id'=>'tipoInscripcion',/* 'default'=>'Común',*/'label'=>'Tipo de inscripción (*Obligatorio)', 'empty' => 'Ingrese un tipo de inscripción...', 'options'=>$tipos_inscripcion, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
        ?>
    <hr>
    <!-- Autocomplete -->
    <div id="formHermanoDeAlumnoRegular">
        <h5><strong>Indique el alumno regular de la institución (*Obligatorio)</strong></h5>
        <input id="AutocompleteHermanoAlumno" class="form-control" placeholder="Indique Alumno por DNI, nombre y/o apellido">
        <input id="AutocompleteHermanoAlumnoId" type="hidden" name="data[Inscripcion][hermano_id]">
        <div class="alert alert-danger" role="alert" id="AutocompleteHermanoAlumnoError" style="display:none;">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>
            No se encontraron resultados de busqueda
        </div>
    </div>
    <script>
        $( function() {
            $( "#AutocompleteHermanoAlumno" ).autocomplete({
                source: "<?php echo $this->Html->url(array('controller'=>'Alumnos', 'action'=>'autocompleteNombreAlumno'));?>",
                minLength: 2,
                select: function( event, ui ) {
                    var nombre_completo = ui.item.Persona.nombres +" "+ ui.item.Persona.apellidos;
                    $("#AutocompleteHermanoAlumno").val( nombre_completo );
                    $("#AutocompleteHermanoAlumnoId").val(ui.item.Alumno.id);
                    //window.location.href = "<?php echo $this->Html->url(array('controller'=>'alumnos','action'=>'view'));?>/"+ui.item.Alumno.id;
                    return false;
                },
                response: function(event, ui) {
                    if (ui.content.length === 0)
                    {
                        $("#AutocompleteHermanoAlumnoError").show();
                    } else {
                        $("#AutocompleteHermanoAlumnoError").hide();
                    }
                }
            }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                var nombre_completo = item.Persona.nombres +" "+ item.Persona.apellidos +' - ' +item.Persona.documento_nro;
                return $( "<li>" )
                    .append( "<div>" +nombre_completo+ "</div>" )
                    .appendTo( ul );
            };
        });
    </script>
    <!-- End Autocomplete -->
    <div id="formPase" style="display:none;">
      <h5><strong>Indique la institución de origen del alumno (*Obligatorio)</strong></h5>
      <input id="AutocompleteForm" class="form-control" placeholder="Indique institucion origen por nombre o CUE">
      <input id="centroId" type="hidden" name="data[Inscripcion][centro_origen_id]">
    </div>
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
        <div id="formSituacionSocial" style="display:none;">
            <?php
                if (($current_user['role'] == 'superadmin') || ($current_user['puesto'] == 'Dirección Colegio Secundario') || ($current_user['puesto'] == 'Supervisión Secundaria')) {
                    $situaciones_sociales = array('Pasó a educación especial' => 'Pasó a educación especial', 'Mudanza de la familia' => 'Mudanza de la familia', 'Problemas de adaptación' => 'Problemas de adaptación', 'Problemas disciplinarios' => 'Problemas disciplinarios', 'Decisión de la institución' => 'Decisión de la institución', 'Problemas de salud' => 'Problemas de salud', 'Dificultad de transporte' => 'Dificultad de transporte', 'Cambio en la situación económica' => 'Cambio en la situación económica', 'No especifica' => 'No especifica', 'Otro' => 'Otro', 'Tenía muchas materias previas' => 'Tenía muchas materias previas', 'Pasó a educación de jóvenes y adultos' => 'Pasó a educación de jóvenes y adultos', 'Comenzó a trabajar' => 'Comenzó a trabajar', 'Quedó embarazada' => 'Quedó embarazada', 'Debe colaborar en la casa' => 'Debe colaborar en la casa');
                } else {
                    $situaciones_sociales = array('Pasó a educación especial' => 'Pasó a educación especial', 'Mudanza de la familia' => 'Mudanza de la familia', 'Problemas de adaptación' => 'Problemas de adaptación', 'Problemas disciplinarios' => 'Problemas disciplinarios', 'Decisión de la institución' => 'Decisión de la institución', 'Problemas de salud' => 'Problemas de salud', 'Dificultad de transporte' => 'Dificultad de transporte', 'Cambio en la situación económica' => 'Cambio en la situación económica', 'No especifica' => 'No especifica', 'Otro' => 'Otro');
                }
                echo $this->Form->input('situacion_social', array('label' => 'Situación social', 'empty' => 'Ingrese una opción...', 'options' => $situaciones_sociales, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
            ?>
        </div>
        <br>
        <?php echo $this->Form->input('observaciones', array('label'=>'Observaciones', 'type' => 'textarea', 'between' => '<br>', 'class' => 'form-control', 'placeholder' => 'Indique observaciones relevantes para esta inscripción.')); ?>
        <?php
        /*
                $tipos_alta = array('Regular' => 'Regular', 'Equivalencia'=>'Equivalencia');
                echo $this->Form->input('tipo_alta', array('label' => 'Alta tipo*', 'default' => 'Ingrese un tipo...', 'options' => $tipos_alta, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));

        if (($current_user['role'] == 'superadmin') || ($current_user['puesto'] == 'Dirección Colegio Secundario') || ($current_user['puesto'] == 'Supervisión Secundaria') || ($current_user['puesto'] == 'Dirección Instituto Superior') || ($current_user['puesto'] == 'Supervisión Secundaria')) {
            echo $this->Form->input('estado', array('type' => 'hidden'));
            $condiciones_aprobacion = array('Promocion directa' => 'Promocion directa', 'Examen regular' => 'Examen regular', 'Examen libre' => 'Examen libre', 'Examen de reválida' => 'Examen de reválida', 'Equivalencia' => 'Equivalencia', 'Saberes adquiridos' => 'Saberes adquiridos', 'Examen regular y Equivalencia' => 'Examen regular y equivalencia');
            echo $this->Form->input('condicion_aprobacion', array('label' => 'Condición de aprobación*', 'options' => $condiciones_aprobacion, 'empty' => 'Ingrese una opción...', 'between' => '<br>', 'class' => 'form-control'));?><br>
            <div class="row">
                <div class="input-group">
                <span class="input-group-addon">
                    <?php  echo $this->Form->input('recursante', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Recursante</label>'));?>
                </span>
                </div>
            </div>
            <?php  $tipos_cursa = array('Cursa algun espacio curricular'=>'Cursa algun espacio curricular', 'Sólo se inscribe a rendir final' =>'Sólo se inscribe a rendir final', 'Cursa espacio curricular y rinde final'=>'Cursa espacio curricular y rinde final');
                echo $this->Form->input('cursa', array('label' => 'Cursa*', 'empty' => 'Ingrese una opción...', 'options' => $tipos_cursa, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
                $tipos_fines = array('No' => 'No', 'Sí línea deudores de materias.' => 'Sí línea deudores de materias.', 'Sí línea trayectos educativos.' => 'Sí línea   trayectos educativos.');
                echo $this->Form->input('fines', array('label' => 'Fines*', 'empty' => 'Ingrese una opción...', 'options' => $tipos_fines, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
            }
        */
        ?>
        </div>
    </div>
   <!-- End Datos de alta -->
   <!-- Documentacion presentada -->
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="unit"><h3>PASO 3: Documentación Presentada</h3><hr />
            <div class="row"><br>
            <?php/* if ($current_user['centro_id'] == 23 || $current_user['centro_id'] == 73
                        || $current_user['centro_id'] == 81 || $current_user['centro_id'] == 180
                        || $current_user['centro_id'] == 181 || $current_user['centro_id'] == 513
                        || $userCentroNivel == 'Común - Secundario') {
            */?>  
            <div class="input-group">
                <span class="input-group-addon">
                <?php 
                    if ($userCentroNivel != 'Común - Servicios complementarios') :
                        $cud_estados = array('Actualizado'=>'Actualizado','Desactualizado'=>'Desactualizado','No tiene'=>'No tiene','No corresponde'=>'No corresponde'); 
                        echo $this->Form->input('cud_estado', array('label'=>'CERTIFICADO ÚNICO DISCAPACIDAD [CUD](*Obligatorio)', 'default'=>'No corresponde', 'options'=>$cud_estados, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
                    endif;
                ?>
                </span>
            </div>
            <?php/* } */?>
            <div class="input-group">
                <span class="input-group-addon">
                    <?php echo $this->Form->input('fotocopia_dni', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Fotocopia DNI</label>')); ?>
                </span>
            </div>
        <?php if ($userCentroNivel != 'Adultos - Secundario' && $userCentroNivel != 'Adultos - Primario' && $userCentroNivel != 'Común - Servicios complementarios') : ?>  
          <div class="input-group">
          <span class="input-group-addon">
            <?php echo $this->Form->input('partida_nacimiento_alumno', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Partida de Nacimiento Alumno</label>'));?>
          </span>
          </div>
          <div class="input-group">
          <span class="input-group-addon">
            <?php echo $this->Form->input('certificado_vacunas', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Certificado Vacunación</label>'));?>
          </span>
          </div>
        <?php endif; ?>  
          <!--
          <div class="input-group">
            <span class="input-group-addon">
             <?php// echo $this->Form->input('partida_nacimiento_tutor', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Partida de Nacimiento Tutor</label>'));?>
            </span>
          </div>
          -->
          <?php 
          if (($current_user['role'] == 'superadmin') || ($current_user['puesto'] == 'Dirección Colegio Secundario') || ($current_user['puesto'] == 'Supervisión Secundaria')) {
          ?>  
            <div class="input-group">
                <span class="input-group-addon">
                 <?php echo $this->Form->input('certificado_septimo', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Certificado Primario Completo</label>'));?>
                </span>
            </div>
            <!--
            <div class="input-group">
              <span class="input-group-addon">
                <?php echo $this->Form->input('analitico', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Certificado Analítico</label>'));?>
              </span>
            </div>
            -->
          <?php } ?>
        </div>
    </div>
</div>
<!-- End documentacion presentada -->
<!--<div class="row">
    <div class="col-sm-8">
        <?php// echo $this->Form->input('observaciones', array('label'=>'Observaciones', 'type' => 'textarea', 'between' => '<br>', 'class' => 'form-control')); ?>
    </div>
</div>-->
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
        $(function() {
            $( "#tipoInscripcion" ).change(function(e){
                // Por defecto oculta todas las opciones de inscripcion
                $('#formHermanoDeAlumnoRegular').hide();
                $('#formPase').hide();
                $('#formSituacionSocial').hide();
                // Resetea los formularios al cambiar el tipo de carga
                $('#formHermanoDeAlumnoRegular input').val('');
                $('#formPase input').val('');
                $('#formSituacionSocial select').val('');
                var opt = $( this ).val();
                switch(opt) {
                    case 'Hermano de alumno regular':
                        $('#formHermanoDeAlumnoRegular').show();
                    break;
                    case 'Pase':
                        $('#formPase').show();
                    break;
                    case 'Situación social':
                        $('#formSituacionSocial').show();
                    break;
                }
            });
            // Quita el modo disabled del formulario, para enviar los datos!
            $('form').submit(function(e) {
                $(':disabled').each(function(e) {
                    $(this).removeAttr('disabled');
                })
            });
        });
</script>
</div>
