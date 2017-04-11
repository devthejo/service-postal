<?php
//require_once '../vendor/autoload.php'; //via composer
require_once 'autoloader.php'; //via stand-alone autoloader

echo "<h1>Envoi d’un Mailing direct</h1>";

try { //Initialisation du service
    $maSessionSP = new SP\Session(SP_LOGIN, SP_PASSWORD, TRUE );
} 
catch (\Exception $e)
{ 
    die("Erreur lors de l'initialisation de Service Postal : ".$e->getMessage() ); 
}

try
{
    $document_modele = "/documents_exemples/lettre_simple.docx"; //en général le fichiers seront localement sur votre serveur
    $donnees_destinataires = "/documents_exemples/mailing_data.csv"; //en général le fichiers seront localement sur votre serveur
    echo "<h2>Documents à envoyer pour le mailing</h2>";
    echo "Les fichiers suivants vont être envoyés à Service Postal pour réaliser le mailing : <br />";
    echo "<a download class='btn btn-primary' href='".(isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).$document_modele."'><span class='glyphicon glyphicon-download-alt'></span> Télécharger le modèle de courrier (au format word)</a> ";
    echo "<a download class='btn btn-primary' href='".(isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).$donnees_destinataires."'><span class='glyphicon glyphicon-download-alt'></span> Télécharger le fichier des destinataires (au format csv)</a><br />";
    
    // Création d'un nouvel envoi (job) 
    $monMailing = $maSessionSP->nouveauMailingJob()
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
            SP\Options\Affranchissement::LETTRE_VERTE // Type d'affranchissement 
            )
        ->setReferenceExterne(
            "2016-123456ab"                   // Référence externe (optionnel)
            )
        ->setDocumentModele(getcwd().$document_modele)
        ->setDocumentDonnees(getcwd().$donnees_destinataires);
        
    // Envoi du mailing en direct
    $mailingSubmitResult = $monMailing->envoyerDirectement();
    echo "<h2>Envoi du mailing</h2>";
    echo "Votre mailing est transmis à la plate-forme et porte l'ID : <b>", $monMailing->jobID, "</b><br />";
    $nb = $mailingSubmitResult->spLetterssCount;
    echo "Votre mailing contient <b>{$nb} lettres</b> ";
    
    if ($mailingSubmitResult->spExpectedDispatchNotice == 0)
        echo "et sera produit <b>aujourd’hui</b> <br />" ;
    else
        echo "et sera produit dans <b>", $mailingSubmitResult->spExpectedDispatchNotice , " jour(s)</b><br />" ;
    
    echo "Vous pouvez suivre chaque lettre individuellement : ";
    for ($index = 1; $index <= $nb; $index ++)
        echo " <a class='btn btn-primary modal-viewer' href='query.php?servicePostalID=",$monMailing->jobID,"&index=",$index,"' target='_blank'><span class='glyphicon glyphicon-eye-open'></span> ",$index,"</a> ";
}
catch (\Exception $e)
{
    die("Erreur lors de l'envoi du mailing de Service Postal : ".$e->getMessage() );
}

echo "<h2>Pour aller plus loin</h2>";
echo "<a href='query.php?servicePostalID=",$monMailing->jobID,"' class='btn btn-primary'><span class='glyphicon glyphicon-eye-open'></span> Suivre ce mailing</a> ";
echo "<a class='btn btn-danger' href='lettre_cancel.php?servicePostalID=",$monMailing->jobID,"'><span class='glyphicon glyphicon-trash'></span> Annuler ce mailing</a> ";
    
$maSessionSP->logout();

echo "<h2>Code source</h2>";
echo "<div class='well small'>";show_source(__FILE__);echo "</div>";
?>
