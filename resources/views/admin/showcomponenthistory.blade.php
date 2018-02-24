@extends('adminlte::page')

@section('title', $title)

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
            @if($type == 'bloodCategory')
              @forelse($logs as $key => $log)
              <li class="time-label">
                    <span class="bg-red">
                    {{\Carbon\Carbon::parse($key)->format('l\, F d Y')}}
                    </span>
              </li>
              @if($log['expired'] != 0)
              <li>
                <i class="fa fa-times bg-darkred"></i>

                <div class="timeline-item">

                  <div class="timeline-body">
                    @if($log['expired'] ==  1)
                    1 blood bag was expired
                    @else
                    {{$log['expired']}} blood bags were expired.
                    @endif
                  </div>
                </div>
              </li>
              @endif
              @if($log['sold'] != 0)
              <li>
                <i class="fa fa-gift bg-fuchsia"></i>

                <div class="timeline-item">

                  <div class="timeline-body">
                    @if($log['sold'] == 1)
                    1 blood bag was given to a requester
                    @else
                    {{$log['sold']}} blood bags were given to requesters.
                    @endif
                  </div>
                </div>
              </li>
              @endif
              @if($log['available'] != 0)
              <li>
                <i class="fa fa-plus bg-maroon"></i>

                <div class="timeline-item">

                  <div class="timeline-body">
                    @if($log['available'] == 1)
                    1 blood bag was marked non reactive and is ready to be given.
                    @else
                    {{$log['available']}} blood bags were marked non-reactive and are ready to be given.
                    @endif
                  </div>
                </div>
              </li>
              @endif
              @if($log['unavailable'] != 0)
              <li>
                <i class="fa fa-times bg-darkred"></i>

                <div class="timeline-item">

                  <div class="timeline-body">
                    @if($log['unavailable'] == 1)
                    1 blood bag was marked reactive after screening
                    @else
                    {{$log['unavailable']}} blood bags were reactive upon screening.
                    @endif
                  </div>
                </div>
              </li>
              @endif
              @if($log['started'] != 0)
              <li>
                <i class="fa fa-check bg-purple"></i>

                <div class="timeline-item">

                  <div class="timeline-body">
                    {{$log['started']}} blood bags have started screening.
                  </div>
                </div>
              </li>
              @endif
              @empty
              <li class="time-label">
                    <span class="bg-red">
                    {{\Carbon\Carbon::now()->format('l\, F d Y')}}
                    </span>
              </li>
              <li>
                <i class="fa fa-question bg-gray"></i>

                <div class="timeline-item">

                  <div class="timeline-body">
                    Nothing happened for this blood category yet.
                  </div>
                </div>
              </li>
              @endforelse
            @else
            @forelse($logs as $fKey => $log)
              <li class="time-label">
                    <span class="bg-red">
                    {{\Carbon\Carbon::parse($fKey)->format('l\, F d Y')}}
                    </span>
              </li>
              <?php
              $prevKey = null;
              ?>
              @forelse($log as $sKey => $entry)
              @forelse($entry as $tKey =>$specific)
              
                @if($sKey == 'expired')
                <li>
                @if($prevKey != $sKey)  
                <i class="fa fa-times bg-darkred"></i>
                @endif
                <div class="timeline-item">

                  <div class="timeline-body">
                    @if($specific == 1)
                    A {{$tKey}}  blood bag was expired
                    @else
                    {{$specific.' '.$tKey}} blood bags were expired.
                    @endif
                  </div>
                </div>
                </li>
                @elseif($sKey == 'sold')
                <li>
                @if($prevKey != $sKey)  
                <i class="fa fa-gift bg-fuchsia"></i>
                @endif
                <div class="timeline-item">

                  <div class="timeline-body">
                    @if($specific == 1)
                    A {{$tKey}}  blood bag was given to a requester
                    @else
                    {{$specific.' '.$tKey}} blood bags were given to requesters.
                    @endif
                  </div>
                </div>
                </li>
                @elseif($sKey == 'available')
                <li>
                @if($prevKey != $sKey)  
                <i class="fa fa-plus bg-maroon"></i>
                @endif
                <div class="timeline-item">

                  <div class="timeline-body">
                    @if($specific == 1)
                    A {{$tKey}}  blood bag was marked non reactive and is ready to be given.
                    @else
                    {{$specific.' '.$tKey}} blood bags were marked non-reactive and are ready to be given.
                    @endif
                  </div>
                </div>
                </li>
                @elseif($sKey == 'unavailable')
                <li>
                @if($prevKey != $sKey)   
                <i class="fa fa-times bg-darkred"></i>
                @endif
                <div class="timeline-item">

                  <div class="timeline-body">
                    @if($specific == 1)
                    A {{$tKey}} blood bag was marked reactive after screening
                    @else
                    {{$specific.' '.$tKey}} blood bags were reactive upon screening.
                    @endif
                  </div>
                </div>
              </li>
                @elseif($sKey == 'started')
                <li>
                @if($prevKey != $sKey)  
                <i class="fa fa-check bg-purple"></i>
                @endif
                <div class="timeline-item">

                  <div class="timeline-body">
                    @if($specific == 1)
                    A {{$tKey}} blood bag was marked reactive after screening
                    @else
                    {{$specific.' '.$tKey}} blood bags were reactive upon screening.
                    @endif
                  </div>
                </div>
                </li>
                @endif
                <?php
                  if($prevKey == null)
                  $prevKey = $sKey;
                  else if($prevKey != $entry)
                  $prevKey = $sKey;

                ?>
              @empty
              @endforelse
              @empty
              <h1>aaa</h1>
              @endforelse
            @empty
              <h1>ccc</h1>
            @endforelse
            @endif
          </ul>
          <h1>awdwas</h1>
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
