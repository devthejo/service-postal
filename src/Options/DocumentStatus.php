<?php
namespace SP\Options;


/**
 * Classe contenant les constantes des différents statuts d'un job
 * - brouillon
 * - soumis
 * - imprimé
 * - scanné
 * - remis
 * - accusé de réception reçu
 * @author servicepostal
 */
class DocumentStatus
{
            //SP_SUBMITTED = "SP_SUBMITTED",
            //SP_PRINTED = "SP_PRINTED",
            //SP_SCANNED = "SP_SCANNED",
            //SP_DELIVERED = "SP_DELIVERED",
            //SP_DEPOSIT_SLIP_RECEIVED = "SP_DEPOSIT_SLIP_RECEIVED",
            //SP_UNKNOWN = "SP_UNKNOWN",
            //SP_PREVIEWED = "SP_PREVIEWED",
    /**
     * Statut brouillon : le document a été transmis à la plate-forme mais n'a pas été validé
     * @var unknown
     */
    const
            BROUILLON = "SP_PREVIEWED";
    /**
     * Statut soumis : le document a été transmis à la plate-forme et validé
     * @var unknown
     */
    const
            SOUMIS = "SP_SUBMITTED";
    /**
     * Statut imprimé : le document a été transmis puis imprimé
     * @var unknown
     */
    const
            IMPRIME = "SP_PRINTED";
    /**
     * Statut scanné : le document a été transmis puis imprimé, scanné 
     * @var unknown
     */
    const
            SCANNE = "SP_SCANNED";
    /**
     * Statut remis :  : le document a été transmis puis imprimé, scanné, remis en poste
     * @var unknown
     */
    const
            REMIS = "SP_DELIVERED";
    /**
     * Statut reçu : le document a été transmis, imprimé, scanné, remis en poste, puis Service Postal a reçu l'accusé de réception
     * @var unknown
     */
    const
            AR_RECU = "SP_DEPOSIT_SLIP_RECEIVED";
    /**
     * Statut de document inconnu
     * @var string
     */
    const
            INCONNU = "SP_UNKNOWN";
}