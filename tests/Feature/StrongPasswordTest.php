<?php

namespace Tests\Feature;

use App\Rules\StrongPassword;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class StrongPasswordTest extends TestCase
{
    public function test_accepts_strong_password()
    {
        $this->app->instance('request', Request::create('/', 'POST'));
        config(['password_security.blocklist' => []]);

        $rule = new StrongPassword();

        $this->assertTrue($rule->passes('password', 'SenhaForte#2026'));
    }

    public function test_rejects_password_without_required_character_classes()
    {
        $this->app->instance('request', Request::create('/', 'POST'));
        config(['password_security.blocklist' => []]);

        $this->assertFalse((new StrongPassword())->passes('password', 'senhaforte#2026'));
        $this->assertFalse((new StrongPassword())->passes('password', 'SENHAFORTE#2026'));
        $this->assertFalse((new StrongPassword())->passes('password', 'SenhaForte####'));
        $this->assertFalse((new StrongPassword())->passes('password', 'SenhaForte2026'));
    }

    public function test_rejects_password_outside_length_limits()
    {
        $this->app->instance('request', Request::create('/', 'POST'));
        config(['password_security.blocklist' => []]);

        $this->assertFalse((new StrongPassword())->passes('password', 'Aa1#abc'));
        $this->assertFalse((new StrongPassword())->passes('password', 'Aa1#' . str_repeat('x', 61)));
    }

    public function test_rejects_password_containing_cpf()
    {
        $request = Request::create('/', 'POST', [
            'cpf' => '123.456.789-09',
        ]);
        $this->app->instance('request', $request);
        config(['password_security.blocklist' => []]);

        $rule = new StrongPassword();

        $this->assertFalse($rule->passes('password', 'Ab#12345678909X'));
    }

    public function test_rejects_password_containing_significant_email_part()
    {
        $request = Request::create('/', 'POST');
        $this->app->instance('request', $request);
        config(['password_security.blocklist' => []]);

        // Evita consulta ao banco: a regra usa primeiro o usuário autenticado.
        $user = new User();
        $user->email = 'fulano@example.com';
        Auth::setUser($user);

        $rule = new StrongPassword();

        $this->assertFalse($rule->passes('password', 'Fulano#2026A'));
    }
}
