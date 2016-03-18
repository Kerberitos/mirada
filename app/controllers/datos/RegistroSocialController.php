<?php namespace datos;

use Anuncia\Repositorios\RegistroSocialRepo;

use Anuncia\Managers\RegistroSocialManager;

use Anuncia\Asistentes\Mensajero;

/**
 * ----------------------------------------------------
 * Clase que permite: 
 * 		- Ingresar a la aplicación web mediante redes sociales
 *
 * ----------------------------------------------------
 * Rutas:
 *
 *		- miradita/app/routes/guest.php
 *		
 * ----------------------------------------------------
 * autor: Edison Alexander Rojas León
 * email: 
 * fecha: 00/00/0000
 *
 */

class RegistroSocialController extends \BaseController
{
	protected $registroSocialRepo;
	
	public function __construct(RegistroSocialRepo $registroSocialRepo)
	{
		$this->registroSocialRepo=$registroSocialRepo;
	}
	
	/* Ingreso mediante api de twitter*/
	public function ingresoTwitter()
	{
		# Obtener datos de entrada
		$token = \Input::get( 'oauth_token' );
		$verify = \Input::get( 'oauth_verifier' );
		
		# Obtener servicio de twitter
		$tw = \OAuth::consumer( 'Twitter' );
		
		/* Si se proporciona un código, obtener datos de usuario y abrir una sesión */
 		# Verifica si el codigo es valido (no está vacio)
 		if (! empty( $token ) && !empty( $verify )) 
 		{
			// Petición de devolución de llamada de Twitter, para obtener el token
			$token = $tw->requestAccessToken( $token, $verify );
		
			// Enviar una solicitud a twitter para verificar las credenciales ingresadas por usuario
			$result = json_decode( $tw->request( 'account/verify_credentials.json' ), true );
			
			$usuario = $this->registroSocialRepo->buscarUsuarioPorSocialId($result['id']);

			/* Si usuario existe*/
			# usuario es diferente de null
			if (! empty($usuario))
			{
				if ($usuario->estado->estado == 'activado' | $usuario->estado->estado == 'desactivado')
				{
					if ($usuario->estado->estado == 'desactivado')
					{
						$this->registroSocialRepo->activarUsuario($usuario);
					}

					// Se crea una session de usuario para ingresar a la aplicación
					\Auth::login($usuario);

					/* si usuario no posee correo */
					if (empty($usuario->correo))
					{
						// Redireccionamos para solicitar correo y genero
						return \Redirect::to('agregar/correo');
					}
					
					return \Redirect::to('/')->with('ingreso_social', 'twitter');

				}
				// Si usuario está bloqueado o eliminado
				$accion = 'conectar';
				$estado = $usuario->estado->estado;

				$mensaje= $this->obtenerMensaje($accion, $estado);

				// Muestra mensaje de estado de cuenta
				return \View::make('mensajes.mensajeestadocuenta', compact('mensaje'));	
			}
			// Si no existe usuario se crea y registra
			$data = $this->cargarData($result);
		
			// Creando el usuario para almacenar en la bd	
			$usuario = $this->registroSocialRepo->nuevoUsuario($data);
			
			// Crea manager 
			$manager = new RegistroSocialManager($usuario, $data);

			$manager->simpleSave();

			// Se crea una session de usuario para ingresar a la aplicación
			\Auth::login($usuario);
			
			// El api de twitter no retorna correo, así que solicitaremos al usuario un correo
			/* si usuario no posee correo */
			if (empty($usuario->correo))
			{
			// Redireccionamos para solicitar correo y genero
				return \Redirect::to('agregar/correo');
			}
					
			return \Redirect::to('/')->with('ingreso_social', 'twitter');
		}
		# Verifica si el codigo no es valido (está vacio) redirecciona a pagina de twiiter login
		else 
		{
			// Obtener solicitud de token
			$reqToken = $tw->requestRequestToken();

			// Obtener autorización Uri enviando el token de solicitud
			$url = $tw->getAuthorizationUri(array('oauth_token' => $reqToken->getRequestToken()));
		
			// Redireccionar a url de twitter login
			return \Redirect::to( (string)$url );
		}
	} // fin ingreso con twiiter

	
	/* Ingreso mediante facebook */
	public function ingresoFacebook()
	{
			$code = \Input::get( 'code' );
			$fb = \OAuth::consumer( 'Facebook' );
			
			if (! empty( $code ))
			{
				$token = $fb->requestAccessToken( $code );
				$result = json_decode( $fb->request( '/me' ), true );
			
				# array_key_exists verifica si existe la llave email en el array
				if (array_key_exists('email', $result)) 
				{	
					$usuario = $this->registroSocialRepo->buscarUsuarioPorCorreo($result['email']);
				}
				else
				{
					$usuario = $this->registroSocialRepo->buscarUsuarioPorSocialId($result['id']);
				}

				/* Si usuario existe*/
				# usuario es diferente de null
				if (! empty($usuario))
				{
					if ($usuario->estado->estado == 'activado' | $usuario->estado->estado == 'desactivado')
					{
						if ($usuario->bandera_social == false)
						{
							$this->registroSocialRepo->activarBanderaSocial($usuario);
							$this->registroSocialRepo->guardarSocialId($usuario, $result['id']);
						}

						if ($usuario->estado->estado == 'desactivado')
						{
							$this->registroSocialRepo->activarUsuario($usuario);
						}

						// Se crea una session de usuario para ingresar a la aplicación
						\Auth::login($usuario);

						/* si usuario no posee correo */
						if (empty($usuario->correo))
						{
							// Redireccionamos para solicitar correo y genero
							return \Redirect::to('agregar/correo');
						}
						
						return \Redirect::to('/')->with('ingreso_social', 'facebook');
					}
					// Si usuario está bloqueado o eliminado
					$accion = 'conectar';
					$estado = $usuario->estado->estado;

					$mensaje= $this->obtenerMensaje($accion, $estado);

					// Muestra mensaje de estado de cuenta
					return \View::make('mensajes.mensajeestadocuenta', compact('mensaje'));	
				}

				// Si no existe usuario se crea y registra
				$data = $this->cargarData($result);
		
				// Creando el usuario para almacenar en la bd	
				$usuario = $this->registroSocialRepo->nuevoUsuario($data);
			
				// Crea manager 
				$manager = new RegistroSocialManager($usuario, $data);

				$manager->simpleSave();

				// Se crea una session de usuario para ingresar a la aplicación
				\Auth::login($usuario);
			
				// Si el api de facebook no retorna correo, solicitaremos al usuario un correo
				/* si usuario no posee correo */
				if (empty($usuario->correo))
				{
				// Redireccionamos para solicitar correo y genero
					return \Redirect::to('agregar/correo');
				}
					
				return \Redirect::to('/')->with('ingreso_social', 'facebook');
		
			// if not ask for permission first
			}
			else 
			{
				// get fb authorization
				$url = $fb->getAuthorizationUri();

				// return to facebook login url
				return \Redirect::to( (string)$url );
			}
	} // Fin ingreso con facebook
	

