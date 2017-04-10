<?php
namespace SP\Options;
/**
 * Classe contenant les constantes des réponses d'une méthode 
 * - succès
 * - échec
 * @author servicepostal
 */
class Succes
{
            //SP_SUCCESS = "SP_SUCCESS",
            //SP_FAILURE = "SP_FAILURE",
    /**
     * Retour succès
     * @var string
     */
    const
            SUCCES = "SP_SUCCESS";
    
    /**
     * Retour en échec, voir le message d'erreur spErrorMessage
     * @var unknown
     */
    const
            ECHEC = "SP_FAILURE";
}
