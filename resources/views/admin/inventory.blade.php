@extends('adminlte::page')

@section('title', 'Inventory')

@section('content_header')
     <!--  <h1>
      &nbsp
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Inventory</li>
      </ol> -->
@stop

@section('content') 
@if (session('status'))
  <div id = "alertmsg" style="display:none">
    {{ session('status') }}
  </div>

@endif
    <!-- Main content -->
      <!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info  ">

      <div class="box-body">
      <?php $counter = 0;  ?> 
        @foreach($bloodTypes as $bloodType)
        @if($counter % 4 == 0)
        <div class="row">
        @endif
        <div class="col-md-3">
          <div class="card">
              <div class ="card-header">
              <button class="clickable-row btn btn-block btn-danger btn-lg" data-href="{{url('/admin/inventory/bloodtype/'.$bloodType->id)}}">{{$bloodType->name}}</button>
              </div>
              <center>
              <div class="card-body">
              </div>
              </center>
              <div class ="table-responsive">
              <table class ="table table-hover">
              <thead>
              <th>Category</th>
              <th>Quantity</th>
              </thead>
              <tbody>
              @foreach($bloodType->bloodType as $bloodBag)
              <tr class='clickable-row' data-href="{{url('/admin/inventory/bloodcategory/'.$bloodBag->id)}}">
              <td> {{$bloodBag->category}} </td>
              <td align="center"> {{count($bloodBag->nonReactive(Auth::guard('web_admin')->user()->institute->id))}} </td>
              </tr>
              @endforeach
              </tbody>
              </table>
              </div>  
            </div>    
        </div>
        <?php $counter++ ?>
        @if($counter %4 == 0)
        </div>
        @endif
        @endforeach
    
      </div>
    </div>
  </div>
</div>
<!-- /.row (main row) --> 


@stop

@section('css')

@stop

@section('js')
    <script type="text/javascript" src="{{asset('theme/js/DataTables/media/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('theme/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('theme/js/dashboard.js') }}"></script>

    <script> 
      $(document).ready(function() {
        $(".clickable-row").click(function() {
          window.open($(this).data("href"));
        });
      });
    </script>

@stop
