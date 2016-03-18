 <div class="modal fade bs-example-modal-sm" id="bloquearanuncio" tabindex="-1" role="dialog" aria-labelledby="bloquearanuncio" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="panel panel-danger">

        <div class="panel-heading">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          
          <h4 class="panel-title custom_align" id="Heading">
            <span class="icon-forbid">
            </span> 
                Bloquear anuncio
          </h4>
        </div>
        
        <div class="modal-body">
         
          <div>
              ¿Realmente desea bloquear este anuncio? 
          </div>
         
        </div>
        
        <div class="panel-footer">
          <a href="{{ route('admin.bloquearanuncio', [$anuncio->id])  }}" type="button" class="btn btn-danger" >
            </span> 
              Bloquear
          </a>
          <button type="button" class="btn btn-default" data-dismiss="modal">
              
              
                No
          </button>
        </div>
      </div><!-- /.modal-content --> 
      
    </div>
  </div>