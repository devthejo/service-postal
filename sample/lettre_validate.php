<?php
require_once 'SP_config.php';
require_once 'servicepostal/ServicePostal.php';

echo "<h1>Validation d'un envoi</H1>";

// Récupération de l'ID de l'envoi pour lequel le suivi est demandé
if (isset($_GET['servicePostalID']))
    $servicePostalID = $_GET['servicePostalID'];
else
    die("Pour tester le suivi de courrier, commencez par exécuter un des exemples d'envoi, puis cliquez sur le lien suivre cet envoi");
    
// Récupération de l'ID de l'envoi pour lequel le suivi est demandé
if (isset($_GET['index']))
    $index = $_GET['index'];
else
    $index = 1;

try //Initialisation du service
{
    $maSessionSP = new SP\Session( SP_LOGIN, SP_PASSWORD, TRUE);
}
catch (\Exception $e)
{
    die("Erreur lors de l'initialisation de Service Postal : ".$e->getMessage() );
}

try
{
    $maLettre = $maSessionSP->chargeLettreFromMailingJob($servicePostalID, $index);
    
    echo "<h2>Statut de l'envoi avant validation {$servicePostalID}-{$index}</h2>";
    $queryStatusResult = $maLettre->queryStatut(); 
    //$maSessionSP->queryJobStatut($servicePostalID, $index );
    echo "Le statut de l'envoi est : <b>", $queryStatusResult->spStatus, "</b><br />";
    
    // Valider l'envoi du document
    $letterValidResult  = $maLettre->valider();
    echo "<h2>Validation</h2>";
    echo "Votre document va être envoyé, le coût sera de : <b>", $letterValidResult->spTotalCost, " €</b>.<br />";
    
    echo "<h2>Statut actuel de l'envoi {$servicePostalID}-{$index}</h2>";
    $queryStatusResult = $maLettre->queryStatut();
    echo "Le statut de l'envoi est : <b>", $queryStatusResult->spStatus, "</b><br />";
    

}
catch (\Exception $e)
{
    echo "Erreur lors de la validation d'un courrier de Service Postal : ".$e->getMessage() ;
}

echo "<h2>Pour aller plus loin</h2>";
echo "<a href='query.php?servicePostalID=",$maLettre->jobID,"' class='btn btn-primary'><span class='glyphicon glyphicon-eye-open'></span> Suivre cet envoi</a> ";
echo "<a class='btn btn-danger' href='lettre_cancel.php?servicePostalID=",$maLettre->jobID,"'><span class='glyphicon glyphicon-trash'></span> Annuler cet envoi</a>";

$maSessionSP->logout();

echo "<h2>Code source</h2>";
echo "<div class='well small'>";show_source(__FILE__);echo "</div>";
?>