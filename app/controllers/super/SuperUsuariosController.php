<?php namespace super;

use Anuncia\Repositorios\UsuarioRepo;
use Anuncia\Repositorios\PostulanteRepo;
use Anuncia\Repositorios\ConfiguracionRepo;

/**
 * ----------------------------------------------------
 * Clase que : 
 * 		- Muestra los distintos paneles de Usuarios
 *			-> Panel de usuairos activos
 *			-> Panel de usuarios postulantes a administradores
 *			-> Panel de usuarios bloqueados 
 *			-> Panel de usuarios desactivados 
 * ----------------------------------------------------
 * Rutas:
 * 		- miradita/app/routes/super.php
 *		
 * ----------------------------------------------------
 * autor: Edison Alexander Rojas León
 * email: 
 * fecha: 00/00/0000
 *
 */

class SuperUsuariosController extends \BaseController
{
	protected $usuarioRepo;
	protected $postulanteRepo;
	protected $configuracionRepo;

	public function __construct(UsuarioRepo $usuarioRepo,
								PostulanteRepo $postulanteRepo,
								ConfiguracionRepo $configuracionRepo)
	{
		$this->postulanteRepo = $postulanteRepo;
		$this->usuarioRepo = $usuarioRepo;
		$this->configuracionRepo = $configuracionRepo;
	}
	
	/* Muestra panel de usuarios al Super administrador */
	// No confundir con panel de usuarios de Administrador
	public function panelUsuarios()
	{
		$numPostulantes = $this->getNumeroPostulantes();
		$numUsuariosBloqueados = $this->getNumeroUsuariosBloqueados();
		$numUsuariosActivos = $this->getNumeroUsuariosActivos();
		$numUsuariosDesactivados = $this->getNumeroUsuariosDesactivados();
		
		return \View::make('modulos.super.panelusuarios', 
							compact('numUsuariosActivos',
									'numUsuariosBloqueados',
									'numUsuariosDesactivados',
									'numPostulantes')
				);
	}
	
	/* Obtiene número de usuarios postulantes a administradores */
	public function getNumeroPostulantes()
	{
		return $this->postulanteRepo->enumerarPostulantes();		
	}

	/* Obtiene número de usuarios bloqueados */	
	public function getNumeroUsuariosBloqueados()
	{
		return $this->usuarioRepo->enumerarUsuariosBloqueados();		
	}

	/* Obtiene número de usuarios activos */
	public function getNumeroUsuariosActivos()
	{
		return $this->usuarioRepo->enumerarUsuariosActivos();		
	}
	
	/* Obtiene número de usuarios desactivados */
	public function getNumeroUsuariosDesactivados()
	{
		return $this->usuarioRepo->enumerarUsuariosDesactivados();		
	}
	
	/* Muestra usuarios postulantes a administradores */
	public function usuariosPostulantes()
	{
		$usuarios = $this->postulanteRepo->postulantes();
		
		return \View::make('modulos.super.listapostulantes',
							compact('usuarios')
				);
	}
	
	/* Muestra en detalle usuario postulante a administrador */
	public function verPostulante($id)
	{
		$usuario = $this->usuarioRepo->buscarUsuario($id);
		return \View::make('modulos.super.verpostulante', 
							compact('usuario')
				);
	}

