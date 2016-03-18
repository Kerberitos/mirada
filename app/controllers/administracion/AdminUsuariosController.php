<?php namespace administracion;

use Anuncia\Repositorios\UsuarioRepo;
use Anuncia\Repositorios\ConfiguracionRepo;

/**
 * ----------------------------------------------------
 * Clase que permite a un Administrador: 
 * 		- Manipular usuarios bloqueados y desactivados
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

class AdminUsuariosController extends \BaseController 
{
	protected $usuarioRepo;
	protected $configuracionRepo;

	public function __construct (UsuarioRepo $usuarioRepo,
								 ConfiguracionRepo $configuracionRepo)
	{
		$this->usuarioRepo = $usuarioRepo;
		$this->configuracionRepo=$configuracionRepo;
	}

	/* Muestra una lista de usuarios bloqueados*/
	public function mostrarUsuariosBloqueados()
	{
		$busqueda = trim(\Input::get('busqueda'));
		$textobuscado = "";
		
		#estado 3 = estado bloqueado
		$estado = 3;

		if ($busqueda == "")
		{
			// si el input de busqueda está vacio no es necesario realizar acciones 
		}
		else if ($busqueda != "")
		{
			$textobuscado = $busqueda;
			$usuarios = $this->usuarioRepo->busquedaUsuariosPorNombre($busqueda, $estado);
			
			# sizeof devuelve el tamaño del argumento recibido
			if (sizeof($usuarios) == 0)
			{
				return \Redirect::route('admin.usuarios.bloqueados')->with('status_nohaycoincidencias',
																		   'No hay resultados de búsqueda
																			para '.$textobuscado);
			}
			
			return \View::make('modulos.administracion.listausuariosbloqueados', 
								compact('usuarios','textobuscado')
					);
		}

		$usuarios = $this->usuarioRepo->usuariosBloqueados();

		return \View::make('modulos.administracion.listausuariosbloqueados', 
							compact('usuarios','textobuscado')
				);
	}
	
	/* Permite ver el usuario bloqueado de manera individual*/
	public  function verUsuarioBloqueado($id)
	{
		$usuario = $this->usuarioRepo->buscarUsuario($id);
		$this->notFoundUnLess($usuario);
		
		# solo usuarios bloqueados
		if ($usuario->estado->estado == "bloqueado")
		{
			$configuracionActual =  $this->configuracionRepo->cargarConfiguracionActual();

			return \View::make('modulos.administracion.verusuariobloqueado', 
								compact('usuario', 'configuracionActual')
					);	
		}
		
		return \App::abort(404);
	}

	/* Muestra una lista de usuarios desactivados */
	public function mostrarUsuariosDesactivados()
	{
		$busqueda = trim(\Input::get('busqueda'));
		$textobuscado = "";
		
		# estado 2 = estado desactivado
		$estado = 2;

		if ($busqueda == "")
		{
			// si el input de busqueda está vacio no es necesario realizar acciones 
		}
		else if ($busqueda != "")
		{
			$textobuscado = $busqueda;
			$usuarios = $this->usuarioRepo->busquedaUsuariosPorNombre($busqueda, $estado);
			
			# sizeof devuelve el tamaño del argumento recibido
			if (sizeof($usuarios) == 0)
			{
				return \Redirect::route('admin.usuarios.desactivados')->with('status_nohaycoincidencias', 
																			 'No hay resultados de búsqueda 
																			 para '.$textobuscado);
			}
			return \View::make('modulos.administracion.listausuariosdesactivados', 
							   compact('usuarios','textobuscado')
					);
		}

		$usuarios = $this->usuarioRepo->usuariosDesactivados();
		
		return \View::make('modulos.administracion.listausuariosdesactivados', 
							compact('usuarios','textobuscado')
				);
	}

	/* Permite ver el usuario desactivado de manera individual*/
	public function verUsuarioDesactivado($id)
	{
		$usuario = $this->usuarioRepo->buscarUsuario($id);
		$this->notFoundUnLess($usuario);

		# solo usuarios desactivados
		if ($usuario->estado->estado == "desactivado")
		{
			return \View::make('modulos.administracion.verusuariodesactivado', 
								compact('usuario')
					);	
		}
		
		return \App::abort(404);
	}

	/* Activa cuenta de usuario si posee estado de desactivado*/
	public function activarCuentaDesactivada($id)
	{
		$usuario = $this->usuarioRepo->buscarUsuario($id);
		$this->notFoundUnLess($usuario);

		// activar solo si la cuenta tiene el estado de desactivado
		if ($usuario->estado->estado == "desactivado")
		{
			$this->usuarioRepo->activarUsuario($usuario->id);

			return \Redirect::route('admin.usuarios.desactivados')->with('status_ok', 
																		'La cuenta del usuario fue activada correctamente'); 
		}
		
		return	\App::abort(404);
	}
}
