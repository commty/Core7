<!DOCTYPE html>
<html lang="<?php print $language->language ?>">
<head>
    <!-- Meta, title, CSS, favicons, etc. -->
    <title><?php print $head_title; ?></title>
    <?php print $head; ?>
    <?php print $styles; ?>
    <?php print $scripts; ?>
  </head>
  <body id="page-top" class="<?php print $classes; ?>">

  <?php print $page_top; ?>
  <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header page-scroll">
        <a class="navbar-brand" href="location.reload();"><?php print $title; ?></a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-right">
          <li class="hidden">
            <a href="#page-top"></a>
          </li>
          <li class="page-scroll">
            <a href="#" data-toggle="modal" data-target="#Help">Помощь</a>
          </li>
        </ul>
      </div>
      <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-sm-3 col-md-3 sidebar">
          <?php if ($logo): ?>
            <img id="logo" src="<?php print $logo ?>" alt="<?php print $site_name ?>" />
          <?php endif; ?>
          <?php print $sidebar_first ?>
      </div>
      <div class="col-sm-9 col-md-9 main">
              <?php if ($messages): ?>
                <?php print $messages; ?>
              <?php endif; ?>
              <?php if ($help): ?>
                <div id="help">
                  <?php print $help; ?>
                </div>
              <?php endif; ?>
              <?php print $content; ?>
      </div>
    </div>
  </div>

  <!-- Help modal -->
  <div class="modal fade" id="Help" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title">Инструкция по установке Core7 </h4>
              </div>
              <div class="modal-body">
                 Господа контрибуторы, просьба написать исчерпывающую инструкцию по установке, обновлению и настройке движка с картинками.
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-info" data-dismiss="modal">Закрыть</button>
              </div>
          </div>
      </div>
  </div>

  <?php print $page_bottom; ?>

  </body>
</html>
