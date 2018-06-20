<?php echo $this->Html->css(array('/js/select2/select2.min')); ?>
<?php echo $this->Html->script(array('tooltip', 'datepicker', 'moment', 'bootstrap-datetimepicker','select2/select2.min')); ?>

<script>
    $(function(){
        $('.s2_alumno').select2();
    });
</script>

<div class="row">
</div><hr />
<div class="row">
   	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="unit"><strong><h3>Datos Generales</h3></strong><hr />
			<!-- Autocomplete nombre de familiares -->
            <div>
                <strong><h5>Nombres y Apellidos del Padre/Tutor*</h5></strong>
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
                            source: "<?php echo $this->Html->url(array('action'=>'autocompleteNombrePersona'));?>",
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
            </script><br>
            <!-- End Autocomplete nombre de familiares -->
        <?php  
            if ($current_user['role'] == 'admin') { 
              echo $this->Form->input('Alumno', array('label'=>'Alumno*', 'empty' => 'Ingrese un alumno...', 'options'=>$alumnosNombre ,'between' => '<br>', 'class' => 's2_alumno form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
            } else if (($current_user['role'] == 'superadmin') || ($current_user['role'] == 'usuario')) {
        ?>
            <!-- Autocomplete nombre del alumno -->
            <div>
                <strong><h5>Nombre Completo del Alumno*</h5></strong>
                <input id="AutocompleteAlumno" class="form-control" placeholder="Buscar alumno por DNI, nombre y/o apellido">
                <div class="alert alert-danger" role="alert" id="AutocompleteAlumnoError" style="display:none;">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>
                    No se encontraron resultados de busqueda
                </div>
            </div>
            <hr />
            <script>
                $( function() {
                    $( "#AutocompleteAlumno" ).autocomplete({
                        source: "<?php echo $this->Html->url(array('controller'=>'alumnos', 'action'=>'autocompleteNombreAlumno'));?>",
                        minLength: 2,
                        select: function( event, ui ) {
                            var nombre_completo = ui.item.Persona.apellidos +" "+ ui.item.Persona.nombres +' - ' +ui.item.Persona.documento_nro;
                            $("#AutocompleteAlumno").val( nombre_completo );
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
            <!-- End Autocomplete nombre del alumno -->
        <?php } ?>
        </div>
	  	<?php echo '</div><div class="col-md-6 col-sm-6 col-xs-12">'; ?>
      	<div class="unit"><strong><h3>Datos Específicos</h3></strong><hr />
        	<?php
			  	$vinculos = array('Padre' => 'Padre', 'Madre'=>'Madre', 'Tutor'=>'Tutor');
			  	echo $this->Form->input('vinculo', array('label'=>'Vinculo*', 'empty' => 'Ingrese un vinculo...', 'options' => $vinculos, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
			?>
        </div>
        <div class="input-group">
            <span class="input-group-addon">
              <?php echo $this->Form->input('conviviente', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Conviviente</label>'));?>
            </span>
        </div>
        <div class="input-group">
            <span class="input-group-addon">
              <?php echo $this->Form->input('autorizado_retirar', array('between' => '<br>', 'class' => 'form-control', 'label' => false, 'type' => 'checkbox', 'before' => '<label class="checkbox">', 'after' => '<br><i></i><br>Autorizado a Retirar</label>'));?>
            </span>
        </div>
    </div>
    <div class="col-md-12 col-sm-6 col-xs-12">
        <?php echo $this->Form->input('observaciones', array('label'=>'Observaciones', 'type' => 'textarea', 'between' => '<br>', 'class' => 'form-control')); ?>
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
    <script>tinymce.init({ selector:'textarea' });</script>
   </div>
</div>
