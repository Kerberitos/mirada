<?php namespace usuariospermisosseguridad;

use Anuncia\Entidades\Usuario;
use Anuncia\Managers\CorreoSimpleManager;
use Anuncia\Managers\EditarPasswordManager;
use Anuncia\Asistentes\Cartero;

use Anuncia\Repositorios\UsuarioRepo;
use Anuncia\Repositorios\PostulanteRepo;

/**
 * ----------------------------------------------------
 * Clase que permite: 
 * 		- Reactivar cuenta de usuario con estado eliminado (cuenta eliminada previamente)
 *		
 * 
 * ----------------------------------------------------
 * autor: Edison Alexander Rojas León
 * email: 
 * fecha: 00/00/0000
 *
 */

class ReactivacionCuentaController extends \BaseController
{
	#objeto que hara consultas a la entidad Usuario
	protected $usuarioRepo;
	protected $postulanteRepo;
	
	/*Constructor para asignar el repositorio que manipulará la entidad Usuario */
	public function __construct(UsuarioRepo $usuarioRepo,
								PostulanteRepo $postulanteRepo)
	{
		$this->usuarioRepo = $usuarioRepo;
		$this->postulanteRepo = $postulanteRepo;
	}

	/* Muestra formulario para reactivación de cuenta */
	public function getReactivarCuenta()
	{
		return \View::make('modulos.usuariospermisosseguridad.reactivar', 
							compact('usuario')
				);
	}

	/* Genera enlace de reactivación de cuenta */
	public function postReactivarCuenta()
	{
		$correo = \Input::get('correo');

		$usuario = $this->usuarioRepo->buscarUsuarioCorreo($correo);

		if (! empty($usuario))
		{
			try
			{

				$estado = $usuario->estado->estado;
				
				if ($estado == 'eliminado')
				{
					/* necesita try-catch para indicar inicio de transacciones*/
					\DB::beginTransaction();

					$manager= new CorreoSimpleManager($usuario, \Input::all());

					if ($manager->isValid())
					{
						$usuario->random = uniqid('reac_',true);
						$manager->save();

						$cartero = new Cartero();
						$cartero->cartaReactivacion($usuario);
						
						/* necesita try-catch para indicar fin de transacciones*/
						\DB::commit();

						return \View::make('mensajes.usuarioencontradoreactivacion',
											compact('usuario')
								);
					}

					return \Redirect::back()->withInput()->withErrors($manager->getErrores());
				}

				return \Redirect::back()->with('cuentaestadonoeliminada_info', $estado);	
		
			}
			catch(\Exception $ex)
			{
				/* necesita try-catch para deshacer todas transacciones en caso de errores*/
				\DB::rollback();
				\Session::flash('error_de_registro_servidor',1);
					
				return \Redirect::back();
			}

		}
		
		return \Redirect::back()->with('noexisteusuario_error', 1);
	}


	/* Muestra formulario para establecer nuevo password después de solicitar reactivación de cuenta */
	public function getNuevoPassword($id, $random)
	{
		$usuario = $this->usuarioRepo->buscarUsuarioRandomId($id, $random);

		if (! empty($usuario))
		{
			return \View::make('modulos.usuariospermisosseguridad.reactivarypassword',
								compact('usuario')
					);
		}

		return \View::make('mensajes.errorreactivacion');
	}

	/* Guarda nuevo password */
	public function postNuevoPassword()
	{
		$correo = \Input::get('correo');

		$usuario = $this->usuarioRepo->buscarUsuarioCorreo($correo);
		$usuario->random = uniqid('mir_',true);
		$usuario->estado_id = 1;
		$usuario->bandera_social = false;
		
		$manager = new EditarPasswordManager($usuario, \Input::all());

		if ($manager->save())
		{
			return \View::make('mensajes.correctarecuperación', 
								compact('usuario')
					);
		}

		return \Redirect::back()->withInput()->withErrors($manager->getErrores());
	}
	
}