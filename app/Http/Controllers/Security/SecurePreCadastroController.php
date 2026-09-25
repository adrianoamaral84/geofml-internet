<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Rules\StrongPassword;
use App\Services\DocumentoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SecurePreCadastroController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        abort_unless($user, 401);

        $request->merge([
            'cpf' => preg_replace('/\D+/', '', (string) $request->input('cpf')),
            'telefone' => preg_replace('/\D+/', '', (string) $request->input('telefone')),
            'idtMil' => preg_replace('/[.\-]/', '', (string) $request->input('idtMil')),
        ]);

        if ($request->boolean('indeterminado')) {
            $request->merge(['validade' => null]);
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('user', 'email')->ignore($user->id),
            ],
            'cpf' => [
                'required',
                'digits:11',
                Rule::unique('user', 'cpf')->ignore($user->id),
            ],
            'idtMil' => 'required|max:15',
            'telefone' => 'required|digits_between:10,11',
            'uf' => 'required',
            'cidade' => 'required',
            'situacao' => 'required',
            'pttc' => 'nullable',
            'siape' => 'nullable',
            'nivel' => 'nullable',
            'om' => 'required',
            'posto' => 'nullable',
            'mecenas' => 'nullable|boolean',
            'dtUltPromo' => 'nullable|date',
            'validade' => 'nullable|date',
            'mesAnoFinal' => [
                'required_if:pttc,1',
                'nullable',
                'regex:/^(0[1-9]|1[0-2])\/[0-9]{4}$/',
            ],
            'documento' => 'nullable|mimes:jpeg,jpg,png,pdf|max:4000',
            'documento_verso' => 'nullable|mimes:jpeg,jpg,png,pdf|max:4000',
        ]);

        if (!$this->cpfValido($validated['cpf'])) {
            return back()->withInput()->withErrors(['cpf' => 'CPF inválido.']);
        }

        if ($request->hasFile('documento') xor $request->hasFile('documento_verso')) {
            return back()->withInput()->withErrors([
                'documento' => 'Envie a frente e o verso do documento.',
            ]);
        }

        try {
            $user->name = strtoupper($validated['nome']);
            $user->email = $validated['email'];
            $user->cpf = $validated['cpf'];
            $user->idtMil = $validated['idtMil'];
            $user->telefone = $validated['telefone'];
            $user->uf_id = $validated['uf'];
            $user->cidade_id = $validated['cidade'];
            $user->situacao_id = $validated['situacao'];
            $user->dtUltPromo = $validated['dtUltPromo'] ?? null;
            $user->forca_id = $request->input('forca');
            $user->om_id = $validated['om'];
            $user->perfil_id = 5;
            $user->postograd_id = $validated['posto'] ?? null;
            $user->siape = $validated['siape'] ?? null;
            $user->status = 3;
            $user->validade = $request->boolean('indeterminado')
                ? null
                : ($validated['validade'] ?? null);
            $user->mecenas = $request->boolean('mecenas') ? 1 : 0;
            $user->indeterminado = $request->boolean('indeterminado') ? 1 : 0;
            $user->pttc = $request->boolean('pttc') ? 1 : 0;
            $user->mesAnoFinal = (
                (int) $validated['situacao'] === 2 && $request->boolean('pttc')
            ) ? ($validated['mesAnoFinal'] ?? null) : null;

            // A senha já foi definida no primeiro acesso e não é alterada aqui.
            $user->save();

            if ($request->hasFile('documento') || $request->hasFile('documento_verso')) {
                $documentoService = new DocumentoService();

                if ($request->hasFile('documento')) {
                    $documentoService->salvarFrente($user, $request->file('documento'));
                }

                if ($request->hasFile('documento_verso')) {
                    $documentoService->salvarVerso($user, $request->file('documento_verso'));
                }
            }

            $user->syncRoles(['5']);
        } catch (\Throwable $e) {
            Log::error('Erro ao concluir pré-cadastro seguro.', [
                'usuario_id' => $user->id,
                'erro' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'cadastro' => 'Ocorreu um erro ao salvar os dados. Tente novamente.',
            ]);
        }

        \Session::flash('message', [
            'msg' => 'Cadastro concluído. Aguarde a confirmação para acesso completo ao sistema.',
            'class' => 'success',
        ]);

        return redirect()->route('usuario.home');
    }

    private function cpfValido($cpf)
    {
        $cpf = preg_replace('/\D+/', '', (string) $cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;

            for ($c = 0; $c < $t; $c++) {
                $soma += (int) $cpf[$c] * (($t + 1) - $c);
            }

            $digito = ((10 * $soma) % 11) % 10;

            if ((int) $cpf[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }
}
