@extends('adminlte::page')

@section('title', 'Institutions')


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
        <h3 class="box-title">Branch Blood Banks</h3>
        <a   href ="#" title="Add Blood Bank Branch" href="javascript:void(0)" id="addBloodBank" ><span style="margin-left:10px"><i class="fa fa-plus"></i></span></a>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
      <div id = "app" class="box-body table-responsive no-padding">
        <table id = "pending_requests" class="table table-hover ">
          <thead>
            <tr>
                <th>ID</th>
                <th>Institution</th>
                <th>Contact</th>
                <th>Address</th> 
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
          <tbody>
            @forelse($branches as $institution)
              <tr>
              <td>{{$institution->id}}</td>
              <td>{{$institution->institution}}</td>
              <td>{{$institution->contact}}</td>
              <td>{{$institution->accepted}}</td>
              <td>{{$institution->address['place']}}</td>
              <td>
                @if($institution->accepted == 'Accepted')
                <a href ='{{url("/admin/institution/$institution->id")}}'><button type="button" class="btn-s btn-info decl viewInstitution" data-toggle="modal" data-target="#viewModal"><i class="fa fa-eye"></i></button></a>
                @endif
              </td>
            </tr>
            @empty
            @endforelse
          </tbody>
      </table>
      </div>
	    </div>
    </div>
  </div>
</div>
<div class="modal fade modal-danger" id ="acceptInstitutionModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Accept Institution</h4>
      </div>
      <form id ="acceptForm" class="form-horizontal" role="form" method="POST" action="{{ url('/admin/branch/add') }}"> 
      <div class="modal-body ui-front">
            {!! csrf_field() !!}
        <div class="row">
          <div class="col-md-12">
            <div class="ui-front">
              <input type="hidden" name ="id" id ="insti_id">
              <input type="text" id = "names" name = "term" value = "" class="form-control" placeholder="Search">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type ="submit" class="btn btn-danger" id ="accptBtn">Accept</button> 
        <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
      </div>
      </form>
      </div>
    </div>
</div>

@stop
@section('adminlte_js')
    @stack('js')
    @yield('js')
    <script src="{{ asset('js/institutions.js') }}"></script>
    <script>
        $("#logout").on('click',function (){
            $("#logout-form").submit();
        })

        $("#addBloodBank").on("click",function () {
          $("#acceptInstitutionModal").modal();
        })

        $( "#names" ).autocomplete({
        source: "/institution/searchajax",
        minLength: 2,
        select: function( event, ui ) {
          console.log(ui);
          // $("#search").submit();
          $("#insti_id").val(ui.item.value);
          $("#names").val(ui.item.label);
          console.log(ui);
          return false;
        }
        });
    var message = document.getElementById('alertmsg').innerHTML;
      if(message != '')
      alert(message);
    </script>


@stop
