 <div class="modal fade" id="denuncia" tabindex="-1" role="dialog" aria-labelledby="contactLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="panel-title" id="contactLabel"><span class="glyphicon glyphicon-info-sign"></span> Denuncie si anuncio incumple alguna norma</h4>
            </div>
            <form action="{{route('denunciaranuncio')}}" method="post" accept-charset="utf-8" novalidate>
                <div class="modal-body" style="padding: 5px;">
                   
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12" style="padding-bottom: 10px;">
                            <label>Motivo de denuncia</label>
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                             <p class="informacion-adicional">Puede escribir aún <span id="max-length-denuncia">100</span> caracteres.</p>
                            <textarea style="resize:vertical;" class="form-control" placeholder="Detalle el motivo de la denuncia, mínimo 10 caracteres y máximo 100" rows="3" name="motivo" maxlength='101' id='descripcion-denuncia' data-validation="required length" data-validation-length="min10" data-validation-error-msg-required="Ingrese el motivo de su denuncia, mínimo 10 caracteres"
                data-validation-error-msg-length="Mínimo 10 caracteres" required novalidate="true"></textarea>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <p><strong>No debe abusar del sistema de denuncias de Miradita Loja.</strong></p>
                            <p>Si usted realiza denuncias y estas no incumplen ninguna norma, es decir, si realiza esta acción repetida e innecesariamente su cuenta de usuario podría ser suspendida. Por favor solo denuncie cuando crea que realmente se está infringuiendo las normas.</p>
                        </div>

                    </div>
                </div>  
                <input id="oculto" type="hidden" name="denunciado_id" value={{ $anuncio->usuario_id }}/>
                <input id="oculto" type="hidden" name="identificativo" value={{ $anuncio->id }}/>

                
                <div class="panel-footer">
                    <input type="submit" class="btn btn-danger" value="Denunciar"/>
                       
                        <!--<span class="glyphicon glyphicon-remove"></span>-->
                        <button style="float: right;" type="button" class="btn btn-default btn-close" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('scripts')
    <script>
       $.validate({
            form : '#denuncia',
            modules : 'file',
            borderColorOnError : '#A52A2A',
            addValidClassOnAll : true,
            //errorMessageClass:'errorsito-msm',
             onSuccess : function() {
                alert('El formulario es valido');
                //return false; // Will stop the submission of the form
                $('#denuncia').find('[type="submit"]').text('Enviando...').addClass('disabled');
            },
        });
    </script>
    <script>
         $('#descripcion-denuncia').restrictLength( $('#max-length-denuncia') );
    </script>
@stop