	/* Conecta con API de Google mediante cuenta de google+ */
	public function ingresoGoogle()
	{
		$code = \Input::get( 'code' );
		$googleService = \OAuth::consumer( 'Google' );
	
		if ( !empty( $code )) 
		{
			$token = $googleService->requestAccessToken( $code );
			$result = json_decode( $googleService->request( 'https://www.googleapis.com/oauth2/v1/userinfo' ), true );
		
			// unset($result['email']) método que sirvió para las pruebas, elimina un elemento de array;
			// unset($result['gender']);
			
			# array_key_exists verifica si existe la llave email en el array
			if (array_key_exists('email', $result)) 
			{	
				$usuario = $this->registroSocialRepo->buscarUsuarioPorCorreo($result['email']);
			}
			else
			{
				$usuario = $this->registroSocialRepo->buscarUsuarioPorSocialId($result['id']);
			}

			/* Si usuario existe*/
			# usuario es diferente de null
			if (! empty($usuario))
			{
				if ($usuario->estado->estado == 'activado' | $usuario->estado->estado == 'desactivado')
				{
					if ($usuario->bandera_social == false)
					{
						$this->registroSocialRepo->activarBanderaSocial($usuario);
						$this->registroSocialRepo->guardarSocialId($usuario, $result['id']);
					}

					if ($usuario->estado->estado == 'desactivado')
					{
						$this->registroSocialRepo->activarUsuario($usuario);
					}

					// Se crea una session de usuario para ingresar a la aplicación
					\Auth::login($usuario);

					/* si usuario no posee correo */
					if (empty($usuario->correo))
					{
						// Redireccionamos para solicitar correo y genero
						return \Redirect::to('agregar/correo');
					}
						
					return \Redirect::to('/')->with('ingreso_social', 'google');
				}
				// Si usuario está bloqueado o eliminado
				$accion = 'conectar';
				$estado = $usuario->estado->estado;

				$mensaje= $this->obtenerMensaje($accion, $estado);

				// Muestra mensaje de estado de cuenta
				return \View::make('mensajes.mensajeestadocuenta', compact('mensaje'));	
			}

			// Si no existe usuario se crea y registra
			$data = $this->cargarData($result);
		
			// Creando el usuario para almacenar en la bd	
			$usuario = $this->registroSocialRepo->nuevoUsuario($data);
			
			// Crea manager 
			$manager = new RegistroSocialManager($usuario, $data);

			$manager->simpleSave();

			// Se crea una session de usuario para ingresar a la aplicación
			\Auth::login($usuario);
			
			// Si el api de facebook no retorna correo, solicitaremos al usuario un correo
			/* si usuario no posee correo */
			if (empty($usuario->correo))
			{
			// Redireccionamos para solicitar correo y genero
				return \Redirect::to('agregar/correo');
			}
					
			return \Redirect::to('/')->with('ingreso_social', 'google');
		
		// if not ask for permission first
		}
		else 
		{
			// get googleService authorization
			$url = $googleService->getAuthorizationUri();

			// return to google login url
			return \Redirect::to( (string)$url );
		}
	} // fin ingreso con google
	

	/* Obtiene mensajes para informar si usuario está bloqueado o eliminado*/
	public function obtenerMensaje($accion, $estado){
		
		$peticion = array(
							'estado' => $estado,
							'accion' => $accion
					);

		$mensajero = new Mensajero($peticion);
		$mensaje = $mensajero->getMensaje();
		
		return $mensaje;
	}

	/* Devuelve array con todos los scopes (alcances) que devuelven las distinats APIs   */
	public function cargarData($resultado){

		$data = array();

		# array_key_exists verifica si existe la llave en un array
		if (array_key_exists('id', $resultado)) 
		{
			$data['social_id'] = $resultado['id'];
		}

		if (array_key_exists('name', $resultado)) 
		{
			$data['nombres'] = $resultado['name'];
		}

		if (array_key_exists('email', $resultado)) 
		{
			$data['correo'] = $resultado['email'];
		}

		if (array_key_exists('gender', $resultado)) 
		{
			$data['genero'] = $resultado['gender'];
		}

		return $data;
	}

}