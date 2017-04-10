<?php
namespace SP;
use SP\Options;
use SP\Job;

/**
 * Fichier contenant les classes d'accès à l'API Service Postal :
 * - Session
 * - Job
 * @author ServicePostal ( ronanpaul )
 * @version 1.0.0
 * @see https://www.servicepostal.com
 */


/**
 * classe permettant l'envoi et le suivi de courrier via l'API Service Postal
 */
class Session
{
    /**
     * Production URL for WSDL
     * @var unknown
     */    
    const URL_PRODUCTION = "C:\Users\Jovan\Desktop\WSDL";
    
    /**
     * Test URL for WSDL
     * @var unknown
     */
    //const URL_TEST = "C:\Users\Jovan\Desktop\WSDL";
    const URL_TEST = __DIR__.DIRECTORY_SEPARATOR.'WSDL'.DIRECTORY_SEPARATOR; 
    
    /**
     * API login
     * @var string
     */
    private $api_login = NULL;
    
    /**
     * API password
     * @var string
     */
    private $api_password = NULL;
    
    /**
     * URL for WSDL
     * @var string
     */
    private $url = null;
    
    /**
     * Session for SOAP SESSION client
     * @var object
     */
    public $clientSession;
    /**
     * Header for SOAP SESSION client
     * @var object
     */
    protected $headerSession ;
    
    /**
     * Session for SOAP SUBMISSION client
     * @var object
     */
    public $clientSubmission ;
    /**
     * Header for SOAP SUBMISSION client
     * @var object
     */
    protected $headerSubmission ;
    
    /**
     * Session for SOAP QUERY client
     * @var object
     */
    public $clientQuery;
    
    /**
     * Header for SOAP QUERY client
     * @var object
     */
    protected $headerQuery;
    
    /**
     * Constructeur 
     * @param string $api_login login de l'API
     * @param string $api_password mot de passe de l'API
     * @param boolean $flag_test paramètre indiquant si on est en test
     */
    //function __construct($api_login = NULL, $api_password = NULL, $flag_test = FALSE)
    function __construct($api_login = NULL, $api_password = NULL, $flag_test = FALSE, $url_test = NULL, $url_production = NULL) //surikat
    {
        if ($api_login)
            $this->api_login = $api_login;
        if ($api_password)
            $this->api_password = $api_password;
        
        if ($flag_test)
        {
            //$this->url = self::URL_TEST;
            $this->url = $url_test ? : self::URL_TEST;
            ini_set("soap.wsdl_cache_enabled", WSDL_CACHE_NONE);
        }
        else
            //$this->url = self::URL_PRODUCTION;
             $this->url = $url_production ? : self::URL_PRODUCTION;
        
        $this->init();
    }
    
    /**
     * Connexion à Service postal, chargemeent des différents fichiers WDSL, authentification
     * Appelé par le constructeur
     * @throws \\Exception
     * @throws \Exception
     */
    protected function init()
    {
        if (!$this->api_login)
            throw new \Exception("Vous devez définir le login de l'API !");
        if (!$this->api_password)
            throw new \Exception("Vous devez définir le mot de passe de l'API !");
            
        // Create the SoapClient Session instance
        $this->clientSession = new \SoapClient( $this->url.DIRECTORY_SEPARATOR."Session.wsdl", array("trace" => 1, "Exception" => 0));
        // Create the header
        $this->headerSession = new \SoapHeader( $this->url, .DIRECTORY_SEPARATOR."Session.wsdl","Session");
        
        // Create the SoapClient Submission instance
        $this->clientSubmission = new \SoapClient( $this->url.DIRECTORY_SEPARATOR."Submission.wsdl", array("trace" => 1, "Exception" => 0));
        // Create the header
        $this->headerSubmission = new \SoapHeader( $this->url.DIRECTORY_SEPARATOR."Submission.wsdl", "Submission");
        
        // Create the SoapClient Query instance
        $this->clientQuery = new \SoapClient( $this->url.DIRECTORY_SEPARATOR."Query.wsdl", array("trace" => 1, "Exception" => 0));
        // Create the header
        $this->headerQuery = new \SoapHeader( $this->url.DIRECTORY_SEPARATOR."Query.wsdl", "Query");
        
        $loginParam = array(); //Partner's User name for bindings
        $loginParam["userName"] = $this->api_login;
        $resultBinding = $this->clientSession->sp_get_bindings($loginParam);
        
        if($resultBinding->sp_get_bindingsResult->ErrorMessage == NULL)
        {
            $urlParam = array();
            $urlParam["url"] = $resultBinding->sp_get_bindingsResult->sessionServiceLocation;
            $urlParamSubmission = array();
            $urlParamSubmission["url"] = $resultBinding->sp_get_bindingsResult->submissionServiceLocation;
        
            $urlParamQuery = array();
            $urlParamQuery["spURL"] = $resultBinding->sp_get_bindingsResult->queryServiceLocation;
        
            $this->clientSession->sp_set_url($urlParam);
            $this->clientSubmission->sp_set_url($urlParamSubmission);
            $this->clientQuery->sp_set_url($urlParamQuery);
        
            $this->clientSession->sp_set_url($urlParam);
            
            $credentialsParam = array();
            $credentialsParam["userName"] = $this->api_login;
            $credentialsParam["password"] = $this->api_password;
            $resultLogin = $this->clientSession->sp_login($credentialsParam);
            if ($resultLogin->sp_loginResult->Error != NULL)
                throw new \Exception("Erreur lors du login Service Postal : ".$resultLogin->sp_loginResult->Error );
            //var_dump($resultLogin);
        
            $sessionHeaderParam = array();
            $spHeader = array();
            $spHeader["spSessionID"] = $resultLogin->sp_loginResult->SessionID;
            $sessionHeaderParam["spSessionHeader"] = $spHeader;
        
            $this->clientSubmission->sp_set_session_header($sessionHeaderParam);
            $this->clientQuery->sp_set_session_header($sessionHeaderParam);
        }
        else 
        {
                throw new \Exception( $resultBinding->sp_get_bindingsResult->ErrorMessage );
        }
    }
    
