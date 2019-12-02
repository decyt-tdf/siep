<?php echo $this->Form->create('Inscripcion',array('type'=>'get','url'=>'index', 'novalidate' => true));?>
<div class="form-group">
   <?php echo $this->Form->input('ciclo_id', array('label' => false, 'class' => 'form-control', 'empty' => 'Ingrese un ciclo...'));	?>
</div><br>
<?php if(($current_user['role'] == 'superadmin') || ($current_user['role'] == 'usuario')): ?>
<div class="form-group">
   <?php echo $this->Form->input('centro_id', array('label' => false, 'class' => 'form-control', 'empty' => 'Ingrese una institución...'));	?>
</div><br>
<?php endif; ?>
<div class="form-group">
   <?php echo $this->Form->input('legajo_nro', array('label' => false, 'class' => 'form-control', 'Placeholder' => 'Ingrese un código de inscripción...'));	?>
</div>
<div class="form-group">
    <?php
    $inscripcion_tipos = array('Común'=>'Común','Hermano de alumno regular'=>'Hermano de alumno regular','Pase'=>'Pase','Integración'=>'Integración','Situación social'=>'Situación social','Hijo de personal de la institución'=>'Hijo de personal de la institución', 'Estudiante de Intercambio'=>'Estudiante de Intercambio');
    echo $this->Form->input('tipo_inscripcion', array('label' => false, 'empty' => 'Ingrese un tipo de inscripción...', 'options' => $inscripcion_tipos, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
    ?>
</div>
<div class="form-group">
    <?php
    $inscripcion_estados = array('CONFIRMADA'=>'CONFIRMADA','NO CONFIRMADA'=>'NO CONFIRMADA','BAJA'=>'BAJA','EGRESO'=>'EGRESO','ANULADA'=>'ANULADA','SIN TERMINALIDAD'=>'SIN TERMINALIDAD');
    echo $this->Form->input('estado_inscripcion', array('label' => false, 'empty' => 'Ingrese un estado de la inscripción...', 'options' => $inscripcion_estados, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
    ?>
</div>
<?php/* if ($current_user['centro_id'] == 23 || $current_user['centro_id'] == 73
        || $current_user['centro_id'] == 81 || $current_user['centro_id'] == 180
        || $current_user['centro_id'] == 181 || $current_user['centro_id'] == 513) { */?>
<div class="form-group">
    <?php
		$cud_estados = array('Actualizado'=>'Actualizado','Desactualizado'=>'Desactualizado','No tiene'=>'No tiene','No corresponde'=>'No corresponde');
		echo $this->Form->input('cud_estado', array('label' => false, 'empty' => 'Ingrese un estado del CUD...', 'options' => $cud_estados, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
    ?>
</div>
<?php/* } */?>
<div class="form-group">
    <?php
		$documentacion_estados = array('COMPLETA' => 'COMPLETA', 'PENDIENTE' => 'PENDIENTE');
		echo $this->Form->input('estado_documentacion', array('label' => false, 'empty' => 'Ingrese un estado de la documentación...', 'options' => $documentacion_estados, 'between' => '<br>', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
    ?>
</div>
<hr>
<div class="text-center">
    <span class="link"><?php echo $this->Form->button('<span class="glyphicon glyphicon-search"></span> BUSCAR', array('class' => 'submit', 'class' => 'btn btn-primary')); ?>
    </span>
    <?php echo $this->Form->end(); ?>
</div>
