<?php namespace Anuncia\Entidades;
use Carbon\Carbon;
use Jenssegers\Date\Date;

class Denuncia extends \Eloquent
{
	protected $table = 'denuncias';
	
	protected $fillable = array(
		'denunciado_id',
		'identificativo',
		'motivo',
	);

	public function usuario()
	{
		return $this->belongsTo('Anuncia\Entidades\Usuario');
	}

	public function getCreatedAtAttribute($value)
	{
		return new Date($value);
	}
}