@extends('layout')
@section('contenido')
	
<div class="contenedor-interno">		
@if (Session::has('login_correcto'))
    <p class="alert alert-success alert-size">Bienvenido a Miradita Loja, ha ingresado correctamente</p>
@endif

@if (Session::has('status_correocompletado'))
	<p class="alert alert-success alert-size">{{Session::get('status_correocompletado')}} </p>
@endif

    <div class="container-fuid">
		<div class="row" >
			<div class="col-xs-12 col-md-12">
				<h1 class="titulo-presentacion text-center">miradita loja</h1>
				<p class="text-center texto-informativo">Comunidad lojana para anuncios clasificados en línea</p>
			</div>
		</div>
	
		<div class="row" id="mensaje-presentacion">
			<p class="col-xs-12 subtitulo-presentacion">¿Qué es miradita Loja?</p>
			<div class="text-center col-xs-12 col-md-offset-2 col-md-8 texto-presentacion">
				 Miradita Loja es una nueva comunidad, nacida bajo el objetivo de brindar a la ciudadanía lojana  un espacio en  Internet dónde se podrá crear, publicar y buscar anuncios de forma gratuita. 
			</div>
			
		</div>

		<div class="row" id="secciones-presentacion">
			<p class="centrar-div col-xs-12 subtitulo-presentacion texto-azul">Conoce las distintas secciones</p>
			
			<div class="col-xs-12 col-sm-4 seccion-presentacion text-center">
				
				<a class="enlace" href="{{ route('verclasificados') }}" title="">
					<span class="glyphicon glyphicon-list-alt icono-presentacion"></span> 
					<h4>SECCIÓN CLASIFICADOS</h4>
				</a>
				
				<p>¿Desea alquilar una casa u oficina? ¿Vender un celular nuevo o usado? ¿Anunciar su vehículo?</p>
				<p>Cualquier bien o artículo que desee vender o alquilar, debe hacerlo en esta sección.	</p>
				<a href="{{ route('clasificados.categorias') }}" title="" class="btn btn-categorias">Categorías de clasificados</a>
			</div>
			
			<div class="col-xs-12  col-sm-4 seccion-presentacion  text-center">
				
				<a class="enlace" href="{{ route('verservicios') }}" title="">
					<span class="glyphicon glyphicon-wrench icono-presentacion"></span> 
					<h4>SECCIÓN SERVICIOS</h4>
				</a>
				<p>¿Brinda algún servicio en Loja?, ¿Es pintor, carpintero? o ¿Tiene una papelería?</p>
				<p>Si usted brinda algún tipo de servicio que todos lo conozcan y puedan contactarlo.</p>
				<a href="{{ route('servicios.categorias') }}" title="" class="btn btn-categorias">Categorías de servicios</a>
			</div>

			<div class="col-xs-12  col-sm-4 seccion-presentacion  text-center">
				
				<a class="enlace"  href="{{ route('verempleos') }}" title="">
					<span class="glyphicon glyphicon-briefcase icono-presentacion"></span> 
					<h4>SECCIÓN EMPLEOS</h4>
				</a>
				<p>Hey! ¿Busca un trabajo en Loja? o ¿Necesita contratar a alguien para un trabajo?</p>
				<p>Entonces la sección empleos le puede ser de ayuda, para ofrecer o buscar trabajo.</p>
				<a href="{{ route('empleos.categorias') }}" title="" class="btn btn-categorias">Categorías de empleos</a>
			</div>
		</div>

	</div>
</div>	
	
	




	
	 
		
       

@stop
