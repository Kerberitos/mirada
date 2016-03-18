<?php namespace Anuncia\Managers;

class ModificarCuentaManager extends BaseManager
{
	public function getRules()
	{
		$rules = [
			'correo' => 'required|email|unique:usuarios,correo,'.$this->entidad->id
		];
		
		return $rules;
	}
}