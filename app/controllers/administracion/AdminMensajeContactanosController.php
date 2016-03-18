<?php namespace administracion;

use Anuncia\Repositorios\ContactoRepo;
use Anuncia\Repositorios\UsuarioRepo;
use Anuncia\Repositorios\ConfiguracionRepo;

use Anuncia\Asistentes\Cartero;

/**
 * ----------------------------------------------------
 * Clase que permite a un Administrador: 
 * 		- Gestionar los mensajes recibidos mediante contáctanos
 * ----------------------------------------------------
 * Rutas:
 * 		- miradita/app/routes/admin.php
 *		
 * ----------------------------------------------------
 * autor: Edison Alexander Rojas León
 * email: 
 * fecha: 00/00/0000
 *
 */

class AdminMensajeContactanosController extends \BaseController
{
	protected $contactoRepo;
	protected $configuracionRepo;

	public function __construct(ContactoRepo $contactoRepo,
								UsuarioRepo $usuarioRepo,
								ConfiguracionRepo $configuracionRepo)
	{
		$this->contactoRepo= $contactoRepo;
		$this->usuarioRepo=$usuarioRepo;
		$this->configuracionRepo=$configuracionRepo;
	}

	/* Muestra una lista de los mensajes recibidos mediante contáctanos */
	public function mostrarMensajesContactanos()
	{
		$anuncios=$this->contactoRepo->mensajesContactanos(\Auth::user()->rol_id);
		return \View::make('modulos.administracion.vermensajescontactanos', 
							compact('anuncios')
				);
	}

	/* Ver el mensaje contactanos de manera individual */
	public function revisarMensajeContactanos($id)
	{
		$mensaje = $this->contactoRepo->buscarMensaje($id);
		$this->notFoundUnLess($mensaje);

		# admin es requerido si el mensaje contactanos ya está siendo revisado por un administrador
		$admin = \Auth::id();

		if(\Helper::compararCadenas($mensaje->estatus_visto, "libre"))
		{

			$this->contactoRepo->estatusRevisionOcupado($mensaje, $admin);

			return \View::make('modulos.administracion.revisionmsmcontactanos', 
								compact('mensaje')
					); 
		}
		else if (\Helper::compararCadenas ($mensaje->estatus_visto, "ocupado") & ($mensaje->admin == $admin))
		{
			return \View::make('modulos.administracion.revisionmsmcontactanos', 
								compact('mensaje')
					); 

		}
		else if (\Helper::compararCadenas ($mensaje->estatus_visto, "ocupado") & ($mensaje->admin != $admin))
		{
			return \Redirect::route('admin.msmcontactanos')->with('status_error', 
																  'Este mensaje ya lo está revisando otro administrador.');
		}
		else
		{
			return \Redirect::route('admin.msmcontactanos')->with('status_error', 
																  'Ese mensaje no ha solicitado revision.');
		}
	}

