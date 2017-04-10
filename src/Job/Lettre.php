<?php
namespace SP\Job;

/**
 * classe SP\Job\Lettre
 * Gestion d'un courrier unitaire
 * @author Service Postal (ronanpaul)
 */
class Lettre
{
    /**
     * Expéditeur
     * @var array
     */
    private $expediteur = NULL;
    /**
     * Destinataire
     * @var array
     */
    private $destinataire = NULL;
    /**
     * Paramètres d'impression
     * @var array
     */
    private $printingParameters = NULL;
    /**
     * Document à imprimer
     * @var array
     */
    private $documentToPrint = NULL;
    /**
     * Paramètres du courrier
     * @var array
     */
    private $letterParameters = null;
    /**
     * Objet \SP\Session
     * @var \SP\Session
     */
    private $session = null;

    /**
     * Identifiant du job sur la plate-forme Service Postal
     * @var string
     */
    public $jobID;
    
    /**
     * Index d'une lettre au sein d'un mailing
     * @var string
     */
    public $index;

    /**
     * Constructeur
     * @param \SP\Session la $session ServicePostal à utiliser pour la communication
     * @param string $jobID l'identifiant du job Service Postal (optionnel)
     * @param numeric $index Index d'une lettre au sein d'un mailing (optionnel) 
     */
    public function __construct( \SP\Session $session, $jobID = NULL, $index = NULL)
    {
        $this->session = $session;
        
        if ($jobID)
        {
            $this->jobID = $jobID;
            if ($index)
                $this->index = $index;
        }
    }
    
    /**
     * Configuration des paramètres d'impression
     * @param enum(SP_Color::NOIR_ET_BLANC|SP_Color::COULEUR) $colorParameter Couleur
     * @param string $enveloppeParameter Enveloppe
     * @param string $rectoParameter Recto Verso
     * @param string $headerPage Page porte-adresses
     */
    public function setImpression(
        $colorParameter = \SP\Options\Couleur::NOIR_ET_BLANC,
        $enveloppeParameter = \SP\Options\Enveloppe::AUTO,
		$enveloppeModePrintingParameter = \SP\Options\EnveloppeImprimanteMode::PRINTED,
        $rectoParameter = \SP\Options\Recto::RECTO,
        $headerPage = \SP\Options\PorteAdresse::INACTIF, // only works when Window enveloppe is selected
		$senderPrintedParameter=\SP\Options\SenderPrinted::ACTIF,
		$barCodeParameter =\SP\Options\BarCode::ACTIF,
		$stitchedEnveloppePrintedParameter =\SP\Options\StitchedEnveloppePrinted::ACTIF
        )
    {
        //Printing Parameters
        if (!$this->printingParameters)
            $this->printingParameters = array();
            $this->printingParameters["spColorParameter"] = $colorParameter;
            $this->printingParameters["spEnveloppeParameter"] = $enveloppeParameter;
            $this->printingParameters["spRectoParameter"] = $rectoParameter;
			$this->printingParameters["spHeaderPageWindowEnvelope"] = $headerPage;
			$this->printingParameters["spEnvelopePrintingMode"] = $enveloppeModePrintingParameter;
			$this->printingParameters["spSenderPrintedOnEnvelope"] = $senderPrintedParameter;
			$this->printingParameters["spAddressStitchedOnDocumentPrintedEnvelope"] = $stitchedEnveloppePrintedParameter;
			$this->printingParameters["spAddBarCodeToDocumentPrintedEnvelope"] = $barCodeParameter;
         /*   $this->printingParameters["spHeaderPage"] = $headerPage;*/

            return $this;
    }

    /**
     * Configuration de l'affranchissement
     * @param string $letterType Type affranchissement
     * @param string $exernalReference Référence externe du courrier
     */
    public function setAffranchissement(
        $letterType = \SP\Options\Affranchissement::LETTRE_RECOMMANDEE
        )
    {
        //Printing Parameters
        if (!$this->printingParameters)
            $this->setParametresImpression();

            $this->printingParameters["spLetterType"] = $letterType;

            return $this;
    }

    /**
     * Configuration de la référence Externe
     * @param string $exernalReference Référence externe du courrier
     */
    public function setReferenceExterne( $externalReference = "" )
    {
        //Printing Parameters
        if (!$this->printingParameters)
            $this->setParametresImpression();

        $this->printingParameters["spExternalReference"] = $externalReference;

        return $this;
    }


