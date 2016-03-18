@extends('layout')
@section('contenido')

<div class="contenedor-interno">		
	<div  class="alert alert-info col-xs-12 text-center alert-size">

		
		<p>	Hola {{ $usuario->nombres }} por favor revise su correo electrónico y <strong>active</strong> su cuenta de Miradita Loja .</p>
	</div>	
	
	<p class="col-xs-12">Le hemos enviado un mensaje al correo {{ $usuario->correo }} con un enlace de activación para su cuenta en Miradita Loja.</p>
	<p class="col-xs-12">Si usted no ha recibido el correo de confirmación, por favor revise en spam.</p>
</div>

@stop
