<?php
    // Si el usuario no es Admin, muestro el filtro.
    if(!$this->Siep->isAdmin()) :
?>
<div class="TituloSec">Filtro</div>
<div id="ContenidoSec">
    <?php echo $this->Form->create('Curso',array('type'=>'get','url'=>'index', 'novalidate' => true));?>
    <div class="row">
        <div class="col-xs-4"> 
         <!-- Autocomplete -->
              <input id="AutocompleteForm" class="form-control" placeholder="Buscar institucion por nombre" type="text">
            <script>
              $( function() {
                $( "#AutocompleteForm" ).autocomplete({
                  source: "<?php echo $this->Html->url(array('controller'=>'Centros','action'=>'autocompleteCentro'));?>",
                  minLength: 2,
                  select: function( event, ui ) {
                    $("#AutocompleteForm").val( ui.item.Centro.sigla );

                    window.location.href = "<?php echo $this->Html->url(array('controller'=>'ingresantes'));?>/index?ciclo=<?php echo $apiParams['ciclo']; ?>&centro_id="+ui.item.Centro.id;
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
        </div>
         <!--<div class="col-xs-2">
            <div class="input select">
                <?php
                echo $this->Form->input('sector', array('options'=>$comboSector, 'empty'=>'- Todos los sectores -', 'label'=>false, 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
                ?>
            </div>
        </div>-->
        <!--<div class="col-xs-2">
            <div class="input select">
                <?php
                echo $this->Form->input('ciudad_id', array('options'=>$comboCiudad, 'empty'=>'- Todas las ciudades -', 'label'=>false, 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Seleccione una opción'));
                ?>
            </div>
        </div>-->
        <!--<div class="col-xs-2">
            <div class="text-center">
                <span class="link">
                    <?php echo $this->Form->button('<span class="glyphicon glyphicon-search"></span> Aplicar filtro', array('class' => 'btn btn-primary')); ?>
                </span>
            </div>
        </div>-->
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<?php endif; ?>
<?php
     $mostrar = false;
     if( $current_user['Centro']['nivel_servicio'] === 'Común - Inicial - Primario' ||
         $current_user['Centro']['nivel_servicio'] === 'Común - Inicial' ||
         $current_user['Centro']['nivel_servicio'] === 'Común - Primario' ) {
         $mostrar = true;
     }
?>
<div class="TituloSec">Ingresantes <?php echo $apiParams['ciclo'];?></div>
<div id="ContenidoSec">
  <?php
  // Permite descargar lista de egresados del ciclo anterior
    /*
    if(isset($apiParams['ciclo']) && isset($apiParams['centro_id'])) :

        $cicloAnterior = $apiParams['ciclo'] -1;
    */
    ?>
    <!--
        <a target="_blank" class="btn btn-success pull-right" href="<?php /*echo '/gateway/excel_egresos/ciclo:'.$cicloAnterior.'/centro_id:'.$apiParams['centro_id']; */?>">
            <span class="glyphicon glyphicon-file"></span> Exportar ingresantes por egreso
        </a>
        <br>
        <br>
    -->
    <?php
     // endif;
    ?>

    <div class="table-responsive">
      <table id="tablaPieBuscador" class="table table-bordered table-hover table-striped    table-condensed">
      <thead>
        <tr>
          <th>Institución</th>
          <th>Año</th>
          <th>Turno</th>
          <?php if ($mostrar) { ?>
            <th>Plazas(Cupo alumnos)</th>
          <?php } else { ?>
            <th>Plazas(*)</th>
          <?php } ?>
          <?php if ($mostrar) { ?>
            <th>Matricula(Total Pre-inscriptos)</th>
          <?php } else { ?>
            <th>Matricula</th>
          <?php } ?>
          <th>Por hermanos</th>
          <?php if ($mostrar) { ?>
            <th>VACANTES(Lugares disponibles)</th>
          <?php } else { ?>
            <th>VACANTES</th>
          <?php } ?>  
            <!-- SOlo muestra acceso al VIEW de la sección ficticia sí corresponde a INGRESANTES 2020. -->
            <?php if ($apiParams['ciclo'] == $cicloNombreUltimo) : ?>
              <th>CONFIRMADAS</th>
            <?php endif ?>
          </tr>
      </thead>
      <tbody>
        <?php $count=0;
        ?>
        <?php
        if(isset($matriculas_por_seccion['total'])) :
        foreach($matriculas_por_seccion['data'] as $seccion): ?>
            <td>
              <?php echo $seccion['nombre']; ?>
            </td>
            <td>
              <?php echo $seccion['anio']; ?>
            </td>
            <td>
              <?php echo $seccion['turno']; ?>
            </td>
            <td>
              <?php echo $seccion['plazas']; ?>
            </td>
            <td>
              <?php echo $seccion['matriculas']; ?>
            </td>
            <td>
              <?php echo $seccion['por_hermano']; ?>
            </td>
            <?php if ($seccion['vacantes'] < 0) { ?>
            <td>
                <?php echo $seccion['vacantes']. ' ' .'<span class="label label-danger">Sorteo</span>'; ?>
            </td>
            <?php } else { ?>
            <td>
              <?php echo $seccion['vacantes']; ?>
            </td>
            <?php } ?>            
            <?php if ($apiParams['ciclo'] == $cicloNombreUltimo) : ?>
            <?php if (($seccion['plazas'] != NULL) && ($seccion['confirmadas'] > $seccion['plazas'])) { ?>
            <td>
                <?php echo $seccion['confirmadas']. ' ' .'<span class="label label-danger">Excede Plaza</span>'; ?>
            </td>
            <?php } else { ?>
            <td>
                <?php echo $seccion['confirmadas']; ?>
            </td>
            <?php } ?>
            <!-- Sólo muestra acceso al VIEW de la sección ficticia sí corresponde a INGRESANTES 2020. -->
            <td>
              <span class="link"><?php echo $this->Html->link('<i class="glyphicon glyphicon-eye-open"></i>', array('controller' => 'Cursos', 'action'=> 'view', $seccion['curso_id']), array('class' => 'btn btn-default', 'escape' => false)); ?></span>
            </td>
            <?php endif ?>
          </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
      <tfoot>
        <tr>
          <!--<th>
            <!-- Autocomplete
              <input id="AutocompleteForm" class="form-control" placeholder="Buscar institucion por nombre" type="text">

            <script>
              $( function() {
                $( "#AutocompleteForm" ).autocomplete({
                  source: "<?php echo $this->Html->url(array('controller'=>'Centros','action'=>'autocompleteCentro'));?>",
                  minLength: 2,
                  select: function( event, ui ) {
                    $("#AutocompleteForm").val( ui.item.Centro.sigla );

                    window.location.href = "<?php echo $this->Html->url(array('controller'=>'ingresantes'));?>/index?centro_id="+ui.item.Centro.id;
                    return false;
                  }
                }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                  return $( "<li>" )
                      .append( "<div>" +item.Centro.sigla + "</div>" )
                      .appendTo( ul );
                };
              });
            </script>
            <!-- End Autocomplete
          </th>-->
          <!--<th>
              <?php echo $this->Form->create('Vacantes',array('id'=>'formFiltroAnio','type'=>'get','url'=>'index', 'novalidate' => true));?>

              <?php
              if ($this->Siep->isSuperAdmin()) {
                  $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años', '1ro' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');
              } else if ($current_user['puesto'] == 'Dirección Jardín') {
                  $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años');
              } else if ($current_user['puesto'] == 'Dirección Escuela Primaria') {
                  $anios = array('1ro' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');
              } else if ($current_user['puesto'] == 'Supervisión Inicial/Primaria') {
                  $anios = array('Sala de 3 años' => 'Sala de 3 años', 'Sala de 4 años' => 'Sala de 4 años', 'Sala de 5 años' => 'Sala de 5 años', '1ro' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');
              } else {
                  $anios = array('1ro' => '1ro', '2do' => '2do', '3ro' => '3ro', '4to' => '4to', '5to' => '5to', '6to' => '6to', '7mo' => '7mo');
              }
              echo $this->Form->input('anio', array('id'=>'filtroAnio','label' =>false, 'empty' => 'Ingrese un año...', 'options' => $anios, 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Selecciones una opción de la lista'));

              ?>

              <?php echo $this->Form->end(); ?>
          </th>-->
          <th>

          </th>
        </tr>
      </tfoot>
    </table>
    <strong>(*) Valores de plazas con resguardo de vacantes e indicados por las Supervisiones de los niveles.</strong>         
      <script>
        $(function(){

          $('#filtroAnio').on( 'change', function () {
              $('#formFiltroAnio').submit();
           });
        });
      </script>
    </div>
</div>

<?php
echo $this->Siep->pagination($matriculas_por_seccion);
?>