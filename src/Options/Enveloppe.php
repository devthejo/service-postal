<?php
namespace SP\Options;

/**
 * Classe contenant les constantes des enveloppes (et pliages) proposées par Service Postal
 * - AUTO : choix de l'enveloppe la moins couteuse en fonction du nombre de pages 
 * - C6/DL (pliage en 3)
 * - C5 (pliage en 2)
 * - C4 (pas de pliage)
 * @author servicepostal
 */
class Enveloppe
{
            //SP_DL_ENVELOPPE_THIRD_A4 = "SP_DL_ENVELOPPE_THIRD_A4",
            //SP_C5_ENVELOPPE_HALF_A4 = "SP_C5_ENVELOPPE_HALF_A4",
            //SP_C4_ENVELOPPE_A4 = "SP_C4_ENVELOPPE_A4",
    /**
     * Enveloppe format C6 (ou DL) : A4 plié en 3, maximum 3 feuilles
     * @var string
     */
    const
            AUTO = "SP_ENVELOPPE_AUTO";
    /**
     * Enveloppe format C6 (ou DL) : A4 plié en 3, maximum 3 feuilles
     * @var string
     */
    const
            C6 = "SP_DL_ENVELOPPE_THIRD_A4";
    /**
     * Enveloppe format C5 : A4 plié en 2, maximum 3 feuilles
     * @var string
     */
    const
            C5 = "SP_C5_ENVELOPPE_HALF_A4";
    /**
     * Enveloppe format C4 : A4 non plié, maximum 3 feuilles
     * @var string
     */
    const
            C4 = "SP_C4_ENVELOPPE_A4";
}
