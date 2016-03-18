<?php namespace Anuncia\Managers;

class ModificarDatosManager extends BaseManager
{
	public function getRules()
	{
		$rules = [
			'celular' => 'numeric|digits:10',
			'compania_id' => '',
			'genero' => '',
			'nombres' => 'alpha_spaces',
			'telefono' => 'numeric|digits_between:6,9',
		];
		
		return $rules;
	}
}