<?php
namespace SP\Options;


/**
 * Classe contenant les constantes des type d'impression
 * - recto
 * - recto/verso
 * @author servicepostal
 */
class Recto
{
            //SP_RECTO_VERSO = "SP_RECTO_VERSO",
            //SP_RECTO = "SP_RECTO",
    /**
     * Impression recto / verso (ne concerne pas la page de porte adresse qui est toujours en recto)
     * @var string
     */
    const
            RECTO_VERSO = "SP_RECTO_VERSO";
    /**
     * Impression recto (ne concerne pas la page de porte adresse)
     * @var string
     */
    const
            RECTO = "SP_RECTO";
}