	/* Muestra usuarios bloqueados */
	public function usuariosBloqueados()
	{
		$busqueda = trim(\Input::get('busqueda'));
		$rolbuscado = \Input::get('rol');
		
		#estado 3 esquivalente a bloqueado
		$estado = 3;
		
		#$rolenvista establece el valor del rol seleccionado en el select de la vista
		$rolenvista = 0;
		
		$textobuscado = "";

		if ($busqueda == "" & $rolbuscado == 0)
		{
			// Si no se escribe nada en el campo de busqueda ni se seleccionó ninguna opción del select
			// entonces no se realiza ninguna acción
		}
		else if ($busqueda != "")
		{
			$textobuscado = $busqueda;
			
			if ($rolbuscado != 0)
			{
				$usuarios = $this->usuarioRepo->busquedaUsuariosPorNombreRol($busqueda, $rolbuscado, $estado);
				$rolenvista = $rolbuscado;
				
				if (sizeof($usuarios) == 0)
				{
					return \Redirect::route('lista.usuarios.bloqueados')->with('status_nohaycoincidencias',
																			   'No hay resultados de búsqueda 
																			    para '.$textobuscado);
				}

				return \View::make('modulos.super.listausuariosbloqueados', 
									compact('usuarios','textobuscado', 'rolenvista')
						);
			}
			else if ($rolbuscado == 0)
			{
				$rolenvista = $rolbuscado;

				$usuarios = $this->usuarioRepo->busquedaUsuariosPorNombre($busqueda, $estado);
				
				if (sizeof($usuarios) == 0)
				{
					return \Redirect::route('lista.usuarios.bloqueados')->with('status_nohaycoincidencias',
																			   'No hay resultados de búsqueda 
																			    para '.$textobuscado);
				}
				return \View::make('modulos.super.listausuariosbloqueados', 
									compact('usuarios','textobuscado', 'rolenvista')
						);
			}
		}
		else if ($busqueda == "" & $rolbuscado != 0)
		{
			$usuarios = $this->usuarioRepo->buscarUsuariosPorRol($rolbuscado, $estado);
			
			$rolenvista = $rolbuscado;
			
			if(sizeof($usuarios)==0)
			{
				return \Redirect::route('lista.usuarios.bloqueados')->with('status_nohaycoincidencias',
																		   'No hay resultados de búsqueda');
			}
			
			return \View::make('modulos.super.listausuariosbloqueados', 
								compact('usuarios','textobuscado', 'rolenvista')
					);
		}	

		$usuarios = $this->usuarioRepo->usuariosBloqueados();
		
		return \View::make('modulos.super.listausuariosbloqueados', 
							compact('usuarios','textobuscado', 'rolenvista')
				);
	}	

	
	/* Muestra usuarios desactivados */
	public function usuariosDesactivados()
	{
		$busqueda = trim(\Input::get('busqueda'));
		$rolbuscado = \Input::get('rol');
		
		#estado 2 esquivalente a desactivado
		$estado = 2;
		
		#$rolenvista establece el valor del rol seleccionado en el select de la vista
		$rolenvista = 0;
		$textobuscado = "";

		if ($busqueda == "" & $rolbuscado == 0)
		{

		}
		else if ($busqueda != "")
		{
			$textobuscado = $busqueda;
			
			if ($rolbuscado != 0)
			{
				$usuarios = $this->usuarioRepo->busquedaUsuariosPorNombreRol($busqueda, $rolbuscado, $estado);
				$rolenvista = $rolbuscado;
				
				if (sizeof($usuarios) == 0)
				{
					return \Redirect::route('lista.usuarios.desactivados')->with('status_nohaycoincidencias', 
																				 'No hay resultados de búsqueda
																				  para '.$textobuscado);
				}
				return \View::make('modulos.super.listausuariosdesactivados', 
									compact('usuarios','textobuscado', 'rolenvista')
						);
			}
			else if ($rolbuscado == 0)
			{
				$rolenvista = $rolbuscado;
				$usuarios = $this->usuarioRepo->busquedaUsuariosPorNombre($busqueda, $estado);
				
				if (sizeof($usuarios) == 0)
				{
					return \Redirect::route('lista.usuarios.desactivados')->with('status_nohaycoincidencias', 
																				 'No hay resultados de búsqueda
																				  para '.$textobuscado);
				}
				return \View::make('modulos.super.listausuariosdesactivados', 
									compact('usuarios','textobuscado', 'rolenvista')
						);
			}
		}
		else if ($busqueda == "" & $rolbuscado != 0)
		{
			$usuarios = $this->usuarioRepo->buscarUsuariosPorRol($rolbuscado, $estado);
			$rolenvista = $rolbuscado;
			
			if (sizeof($usuarios) == 0)
			{
				return \Redirect::route('lista.usuarios.desactivados')->with('status_nohaycoincidencias', 
																			 'No hay resultados de búsqueda');
			}
			return \View::make('modulos.super.listausuariosdesactivados', 
								compact('usuarios','textobuscado', 'rolenvista')
					);
		}	

		$usuarios = $this->usuarioRepo->usuariosDesactivados();
		
		return \View::make('modulos.super.listausuariosdesactivados', 
							compact('usuarios','textobuscado', 'rolenvista')
				);
	}


	/* Muestra usuario activos */
	public function usuariosActivos()
	{
		$busqueda = trim(\Input::get('busqueda'));
		
		$rolbuscado = \Input::get('rol');
		
		#estado 1 esquivalente a activo
		$estado = 1;
		
		#$rolenvista establece el valor del rol seleccionado en el select
		$rolenvista = 0;
		
		$textobuscado="";

		if ($busqueda == "" & $rolbuscado == 0)
		{

		}
		else if ($busqueda != "")
		{
			$textobuscado = $busqueda;
			
			if ($rolbuscado != 0)
			{
				$usuarios = $this->usuarioRepo->busquedaUsuariosPorNombreRol($busqueda, $rolbuscado, $estado);
				$rolenvista = $rolbuscado;
				
				if (sizeof($usuarios) == 0)
				{
					return \Redirect::route('lista.usuarios.activos')->with('status_nohaycoincidencias', 
																			'No hay resultados de búsqueda 
																			 para '.$textobuscado);
				}
				
				return \View::make('modulos.super.listausuariosactivos', 
									compact('usuarios','textobuscado', 'rolenvista')
						);
			}else if ($rolbuscado == 0)
			{
				$rolenvista = $rolbuscado;
				$usuarios = $this->usuarioRepo->busquedaUsuariosPorNombre($busqueda, $estado);
				
				if (sizeof($usuarios) == 0)
				{
					return \Redirect::route('lista.usuarios.activos')->with('status_nohaycoincidencias', 
																			'No hay resultados de búsqueda 
																			 para '.$textobuscado);
				}
				
				return \View::make('modulos.super.listausuariosactivos', 
									compact('usuarios','textobuscado', 'rolenvista')
						);
			}
		}
		else if ($busqueda == "" & $rolbuscado != 0)
		{
			$usuarios = $this->usuarioRepo->buscarUsuariosPorRol($rolbuscado, $estado);
			$rolenvista = $rolbuscado;
			
			if (sizeof($usuarios) == 0)
			{
				return \Redirect::route('lista.usuarios.activos')->with('status_nohaycoincidencias', 
																		'No hay resultados de búsqueda');
			}
			
			return \View::make('modulos.super.listausuariosactivos', 
								compact('usuarios','textobuscado', 'rolenvista')
					);
		}	
		
		$usuarios = $this->usuarioRepo->usuariosActivos();
		
		return \View::make('modulos.super.listausuariosactivos', 
							compact('usuarios','textobuscado', 'rolenvista')
				);
	}


	/* Muestra en detalle usuario activado, bloqueado o desactivado */
	public function verUsuario($id)
	{
		$usuario = $this->usuarioRepo->buscarUsuario($id);

		$this->notFoundUnLess($usuario);

		if (\Auth::user()->rol_id == 3)
		{
			$configuracionActual =  $this->configuracionRepo->cargarConfiguracionActual();

			return \View::make('modulos.super.verdetalleusuario', 
								compact('usuario','configuracionActual')
					);
		}
	}
	
}
