@extends('adminlte::page')

@section('title', 'Settings')


@section('content') 
@if (session('status'))
  <div id = "alertmsg" style="display:none">
    {{ session('status') }}
  </div>
@endif

@if($hq)
<div class="row">
  <div class="col-md-8 col-md-offset-2 ">

          <!-- Profile Image -->
          <div class="box box-info">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="{{$hq->picture()}}" onError="this.onerror=null;this.src='{{ asset('assets/img/bloodplus/Logo2.png') }}" alt="User profile picture">

              <h3 class="profile-username text-center">{{$hq->name()}}</h3>
            </div>
            <!-- /.box-body -->
          </div>

          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs nav-justified">
              <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="false">Profile</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="activity">
                <div class="box-body">
              <h4><strong><i class="fa fa-book margin-r-5"></i> General Information</strong></h4>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Institution</b> <a class="pull-right">{{$hq->name()}}</a>
                </li>
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right">{{$hq->email}}</a>
                </li>
                <li class="list-group-item">
                  <b>Contact</b> <a class="pull-right">0{{$hq->contact}}</a>
                </li>
                <li class="list-group-item">
                  <b>Website</b> <a class="pull-right">{{$hq->links['website']}}</a>
                </li>
                <li class="list-group-item">
                  <b>Facebook</b> <a class="pull-right">{{$hq->links['facebook']}}</a>
                </li>
                <li class="list-group-item">
                  <b>Twitter</b> <a class="pull-right">{{$hq->links['twitter']}}</a>
                </li>
              </ul>
              <br>
              <h4><strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong></h4>
              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Location</b> <a class="pull-right">{{$hq->address['place']}}</a>
                </li>
              </ul>
              <h4><strong><i class="fa fa-file-text-o margin-r-5"></i> About us</strong></h4>  
              {!!$hq->about_us!!}
                </div>
              </div>
              @if(Auth::guard('web_admin')->user()->institute->accepted == 'Pending')
              <form action ="{{url('/admin/hq/remark')}}" method ="post">
                <div class="row">
                  {{csrf_field()}}
                <div class="col-md-6">
                <button type ="submit" name ="remark" value ="accept" class="btn btn-danger form-control">Accept as your Commanding Blood Bank</button>
                </div>
                <div class="col-md-6">
                <button type ="submit" name ="remark" value ="decline" class="btn btn-danger form-control">Decline as your Commanding Blood Bank</button>
                </div>
                </div>
              </form>
              @endif
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
</div>
</div>
@else
@endif
@stop

@section('css')
@stop
@section('js')
      <script src="{{ asset('theme/js/dashboard.js') }}"></script>
       
@stop