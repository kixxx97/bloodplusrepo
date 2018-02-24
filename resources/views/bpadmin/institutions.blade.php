@extends('adminlte::bpage')

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
        <h3 class="box-title">Institutions</h3>
  </div>
      <!-- /.box-header -->
      <div class="box-body">
      <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_1" data-toggle="tab">Active</a></li>
        <li><a href="#tab_2" data-toggle="tab">Pending</a></li>
      </ul>
      <div class="tab-content"> 
      <div class="tab-pane active" id = "tab_1">
      <div id = "app" class="box-body table-responsive no-padding">
        <table id = "pending_requests" class="table table-hover ">
          <thead>
            <tr>
                <th>ID</th>
                <th>Institution</th>
                <th>Date</th>
                <th>Contact Information</th>
                <th>Address</th> 
                <th>Actions</th>
            </tr>
        </thead>
          <tbody>
          @forelse($activeInstitutions as $institution)
            <tr>
              <td>{{$institution->id}}</td>
              <td>{{$institution->institution}}</td>
              <td>{{$institution->created_at}}</td>
              <td>{{$institution->contact}}</td>
              <td>{{$institution->address['place']}}</td>
              <td><a href ='{{url("/bpadmin/institution/$institution->id")}}'><button type="button" class="btn-s btn-info decl viewInstitution" data-toggle="modal" data-target="#viewModal"><i class="fa fa-eye"></i></button></a>
              </td>
            </tr>
            @empty
          @endforelse
          </tbody>
        </table>
      </div>
      </div>
      <div class="tab-pane" id = "tab_2">
      <div id = "app" class="box-body table-responsive no-padding">
        <table id = "pending_requests" class="table table-hover ">
          <thead>
            <tr>
                <th>ID</th>
                <th>Institution</th>
                <th>Date</th>
                <th>Contact Information</th>
                <th>Address</th> 
                <th>Actions</th>
            </tr>
        </thead>
          <tbody>
          @forelse($pendingInstitutions as $institution)
            <tr>
              <td>{{$institution->id}}</td>
              <td>{{$institution->institution}}</td>
              <td>{{$institution->created_at}}</td>
              <td>{{$institution->contact}}</td>
              <td>{{$institution->address['place']}}</td>
              <td><a href ='{{url("/bpadmin/institution/$institution->id")}}'><button type="button" class="btn-s btn-info decl viewInstitution" data-toggle="modal" data-target="#viewModal"><i class="fa fa-eye"></i></button></a>
              <button type="button" value = "{{$institution->id}}" class="btn-s btn-success acceptInstitution"><i class="fa fa-check" aria-hidden="true"></i></button>
              <button type="button" value = "{{$institution->id}}" class="btn-s btn-danger decl declineInstitution"><i class="fa fa-times" aria-hidden="true"></i></button>
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
      <form id ="acceptForm" class="form-horizontal" role="form" method="POST" action="{{ url('/bpadmin/institution/accept') }}"> 
      <div class="modal-body">
        <div id = "view" class="bootstrap-timepicker"> 
            {!! csrf_field() !!}
            <input type="hidden" id ="acceptId" name ="id" /> 
            <div class="form-group">
            <label style ="margin-left:3%" class="control-label">Terms:</label>
            <br>
            <br>
            <p style ="margin-left:3%;color: black">You can proceed to accept via notify users, this would notify  to all your available donors. <br> Or reply to the requestor for updates.</p><br>
            <br>
            <p style ="margin-left:3%;color: black" id = "recommended"></p>
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
<div class="modal fade modal-danger" id ="declineInstitutionModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Decline Request Form</h4>
      </div>
      <form id ="deleteForm" class="form-horizontal" role="form" method="POST" action="{{ url('/bpadmin/institution/delete') }}"> 
      <div class="modal-body">
        <div id = "view" class="bootstrap-timepicker"> 
            {!! csrf_field() !!}
            <input type="hidden" id ="acceptId" name ="id" />
            <div class="form-group" style ="padding: 10px">
            <label style ="margin-left:3%" class="control-label">Terms:</label>
            <br>

            <p style ="margin-left:3%;color: black">Upon deletion of this request, it will no longer be able to be processed.</p>
            </div>
          </div>
        </div>
      <div class="modal-footer">
        <input type="submit" name = "submitRequest" class="btn btn-danger" value="Submit">
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
    var message = document.getElementById('alertmsg').innerHTML;
      if(message != '')
      alert(message);
    </script>


@stop
