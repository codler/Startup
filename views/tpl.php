<!DOCTYPE html>
<!--[if lt IE 8 ]> <html lang="sv" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="sv" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="sv"> <!--<![endif]-->
<head>
<!-- meta -->
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title></title>
<!-- css -->
<link rel="stylesheet" type="text/css" href="/yap-goodies/css/global2.css" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/ui-lightness/jquery-ui.css" />

<!-- javascript -->
<script src="http://static.zencodez.net/js.php?f=jquery-1.5,jquery-ui-1.8,css3finalize-latest"></script>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php SU::Ui()->load_external(); ?>
<style>

</style>
<script>
jQuery(function ($) {

});
</script>
</head>
<body>
<!--[if lt IE 9]> <div style=' clear: both; height: 59px; padding:0 0 0 15px; position: relative;'> <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode"><img src="http://www.theie6countdown.com/images/upgrade.jpg" border="0" height="42" width="820" alt="" /></a></div> <![endif]-->
<?php 
echo $html;
SU::view($page, $data); 
?>
</body>
</html>