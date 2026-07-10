<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;
use Laratrust\Traits\LaratrustUserTrait;
use App\GerenciarOm;
use App\PerfilAcesso;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;
    protected $table = "user";

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'idtMil', 'cpf', 'om', 'nomeguerra ', 'telefone', 'om_id', 'password', 'status', 'postograd_id', 'perfil_id', 'endereco', 'cep', 'uf_id', 'cidade_id', 'pttc', 'dtUltPromo', 'forca_id', 'situacao_id', 'siape', 'mecenas',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public static function listAllUser($search = null){
        $user_id = Auth::user()->id;
        $perfil_id = Auth::user()->perfil_id;
        //$perfil_id = 4;
        
        if ($search == null) {
            //$user = User::find(15)->roles();
            //dd($user);
            //$u = $user->roles;
            return User::all();
            
            //return User::where('id', '!=', $user_id)->where('perfil_id', '>', $perfil_id)->get();
        } else {
            return User::where('id', '!=', $user_id)->where('perfil_id', '>', $perfil_id)
                        ->where('nome', 'LiKE', '%'.$search.'%')->orWhere('cpf', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')
                        ->get();
        }
    }


    public static function changeStatus($usuario_id) {
        try {
            $usuario = User::find($usuario_id);
            $usuario->status = $usuario->status == 1 ? 2 : 1;
            $usuario->update();
        } catch (\Throwable $th) {
            //throw new Exception("Falha ao alterar status de usuário.", 1);
            
        } 
    }

    public function om()
    {
        return $this->belongsTo(GerenciarOm::class, 'om_id');
    }

    public function perfil()
    {
        return $this->belongsTo(PerfilAcesso::class, 'perfil_id');
    }

    public function posto()
    {
            //$a = $this->belongsTo(PostoGraduacao::class, 'postograd_id')->orderBy('id','DESC');
            //dd($a);

        return $this->belongsTo(PostoGraduacao::class, 'postograd_id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role');
    }

    public function status()
    {
        return $this->belongsToMany(Status::class);
    }


     public function uf()
    {
        return $this->belongsTo(Uf::class, 'uf_id');
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }
    
 public function documentos()
{
    return $this->hasMany(\App\UserDocumento::class, 'user_id');
}

public function documentoFrente()
{
    return $this->hasOne(\App\UserDocumento::class, 'user_id')
                ->where('tipo', 'frente');
}

public function documentoVerso()
{
    return $this->hasOne(\App\UserDocumento::class, 'user_id')
                ->where('tipo', 'verso');
}



/**
 * Retorna o percentual de desconto do usuário.
 */
public function getPercentualDescontoAttribute()
{
    return $this->mecenas ? 30 : 0;
}

/**
 * Aplica o desconto ao valor informado.
 */
public function aplicarDesconto($valor)
{
    if (!$this->mecenas) {
        return $valor;
    }

    return round($valor * (1 - ($this->percentual_desconto / 100)), 2);
}

}
