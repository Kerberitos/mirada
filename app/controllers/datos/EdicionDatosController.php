<?php namespace datos;

use Anuncia\Entidades\Compania;

use Anuncia\Managers\ModificarDatosManager;

/**
 * ----------------------------------------------------
 * Clase que permite: 
 * 		- Editar los datos generales de usuario
 * ----------------------------------------------------
 * Rutas:
 *
 *		- miradita/app/routes/auth.php
 *		
 * ----------------------------------------------------
 * autor: Edison Alexander Rojas León
 * email: 
 * fecha: 00/00/0000
 *
 */

class EdicionDatosController extends \BaseController
{
	/* Muestra formulario para editar datos de usuario */
	public function getEditarDatos($slug)
	{
		$usuario = \Auth::user();
		
		# compañias de celulares: Claro, Movistar, CNT
		// no hacer esto, código sucio, obligado a realizar para evitar crear una nueva clase Repositorio
		$companias = Compania::orderBy('nombre','asc')->get()->lists('nombre','id');

		return \View::make('modulos.datos.editardatos', compact('usuario', 'companias'));
	}
	
	/* Procesa formulario editar datos */
	public function postEditarDatos($slug)
	{
		$usuario = \Auth::user();

		$nuevoNombre = \Input::get('nombres');

		// Se puede cambiar el nombre una sola vez en el sistema
		# compara si el nombre ha cambiado 
		if (! (\Helper::compararCadenas($usuario->nombres, $nuevoNombre)))
		{
			$usuario->cambio = true;
			$usuario->slug = \Str::slug($nuevoNombre);
		}

		$manager = new ModificarDatosManager($usuario, \Input::all());

		if($manager->save())
		{
			return \Redirect::route('perfil',[$usuario->slug ])->with('cambio_correcto', 
																	  'Su información general 
																	   se ha modificado 
																	   correctamente');
		}
		
		return \Redirect::back()->withInput()->withErrors($manager->getErrores());
	}
	
}

