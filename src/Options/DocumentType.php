<?php
namespace SP\Options;

/**
 * Fichier contenant les classes d'option pour l'accès à l'API Service Postal
 * @author ServicePostal ( ronanpaul )
 * @version 1.0.0
 * @see https://www.servicepostal.com
 */


/**
 * Classe contenant les constantes des types de document stockés par service postal
 * - document
 * - preuve de dépôt
 * - accusé de récepton
 * @author servicepostal
 */
class DocumentType
{
            //SP_DOCUMENT = "SP_DOCUMENT",
            //SP_PROOF_OF_DEPOSIT = "SP_PROOF_OF_DEPOSIT",
            //SP_PROOF_OF_DELIVERY = "SP_PROOF_OF_DELIVERY",
    /**
     * Le document qui constitue le courrier, incluant le porte-adresse s'il est activé
     * @var string
     */
    const   
            DOCUMENT = "SP_DOCUMENT";
    /**
     * La preuve de dépôt du courrier
     * @var string
     */
    const
            PREUVE_DE_DEPOT = "SP_PROOF_OF_DEPOSIT";
    /**
     * L'accusé de réception du courrier (seulement lorsque cette option a été souscrite auprès de Service Postal)
     * par défaut c'est l'expéditeur qui la reçoit par la poste
     * @var string
     */
    const
            PREUVE_DE_RECEPTION = "SP_PROOF_OF_DELIVERY";
}