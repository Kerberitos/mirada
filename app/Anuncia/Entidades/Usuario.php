<?php namespace Anuncia\Entidades;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Carbon\Carbon;
use Jenssegers\Date\Date;

class Usuario extends \Eloquent implements UserInterface, RemindableInterface 
{
	use UserTrait, RemindableTrait;
	
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'usuarios';

	protected  $fillable=array(
		'celular',
		'compania_id',
		'correo',
		'estado_id',
		'foto',
		'genero',
		'nombres',
		'password',
		'telefono',
		'social_id',
	);

	public function alerta()
	{
		return $this->hasOne('Anuncia\Entidades\Alerta');
	}

	public function anuncios()
    {
        return $this->hasMany('Anuncia\Entidades\Anuncio');
    }

	public function comentarios()
    {
        return $this->hasMany('Anuncia\Entidades\Comentario');
    }

    public function compania()
	{
		return $this->belongsTo('Anuncia\Entidades\Compania');
	}
  	
  	public function estado()
	{
		return $this->belongsTo('Anuncia\Entidades\Estado');
	}

    public function historial(){
		
		return $this->hasOne('Anuncia\Entidades\Historial');
	}

    public function notificaciones()
    {
        return $this->hasMany('Anuncia\Entidades\Notificacion')->orderBy('created_at', 'desc');
    }

    public function postulante(){
		return $this->hasOne('Anuncia\Entidades\Postulante');
	}

    public function respuesta()
    {
        return $this->hasMany('Anuncia\Entidades\Respuesta');
    }

	public function getCreatedAtAttribute($value)
	{
	    return new Date($value);
	}

	public function setCelularAttribute($value)
	{
		if ( ! empty ($value))
		{
			$this->attributes['celular'] = \Crypt::encrypt($value);
		}
	}

	public function getCelularAttribute($value)
	{
		if ( ! empty ($value))
		{
			//return $this->attributes['celular'] = \Crypt::decrypt($value);
			return \Crypt::decrypt($value);
		}
	}

	public function getGeneroTitleAttribute()
	{
		if ($this->genero=='male')
		{
			return 'Masculino';
		}
		else
		{
			return 'Femenino';
		}
	}

	public function getGeneroEresTitleAttribute()
	{
		if ($this->genero=='male')
		{
			return 'Hombre';
		}
		else
		{
			return 'Mujer';
		}
	}

	public function setPasswordAttribute($value)
	{
		if (! empty ($value))
		{
			$this->attributes['password'] = \Hash::make($value);
		}
	}

	public function getRolTitleAttribute()
	{
		if ($this->rol_id==1)
		{
			return 'usuario';
		}
		else if  ($this->rol_id==2)
		{
			return 'administrador';
		}
		else if  ($this->rol_id==3)
		{
			return 'Miradita Loja';
		}
	}

	public function getRolVistosoTitleAttribute()
	{
		if ($this->rol_id == 1)
		{
			return 'Usuario';
		}
		else if	($this->rol_id == 2)
		{
			return 'Administrador';
		}
		else if ($this->rol_id == 3)
		{
			return 'Super';
		}
	}

	public function setTelefonoAttribute($value)
	{
		if (! empty ($value))
		{
			$this->attributes['telefono'] = \Crypt::encrypt($value);
		}
	}

	public function getTelefonoAttribute($value)
	{
		if (! empty ($value))
		{
			return  \Crypt::decrypt($value);
		}
	}
}
