<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;

class BlockedLegacyGetController extends Controller
{
    public function pagamento()
    {
        abort(405, 'Esta operação de pagamento exige POST.');
    }
}
