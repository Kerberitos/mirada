<?php namespace Anuncia\Repositorios;

use Anuncia\Entidades\Usuario;

use Carbon\Carbon;
use Jenssegers\Date\Date;

class UsuarioRepo extends BaseRepo
{
	public function getModel()
	{
		return new Usuario;	
	}
	
	public function nuevoUsuario($nombres)
	{
		$usuario = new Usuario();
		$usuario->rol_id = 1;
		$usuario->compania_id = 1;
		$usuario->estado_id = 2;
		$usuario->random = uniqid('mir_',true);
		$usuario->nav_avanzada = false;
		$usuario->slug = \Str::slug($nombres);
		$usuario->cambio = false;
		$usuario->bandera_social = false;
		
		return $usuario;
	}

	/* Devuelve los usuarios buscando por su nombre y estado */
	public function busquedaUsuariosPorNombre($busqueda, $estado)
	{
		return  Usuario::where('estado_id', '=', $estado)->where(function($query) use($busqueda) {
                $query->where('nombres', 'like', '%'.$busqueda.'%')->orWhere('correo', 'like', '%'.$busqueda.'%');
            })->paginate(1);	
	}

	/* Busca usuario por su id */
	public function buscarUsuario($usuario_id)
	{
		return Usuario::find($usuario_id);
	}

	/* Busca usuario por su correo */
	public function buscarUsuarioCorreo($correo)
	{
		return Usuario::whereCorreo($correo)->first();
	}

	/* Devuelve todos los usuarios desactivados */
	public function usuariosDesactivados()
	{
		return Usuario::where('estado_id','=', 2)->paginate(6);
	}

	/* Devuelve todos los usuarios bloqueados */
	public function usuariosBloqueados()
	{
		return Usuario::where('estado_id','=',3)->paginate(6);
	}

	/* Asciende usuario a administrador */
	public function ascenderAAdministrador($usuario_id)
	{
		$usuario = Usuario::find($usuario_id);
		$usuario->rol_id = 2;
		$usuario->nav_avanzada = true;
		$usuario->save();

		return true;
	}

	/* Asciende usuario a Super administrador */
	public function ascenderASuper($usuario_id)
	{
		$usuario = Usuario::find($usuario_id);
		$usuario->rol_id = 3;
		$usuario->nav_avanzada = true;
		$usuario->save();

		return true;
	}

	/* Desciende Super administrador a administrador */
	public function descenderAAdministrador($usuario_id)
	{
		$usuario = Usuario::find($usuario_id);
		$usuario->rol_id = 2;
		$usuario->save();

		return true;
	}

	/* Desciende Administrador a usuario */
	public function descenderAUsuario($usuario_id)
	{
		$usuario = Usuario::find($usuario_id);
		$usuario->rol_id = 1;
		$usuario->nav_avanzada = false;
		$usuario->save();

		return true;
	}

	/* Busca usuario mediante el campo random (token generado aleatoriamente) */
	public function buscarUsuarioRandom($random)
	{
		$usuario = Usuario::whereRandom($random)->first();
		
		return $usuario;
	}

	/* Busca usuairo por id y por random (TOKEN de verificación) */
	public function buscarUsuarioRandomId($id, $random)
	{
		$usuario = Usuario::whereRandom($random)->first();
		
		if (! empty($usuario))
		{
			# comprueba si id coincide con id de usuario almacenado
			if ($id == $usuario->id)
			{
				return $usuario;		
			}	
		}

		return null;
	}








	/* Verifica si existe usuario en el sistema mediante su correo */
	public function existeUsuario($correo)
	{
		$usuario = Usuario::whereCorreo($correo)->first();
		
		if (!empty($usuario))
		{
			return true;
		}
		return false;
	}

	public function usuarioAnonimo()
	{
		$user = new Usuario();
		return $user;
	}
	
	public function busquedaUsuariosPorNombreRol($busqueda, $rol, $estado)
	{
		return  Usuario::where('estado_id', '=', $estado)->where('rol_id', '=', $rol)->where(function($query) use($busqueda) {
                $query->where('nombres', 'like', '%'.$busqueda.'%')->orWhere('correo', 'like', '%'.$busqueda.'%');
            })->paginate(1);	
		 
	}

	public function buscarUsuariosPorRol($rol, $estado)
	{
		$usuarios= Usuario::where('estado_id','=', $estado)->where('rol_id','=', $rol)->paginate(1);
		
		return $usuarios;
	}

	


	/*
	public function buscarUsuarioTwitter_id($twitter_id)
	{
		$user = Usuario::whereTwitter_id($twitter_id)->first();
		
		return $user;
	}
*/

	
	public function usuariosActivos()
	{
		return Usuario::where('estado_id','=', 1)->paginate(6);
	}
	
	public function enumerarUsuariosActivos()
	{
		return Usuario::where('estado_id','=', 1)->count();
	}

	

	public function enumerarUsuariosDesactivados()
	{
		return Usuario::where('estado_id','=', 2)->count();
	}

	
	
	public function enumerarUsuariosBloqueados()
	{
		return Usuario::where('estado_id','=',3)->count();	
	}

	/*devuelve los administradores del sistema en orden alfabetico*/
	public function buscarAdministradores()
	{
		return Usuario::where('rol_id','=', 2)->orWhere('rol_id','=', 3)->orderBy('nombres', 'asc')->paginate(6);
	}

	public function bloquearUsuario($usuario_id)
	{
		$usuario=Usuario::find($usuario_id);
		$usuario->estado_id=3;
		$usuario->save();

		return true;
	}

	public function activarUsuario($usuario_id)
	{
		$usuario=Usuario::find($usuario_id);
		$usuario->estado_id=1;
		$usuario->save();

		return true;
	}
}
