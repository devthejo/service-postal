<?php
//require_once '../vendor/autoload.php'; //via composer
require_once 'autoloader.php'; //via stand-alone autoloader

echo "<h1>Validation d'un mailing</H1>";

// Récupération de l'ID de l'envoi pour lequel le suivi est demandé
if (isset($_GET['servicePostalID']))
    $servicePostalID = $_GET['servicePostalID'];
else
    die("Pour tester le suivi de courrier, commencez par exécuter un des exemples d'envoi, puis cliquez sur le lien suivre cet envoi");
    
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
    $monMailing = $maSessionSP->chargeMailingJob($servicePostalID);
    
    echo "<h2>Statut de l'envoi avant validation {$servicePostalID}</h2>";
    $queryStatusResult = $monMailing->queryStatut(); 
    echo "Le statut du mailing est : <b>", $queryStatusResult->spStatus, "</b><br />";
    
    // Valider l'envoi du document
    $mailingValidResult  = $monMailing->valider();
    echo "<h2>Validation</h2>";
    echo "Votre document va être envoyé, le coût sera de : <b>", $mailingValidResult->spTotalCost, " €</b>.<br />";
    
    $nb = $mailingValidResult->spLetterssCount;
    echo "Votre mailing contient <b>{$nb} lettres</b> ";
    
    if ($mailingValidResult->spExpectedDispatchNotice == 0)
        echo "et sera produit <b>aujourd’hui</b> <br />" ;
    else
        echo "et sera produit dans <b>", $mailingValidResult->spExpectedDispatchNotice , " jour(s)</b><br />" ;
    
    echo "Vous pouvez suivre chaque lettre individuellement : ";
    for ($index = 1; $index <= $nb; $index ++)
        echo " <a class='btn btn-primary modal-viewer' href='query.php?servicePostalID=",$monMailing->jobID,"&index=",$index,"' target='_blank'><span class='glyphicon glyphicon-eye-open'></span> ",$index,"</a> ";
    
    echo "<h2>Statut actuel de l'envoi {$servicePostalID}</h2>";
    $queryStatusResult = $monMailing->queryStatut();
    echo "Le statut de l'envoi est : <b>", $queryStatusResult->spStatus, "</b><br />";
}
catch (\Exception $e)
{
    die("Erreur lors de l'annulation d'un courrier de Service Postal : ".$e->getMessage() );
}

echo "<h2>Pour aller plus loin</h2>";
echo "<a href='query.php?servicePostalID=",$monMailing->jobID,"' class='btn btn-primary'><span class='glyphicon glyphicon-eye-open'></span> Suivre ce mailing</a> ";
echo "<a class='btn btn-danger' href='lettre_cancel.php?servicePostalID=",$monMailing->jobID,"'><span class='glyphicon glyphicon-trash'></span> Annuler ce mailing</a>";

$maSessionSP->logout();

echo "<h2>Code source</h2>";
echo "<div class='well small'>";show_source(__FILE__);echo "</div>";
?>
