@extends('adminlte::page')

@section('title', 'Blood Bag Screening')

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
        <h3 class="box-title">Blood Bags Screening</h3>
      </div>
      <form method ="get" action ="{{url('/admin/bloodbags/stage')}}">
        {{ csrf_field() }}
      <div class="box-body table-responsive">
       <table id = "pending_screening" class="table table-hover ">
          <thead>
            <tr>
              <th><input type ="checkbox" id ="checkAll"></th>
              <th>Serial Number</th>
              <th>Donor</th>
              <th>Blood Type</th>  
              <th>Blood Bag Brand</th>
              <th>Blood Bag Type</th>
              <th>Blood Bag Components</th>
              <th>Date Extracted</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bloodBags as $screenedBlood)
              <tr>
                <td><input type="checkbox" class = "bloodbags" name ="bloodbags[]" value ="{{$screenedBlood->id}}"></td>
                <td>{{$screenedBlood->serial_number}}</td>
                <td>{{$screenedBlood->donation->user->name()}}</td>
                <td>{{$screenedBlood->donation->user->bloodType}}</td>
                <td>{{$screenedBlood->bag_type}}</td>
                <td>{{$screenedBlood->bag_component}}</td>
                <td>{{$screenedBlood->componentsToString()}}</td>
                <td>{{$screenedBlood->created_at->format(' F d, Y ')}}</td>
                <td>
                  <a href ="{{url('admin/donate/'.$screenedBlood->donation->id.'/view')}}">
                  <button type="button" value = "{{$screenedBlood->donation->id}}" class="btn-s btn-info"><i class="fa fa-eye"></i></button>
                  </a>
                  <a href ="{{url('admin/bloodbags/'.$screenedBlood->id.'/stage')}}">
                  <button type="button" value = "{{$screenedBlood->donation->id}}" class="btn-s btn-success"><i class="fa fa-check"></i></button>
                  </a>
                </td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
        <div class="container-fluid">
        <button type ="submit" class ="btn btn-danger" value =""/> Complete Screening
        </button>
        <br><br>
        </div>
      </div>
    </form>
    </div>
</div>
</div>
@stop

@section('css')
    <link href="{{asset('theme/js/DataTables/media/css/dataTables.bootstrap.min.css')}}" rel ="stylesheet" type ="text/css">
    <link href="{{asset('theme/js/DataTables/media/css/jquery.dataTables.min.css')}}" rel ="stylesheet" type ="text/css">
    <link href="{{asset('theme/css/datepicker3.css')}}" rel ="stylesheet" type ="text/css">
@stop

@section('js')
    <script type="text/javascript" src="{{asset('theme/js/DataTables/media/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('theme/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('theme/js/dashboard.js') }}"></script>
    <script src ="{{ asset('js/moment.min.js')}}"></script>
    <script src ="{{ asset('js/datetime-moment.js')}}"></script>
    <script> 
      $(document).ready(function() {

      $( "#donateDate" ).datepicker();
      $.fn.dataTable.moment( 'MMMM DD, YYYY' );
      $.fn.dataTable.moment( 'HH:MM AA' );  

      $('#pending_screening').DataTable();
      });

      var message = document.getElementById('alertmsg').innerHTML;
      if(message != '')
      alert(message);
    </script>
@stop