<!-- ************ bootstrap navbar ************ -->
<p>
<nav class="navbar navbar-custom navbar-static-top navbar-fixed-top" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Mostrar/ocultar navegación</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <!--<a class="navbar-brand" href="#">-->SIEP. [ <span class="glyphicon glyphicon-user"</span> <?php echo $this->Html->link($current_user['username'], array('controller' => 'users', 'action' => 'view', $current_user['id'])); ?> ]<!--</a>-->
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <!--<ul class="nav navbar-nav">
          <li><?php echo $this->Html->link(__('Alta de Personas'),'/personas'); ?></li>
        </ul>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">CUEs <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><?php echo $this->Html->link(__('Instituciones'),'/centros'); ?></li>
            <li><?php echo $this->Html->link(__('Establecimientos'),'/'); ?></li>
            <li><?php echo $this->Html->link(__('Infraestructura'),'/'); ?></li>
            <li><?php echo $this->Html->link(__('Inventario'),'/'); ?></li>
          </ul>
        </li>-->
        <!--<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Normativas <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><?php echo $this->Html->link(__('Resoluciones'), '/resolucions'); ?></li>
            <li><?php echo $this->Html->link(__('Anexos'), '/anexos'); ?></li>
          </ul>
        </li>-->
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Ofertas <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <!--<li><?php echo $this->Html->link(__('Titulaciones'),'/titulacions'); ?></li>-->
            <!--<li><?php echo $this->Html->link(__('Diseños Curriculares'), '/disenocurriculars'); ?></li>-->
            <li><?php echo $this->Html->link(__('Secciones'), '/cursos'); ?></li>
            <!--<li><?php echo $this->Html->link(__('Unidades Curriculares'),'/materias'); ?></li>-->
          </ul>
        </li>  
        <!--<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Alumnado <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><?php echo $this->Html->link(__('Inscripciones'), '/inscripcions'); ?></li>
            <li><?php echo $this->Html->link(__('Familiares'), array('controller'=>'familiars', 'action'=>'add')); ?></li>
            <li><?php echo $this->Html->link(__('Alumnos'),'/alumnos'); ?></li>
            <!--<li><?php echo $this->Html->link(__('Pases'), '/pases'); ?></li>-->
            <!--<li><?php echo $this->Html->link(__('Inasistencias'), '/inasistencias'); ?></li>-->
            <!--<li><?php echo $this->Html->link(__('Calificaciones'), '/notas'); ?></li>-->
            <!--<li><?php echo $this->Html->link(__('Mesa de Exámenes'), '/mesaexamens'); ?></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Docentes <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><?php echo $this->Html->link(__('Docentes'),'/'); ?></li>
            <li><?php echo $this->Html->link(__('Cargos'), '/'); ?></li>
            <li><?php echo $this->Html->link(__('Altas y Bajas'), '/'); ?></li>
            <li><?php echo $this->Html->link(__('Licencias'), '/'); ?></li>
            <li><?php echo $this->Html->link(__('Jornadas Institucionales'), '/'); ?></li>
            <li><?php echo $this->Html->link(__('Capacitaciones'), '/'); ?></li>
          </ul>
        </li>-->
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Ver... <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><?php echo $this->Html->link(__('Instituciones'),'/centros'); ?></li>
            <!--<li><?php echo $this->Html->link(__('Alumnos por Sección [nominal]'), '/cursos_inscripcions');?></li>-->
            <li><?php echo $this->Html->link(__('Alumnos por Sección [cuantitativo]'), '/matriculas');?></li>
            <li><?php echo $this->Html->link(__('Inscriptos por año [cuantitativo]'), '/vacantes');?></li>
            <!--<li><?php echo $this->Html->link(__('Gráficos Estadísticos'), '/graficos'); ?></li>-->
            <!--<li><?php echo $this->Html->link('Respaldos', 'http://localhost/mybackups/import.php', array('target'=>'_blank'));?></li>-->
            <!--<li><?php echo $this->Html->link(__('Reportes'),'/report_manager/reports');?></li>-->
            <!--<li><?php echo $this->Html->link(__('Calendario'),'/full_calendar');?></li>--> 
          </ul>
        </li>
      <li>
      <?php echo $this->Html->link('CERRAR SESIÓN', '/logout', array('class' => 'btn btn-success navbar-btn btn-lg', 'escape' => false)); ?>
      </li>
    </div> <!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
