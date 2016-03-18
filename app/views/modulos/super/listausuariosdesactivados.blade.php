@extends('layout')
@section('contenido')
<div class="contenedor-interno">
    <div class="text-center">
        <h4>Usuarios desactivados</h4>
    </div>
    
    <div class="row">
    	@if (Session::has('status_error'))
			<p class="alert alert-danger alert-size">{{Session::get('status_error')}} </p>
   		@endif
   		@if (Session::has('status'))
			<p class="alert alert-success alert-size">{{Session::get('status_ok')}} </p>
   		@endif
    		<?php $i=1  ?>
	    	@if (sizeof($usuarios)==0)
	  			@if (Session::has('status_nohaycoincidencias'))

				@else
					<div class="col-xs-12">
		      			<p class="alert alert-info alert-size">No hay usuarios desactivados</p>
		    		</div>
		   		@endif
	  		@else
	  		<div class="col-xs-12">
		  		<ul class="pagination pagination-sm">
				    <li class="disabled"><a >Total <span class="hidden-xs">de usuarios</span>: {{$usuarios->getTotal() }}</a></li>
				    <li class="disabled"><a >Pág<span class="hidden-xs">ina Nº</span> {{$usuarios->getCurrentPage() }} de {{$usuarios->getLastPage() }}</a></li>
				</ul>
			</div>	
			<div>
				<form action="{{route('lista.usuarios.desactivados')}}" method="GET" role="form">
				<div class="col-xs-12">
				 	<a href="{{route('lista.usuarios.desactivados')}}" class="btn btn-success btn-sm btn-recargar pull-right" title="Recargar todos"><span class="icon-repeat" > </span> </a>
				 	
				</div>	
				<div class="col-xs-12 col-sm-3 col-md-2 espacio-inferior-peq ">
					<div >
					    <input type="text" class="form-control  input-sm" name="busqueda" placeholder="Correo o nombre">
					</div>
				</div>	
				<div class="col-xs-12 col-sm-3 col-md-2  espacio-inferior-peq">  
					<select class="form-control input-sm" name="rol">
                       	@if($rolenvista==0)
	                       	<option value=0 selected>Todos</option>
	                       	<option value=1 >Usuarios</option>
	                       	<option value=2>Administradores</option>
	                       	<option value=3>Super admin</option>
                  		@elseif($rolenvista==1)
                  			<option value=0 >Todos</option>
	                       	<option value=1 selected>Usuarios</option>
	                       	<option value=2>Administradores</option>
	                       	<option value=3>Super admin</option>
                  		@elseif($rolenvista==2)
                  			<option value=0 >Todos</option>
	                       	<option value=1 >Usuarios</option>
	                       	<option value=2 selected>Administradores</option>
	                       	<option value=3>Super admin</option>
	                    @elseif($rolenvista==3)
                  			<option value=0 >Todos</option>
	                       	<option value=1 >Usuarios</option>
	                       	<option value=2>Administradores</option>
	                       	<option value=3 selected>Super admin</option>
                  		@endif
                  	</select>
				</div>	

				<div class="col-xs-12 col-sm-3 col-md-2 espacio-inferior-peq">
				 	<button type="submit" class="btn btn-primary btn-sm col-xs-12">Buscar</button>
				</div>
				</form>
			</div>

			@if (Session::has('status_nohaycoincidencias'))
				<div class="col-xs-12">
					<p class="alert alert-success alert-size">{{Session::get('status_nohaycoincidencias')}} </p>	
				</div>
	   		@endif		
		<div class="col-xs-12">
		
		@if($textobuscado!="")
			<p><span class="texto-negrita"> {{$usuarios->getTotal() }} resultados para:</span> <span class="texto-azul">{{ $textobuscado }}</span></p>
		@endif
		
    	<div class="table-responsive">
	    	<table class="table">
	    		<thead>
			        <tr>
			            <th>#</th>
			            <th>Nombres</th>
			          	<th class="ocultar-menor-a600">Rol</th>
			            <th >Correo</th>
			            <th class="hidden-xs">Estado</th>
			            <th>Accción </th>
			        </tr>
			    </thead>
	          
		        <tbody>
		        	 @foreach ($usuarios as $usuario)
			        <tr class="fila-tabla-miradita">
			            <td>
			            	<a href="{{route('super.ver.usuario', [$usuario->id])}}">
			            		{{ $i++ }}
			            	</a>
			            </td>
			            
			            <td>
			            	<a href="{{route('super.ver.usuario', [$usuario->id])}}">
		            			{{ ($usuario->nombres) }}
			            		
			            	</a>
			            </td>
			            <td class="ocultar-menor-a600">
			            	<a href="{{route('super.ver.usuario', [$usuario->id])}}">

			            		@if($usuario->rol_id==1)
			            			<span class="label label-primary texto-blanco text-center">
				            			{{ $usuario->rolvistoso_title}}
				            		</span>	
			            		@elseif($usuario->rol_id==2)
			            			<span class="label label-warning texto-blanco text-center">
				            			{{ $usuario->rolvistoso_title}}
				            		</span>	


			            		@elseif($usuario->rol_id==3)
			            			<span class="label label-danger texto-blanco text-center">
				            			{{ $usuario->rolvistoso_title}}
				            		</span>	

			            		@endif
			            	</a>
			            </td>
		           
			            <td>
			            	<a href="{{route('super.ver.usuario', [$usuario->id])}}">
			            		{{ $usuario->correo }}
			            	</a>

			            </td>
			            <td class="hidden-xs">
			            	<a href="{{route('super.ver.usuario', [$usuario->id])}}">
			            		{{ $usuario->estado->estado }}
			            	</a>
			            </td>
			            <td> 
			            	<a href="{{route('super.ver.usuario', [$usuario->id])}}" title="Ver usuario" class="btn btn-success btn-xs" > 
			            		<i class="glyphicon glyphicon-eye-open" aria-hidden="true">  
	            				</i> 
	            				<span class="texto-blanco"> Ver </span>
	            			</a>
	            		</td>
			        </tr>
	    			@endforeach
	    		</tbody>	          
			</table>
		</div><!--fin table responsive-->  
		</div>
		@endif
		<div class="col-xs-12">
			{{ $usuarios->appends(array("busqueda"=>Input::get("busqueda"),"rol"=>Input::get("rol")))->links() }}	
		</div>
        
        
    </div><!--fin row-->
</div><!--fin contenedor interno-->
@stop