<?php namespace Anuncia\Repositorios;

use Anuncia\Entidades\Contacto;

use Carbon\Carbon;
use Jenssegers\Date\Date;

class ContactoRepo extends BaseRepo
{
	public function getModel()
	{
		return new Contacto;	
	}
	
	public function nuevoContacto()
	{
		$contacto = new Contacto();
		
		$contacto->estatus_visto = "libre";

		return $contacto;
	}

	/* Devuelve todos los mensajes contactanos */
	public function mensajesContactanos($receptor_rol)
	{
		
		if ($receptor_rol == 2)
		{
			# mensajes para administrador
			return Contacto::where('receptor_rol','=', 2)->paginate(4);
		}
		else if ($receptor_rol == 3)
		{
			# mensajes para super administrador
			return Contacto::where('receptor_rol','=', 2)->orWhere('receptor_rol','=', 3)->paginate(4);
		}
	}

	/* Establece el estatus de revision del mensaje como ocupado */
	public function estatusRevisionOcupado($contacto, $admin)
	{
		$contacto->estatus_visto = "ocupado";
		$contacto->admin = $admin;
		$contacto->save();
	}

	/* Busca mensaje contactanos mediante su id */
	public function buscarMensaje($mensaje_id)
	{
		return Contacto::find($mensaje_id);
	}

	/* Elimina mensaje contactanos, recibe objeto Contacto */
	public function eliminarMensaje($mensaje)
	{
		if ($mensaje->delete())
		{
			return true;
		}
		
		return false;
	}

	
	






	public function numContactanos($receptor_rol)
	{
		if ($receptor_rol == 2)
		{
			return Contacto::where('receptor_rol','=', $receptor_rol)->count();
		}
		else if ($receptor_rol == 3)
		{
			return Contacto::all()->count();
		}
	}
	
	/*
	public function verificarCorreo($correo)
	{
		$usuarios = Contacto::all();

		foreach ($usuarios as $usuario)
		{
			if($correo == $usuario->correo )
			{
				return $usuario;
			}
			else
			{
				return false;
			}
		}
	}
	*/
	
}
