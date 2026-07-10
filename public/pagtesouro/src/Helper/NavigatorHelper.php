<?php

namespace PagTesouro\Helper;

class NavigatorHelper
{

    public static function voltarParaPrincipal(): void
    {
        NavigatorHelper::goAndDie("/pagtesouro");
    }

    public static function vaiParaAviso(): void
    {
        NavigatorHelper::goAndDie("aviso.php");
    }

    private static function goAndDie($location)
    {
        header("Location: " . $location);
        die();
    }
}
