<?php

namespace App\Http\Controllers;

use App\Rules\StrongPassword;
use App\Services\DocumentoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PreCadastroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        if ($request->indeterminado == 1) {
            $request->validade = null;
        }

        date_default_timezone_set('America/Sao_Paulo');

        if ($request->hasFile('documento') xor $request->hasFile('documento_verso')) {
            return back()->withInput()->withErrors(['É necessário enviar a frente e o verso do documento.']);
        }

        if ($request->hasFile('documento') && $request->hasFile('documento_verso')) {
            if (!$request->file('documento')->isValid() || !$request->file('documento_verso')->isValid()) {
                return back()->withInput()->withErrors(['Arquivo inválido.']);
            }
        }

        if ($request->filled('cpf')) {
            $request->merge([
                'cpf' => str_replace(['.', '-'], '', $request->cpf),
            ]);
        }

        if ($request->filled('telefone')) {
            $request->merge([
                'telefone' => str_replace(['(', ')', ' ', '-'], '', $request->telefone),
            ]);
        }

        if ($request->filled('idtMil')) {
            $request->merge([
                'idtMil' => str_replace('-', '', $request->idtMil),
            ]);
        }

        $validatedData = $request->validate([
            'nome' => 'required|max:100',
            'email' => 'required|email|max:100',
            'cpf' => 'required|max:11',
            'idtMil' => 'required|max:15',
            'telefone' => 'required|max:11',
            'uf' => 'required',
            'cidade' => 'required',
            'situacao' => 'required',
            'pttc' => 'nullable',
            'siape' => 'nullable',
            'nivel' => 'nullable',
            'om' => 'required',
            'mesAnoFinal' => [
                'required_if:pttc,1',
                'nullable',
                'regex:/^(0[1-9]|1[0-2])\/[0-9]{4}$/',
            ],
            'mecenas' => 'nullable|boolean',
            'password' => ['required', 'same:resenha', new StrongPassword()],
            'posto' => 'nullable',
            'resenha' => 'required|string|max:64|same:password',
            'documento' => 'nullable|mimes:jpeg,png,pdf|max:4000',
            'documento_verso' => 'nullable|mimes:jpeg,png,pdf|max:4000',
        ], [
            'password.required' => 'Campo senha obrigatório.',
            'password.same' => 'A confirmação da senha não confere.',
            'resenha.required' => 'Campo confirmação de senha obrigatório.',
            'resenha.same' => 'A confirmação da senha não confere.',
            'mesAnoFinal.required_if' => 'O campo Mês/Ano Final é obrigatório quando PTTC estiver marcado.',
            'mesAnoFinal.regex' => 'Informe o Mês/Ano Final no formato MM/AAAA.',
            'documento.max' => 'O Documento Frente precisa ter no máximo 4MB.',
            'documento_verso.max' => 'O Documento Verso precisa ter no máximo 4MB.',
        ]);

        if (!$this->validarCPF($validatedData['cpf'])) {
            return back()->withInput()->withErrors(['CPF inválido.']);
        }

        if (!$this->verificarCPFCadastrado($validatedData['cpf'])) {
            return back()->withInput()->withErrors(['Este CPF já está cadastrado no sistema.']);
        }

        if (!$this->verificarEmailCadastrado($validatedData['email'])) {
            return back()->withInput()->withErrors(['Este E-mail já está cadastrado no sistema.']);
        }

        $usuario = Auth::user();
        $usuario->name = strtoupper($validatedData['nome']);
        $usuario->email = $validatedData['email'];
        $usuario->cpf = $validatedData['cpf'];
        $usuario->idtMil = $validatedData['idtMil'];
        $usuario->telefone = $validatedData['telefone'];
        $usuario->uf_id = $validatedData['uf'];
        $usuario->cidade_id = $validatedData['cidade'];
        $usuario->situacao_id = $validatedData['situacao'];
        $usuario->dtUltPromo = $request->dtUltPromo;
        $usuario->forca_id = $request->forca;
        $usuario->om_id = $validatedData['om'];
        $usuario->perfil_id = 5;
        $usuario->postograd_id = $request->posto;
        $usuario->siape = $request->siape;
        $usuario->status = 3;
        $usuario->validade = $request->validade;
        $usuario->mecenas = $request->mecenas ? 1 : 0;
        $usuario->indeterminado = $request->has('indeterminado') ? 1 : 0;
        $usuario->pttc = $request->has('pttc') ? 1 : 0;
        $usuario->mesAnoFinal = ((int) $validatedData['situacao'] === 2 && $request->has('pttc'))
            ? $validatedData['mesAnoFinal']
            : null;
        $usuario->password = Hash::make($validatedData['password']);

        try {
            if (!$usuario->update()) {
                return back()->withInput()->withErrors(['Ocorreu um erro ao salvar os dados.']);
            }

            if ($request->hasFile('documento') || $request->hasFile('documento_verso')) {
                $documentoService = new DocumentoService();

                if ($request->hasFile('documento')) {
                    $documentoService->salvarFrente($usuario, $request->file('documento'));
                }

                if ($request->hasFile('documento_verso')) {
                    $documentoService->salvarVerso($usuario, $request->file('documento_verso'));
                }
            }

            $usuario->syncRoles(['5']);

            \Session::flash('message', [
                'msg' => 'Aguarde o recebimento do e-mail de confirmação para acessar o sistema completo!',
                'class' => 'success',
            ]);

            return redirect()->route('usuario.home');
        } catch (\Throwable $erro) {
            Log::error('Erro ao finalizar pré-cadastro.', [
                'usuario_id' => $usuario->id ?? null,
                'erro' => $erro->getMessage(),
                'arquivo' => $erro->getFile(),
                'linha' => $erro->getLine(),
            ]);

            return back()->withInput()->withErrors(['Ocorreu um erro ao salvar os dados. Tente novamente.']);
        }
    }
}
