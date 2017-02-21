<html>
<head>
<title>Kit de développement Service Postal</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<style>
.fa-spin-custom, .glyphicon-spin {
    -webkit-animation: spin 1000ms infinite linear;
    animation: spin 1000ms infinite linear;
}
@-webkit-keyframes spin {
    0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(359deg);
        transform: rotate(359deg);
    }
}
@keyframes spin {
    0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(359deg);
        transform: rotate(359deg);
    }
}
</style>
</head>
<body>
<div class="container-fluid">
	<div class="row hidden-xs">
		<div class="col-xs-3"><img src="http://testsp.servicepostal.com/Images/service-postal.png"></div>
		<div class="col-xs-9"><h1>Kit de développement PHP - API Service Postal</h1></div>
	</div><!-- div row -->
	<div class="row">
    	<div class="col-xs-3 col-md-2">
    		<h2>Exemples</h2>
    		<div class="list-group">
    			<a class="list-group-item" href="lettre_direct.php">Envoi d'un courrier<br />(direct)</a>
    			<a class="list-group-item" href="lettre_preview.php">Envoi d'un courrier<br />(avec étape de prévisualisation)</a>
    			<a class="list-group-item" href="mailing_direct.php">Envoi d'un mailing<br />(direct)</a>
    			<a class="list-group-item" href="mailing_preview.php">Envoi d'un mailing<br />(avec étape de prévisualisation)</a>
    			<a class="list-group-item" href="jobs_list.php" >Liste des courriers</a>
    		</div>
    		<h2>Référence</h2>
    		<p><a href="documentation/ServicePostal_kit_developpement_php_11_5.pdf" class="btn btn-primary btn-sm btn-block doc-viewer"><span class='glyphicon glyphicon-file'></span>  Guide du développeur</a></p>
    		<p><a href="documentation/ServicePostal_api_reference_11_4.pdf" class="btn btn-primary btn-sm btn-block doc-viewer"><span class='glyphicon glyphicon-file'></span>  API référence</a></p>
    	</div>
    	<div class="col-xs-9 col-md-10">
    		<div id="status" class="alert alert-info hidden" role="alert"><span class="glyphicon glyphicon-spin glyphicon-refresh"></span> L'appel à l'API est en cours... veuillez patienter ! <span class="glyphicon glyphicon-spin glyphicon-refresh"></span></div>
    		<div id="test_content" width="100%" height="100%">
    			<h1>Kit de développement PHP</h1>
    			Grâce au menu de gauche découvrez les différentes fonctions proposées par le kit de développement PHP.
    		</div>
    	</div>
	</div> <!-- div row -->			
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Détail</h4>
      </div>
      <div class="modal-body">
          <iframe id="doc-viewer-iframe" src="" width="100%" height="100%" frameborder="0" allowtransparency="true"></iframe>
          <div id="modal-status" class="alert alert-info hidden" role="alert"><span class="glyphicon glyphicon-spin glyphicon-refresh"></span> L'appel à l'API est en cours... veuillez patienter ! <span class="glyphicon glyphicon-spin glyphicon-refresh"></span></div>
          <div id="modal-viewer"></div>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
</body>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script>
function init_page()
{
	
    $('a.doc-viewer:not(.boundAjax)').addClass('boundAjax').bind('click', function (event) {
    	event.preventDefault();
    	console.log('doc-viewer');
    	$('#modal-viewer').hide();
    	$('#doc-viewer-iframe').show();
    	$('#myModal').modal('show');
    	$('#doc-viewer-iframe').attr('src', this.href);
    });

    $('a.modal-viewer:not(.boundAjax)').addClass('boundAjax').bind('click', function (event) {
    	event.preventDefault();
    	console.log('modal-viewer');
    	$('#modal-viewer').empty();
    	$('#modal-viewer').show();
    	$('#doc-viewer-iframe').hide();
    	$('#myModal').modal('show');
    	$('#modal-status').show();
    	$("#modal-viewer").load(this.href, function(response, status, xhr)
    	{
    		$('#modal-status').hide();
    		init_page();
    	});
    });

    $('.modal a:not(.boundAjax)').addClass('boundAjax').bind('click', function (event) {
    	event.preventDefault();
    	console.log('modal-viewer');
    	$('#modal-viewer').empty();
    	$('#modal-viewer').show();
    	$('#doc-viewer-iframe').hide();
    	$('#myModal').modal('show');
    	$('#modal-status').show();
    	$("#modal-viewer").load(this.href, function(response, status, xhr)
    	{
    		$('#modal-status').hide();
    		init_page();
    	});
    });
    
    
    $('a:not([href^="#"]):not(.boundAjax):not([download])').addClass('boundAjax').bind('click', function (event) {
    	event.preventDefault();
    	console.log('ajax load');
    	$("#test_content").hide();
    	$('#status').show();
    	$("#test_content").load(this.href, function(response, status, xhr)
    	{
    		$('#status').hide();
    		$("#test_content").fadeIn();
    		init_page();
    	});
    });
}

$(document).ready(function() {
	$('.hidden').hide().removeClass('hidden');
	$.ajaxSetup({
		crossDomain: true
	});
	
	init_page();
});
</script>
</html>