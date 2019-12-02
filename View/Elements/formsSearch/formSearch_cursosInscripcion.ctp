<?php echo $this->Form->create('CursosInscripcion',array('type'=>'get','url'=>'index', 'novalidate' => true));?>

<!-- COMBO DISPLAY -->
<!--<div class="form-group">
    <div class="input select">
        <select name="modo" class="form-control" data-toggle="tooltip" data-placement="bottom">
            <option value="tarjeta">Ver resultados como tarjetas</option>
            <option value="lista" selected="selected">Ver resultados como una lista</option>
            ?>
        </select>
    </div>
</div>-->

<!-- COMBO CICLOS -->
<div class="form-group">
    <div class="input select">
        <?php
        echo $this->Form->input('ciclo_id', array('label' => '* Debe indicar un CICLO para imprimir listado.', 'empty'=>'Seleccione un ciclo ( * Obligatorio )', 'options'=>$comboCiclo, 'default'=>$defaultForm['ciclo_id'], 'class' => 'form-control'));	?>
    </div>
</div>

<!-- COMBO CENTROS -->
<div class="form-group">
    <?php
    // Si la persona que navega no es Admin, muestro autocomplete de todas las secciones
    if(!$this->Siep->isAdmin()) :
        ?>
            <!-- Autocomplete -->
            <input id="Autocomplete" class="form-control" placeholder="Ingrese el nombre de la institución..." type="text">
            <input id="AutocompleteId" type="hidden" name="centro_id">
            <script>
                $( function() {
                    $( "#Autocomplete" ).autocomplete({
                        source: "<?php echo $this->Html->url(array('controller'=>'Centros','action'=>'autocompleteCentro'));?>",
                        minLength: 2,
                        select: function( event, ui ) {
                            $("#Autocomplete").val( ui.item.Centro.sigla );
                            $("#AutocompleteId").val( ui.item.Centro.id );
                            return false;
                        }
                    }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                        return $( "<li>" )
                            .append( "<div>" +item.Centro.sigla + "</div>" )
                            .appendTo( ul );
                    };
                });
            </script>
        <!-- End Autocomplete -->
        <?php
    endif;
    ?>
</div>

<!-- COMBO TURNOS -->
<div class="form-group">
   <?php
   		$turnos = array('Mañana' => 'Mañana', 'Tarde' =>'Tarde', 'Mañana Extendida' =>'Mañana Extendida', 'Tarde Extendida' => 'Tarde Extendida', 'Doble Extendida' =>'Doble Extendida', 'Vespertino' => 'Vespertino', 'Noche' =>'Noche', 'Otro' =>'Otro', 'Tecnico' => 'Tecnico', 'Bachiller' => 'Bachiller'); 
   		echo $this->Form->input('Curso.turno', array('label' => false, 'empty'=>'Ingrese un turno...', 'options'=>$turnos, 'default'=>$defaultForm['turno'], 'class' => 'form-control'));	?>
</div>

<!-- COMBO AÑOS -->
<div class="form-group">
   <?php
        if ($current_user['role'] == 'superadmin') {
                $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años', '1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo', '8vo' => '8vo', '9no' => '9no', '10mo' => '10mo', '11ro' => '11ro', '12do' => '12do', '13ro' => '13ro', '14to' => '14to', '15to' => '15to', '16to' => '16to', '17mo' => '17mo', '18vo' => '18vo', '19no' => '19no', '20mo' => '20mo', '21ro' => '21ro', '22do' => '22do', '23ro' => '23ro', '24to' => '24to', '25to' => '25to', '26to' => '26to', '27mo' => '27mo', '28vo' => '28vo', '29no' => '29no', '30mo' => '30mo', '31ro' => '31ro', '32do' => '32do', '33ro' => '33ro', '34to' => '34to');
          } else if ($current_user['puesto'] == 'Dirección Jardín') {
                $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años');
          } else if ($current_user['puesto'] == 'Dirección Escuela Primaria') {
                $anios = array('1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');
          } else if ($current_user['puesto'] == 'Supervisión Inicial/Primaria') {
                $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años', '1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo', '8vo' => '8vo', '9no' => '9no', '10mo' => '10mo', '11ro' => '11ro', '12do' => '12do', '13ro' => '13ro', '14to' => '14to', '15to' => '15to', '16to' => '16to', '17mo' => '17mo', '18vo' => '18vo', '19no' => '19no', '20mo' => '20mo', '21ro' => '21ro', '22do' => '22do', '23ro' => '23ro', '24to' => '24to', '25to' => '25to', '26to' => '26to', '27mo' => '27mo', '28vo' => '28vo', '29no' => '29no', '30mo' => '30mo', '31ro' => '31ro', '32do' => '32do', '33ro' => '33ro', '34to' => '34to');
          } else {
                $anios = array('1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');  
          }
        echo $this->Form->input('anio', array('label' =>false, 'empty' => 'Ingrese un año...', 'options' => $anios, 'default'=>$defaultForm['anio'],  'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Selecciones una opción de la lista'));
    ?>
</div>

<!-- COMBO DIVISION-->
<?php
    if ($current_user['role'] == 'admin') {
?>
    <div class="form-group">
        <?php
        // SI ESTA DEFINIDO EL CENTRO... FILTRAR SECCIONES
        echo $this->Form->input('division', array('label' => false, 'empty' => 'Ingrese una division...', 'options' => $comboDivision, 'default'=>$defaultForm['division'], 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Selecciones una opción de la lista'));
        ?>
    </div>
<?php
    }
?>

<div class="form-group">
    <?php
    $inscripcion_estados = array('CONFIRMADA'=>'CONFIRMADA','NO CONFIRMADA'=>'NO CONFIRMADA','BAJA'=>'BAJA','EGRESO'=>'EGRESO','ANULADA'=>'ANULADA','SIN TERMINALIDAD'=>'SIN TERMINALIDAD');
    echo $this->Form->input('estado_inscripcion', array('label' => false, 'empty' => 'Ingrese un estado de la inscripción...', 'options' => $inscripcion_estados, 'default'=>$defaultForm['estado_inscripcion'],'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
    ?>
</div>

<?php /*
<div class="form-group">
    <div class="input select">
        <select name="seccion" class="form-control" data-toggle="tooltip" data-placement="bottom">
            <option value="">Seleccione una seccion...</option>
            <?php
            foreach($comboSecciones as $seccion_id=> $seccion_value) :
                ?>
                <option value="<?php echo $seccion_id;  ?>"><?php echo $seccion_value; ?></option>
                <?php
            endforeach;
            ?>
        </select>
    </div>
</div><br>
*/ ?>
<hr />
<div class="text-center">
    <span class="link"><?php echo $this->Form->button('<span class="glyphicon glyphicon-search"></span> BUSCAR', array('class' => 'submit', 'class' => 'btn btn-primary')); ?>
    </span>
    <?php echo $this->Form->end(); ?>
</div>
