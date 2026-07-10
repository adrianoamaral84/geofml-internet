<?php

namespace PagTesouro\Helper;

class InfraHelper
{

    public static function faltaBibliotecaCurl(): bool
    {
        return !function_exists('curl_init');
    }

}