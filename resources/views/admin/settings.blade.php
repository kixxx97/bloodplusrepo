@extends('adminlte::page')

@section('title', 'Settings')


@section('content') 
@if (session('status'))
  <div id = "alertmsg" style="display:none">
    {{ session('status') }}
  </div>
@endif

<div class="row">
  <div class="col-md-8 col-md-offset-2 ">

          <!-- Profile Image -->
          <div class="box box-info">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="{{Auth::guard('web_admin')->user()->picture}}" onError="this.onerror=null;this.src='{{ asset('assets/img/bloodplus/Logo2.png') }}" alt="User profile picture">

              <h3 class="profile-username text-center">{{Auth::guard('web_admin')->user()->name()}}</h3>
            </div>
            <!-- /.box-body -->
          </div>

          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs nav-justified">
              <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="false">Profile</a></li>
 
              <li class=""><a href="#settings" data-toggle="tab" aria-expanded="true">Settings</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active " id="activity">
                <div class="box-body">
              <h4><strong><i class="fa fa-book margin-r-5"></i> General Information</strong></h4>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Institution</b> <a class="pull-right">{{$institution->name()}}</a>
                </li>
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right">{{$institution->email}}</a>
                </li>
                <li class="list-group-item">
                  <b>Contact</b> <a class="pull-right">0{{$institution->contact}}</a>
                </li>
                <li class="list-group-item">
                  <b>Website</b> <a class="pull-right">{{$institution->links['website']}}</a>
                </li>
                <li class="list-group-item">
                  <b>Facebook</b> <a class="pull-right">{{$institution->links['facebook']}}</a>
                </li>
                <li class="list-group-item">
                  <b>Twitter</b> <a class="pull-right">{{$institution->links['twitter']}}</a>
                </li>
              </ul>
              <br>
              <h4><strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong></h4>
              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Location</b> <a class="pull-right">{{$institution->address['place']}}</a>
                </li>
              </ul>
              <h4><strong><i class="fa fa-file-text-o margin-r-5"></i> About us</strong></h4>  
              {!!$institution->about_us!!}
            </div>
              </div>
              <!-- /.tab-pane -->

              <!-- /.tab-pane -->

              <div class="tab-pane" id="settings">
                <h3> Blood Type Components </h3>
                <div class="row">
                @foreach($institution->settings['bloodtype_available'] as $key => $bloodType)
                <div class ="col-md-4">
                  <input type="text" class="form-control" name="" value = "{{$bloodType}}" autofocus>
                  </div>
                  @endforeach
                </div>

                <h3> Blood Bag and Blood Bag Components </h3>
                <div class="row">
                @foreach($institution->settings['bloodbags'] as $key => $bloodbags)
                <div class ="col-md-4">
                  <input type="text" class="form-control" name="bag_brand[{{$key}}]" value = "{{$key}}" autofocus>
                  <div class="checkbox">
                  @foreach($bloodbags as $component)
                  <label>
                    <input type="checkbox" name ="bag_qty[0][]" value ="450s">{{$component}}</label>
                  @endforeach
                  </div>
                </div>
                @endforeach
              </div>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
</div>
</div>
@stop

@section('css')
@stop
@section('js')
      <script src="{{ asset('theme/js/dashboard.js') }}"></script>
       
@stop