	/** 
	* Verifica si existe algun usuario registrado asociado al correo proporcionado desde contactanos 
	* Muestra información de la cuenta de usuario y las acciones de administrador sobre el mensaje
	*/
	public function verificarCuenta($id)
	{
		$mensaje = $this->contactoRepo->buscarMensaje($id);
		$this->notFoundUnLess($mensaje);
		
		$correo = $mensaje->correo;
		
		if ($this->usuarioRepo->existeUsuario($correo))
		{
			$usuario = $this->usuarioRepo->buscarUsuarioCorreo($correo);
			
			$configuracionActual =  $this->configuracionRepo->cargarConfiguracionActual();

			return \View::make('modulos.administracion.ververificarcuenta', 
								compact('usuario','mensaje', 'configuracionActual')
					);
		}
		
		return \Redirect::back()->with('status_error', 
										  'No hay ninguna cuenta de usuario asociada 
										   al correo que brindó el usuario');
			
	}

	/* Elimina mensaje contactanos */
	public function eliminarMensajeContactanos($mensaje_id)
	{
		$mensaje = $this->contactoRepo->buscarMensaje($mensaje_id);
		$this->notFoundUnLess($mensaje);
		
		$rol_admin = \Auth::user()->rol_id;

		if($rol_admin == 2 | $rol_admin == 3)
		{
			if($this->contactoRepo->eliminarMensaje($mensaje))
			{
				return \Redirect::to('admin/mensajes-contactanos')->with('status_ok',
																	     'El mensaje de contáctanos ha sido eliminado');
			}
			return \Redirect::to('admin/mensajes-contactanos')->with('status_error',
																	 'El mensaje de contáctanos no pudo ser eliminado');
		}
		
		return	\App::abort(404);
	}

	/* Envia respuesta a usuario que escribió desde contactanos */
	public function responderContactanos($usuario_id, $mensaje_id)
	{
		try
		{
			\DB::beginTransaction();

			$usuario = $this->usuarioRepo->buscarUsuario($usuario_id);
			$this->notFoundUnLess($usuario);

			$estadocuenta = $usuario->estado->estado;
			$motivo = '';
			
			# cartero enviara el correo electrónico
			$cartero = new Cartero();
			
			# se requiere la configuracion del sistema para realizar comparaciones
			$configuracion = $this->configuracionRepo->cargarConfiguracionActual();
			
			if ($estadocuenta == "bloqueado")
			{
				if ($usuario->historial)
				{
					$contadorDeDenunciasUsuario = $usuario->historial->denunciasfalsas - $usuario->historial->denunciasverdaderas;
					$anunciosBloqueadosUsuario = $usuario->historial->anunciosbloqueados;
					$comentariosEliminadosUsuario = $usuario->historial->comentarioseliminados;

					if ( $anunciosBloqueadosUsuario >= $configuracion->anunciosbloqueados )
					{
	                	$respuesta = $usuario->nombres.' su cuenta de usuario en Miradita Loja se encuentra bloqueada, 
	                								debido a que modificó tres de sus anuncios ya publicados con contenido 
	                								que infringe las normas de uso, no se permite a un usuario más de tres 
	                								anuncios bloqueados.';

	                }
	                else if ($contadorDeDenunciasUsuario >=	$configuracion->contadordedenuncias)
	                {
	                	$respuesta = $usuario->nombres.' su cuenta de usuario en Miradita Loja se encuentra bloqueada, 
	                									 debido a que abusó del sistema de denuncias, realizando denuncias
	                									 falsas repetida e innecesariamente.';
	                
	                }
	                else if ($comentariosEliminadosUsuario >= $configuracion->comentarioseliminados)
	                {
	               		$respuesta = $usuario->nombres.' su cuenta de usuario en Miradita Loja se encuentra bloqueada, 
	               										 debido a que realizó tres comentarios con contenido que 
	               										 incumple alguna norma de uso.';
	               	}
				}
			}
			else if ($estadocuenta == "desactivado")
			{
				$respuesta = $usuario->nombres.' su cuenta de usuario en Miradita Loja se encuentra desactivada, en el 
												 momento que se registró un link de activación fue enviado a su correo
												 electrónico, por favor sino encuentra el mensaje en su bandeja de entrada
												 revise en spam, o puede solicitar un nuevo link de activación.';
			}
			
			/* Envía un correo electrónico con la respuesta */
			if($cartero->cartaRespuestaContactanos($usuario, $respuesta, $estadocuenta))
			{
				# eliminar mensaje contactanos gestionado
				$this->eliminarMensajeContactanos($mensaje_id);
				
				\DB::commit();

				return \Redirect::to('admin/mensajes-contactanos')->with('status_ok',
																		'El correo electrónico fue enviado correctamente y 
																		 el mensaje que revisó fue procesado.');
			}

			return \Redirect::to('admin/mensajes-contactanos')->with('status_error',
																	'La respuesta no se pudo enviar');
		}
		catch(\Exception $ex)
		{
			\DB::rollback();
			\Session::flash('error_de_servidor',1);
			return \Redirect::back();
		}	
	}
}