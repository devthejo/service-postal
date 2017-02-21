<?php
namespace SP\Job;

/**
 * classe SP\Job\Mailing
 * Gestion d'un courrier sous forme de Mailing
 * @author Service Postal (ronanpaul)
 */
class Mailing
{
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
     * Constructeur
     * @param \SP\Session la $session ServicePostal à utiliser pour la communication
     * @param string $jobID l'identifiant du job Service Postal (optionnel)
     */
    public function __construct( \SP\Session $session, $jobID = NULL)
    {
        $this->session = $session;
        
        if ($jobID)
            $this->jobID = $jobID;
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
        $rectoParameter = \SP\Options\Recto::RECTO,
        $headerPage = \SP\Options\PorteAdresse::ACTIF
        )
    {
        //Printing Parameters
        if (!$this->printingParameters)
            $this->printingParameters = array();
            $this->printingParameters["spColorParameter"] = $colorParameter;
            $this->printingParameters["spEnveloppeParameter"] = $enveloppeParameter;
            $this->printingParameters["spRectoParameter"] = $rectoParameter;
            $this->printingParameters["spHeaderPage"] = $headerPage;

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
    public function setReferenceExterne(
        $externalReference = ""
        )
    {
        //Printing Parameters
        if (!$this->printingParameters)
            $this->setParametresImpression();

            $this->printingParameters["spExternalReference"] = $externalReference;

            return $this;
    }

    
    /**
     * Ajout du document type (local ou distant)
     * @param string $file URL du fichier à ajouter
     * @throws \Exception
     */
    public function setDocumentModele($file)
    {
        try
        {
            $contents = file_get_contents($file);

            $spDocumentToPrint = array();
            $spDocumentToPrint["spName"] = "modele.docx"; //basename($file);
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
     * Ajout du document de données (local ou distant)
     * @param string $file URL du fichier à ajouter
     * @throws \Exception
     */
    public function setDocumentDonnees($file)
    {
        try
        {
            $contents = file_get_contents($file);
            if ($contents == FALSE)
                throw new \Exception("Envoi impossible : erreur lors du chargement du fichier {$file} ");
    
            $spDocumentToPrint = array();
            $spDocumentToPrint["spName"] = "data.csv"; //basename($file);
            $spDocumentToPrint["spContent"] = $contents;
    
            $this->documentDonnees = $spDocumentToPrint;
    
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
        //TODO : check current parameters + is there a file to print ?
        try
        {
            //we need to set file in printing parameters
            $this->letterParameters = array();
            $this->letterParameters["spPrintingParameters"] = $this->printingParameters;
            $this->letterParameters["spDocumentToPrint"] = $this->documentToPrint;
            $this->letterParameters["spFieldValuesFile"] = $this->documentDonnees;
            $this->letterParameters["spReturnMode"] = \SP\Options\FileStorageMode::SP_DISTANT;
            $this->letterParameters["spIndex"]=1;
            $result = $this->session->clientSubmission->sp_preview_mailing($this->letterParameters);
            
            if ($result->sp_preview_mailingResult->spSuccessFailure != \SP\Options\Succes::SUCCES)
                throw new \Exception("Erreur lors de la prévisualisation : ".$result->sp_preview_mailingResult->spErrorMessage);

            $this->jobID = $result->sp_preview_mailingResult->spServicePostalID;

            return $result->sp_preview_mailingResult;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Validation du mailing (déjà soumis à la plate-forme)
     * @throws \Exception
     * @return unknown
     */
    public function valider()
    {
        return $this->session->validerMailing($this->jobID);
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
            $this->letterParameters["spFieldValuesFile"] = $this->documentDonnees;
            $this->letterParameters["spReturnMode"] = \SP\Options\FileStorageMode::SP_DISTANT;
            $this->letterParameters["spIndex"]=1;
            $result = $this->session->clientSubmission->sp_submit_mailing($this->letterParameters);
            if ($result->sp_submit_mailingResult->spErrorMessage)
                throw new \Exception("Erreur lors de la soumission du mailing : ".$result->sp_submit_mailingResult->spErrorMessage);

            $this->jobID = $result->sp_submit_mailingResult->spServicePostalID;

            return $result->sp_submit_mailingResult;
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
        return $this->session->annulerJob($this->jobID);
    }

    /**
     * Interrogation du serveur pour connaître le statut du job
     * @param integer $spIndex index du courrier au sein du mailing
     * @throws \Exception
     */
    public function queryStatut($spIndex = 1)
    {
        return $this->session->queryJobStatut($this->jobID, $spIndex);
    }

    /**
     * Interrogation du serveur pour récupérer un document du mailing
     * @param integer $spIndex index du courrier au sein du mailing
     * @param string $documentType type de document parmi les constantes de la classe Options\DocumentType
     * valeur par défaut : Options\DocumentType::DOCUMENT
     * @throws \Exception
     */
    public function queryDocument($spIndex = 1, $documentType = \SP\Options\DocumentType::DOCUMENT)
    {
        return $this->session->queryJobDocument($this->jobID, 1, $documentType);
    }

    /**
     * Interrogation du serveur pour connaître le prix du service du job
     * @param integer $spIndex index du courrier au sein du mailing
     * @throws \Exception
     */
    public function queryCoutTotal($spIndex = 1)
    {
        return $this->session->queryJobCoutTotal($this->jobID);
    }

    /**
     * Interrogation du serveur pour connaître le prix total et détaillé du job
     * @param integer $spIndex index du courrier au sein du mailing
     * @throws \Exception
     */
    public function queryCoutDetail($spIndex = 1)
    {
        return $this->session->queryJobCoutDetail($this->jobID);
    }


} // class Mailing