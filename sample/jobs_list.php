<?php
require_once 'SP_config.php';
require_once 'servicepostal/ServicePostal.php';

echo "<h1>Liste des envois</H1>";

try //Initialisation du service
{
    $maSessionSP = new SP\Session( SP_LOGIN, SP_PASSWORD, TRUE);
}
catch (\Exception $e)
{
    die("Erreur lors de l'initialisation de Service Postal : ".$e->getMessage() );
}

try // Récupération des envois
{
    $result = $maSessionSP->queryJobs("2016-02-02T11:50:15", "2016-02-02T11:54:15");
    if(is_array($result) && count($result)>0)
    {
        foreach ($result as $letter)
            echo $letter->spStatus->spStatus,"<br />";
    }
}
catch (\Exception $e)
{
    die("Erreur lors de la récupération des envois : ".$e->getMessage() );
}

$maSessionSP->logout();

echo "<h2>Code source</h2>";
echo "<div class='well small'>";show_source(__FILE__);echo "</div>";
?>