    /**
     * Fonction de définition de l'expéditeur
     * Un seul expéditeur autorisé
     * Il faut fournir au moins une des valeurs nom/prénom ou société
     * @param string $prenom Prénom (optionnel)
     * @param string $nom Nom (optionnel)
     * @param string $societe Société (optionnel)
     * @param string $adresse1 1ère ligne d'adresse (obligatoire)
     * @param string $adresse2 2ème ligne d'adresse (optionnel)
     * @param string $codePostal code postal (obligatoire)
     * @param string $ville ville (obligatoire)
     * @param string $pays Pays (optionnel, France par défaut)
     * @throws \Exception
     */
    public function setExpediteur(
        $prenom = '',
        $nom = '',
        $societe = '',
        $adresse1 = '',
        $adresse2 = '',
        $codePostal = '',
        $ville = '',
        $pays = 'France'
        )
    {
        //if ($this->sender)
        //throw new \Exception("Un expéditeur a déjà été défini, vous ne pouvez pas en ajouter un second.");

        $prenom = trim( $prenom );
        $nom = trim( $nom );
        $societe = trim( $societe );

        if( (empty($prenom) || empty($nom)) && empty($societe) )
            throw new \Exception("L'expéditeur doit avoir au moins un prénom/nom ou un nom de société.");

        $adresse1 = trim( $adresse1 );
        if( empty($adresse1) )
            throw new \Exception("La 1ere ligne d'adresse de l'expéditeur ne peut pas être vide.");

        $codePostal = trim( $codePostal );
        if (empty($codePostal))
            throw new \Exception("L'adresse de l'expéditeur doit avoir un code postal.");

        $ville = trim( $ville );
        if (empty($ville))
            throw new \Exception("L'adresse de l'expéditeur doit avoir une ville.");

        $pays = trim( $pays );
        if (empty($pays))
            throw new \Exception("L'adresse de l'expéditeur doit avoir un pays.");

        $spSender = array();
        $spSender["spFirstName"] = $prenom;
        $spSender["spLastName"] = $nom;
        $spSender["spCompanyName"] = $societe;
        $spSender["spFirstLine"] = $adresse1;
        $spSender["spSecondLine"] = $adresse2;
        $spSender["spPostalCode"]  = $codePostal;
        $spSender["spCityName"] = $ville;
        $spSender["spCountry"] = $pays;

        $this->expediteur = $spSender;
        $this->printingParameters["spSender"] = $this->expediteur;

        return $this;
    }

    /**
     * Fonction d'ajout d'un destinataire
     * Un seul destinataire autorisé
     * Il faut fournir au moins une des valeurs nom/prénom ou société
     * @param string $prenom Prénom (optionnel)
     * @param string $nom Nom (optionnel)
     * @param string $societe Société (optionnel)
     * @param string $adresse1 1ère ligne d'adresse (obligatoire)
     * @param string $adresse2 2ème ligne d'adresse (optionnel)
     * @param string $codePostal code postal (obligatoire)
     * @param string $ville ville (obligatoire)
     * @param string $pays Pays (optionnel, France par défaut)
     * @throws \Exception
     */
    public function setDestinataire(
        $prenom = '',
        $nom = '',
        $societe = '',
        $adresse1 = '',
        $adresse2 = '',
        $codePostal = '',
        $ville = '',
        $pays = 'France'
        )
    {
        $prenom = trim( $prenom );
        $nom = trim( $nom );
        $societe = trim( $societe );

        if( (empty($prenom) || empty($nom)) && empty($societe) )
            throw new \Exception("Le destinataire doit avoir au moins un prénom/nom ou un nom de société.");

        $adresse1 = trim( $adresse1 );
        if( empty($adresse1) )
            throw new \Exception("La 1ere ligne d'adresse du destinataire ne peut pas être vide.");

        $adresse2 = trim( $adresse2 );
        if (empty($adresse2))
            $adresse2 = null;

        $codePostal = trim( $codePostal );
        if (empty($codePostal))
            throw new \Exception("L'adresse du destinataire doit avoir un code postal.");

        $ville = trim( $ville );
        if (empty($ville))
            throw new \Exception("L'adresse du destinataire doit avoir une ville.");

        $pays = trim( $pays );
        if (empty($pays))
            throw new \Exception("L'adresse du destinataire doit avoir un pays.");

        $spRecipient= array();
        $spRecipient["spFirstName"] = $prenom;
        $spRecipient["spLastName"] = $nom;
        $spRecipient["spCompanyName"] = $societe;
        $spRecipient["spFirstLine"] = $adresse1;
        $spRecipient["spSecondLine"] = $adresse2;
        $spRecipient["spPostalCode"]  = $codePostal;
        $spRecipient["spCityName"] = $ville;
        $spRecipient["spCountry"] = $pays;

        //TODO : handle multiple documents
        /*
        if (!$this->recipients || !is_array($this->recipients) )
            $this->recipients = array();
            $this->recipients[] = $spRecipient;
            */
        $this->destinataire = $spRecipient;
        $this->printingParameters["spRecipient"] = $this->destinataire;

        return $this;
    }

