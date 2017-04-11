<?php
//require_once '../vendor/autoload.php'; //via composer
require_once 'autoloader.php'; //via stand-alone autoloader

echo "<h1>Suivi d'envoi</H1>";

// Récupération de l'ID de l'envoi pour lequel le suivi est demandé
if (isset($_GET['servicePostalID'])) 
    $servicePostalID = $_GET['servicePostalID'];
else 
    die("Un identifiant servicePostalID doit être transmis en paramètre d'URL !");

// Récupération de l'ID de l'envoi pour lequel le suivi est demandé
if (isset($_GET['index'])) 
    $index = $_GET['index']; 
else 
    $index = 1; 

try //Initialisation du service
{   $maSessionSP = new SP\Session(SP_LOGIN, SP_PASSWORD, TRUE ); }
catch (\Exception $e)
{   die("Erreur lors de l'initialisation de Service Postal : ".$e->getMessage() ); }

try
{
    echo "<h2>Suivi lettre : {$servicePostalID}-{$index}</h2>";
    
    // Statut de l'envoi
    $queryStatusResult = $maSessionSP->queryJobStatut($servicePostalID, $index );
    echo "Le statut de l'envoi est : ", $queryStatusResult->spStatus, "<br />";

    // Récupération des documents de l'envoi
    $queryDocumentResult = $maSessionSP->queryJobDocument($servicePostalID, $index , SP\Options\DocumentType::DOCUMENT);
    echo "<a class='btn btn-primary doc-viewer' href='", $queryDocumentResult->spURL, "'><span class='glyphicon glyphicon-file'></span> Visualiser le document</a><br />";

    // Détail du coût de l'envoi
    $queryRateResult = $maSessionSP->queryJobCoutDetail($servicePostalID, $index);
    echo "<table border='1'><tr><th>code</th><th>prix</th><th>qté</th></tr>";
    foreach($queryRateResult->spServiceCodeList->SP_ServiceCode as $code)
        echo "<tr><td>", $code->spCode, "</td><td>", $code->spPrice, "</td><td>", $code->spQuantity, "</td></tr>";
    echo "</table>";
    echo "Poids du courrier : ", $queryRateResult->spWeight, " g<br />";
    echo "Coût du service : ", $queryRateResult->spServicePrice, " €<br />";
    echo "Coût de l'affranchissement : ", $queryRateResult->spStampPrice, " €<br />";

    // Coût total de l'envoi
    $queryCostResult = $maSessionSP->queryJobCoutTotal($servicePostalID, $index );
    echo "Le coût total est de : ", $queryCostResult, " €<br />";
        
}
catch (\Exception $e)
{
    echo "Erreur lors de la récupération du statut du courrier de Service Postal : ".$e->getMessage(), "<br />";
}

echo "<h2>Pour aller plus loin</h2>";
echo "<a  class='btn btn-success' href='lettre_validate.php?servicePostalID=",$servicePostalID,"&index=",$index,"'><span class='glyphicon glyphicon-ok'></span> Valider cet envoi</a> ";
echo "<a class='btn btn-danger' href='lettre_cancel.php?servicePostalID=",$servicePostalID,"&index=",$index,"'><span class='glyphicon glyphicon-trash'></span> Annuler cet envoi</a>";

$maSessionSP->logout();

echo "<h2>Code source</h2>";
echo "<div class='well small'>";show_source(__FILE__);echo "</div>";
?>
