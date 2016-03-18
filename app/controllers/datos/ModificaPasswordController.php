<?php namespace datos;

use Anuncia\Managers\EditarPasswordManager;

/**
 * ----------------------------------------------------
 * Clase que permite: 
 * 		- Modificar la contraseña de cuenta de usuario
 *		- Fijar una contraseña si usuario se registró con social login
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

class ModificaPasswordController extends \BaseController
{
	/* Muestra formulario cambio de password */
	public function getCambiarPassword()
	{
		$usuario = \Auth::user();
		
		// si hay clave se registró mediante correo electrónico
		if (! empty($usuario->password))
		{
			return \View::make('modulos.datos.cambiarpassword', 
								compact('usuario')
					);	
		}
		// se reegistro con social login entonces hay que fijar password, no cambiar
		return \Redirect::route('fijarpassword');
	}

	/* Cambia password de cuenta de usuario */
	public function postCambiarPassword()
	{
		$usuario = \Auth::user();
		$password = \Input::get('actualpassword');
		
		// compara las credenciales para verificar si coinciden con los almacenados en la BD	
		if (\Auth::validate(['password' => $password, 'correo' => $usuario->correo ]))
		{
			$manager = new EditarPasswordManager($usuario, \Input::all());

			if ($manager->save())
			{
				return \Redirect::route('perfil',[$usuario->slug ])->with('cambio_password',
																		  'Su contraseña ha 
																		   sido modificada 
																		   correctamente');
			}
			
			return \Redirect::back()->withInput()->withErrors($manager->getErrores());	
		}
		
		return \Redirect::back()->with('password_error', 1);
	}

	/* Muestra formulario fijar password (cuando usuario se registro con social login) */
	public function getFijarPassword()
	{
		return \View::make('modulos.datos.fijarpassword', 
							compact('usuario')
				);
	}

	/* Fija un password si usuario se registró con social login*/
	public function postFijarPassword()
	{
		$usuario = \Auth::user();
		$manager = new EditarPasswordManager($usuario, \Input::all());

		if ($manager->save())
		{
			return \Redirect::route('perfil',[$usuario->slug ])->with('cambio_password',
																	  'Su Contraseña ha sido 
																	   guardada correctamente, 
																	   ahora también puede ingresar
																	   con su correo y contraseña o 
																	   con su red social de preferencia');
		}
		
		return \Redirect::back()->withInput()->withErrors($manager->getErrores());	
	}

}