<div class="col-md-4">
    <div class="unit">
        <?php if(($current_user['role'] == 'superadmin') || ($current_user['role'] == 'usuario')): ?>
        <span class="name"><span class="glyphicon glyphicon-home"></span> <b>Centro: </b> <?php echo $this->Html->link($curso['Centro']['sigla'], array('controller' => 'centros', 'action' => 'view', $curso['Centro']['id'])); ?></span><br/>
    <?php endif; ?>
        <?php $AñoNombre = ($curso['Curso']['centro_id']==73) ? 'Grupo' : 'Año'; ?>
        <span class="name"><span class="glyphicon glyphicon-info-sign"></span> <b><?php echo $AñoNombre.':';?></b> <?php echo $curso['Curso']['anio']; ?></span><br/>
        <span class="name"><span class="glyphicon glyphicon-info-sign"></span> <b>División: </b> <?php echo $curso['Curso']['division']; ?></span><br/>
        <span class="name"><span class="glyphicon glyphicon-info-sign"></span> <b>Turno: </b> <?php echo $curso['Curso']['turno']; ?></span></br>
        <span class="name"><span class="glyphicon glyphicon-info-sign"></span> <b>Tipo: </b> <?php echo $curso['Curso']['tipo']; ?></span></br>
    <?php if (array_key_exists($curso['Curso']['centro_id'], $centrosIds)): ?>
        <span class="name"><span class="glyphicon glyphicon-info-sign"></span> <b>Titulación: </br></b> <?php echo $titulacionesNombres[$curso['Curso']['titulacion_id']]; ?></span>
    <?php endif; ?>
        <hr />
        <div class="text-right">
            <span class="link"><?php echo $this->Html->link('<i class= "glyphicon glyphicon-eye-open"></i>', array('controller' => 'cursos', 'action' => 'view', $curso['Curso']['id']), array('class' => 'btn btn-success','escape' => false)); ?></span>
            <?php if(($current_user['role'] == 'superadmin' && ($current_user['puesto'] == 'Sistemas' || $current_user['puesto'] == 'Atei')) || ($current_user['role'] == 'usuario' && ($current_user['puesto'] == 'Supervisión Inicial/Primaria' || $current_user['puesto'] == 'Supervisión Secundaria'))): ?>  
                <span class="link"><?php echo $this->Html->link(__('<i class= "glyphicon glyphicon-edit"></i>'), array('action' => 'edit', $curso['Curso']['id']), array('class' => 'btn btn-warning','escape' => false)); ?></span>
            <?php endif; ?>
            <?php if($current_user['role'] == 'superadmin' && $current_user['puesto'] == 'Sistemas'): ?> 
                 <!--<span class="link"><?php //echo $this->Html->link('<i class= "glyphicon glyphicon-trash"></i>', array('controller' => 'cursos', 'action' => 'delete', $curso['Curso']['id']), array('confirm' => 'Está seguro de borrar a '.$curso['Curso']['nombre_completo_curso'], 'class' => 'btn btn-danger','escape' => false)); ?></span>-->
                <?php if ($curso['Curso']['status'] != 0) { ?>
                    <span class="link"><?php echo $this->Html->link('<i class="glyphicon glyphicon-trash"></i>', array('action' => 'delete', $curso['Curso']['id']), array('confirm' => 'Está seguro de desactivar a '.$curso['Curso']['nombre_completo_curso'], 'class' => 'btn btn-danger', 'escape' => false)); ?></span>
                <?php } else { ?>
                <span class="link"><?php echo $this->Html->link('<i class="glyphicon glyphicon-repeat"></i>', array('action' => 'activate', $curso['Curso']['id']), array('class' => 'btn btn-danger', 'escape' => false)); ?></span>
            <?php } ?>
          <?php endif; ?>   
        </div>
    </div>
</div>
