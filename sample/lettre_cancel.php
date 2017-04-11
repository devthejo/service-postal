<?php
//require_once '../vendor/autoload.php'; //via composer
require_once 'autoloader.php'; //via stand-alone autoloader

echo "<h1>Annulation d'un envoi</H1>";

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
    
    echo "<h2>Statut de l'envoi avant annulation {$servicePostalID}-{$index}</h2>";
    $queryStatusResult = $maLettre->queryStatut();
    echo "Le statut de l'envoi est : ", $queryStatusResult->spStatus, "<br />";
    
    echo "<h2>Annulation d'un courrier au sein d'un mailing {$servicePostalID}-{$index}</h2>";
    $cancelResult = $maLettre->annuler();
    //var_dump($cancelResult);
    echo "La lettre a été <b>annulée</b><br />";

    echo "<h2>Statut actuel de l'envoi {$servicePostalID}-{$index}</h2>";
    $queryStatusResult = $maLettre->queryStatut();
    echo "Le statut de l'envoi est : ", $queryStatusResult->spStatus, "<br />";
    

}
catch (\Exception $e)
{
    echo "Erreur lors de l'annulation d'un courrier de Service Postal : ".$e->getMessage() ;
}

echo "<a class='btn btn-primary' href='query.php?servicePostalID=",$maLettre->jobID,"&index=",$index,"'><span class='glyphicon glyphicon-eye-open'></span> Suivre cette lettre</a> ";
echo "<a  class='btn btn-success' href='lettre_validate.php?servicePostalID=",$maLettre->jobID,"&index=",$index,"'><span class='glyphicon glyphicon-ok'></span> Valider cette lettre</a> ";

$maSessionSP->logout();

echo "<h2>Code source</h2>";
echo "<div class='well small'>";show_source(__FILE__);echo "</div>";
?>
