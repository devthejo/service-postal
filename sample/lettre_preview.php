<?php
//require_once '../vendor/autoload.php'; //via composer
require_once 'autoloader.php'; //via stand-alone autoloader

echo "<h1>Envoi d’une lettre : déroulé pas à pas</h1>";

try //Initialisation du service
{
    $maSessionSP = new SP\Session(
        SP_LOGIN,       // login de l'API
        SP_PASSWORD,    // mot de passe de l'API
        TRUE            // mode test (TRUE) ou mode production (FALSE ou vide)
        );
}
catch (\Exception $e)
{
    die("Erreur lors de l'initialisation de Service Postal : ".$e->getMessage() );
}

try
{
    $document = "/documents_exemples/lettre_simple.docx"; //en général le fichiers seront localement sur votre serveur
    echo "<h2>Document à envoyer</h2>";
    echo "Le document suivant va être envoyé à Service Postal : <a download class='btn btn-primary' href='".(isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).$document."'><span class='glyphicon glyphicon-download-alt'></span> Télécharger le document word</a><br />";
    
    
    // Création d'un nouvel envoi (job) 
    $maLettre = $maSessionSP->nouveauLettreJob()
        ->setImpression(    
            SP\Options\Couleur::COULEUR,            // Couleur du courrier 
            SP\Options\Enveloppe::AUTO, 			// Taille de l'enveloppe 
			SP\Options\EnveloppeImprimanteMode::WINDOW,
            SP\Options\Recto::RECTO,          // Impresstion recto ou recto/verso 
            SP\Options\PorteAdresse::ACTIF , // Porte-adresse (optionnel) 
			SP\Options\SenderPrinted::ACTIF,
			SP\Options\BarCode::ACTIF,
			SP\Options\StitchedEnveloppePrinted::INACTIF			
            )
        ->setAffranchissement(
            SP\Options\Affranchissement::LETTRE_RECOMMANDEE   // Type d'affranchissement 
            )
        ->setReferenceExterne(
            "2016-123456ab"                                  // Référence externe (optionnel)
            )
        ->setExpediteur(
            "Ronan",                                // Prénom
            "PAUL",                                 // Nom de famille
            "Service Postal",                       // Société
            "9 rue Ambroise Thomas",                // Adresse 1 (obligatoire)
            "Bâtiment B",                           // Adresse 2 (optionnel)
            "13858",                                // Code postal (obligatoire)
            "Paris",                                // Ville (obligatoire)
            "France"                                // Pays (optionnel)
            )
        ->setDestinataire(
            "Jovana",                               // Prénom
            "Stamenkivic",                          // Nom de famille
            "Net4Com",                              // Société
            "4 rue Edouard Branly",                 // Adresse 1 (obligatoire)
            "Zone d'activité de la louvère",        // Adresse 2 : (optionnel)
            "78190",                                // Code postal (obligatoire)
            "Trappes",                              // Ville (obligatoire)
            "France"                                // Pays (optionnel)
            )
        ->setDocument(getcwd().$document);
        
    // Obtenir le coût théorique d'un courrier de 5 pages avec ces options d'impression / affranchissement
    $estimatePriceResult = $maLettre->estimerPrix(2);
    echo "<h2>Estimation</h2>";
    echo "Votre courrier de ", $estimatePriceResult->spWeight, " g sera produit pour un coût de ",  ($estimatePriceResult->spServicePrice + $estimatePriceResult->spStampPrice), " €.<br />";
    echo "<table border='1'><tr><th>code</th><th>prix</th><th>qté</th></tr>";
    foreach($estimatePriceResult->spServiceCodeList->SP_ServiceCode as $code)
        echo "<tr><td>", $code->spCode, "</td><td>", $code->spPrice, "</td><td>", $code->spQuantity, "</td></tr>";
    echo "</table>";

    // Prévisualisation du courrier
    $letterPreviewResult = $maLettre->preparer();
    echo "<h2>Prévisualisation</h2>";
    $spServicePostalID = $maLettre->jobID;
    echo "Votre document est accepté sur la plate-forme et porte le numéro : <b>", $spServicePostalID, "</b> <a class='btn btn-primary doc-viewer' href='", $letterPreviewResult->spOutputFile->spURL, "'><span class='glyphicon glyphicon-file'></span> Pré-visualiser le document</a><br />";
    if ($letterPreviewResult->spExpectedDispatchNotice == 0)
        echo "et peut être produit <b>aujourd’hui</b> <br />" ;
    else
        echo "et peut être produit dans <b>", $letterPreviewResult->spExpectedDispatchNotice , " jour(s)</b><br />" ;
}
catch (\Exception $e)
{
    die("Erreur lors de l'envoi du courrier de Service Postal : ".$e->getMessage() );
}

echo "<h2>Pour aller plus loin</h2>";
echo "<a href='lettre_validate.php?servicePostalID=",$maLettre->jobID,"' class='btn btn-success'><span class='glyphicon glyphicon-ok'></span> Valider cette lettre</a> ";
echo "<a href='query.php?servicePostalID=",$maLettre->jobID,"' class='btn btn-primary'><span class='glyphicon glyphicon-eye-open'></span> Suivre cette lettre</a> ";
echo "<a href='lettre_cancel.php?servicePostalID=",$maLettre->jobID,"' class='btn btn-danger'><span class='glyphicon glyphicon-trash'></span> Annuler cette lettre</a>";

echo "<h2>Code source</h2>";
echo "<div class='well well-sm'>";show_source(__FILE__);echo "</div>";  
?>
