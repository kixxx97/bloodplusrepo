@extends('adminlte::page')

@section('title', $user->name())

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
        <div class="col-md-12">
          <!-- The time line -->
          <ul class="timeline">
            <!-- timeline time label -->
              <li class="time-label">
                    <span class="bg-red">
                      {{$user->name().' Logs'}}
                    </span>
              </li>
              <li>
                <i class="fa fa-gift bg-fuchsia"></i>

                <div class="timeline-item">

                  <div class="timeline-body">
                    @if(count($user->donations) != 0)
                    {{count($user->donations).' Blood Donations'}}
                    @else
                    Have not yet donated his blood
                    @endif
                  </div>
                </div>
              </li>
              <li>
                <i class="fa fa-plus bg-maroon"></i>

                <div class="timeline-item">

                  <div class="timeline-body">
                    @if(count($user->requests) != 0)
                    {{count($user->requests).' Blood Requests'}}
                    @else
                    Have not requested for blood.
                    @endif
                  </div>
                </div>
              </li>
              <li>
                <i class="fa fa-check bg-purple"></i>

                <div class="timeline-item">

                  <div class="timeline-body">
                    @if(count($user->attendance) != 0)
                    {{count($user->attendance).' Campaigns Joined'}}
                    @else
                    Have not joined our campaigns yet!
                    @endif
                  </div>
                </div>
              </li>
              <li>
                <i class="fa fa-flag bg-darkred"></i>

                <div class="timeline-item">

                  <div class="timeline-body">
                  {{$user->created_at->format('F d Y')}} joined your institution
                  </div>
                </div>
              </li>
          </ul>
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
        window.location = $(this).data("href");
        });
      });
    </script>

@stop
