<?php namespace datos;

use Anuncia\Repositorios\UsuarioRepo;
use Anuncia\Managers\RegistroManager;

use Anuncia\Asistentes\Mensajero;
use Anuncia\Asistentes\Consejero;
use Anuncia\Asistentes\Cartero;

/**
 * ----------------------------------------------------
 * Clase que permite: 
 * 		- Dar de alta usuarios (Registro de usuarios) en la aplicación
 * ----------------------------------------------------
 * Rutas:
 *
 *		Rutas cuando el usuario no ha iniciado sesión
 * 		- miradita/app/routes/guest.php
 * ----------------------------------------------------
 * autor: Edison Alexander Rojas León
 * email: 
 * fecha: 00/00/0000
 *
 */

class UsuarioController extends \BaseController {
	
	# objeto que hara consultas a la entidad Usuario
	protected $usuarioRepo;
	
	/* Constructor para asignar el repositorio que manipulará la entidad Usuario */
	public function __construct(UsuarioRepo $usuarioRepo)
	{
		$this->usuarioRepo = $usuarioRepo;
	}

	/* Muestra formulario de registro */
	public function getRegistro()
	{
		return \View::make('modulos.datos.registro');
	}
	
	/* Resgistra (da de alta) usuario en la aplicación */
	public function postRegistro()
	{
		# \Input::only retorna array
		# \Input::get retorn cadena
		
		$correo = \Input::only('correo');
	
		$usuario = $this->usuarioRepo->buscarUsuarioCorreo($correo['correo']);

		/* Si usuario NO existe en la BD*/
		# usuario está vacio
		if (empty($usuario))
		{
			try
			{
				$nombres = \Input::get('nombres');

				$usuario = $this->usuarioRepo->nuevoUsuario($nombres);
					
				$manager = new RegistroManager($usuario, \Input::all());
					
				# cartero envia emails
				$cartero = new Cartero();
					
				/* necesita try-catch para indicar inicio de transacciones*/
				\DB::beginTransaction();

				if ($manager->save())
				{
					$cartero->cartaRegistro($usuario);

					/* necesita try-catch para indicar fin de transacciones*/
					\DB::commit();
						
					return \View::make('mensajes.usuarioregistrado', compact('usuario'));
				}

				return \Redirect::back()->withInput()->withErrors($manager->getErrores());
			}
			catch(\Exception $ex)
			{
				/* necesita try-catch para deshacer todas transacciones en caso de errores*/
				\DB::rollback();
				\Session::flash('error_de_registro_servidor',1);
					
				return \Redirect::back();
			}	
		}

		// estado del usuario
		$estado = $usuario->estado->estado;

		if ($estado == 'activado')
		{
			return \Redirect::back()->with('status_error', 
										   'El correo que desea utilizar ya se encuentra registrado en Miradita');
		}
		
		// Si usuario está desactivado, bloqueado o eliminado

		# los posibles valores para accion pueden ser: 'ingresar', 'registrar', 'conectar'
		# 'registrar' cuando el usuario desea registrarse en la aplicación

		$accion = 'registrar';
		$mensaje= $this->obtenerMensaje($accion, $estado);
		
		// Muestra mensaje de estado de cuenta

		return \View::make('mensajes.mensajeestadocuenta', compact('mensaje', 'correo'));	
	}

	/* Obtiene mensajes para informar si usuario está desactivado, bloqueado o eliminado*/
	public function obtenerMensaje($accion, $estado){
		
		$peticion = array(
							'estado' => $estado,
							'accion' => $accion
					);

		$mensajero = new Mensajero($peticion);
		
		$mensaje = $mensajero->getMensaje();
		
		return $mensaje;
	}
	

}
