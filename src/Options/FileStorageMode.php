<?php
namespace SP\Options;
/**
 * Classe contenant les constantes des types de transfert des pièces jointes
 * - dans la trame
 * - sous forme de fichier externe (URL)
 * @author servicepostal
 */
class FileStorageMode
{
    const
            //SP_INLINED = "SP_INLINED",
            //SP_UPLOADED = "SP_UPLOADED";
            SP_INCLUS = "SP_INLINED",
            SP_DISTANT = "SP_UPLOADED";
}