    /**
     * Création d'un nouveau job de type lettre 
     * @return \SP\Job\Lettre
     */
    function nouveauLettreJob()
    {
        $job = new Job\Lettre( $this );
        return $job;
    }

    /**
     * Création d'un nouveau job de type mailing 
     * @return \SP\Job\Mailing
     */    
    function nouveauMailingJob()
    {
        $job = new Job\Mailing( $this );
        return $job;
    }

    /**
     * Chargement d'un job de type lettre existant
     * @param string $jobID paramètre obligatoire : identifiant du job Service Postal
     * @return \SP\Job\Lettre
     */
    function chargeLettreJob( $jobID = NULL )
    {
        if (!$jobID)
            throw new \Exception("Chargement de job impossible : vous devez transmettre un ID de job");
        
        $job = new Job\Lettre( $this, $jobID );
        return $job;
    }
    
    /**
     * Chargement d'un job de type lettre existant
     * @param string $jobID paramètre obligatoire : identifiant du job Service Postal
     * @return \SP\Job\Lettre
     */
    function chargeLettreFromMailingJob( $jobID = NULL, $index = NULL )
    {
        if (!$jobID)
            throw new \Exception("Chargement de job impossible : vous devez transmettre un ID de job");

        if (!$index)
            throw new \Exception("Chargement de job impossible : vous devez transmettre un numéro d'index");
            
            
            $job = new Job\Lettre( $this, $jobID, $index );
            return $job;
    }
    
