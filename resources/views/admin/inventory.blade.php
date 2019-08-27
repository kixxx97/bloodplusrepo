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
@if(Auth::guard('web_admin')->user()->institute->mother_branch == null)
<div class="row">
  <div class="col-xs-12">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs nav-justified">
        <li class="active"><a href="#tab_1" data-toggle="tab">Overall</a></li>
        <li><a href="#tab_2" data-toggle="tab">Here</a></li>
      </ul>
    <div class="tab-content">
    <div class="tab-pane active" id = "tab_1">  
    <div class="box box-info  ">
      <div class="box-header">
        <form method ="get" action="{{url('admin/inventory')}}">
        <input type="hidden" name ="sort" value ="true">
      </div>

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
              @if($bloodType->name == 'O+')
              <td align="center"> {{ rand(5,15)}} </td>
              @else
              <td align="center"> {{ rand(10,100)}} </td>
              @endif
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
  <div class="tab-pane" id = "tab_2">  
    <div class="box box-info  ">
      <div class="box-header">
        <form method ="get" action="{{url('admin/inventory')}}">
        <input type="hidden" name ="sort" value ="true">
        <div class="col-md-2">
        <div class="form-group">
          <label>Status </label>
          <select name="type" id ="changeStatus" class="form-control">
            @if( app('request')->input('type'))
            <option value = "{{app('request')->input('type')}}" hidden selected>{{app('request')->input('type') }}</option>

            @endif
            <option value ="non-reactive">non-reactive</option>
            <option value ="reactive" >reactive</option>
            <option value ="expired" >expired</option>
            <option value ="distributed">distributed</option>
          </select>
        </div>
        </div>
          <div class="col-md-1">
          <label>Date From </label>
            @if( app('request')->input('dateFrom') )
            @if($errors->has('dateFrom'))
            <input type="text" name="dateFrom" class ="datepicker form-control" value="">
              <span class="help-block">
                  <strong> {{$errors->first('dateFrom')}} </strong>
              </span>
            @else
            <input type="text" name="dateFrom" class ="datepicker form-control" value="{{ app('request')->input('dateFrom') }}">
            @endif
            @else
            <input type="text" name="dateFrom" class ="datepicker form-control" value="{{ old('dateFrom') }}">
            @if ($errors->has('dateFrom'))
              <span class="help-block">
                  <strong> {{$errors->first('dateFrom')}} </strong>
              </span>
            @endif

            @endif
          </div>
          <div class="col-md-1">
          <label>Date To </label>
            @if( app('request')->input('dateTo'))
            @if ($errors->has('dateTo'))
            <input type="text" name="dateTo" class ="datepicker form-control" value="">
              <span class="help-block">
                  <strong> {{$errors->first('dateTo')}} </strong>
              </span>
              @else
              <input type="text" name="dateTo" class ="datepicker form-control" value="{{ app('request')->input('dateTo') }}">
            @endif
            @else
            <input type="text" name="dateTo" class ="datepicker form-control" value="{{ old('dateTo') }}">
            @if ($errors->has('dateTo'))
              <span class="help-block">
                  <strong> {{$errors->first('dateTo')}} </strong>
              </span>
            @endif
            @endif
          </div>
        <div class="col-md-2">
        <div class="form-group">
          <labeL>&nbsp</labeL><br>
          <button type="submit" class="btn btn-danger">Select</button>
        </div>
        </div>
      </form>
      </div>

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
              <td align="center"> {{count($bloodBag->institutionInventory(Auth::guard('web_admin')->user()->institution_id))}} </td>
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
</div>
</div>
@else
<div class="row">
<div class="col-xs-12">
<div class="box box-info  ">
      <div class="box-header">
        <form method ="get" action="{{url('admin/inventory')}}">
        <input type="hidden" name ="sort" value ="true">
        <div class="col-md-2">
        <div class="form-group">
          <label>Status </label>
          <select name="type" id ="changeStatus" class="form-control">
            @if( app('request')->input('type'))
            <option value = "{{app('request')->input('type')}}" hidden selected>{{app('request')->input('type') }}</option>

            @endif
            <option value ="non-reactive">non-reactive</option>
            <option value ="reactive" >reactive</option>
            <option value ="expired" >expired</option>
            <option value ="distributed">distributed</option>
          </select>
        </div>
        </div>
          <div class="col-md-1">
          <label>Date From </label>
            @if( app('request')->input('dateFrom') )
            @if($errors->has('dateFrom'))
            <input type="text" name="dateFrom" class ="datepicker form-control" value="">
              <span class="help-block">
                  <strong> {{$errors->first('dateFrom')}} </strong>
              </span>
            @else
            <input type="text" name="dateFrom" class ="datepicker form-control" value="{{ app('request')->input('dateFrom') }}">
            @endif
            @else
            <input type="text" name="dateFrom" class ="datepicker form-control" value="{{ old('dateFrom') }}">
            @if ($errors->has('dateFrom'))
              <span class="help-block">
                  <strong> {{$errors->first('dateFrom')}} </strong>
              </span>
            @endif

            @endif
          </div>
          <div class="col-md-1">
          <label>Date To </label>
            @if( app('request')->input('dateTo'))
            @if ($errors->has('dateTo'))
            <input type="text" name="dateTo" class ="datepicker form-control" value="">
              <span class="help-block">
                  <strong> {{$errors->first('dateTo')}} </strong>
              </span>
              @else
              <input type="text" name="dateTo" class ="datepicker form-control" value="{{ app('request')->input('dateTo') }}">
            @endif
            @else
            <input type="text" name="dateTo" class ="datepicker form-control" value="{{ old('dateTo') }}">
            @if ($errors->has('dateTo'))
              <span class="help-block">
                  <strong> {{$errors->first('dateTo')}} </strong>
              </span>
            @endif
            @endif
          </div>
        <div class="col-md-2">
        <div class="form-group">
          <labeL>&nbsp</labeL><br>
          <button type="submit" class="btn btn-danger">Select</button>
        </div>
        </div>
      </form>
      </div>

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
              <td align="center"> {{count($bloodBag->institutionInventory(Auth::guard('web_admin')->user()->institution_id))}} </td>
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
@endif
<!-- /.row (main row) --> 


@stop

@section('css')
  <link href="{{asset('theme/css/datepicker3.css')}}" rel="stylesheet">
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
        $(".datepicker").datepicker();
      });
    </script>

@stop
