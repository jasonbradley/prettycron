<?php
    if (isset($_REQUEST['crontab']) && $_REQUEST['crontab'] != '') {
        require_once('./lib/PrettyCron.php');
        $prettyCron = new PrettyCron($_REQUEST['crontab']);
    }
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Pretty Cron - Visualize Your Cron</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="vendor/twitter/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="#">Home</a></li>
        </ul>
        <h3 class="text-muted">Pretty Cron</h3>
      </div>

      <br/>

      <div class="jumbotron">
        <h1>Visualize Your Cron</h1>
        <p class="lead">Paste your crontab and hit visualize.</p>
        <form id="form1">
            <textarea style="height:200px;" name="crontab" class="form-control"><?php echo (isset($_REQUEST['crontab']) ? $_REQUEST['crontab'] : '');?></textarea>
            <br/>
            <p style="float:right"><a class="btn btn-lg btn-success" href="#" role="button" onclick="$('#form1').submit();">Visualize</a></p>
        </form>
        <br clear="all">
      </div>

    </div> <!-- /container -->

    <?php if (isset($prettyCron) && $prettyCron instanceof PrettyCron && count($prettyCron->getCronLines()) > 0): ?>
        <div class="container">
            <h1><span class="glyphicon glyphicon-time"></span> &nbsp;<span style="position:relative;top:-2px;">Schedule</span></h1>
            <table  class="table table-striped">
                <thead>
                    <tr>
                        <th>Command</th>
                        <th>Last Run</th>
                        <th>Next Run</th>
                    </tr>
                </thead>
                <tbody>
            <?php foreach ($prettyCron->getCronLinesByDate() as $cronLine): ?>
                <?php if ($cronLine['cron_expression'] instanceof \Cron\CronExpression): ?>
                        <?php try { ?>
                            <tr>
                                <td><?php echo $cronLine['crontab_line']; ?></td>
                                <td><?php echo $cronLine['cron_expression']->getPreviousRunDate()->format('Y-m-d H:i:s');?></td>
                                <td><?php echo $cronLine['cron_expression']->getNextRunDate()->format('Y-m-d H:i:s');?></td>
                            </tr>
                        <?php } catch (Exception $e) {} ?>
                <?php endif; ?>
            <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="vendor/components/jquery/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="vendor/twitter/bootstrap/dist/js/bootstrap.min.js"></script>
  </body>
</html>