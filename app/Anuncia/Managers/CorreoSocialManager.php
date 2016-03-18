<?php namespace Anuncia\Managers;

class CorreoSocialManager extends BaseManager
{
	public function getRules()
	{
		$rules = [
			'correo' => 'required|email|unique:usuarios,correo,'.$this->entidad->id,
			'genero' => 'required'
		];
		
		return $rules;
	}
}