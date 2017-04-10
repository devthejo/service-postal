<?php
require_once 'SP_config.php';
require_once 'servicepostal/ServicePostal.php';

echo "<h1>Envoi de lettre (direct)</h1>";
 
try 
{
    $document = "/documents_exemples/lettre_simple.docx"; //en général le fichiers seront localement sur votre serveur
    echo "<h2>Document à envoyer</h2>";
    echo "Le document suivant va être envoyé à Service Postal : <a download class='btn btn-primary' href='".(isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).$document."'><span class='glyphicon glyphicon-download-alt'></span> Télécharger le document word</a><br />";
    
    // Authentification
    $maSessionSP = new SP\Session(SP_LOGIN, SP_PASSWORD, TRUE);
    
    // Création d'un nouvel envoi d'une lettre
    $maLettre = $maSessionSP->nouveauLettreJob()
        ->setImpression(SP\Options\Couleur::COULEUR, SP\Options\Enveloppe::AUTO,\SP\Options\EnveloppeImprimanteMode::PRINTED, SP\Options\Recto::RECTO, SP\Options\PorteAdresse::INACTIF, SP\Options\SenderPrinted::ACTIF,SP\Options\BarCode::ACTIF,SP\Options\StitchedEnveloppePrinted::ACTIF)
        ->setAffranchissement(SP\Options\Affranchissement::LETTRE_RECOMMANDEE)
        ->setReferenceExterne("2016-123456cc")
        ->setExpediteur("Ronan", "PAUL", "", "9 rue Ambroise Thomas", "", "75009", "Paris","France")
        ->setDestinataire("", "", "Service Postal", "4 rue Edouard Branly", "", "78190", "Trappes", "France")
        ->setDocument(getcwd().$document); 
    
    // Envoi du courrier
    echo "<h2>Envoi de la lettre (direct)</h2>";
    $result = $maLettre->envoyerDirectement();
    echo "Le courrier a été envoyé sur la plate-forme Service postal et porte le numéro : <b>", $maLettre->jobID, "</b><br />";
    
    echo "<h2>Pour aller plus loin</h2>"; 
    echo "<a href='query.php?servicePostalID=",$maLettre->jobID,"' class='btn btn-primary'><span class='glyphicon glyphicon-eye-open'></span> Suivre cet envoi</a> ";
    echo "<a class='btn btn-danger' href='lettre_cancel.php?servicePostalID=",$maLettre->jobID,"'><span class='glyphicon glyphicon-trash'></span> Annuler cet envoi</a>";
    
    $maSessionSP->logout();
}
catch (\Exception $e)
{
    echo $e->getMessage();
}

echo "<h2>Code source</h2>";
echo "<div class='well small'>";show_source(__FILE__);echo "</div>";  
?>