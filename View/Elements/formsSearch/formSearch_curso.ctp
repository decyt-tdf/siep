<?php echo $this->Form->create('Curso',array('type'=>'get','url'=>'index', 'novalidate' => true));?>
<!-- COMBO CENTROS -->
<div class="form-group">
    <?php
    // Si la persona que navega no es Admin, muestro autocomplete de todas las secciones
    if(!$this->Siep->isAdmin()) :
        ?>
            <!-- Autocomplete -->
            <input id="Autocomplete" class="form-control" placeholder="Buscar institucion por nombre" type="text">
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
      $turnos = array('Mañana' => 'Mañana', 'Tarde' =>'Tarde', 'Mañana Extendida' =>'Mañana Extendida', 'Tarde Extendida' => 'Tarde Extendida', 'Doble Extendida' =>'Doble Extendida', 'Vespertino' => 'Vespertino', 'Noche' =>'Noche', 'Otro' =>'Otro'); 
      echo $this->Form->input('Curso.turno', array('label' => false, 'empty'=>'Ingrese un turno...', 'options'=>$turnos, 'class' => 'form-control')); ?>
</div>
<!-- COMBO AÑOS -->
<div class="form-group">
   <?php
        if ($current_user['role'] == 'superadmin') {
            $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años', '1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');
          } else if ($current_user['puesto'] == 'Dirección Jardín') {
              $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años');
          } else if ($current_user['puesto'] == 'Dirección Escuela Primaria') {
              $anios = array('1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');
          } else if ($current_user['puesto'] == 'Supervisión Inicial/Primaria') {
              $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años', '1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');
          } else {
              $anios = array('1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');  
          }
        echo $this->Form->input('anio', array('label' =>false, 'empty' => 'Ingrese un año...', 'options' => $anios, 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Selecciones una opción de la lista'));
    ?>
</div>
<!-- COMBO DIVISION-->
<?php
    if ($current_user['role'] == 'admin') {
?>
    <div class="form-group">
        <?php
        // SI ESTA DEFINIDO EL CENTRO... FILTRAR SECCIONES
        echo $this->Form->input('division', array('label' => false, 'empty' => 'Ingrese una division...', 'options' => $comboDivision, 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Selecciones una opción de la lista'));
        ?>
    </div>
<?php
    }
?>
<!-- COMBO TIPO -->
<div class="form-group">
   <?php
      $tipos = array('Independiente' => 'Independiente', 'Independiente de recuperación' => 'Independiente de recuperación', 'Independiente semipresencial' => 'Independiente semipresencial', 'Independiente presencial y semipresencial' => 'Independiente presencial y semipresencial', 'Múltiple' => 'Múltiple', 'Múltiple de recuperación' => 'Múltiple de recuperación', 'Múltiple semipresencial' => 'Múltiple semipresencial', 'Múltiple presencial y semipresencial' => 'Múltiple presencial y semipresencial', 'No Corresponde' => 'No Corresponde', 'Independiente presencial y semipresencial (violeta)' => 'Independiente presencial y semipresencial (violeta)','Mixta / Bimodal' => 'Mixta / Bimodal', 'Múltiple presencial y semipresencial (violeta)' => 'Múltiple presencial y semipresencial (violeta)', 'Multinivel' => 'Multinivel', 'Multiplan' => 'Multiplan'); 
      echo $this->Form->input('Curso.tipo', array('label' => false, 'empty'=>'Ingrese un tipo...', 'options'=>$tipos, 'class' => 'form-control')); ?>
</div>
<hr />
<div class="text-center">
    <span class="link"><?php echo $this->Form->button('<span class="glyphicon glyphicon-search"></span> BUSCAR', array('class' => 'submit', 'class' => 'btn btn-primary')); ?>
    </span>
    <?php echo $this->Form->end(); ?>
</div>




<!--
<div class="form-group">
	<?php if(($current_user['role'] == 'superadmin') || ($current_user['role'] == 'usuario')): ?>
    <?php
        echo $this->Form->input('centro_id', array('label' =>false, 'empty' => 'Ingrese una institución...', 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Selecciones una opción de la lista'));
    ?>
<?php endif; ?>
    <?php
        if ($current_user['role'] == 'superadmin') {
            $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años', '1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');
          } else if ($current_user['puesto'] == 'Dirección Jardín') {
              $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años');
          } else if ($current_user['puesto'] == 'Dirección Escuela Primaria') {
              $anios = array('1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');
          } else if ($current_user['puesto'] == 'Supervisión Inicial/Primaria') {
              $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años', '1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');
          } else {
              $anios = array('1ro ' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');  
          }
        echo $this->Form->input('anio', array('label' =>false, 'empty' => 'Ingrese un año...', 'options' => $anios, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Selecciones una opción de la lista'));
    ?>
    <?php
		if ($current_user['role'] == 'superadmin') {
              $divisiones = array('ROJA' => 'ROJA', 'CELESTE' => 'CELESTE', 'NARANJA' => 'NARANJA', 'AMARILLA' => 'AMARILLA', 'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G', 'H' => 'H', '1°' => '1°', '2°' => '2°', '3°' => '3°', '4°' => '4°', '5°' => '5°', '6°' => '6°', '7°' => '7°', '8°' => '8°', '9°' => '9°', '10°' => '10°', '11°' => '11°');
          } else if ($current_user['puesto'] == 'Dirección Jardín') {
              $divisiones = array('ROJA' => 'ROJA', 'CELESTE' => 'CELESTE', 'NARANJA' => 'NARANJA', 'AMARILLA' => 'AMARILLA');
          } else {
            $divisiones = array('A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G', 'H' => 'H', '1°' => '1°', '2°' => '2°', '3°' => '3°', '4°' => '4°', '5°' => '5°', '6°' => '6°', '7°' => '7°', '8°' => '8°', '9°' => '9°', '10°' => '10°', '11°' => '11°');       
          }
		echo $this->Form->input('division', array('label' => false, 'empty' => 'Ingrese una división...', 'options' => $divisiones, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción de la lista'));
    ?>
</div>
<hr />
<div class="text-center">
    <span class="link"><?php echo $this->Form->button('<span class="glyphicon glyphicon-search"></span> BUSCAR', array('class' => 'submit', 'class' => 'btn btn-primary')); ?>
    </span>
    <?php echo $this->Form->end(); ?>
</div>
-->