    /**
     * Ajout d'un document (local ou distant)
     * @param string $file URL du fichier à ajouter
     * @throws \Exception
     */
    public function setDocument($file)
    {
        try
        {
            $contents = file_get_contents($file);
            if ($contents == FALSE)
                throw new \Exception("Envoi impossible : erreur lors du chargement du fichier {$file} ");

            $spDocumentToPrint = array();
            $spDocumentToPrint["spName"] = basename($file);
            $spDocumentToPrint["spContent"] = $contents;

            $this->documentToPrint = $spDocumentToPrint;

            return $this;

        }
        catch( \Exception $e)
        {
            throw $e;
        }

    }

    /**
     * Soumission du job avec les paramètres actuels
     * @param integer NbOfPages nombre de pages
     * @throws \Exception
     * @return array
     */
    public function estimerPrix($NbOfPages = 1)
    {
        //TODO : check current parameters + is there a file to print ?

        try
        {
            $estimatePriceParam = array();
            $estimatePriceParam["spNbOfPages"] = $NbOfPages;
            $estimatePriceParam["spLetterOptions"] = $this->printingParameters;
            $result = $this->session->clientSubmission->sp_estimate_price($estimatePriceParam);
			echo print_r($this->printingParameters);
			
            return $result->sp_estimate_priceResult;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Soumission du job avec les paramètres actuels
     * @throws \Exception
     * @return unknown
     */
    public function preparer()
    {
        if(!$this->documentToPrint)
            throw new \Exception("Vous devez ajouter un document à envoyer ! ");

        try
        {
            //we need to set file in printing parameters
            $this->letterParameters = array();
            $this->letterParameters["spPrintingParameters"] = $this->printingParameters;
            $this->letterParameters["spDocumentToPrint"] = $this->documentToPrint;
            $this->letterParameters["spReturnMode"] = \SP\Options\FileStorageMode::SP_DISTANT;
            
            $result = $this->session->clientSubmission->sp_preview_letter($this->letterParameters);
            
            //if ($result->sp_preview_letterResult->spErrorMessage)
            if ($result->sp_preview_letterResult->spSuccessFailure != \SP\Options\Succes::SUCCES)
                throw new \Exception("Erreur lors de la prévisualisation : ".$result->sp_preview_letterResult->spErrorMessage);

                $this->jobID = $result->sp_preview_letterResult->spServicePostalID;

                return $result->sp_preview_letterResult;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Validation du job (déjà soumis à la plate-forme)
     * @throws \Exception
     * @return unknown
     */
    public function valider()
    {
        return $this->session->validerJob($this->jobID);
    }

    /**
     * Envoi du job directement
     * @throws \Exception
     * @return object
     */
    public function envoyerDirectement()
    {
        try
        {
            //we need to set file in printing parameters
            $this->letterParameters = array();
            $this->letterParameters["spPrintingParameters"] = $this->printingParameters;
            $this->letterParameters["spDocumentToPrint"] = $this->documentToPrint;
    
            $result = $this->session->clientSubmission->sp_submit_letter($this->letterParameters);
            if ($result->sp_submit_letterResult->spSuccessFailure != \SP\Options\Succes::SUCCES)
                throw new \Exception("Erreur lors de la prévisualisation : ".$result->sp_submit_letterResult->spErrorMessage);
            
            $this->jobID = $result->sp_submit_letterResult->spServicePostalID;
                
            return $result->sp_submit_letterResult;    
            
            
            /*
            $result = $this->session->clientSubmission->sp_preview_letter($this->letterParameters);
            if ($result->sp_preview_mailingResult->spErrorMessage)
                throw new \Exception("Erreur lors de la prévisualisation : ".$result->sp_preview_mailingResult->spErrorMessage);

                $this->jobID = $result->sp_preview_mailingResult->spServicePostalID;

                return $this->session->validaterJob($this->jobID);
            */
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Annulation du job (déjà soumis à la plate-forme)
     * @throws \Exception
     * @return object
     */
    public function annuler()
    {
        return $this->session->annulerJob($this->jobID, $this->index);
    }

    /**
     * Interrogation du serveur pour connaître le statut du job
     * @throws \Exception
     */
    public function queryStatut()
    {
        return $this->session->queryJobStatut($this->jobID, $this->index);
    }

    /**
     * Interrogation du serveur pour récupérer un document du job
     * @param string $documentType type de document parmi les constantes de la classe Options\DocumentType
     * valeur par défaut : Options\DocumentType::DOCUMENT
     * @throws \Exception
     */
    public function queryDocument($documentType = \SP\Options\DocumentType::DOCUMENT)
    {
        return $this->session->queryJobDocument($this->jobID, $this->index, $documentType);
    }

    /**
     * Interrogation du serveur pour connaître le prix du service du job
     * @throws \Exception
     */
    public function queryCoutTotal()
    {
        return $this->session->queryJobCoutTotal($this->jobID);
    }

    /**
     * Interrogation du serveur pour connaître le prix total et détaillé du job
     * @throws \Exception
     */
    public function queryCoutDetail()
    {
        return $this->session->queryJobCoutDetail($this->jobID);
    }


}
