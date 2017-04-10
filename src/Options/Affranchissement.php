<?php
namespace SP\Options;

/**
 * Classe contenant les constantes des affranchissements
 * @author servicepostal
 */
class Affranchissement
{
            //SP_REGISTERED_LETTER = "SP_REGISTERED_LETTER",
            //SP_REGISTERED_WITH_PROOF = "SP_REGISTERED_WITH_PROOF",
            //SP_PRIORITY_LETTER = "SP_PRIORITY_LETTER",
            //SP_ECONOMIC_LETTER = "SP_ECONOMIC_LETTER",
            //SP_GREEN_LETTER = "SP_GREEN_LETTER",
    /**
     * Affranchissement lettre recommandée (sans accusé de réception)
     * @var string
     */
    const
            LETTRE_RECOMMANDEE = "SP_REGISTERED_LETTER";
    /**
     * Affranchissement lettre recommandée avec accusé de réception
     * @var string
     */
    const
            LETTRE_RECOMMANDEE_AVEC_AR = "SP_REGISTERED_WITH_PROOF";
    /**
     * Affranchissement lettre prioritaire
     * @var string
     */
    const
            LETTRE_PRIORITAIRE = "SP_PRIORITY_LETTER";
    /**
     * Affranchissement lettre écopli
     * @var string
     */
    const
            LETTRE_ECOPLI = "SP_ECONOMIC_LETTER";
    /**
     * Affranchissement lettre verte
     * @var string
     */
    const
            LETTRE_VERTE = "SP_GREEN_LETTER";
}