    /**
     * Chargement d'un nouveau job de type mailing
     * @param string $jobID paramètre optionnel : identifiant du job Service Postal (si sourni, le job sera préchargé)
     * @return \SP\Job\Mailing
     */
    function chargeMailingJob( $jobID = NULL )
    {
        if (!$jobID)
            throw new \Exception("Chargement de job impossible : vous devez transmettre un ID de job");
        
        $job = new Job\Mailing( $this, $jobID );
        return $job;
    }
    
    
    /**
     * Validation d'un job déjà soumis à la plate-forme
     * @param string $servicePostalID
     * @throws \Exception
     * @return unknown
     */
    public function validerJob($servicePostalID)
    {
        try
        {
            $servicePostalParam = array();
            $servicePostalParam["spServicePostalID"] = $servicePostalID;
            $result = $this->clientSubmission->sp_validate_letter($servicePostalParam);
    
            if ($result->sp_validate_letterResult->spErrorMessage)
                throw new \Exception("Erreur lors de la validation de l'envoi :".$result->sp_validate_letterResult->spErrorMessage);
    
                return $result->sp_validate_letterResult;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Validation d'un job déjà soumis à la plate-forme
     * @param string $servicePostalID
     * @throws \Exception
     * @return unknown
     */
    public function validerMailing($servicePostalID)
    {
        try
        {
            $servicePostalParam = array();
            $servicePostalParam["spServicePostalID"] = $servicePostalID;
            $result = $this->clientSubmission->sp_validate_mailing($servicePostalParam);
    
            if ($result->sp_validate_mailingResult->spErrorMessage)
                throw new \Exception("Erreur lors de la validation de l'envoi :".$result->sp_validate_mailingResult->spErrorMessage);
    
                return $result->sp_validate_mailingResult;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }
    
    
    /**
     * Annulation d'un job déjà soumis à la plate-forme
     * @param string $servicePostalID
     * @param integer $index
     * @throws \Exception
     * @return unknown
     */
    public function annulerJob($servicePostalID, $index = NULL)
    {
        try
        {
            $servicePostalParam = array();
            $servicePostalParam["spServicePostalID"] = $servicePostalID;
            if ($index !== NULL)
                $servicePostalParam["spIndex"] = $index;
            $result = $this->clientSubmission->sp_cancel_job($servicePostalParam);
            //if ($result->spSuccessFailure != Options\Succes::SUCCES)
            if ($result->sp_cancel_jobResult != Options\Succes::SUCCES)
                throw new \Exception( $result->errorMessage );
    
            return $result;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }
    
    /**
     * Interrogation du serveur pour connaître le statut d'un document
     * @param unknown $spServicePostalID identifiant du job Service Postal
     * @param integer $spIndex index du courrier au sein du job (optionnel, utilise uniquement pour les mailings)
     * @throws \Exception
     */
    public function queryJobStatut($servicePostalID, $index = 1)
    {
        try
        {
            $servicePostalParam = array();
            $servicePostalParam["spServicePostalID"] = $servicePostalID;
            
            if ($index !== NULL)
                $servicePostalParam["spIndex"] = $index;
            
            $result = $this->clientQuery->sp_query_status($servicePostalParam);
    
            return $result->sp_query_statusResult;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }
    
    
    /**
     * Interrogation du serveur pour récupérer un document
     * @param unknown $spServicePostalID identifiant du job Service Postal
     * @param integer $spIndex index du courrier au sein du job (optionnel, utilisé uniquement pour les mailings)
     * @throws \Exception
     */
    public function queryJobDocument($servicePostalID, $index = 1, $documentType = Options\DocumentType::DOCUMENT)
    {
        try
        {
            $servicePostalParam = array();
            $servicePostalParam["spServicePostalID"] = $servicePostalID;
            if ($index !== NULL)
                $servicePostalParam["spIndex"] = $index;
            $servicePostalParam["spDocumentType"] = $documentType;
            $servicePostalParam["spReturnMode"] = Options\FileStorageMode::SP_DISTANT;
            //print_r($servicePostalParam); 
            $result = $this->clientQuery->sp_query_document($servicePostalParam);
            //print_r($result);
            if ($result->sp_query_documentResult->spErrorMessage)
                throw new \Exception("Erreur lors de la récupération d'un document : ".$result->sp_query_documentResult->spErrorMessage);
                return $result->sp_query_documentResult;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }
    
    /**
     * Interrogation du serveur pour connaître le prix du srviec d'un envoi
     * @param string $spServicePostalID identifiant du job Service Postal
     * @param integer $spIndex index du courrier au sein du job (optionnel, utilie uniquement pour les mailings)
     * @throws \Exception
     */
    public function queryJobCoutTotal($servicePostalID, $index = 1)
    {
        try
        {
            $servicePostalParam = array();
            $servicePostalParam["spServicePostalID"] = $servicePostalID;
            if ($index !== NULL)
                $servicePostalParam["spIndex"] = $index;
            $result = $this->clientQuery->sp_query_letter_cost($servicePostalParam);
    
            return $result->sp_query_letter_costResult;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }
    
    /**
     * Interrogation du serveur pour connaître le prix total et détaillé d'un envoi
     * @param string $spServicePostalID identifiant du job Service Postal
     * @param integer $spIndex index du courrier au sein du job (optionnel, utilie uniquement pour les mailings)
     * @throws \Exception
     */
    public function queryJobCoutDetail($servicePostalID, $index = 1)
    {
        try
        {
            $servicePostalParam = array();
            $servicePostalParam["spServicePostalID"] = $servicePostalID;
            if ($index !== NULL)
                $servicePostalParam["spIndex"] = $index;
            $result = $this->clientQuery->sp_query_letter_rate($servicePostalParam);
    
            return $result->sp_query_letter_rateResult;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }
     
    /**
     * Interrogation du serveur pour connaître le prix total et détaillé d'un envoi
     * @param string $spServicePostalID identifiant du job Service Postal
     * @param integer $spIndex index du courrier au sein du job (optionnel, utilie uniquement pour les mailings)
     * @throws \Exception
     */
    public function queryJobs($dateFrom = '2016-01-01T00:00:00' , $dateTo = '2017-01-01T00:00:00')
    {
        try
        {
            $servicePostalParam = array();
            $servicePostalParam['spDateFrom'] = $dateFrom;
            $servicePostalParam['spDateTo'] = $dateTo;
            $result = $this->clientQuery->sp_query_jobs_statistics($servicePostalParam);
            return $result->sp_query_jobs_statisticsResult->SP_QueryStatisticsResult;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Déconnexion de Service Postal
     */
    public function logout()
    {
        $this->clientSession->sp_logout();
    }
}
