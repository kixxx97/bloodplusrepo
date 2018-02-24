@extends('adminlte::page')

@section('title', 'Donate')


@section('content') 
@if (session('status'))
  <div id = "alertmsg" style="display:none">
    {{ session('status') }}
  </div>
@endif

<div class="row">
  <div class="col-xs-12">
    <div class="box box-info  ">
      <div class="box-header">
  </div>
      <!-- /.box-header -->
      <div class="box-body">
      <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">

      pdf button 
      <form action ="{{url('admin/donate/'.$donate->id).'/view/pdf'}}" method ="post">
        {{csrf_field()}}
      <button type="submit" class="btn-s btn-info">PDF</button>
      </form>
      </ul>
      <div class="tab-content"> 
        <div class="tab-pane active" id = "tab_1">
          <div id = "app" class="box-body table-responsive no-padding">
            
          </div>
        </div>
      </div>
      </div>
      </div>
  	</div>
</div>
</div>
@stop
@section('js')
      <script src="{{ asset('theme/js/dashboard.js') }}"></script>
       
@stop