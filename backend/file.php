<?php 

$cmd = "docker exec mts-mc-server bash ls";

// Esegui e cattura l'output
$output = shell_exec($cmd);