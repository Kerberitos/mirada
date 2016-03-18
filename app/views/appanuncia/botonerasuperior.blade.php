<ul class="nav navbar-nav">
    @if (!Auth::check())
        <li title="Ingreso al sistema">
            <a class="btn btn-sm boton-menu-registro" href="{{ URL::route('registro') }}">
                <span class="icon-user-add">
                </span> 
                    REGISTRATE
            </a>
        </li>   
        <li title="Ingreso al sistema">
            <a class="btn btn-sm boton-menu-ingreso" href="{{ URL::route('ingreso') }}">
                <span class="icon-key">
                </span> 
                    INGRESA
            </a>
        </li>   
                                
    @else
        
        @if(!empty(Auth::user()->correo))
            <li title="Crear un anuncio">
                <a class="btn btn-sm boton-menu-crear" href="{{ URL::route('mostrar.pasouno') }}">
                    <span class="glyphicon glyphicon-pencil">
                    </span> 
                        CREAR ANUNCIO
                </a>
            </li>  
        
        @else    
            <li title="Crear un anuncio">
                <a class="btn btn-sm boton-menu-crear" data-toggle="modal" data-target="#nocorreo">
                    <span  class="glyphicon glyphicon-pencil"></span> 
                        <span>CREAR ANUNCIO</span>
                </a>

            </li>
        @endif
    @endif
</ul>