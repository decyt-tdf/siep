<div class="TituloSec"><?php echo __('Editar Espacio'); ?></div>
    <div id="ContenidoSec">
        <div class="cursos form">
            <?php echo $this->Form->create('Materia', array('enctype'=>'multipart/form-data', 'novalidate' => true));?>
            <div class="unit">
                 <?php echo $this->element('forms/form_materia_edit'); ?><p>
            </div>
                 <?php echo $this->Form->input('id', array('type' => 'hidden')); ?>
            <div class="text-center">
                <div class="submit">                 
				   <?php echo $this->Form->submit(__('GUARDAR', true), array('name' => 'ok', 'div' => false, 'class' => 'btn btn-success')); ?>
                   <?php echo $this->Form->submit(__('CANCELAR', true), array('name' => 'cancel', 'div' => false, 'class' => 'btn btn-warning')); ?>
                </div>
                <?php echo $this->Form->end();?>   
        	 </div>
		</div>
	</div>
</div>    