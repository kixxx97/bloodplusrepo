 @extends('adminlte::master')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/auth.css') }}">
    @yield('css')
@stop

@section('body_class', 'register-page')

@section('body')
<div class="container">
    <div class="row">
        <section>
        <div class="wizard">
            <div class="wizard-inner">
                <div class="connecting-line"></div>
                <ul class="nav nav-tabs" role="tablist">

                    <li role="presentation" class="active">
                        <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1">
                            <span class="round-tab">        
                                <i class="glyphicon glyphicon-folder-open"></i>
                            </span>
                        </a>
                    </li>

                    <li role="presentation" class="disabled">
                        <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </span>
                        </a>
                    </li>

                     <li role="presentation" class="disabled">
                        <a href="#step3" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-folder-close"></i>
                            </span>
                        </a>
                    </li>

                    <li role="presentation" class="disabled">
                        <a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-ok"></i>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <form role="form" method="post" action ="{{url('admin/register')}}">
                {{ csrf_field() }}
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel" id="step1">
                        <div style ="margin-left:3%;margin-right:3%">
                            <div class="row">
                            <label for="name" class="col-md-4 control-label">Institution</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control" name="institution_name" value="{{ old('institution_name') }}" required autofocus>

                                    @if ($errors->has('institution_name'))
                                        <span class="help-block">
                                            <strong> {{$errors->first('institution_name')}} </strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <label class="col-md-4 control-label">Address</label>
                                <div class="col-md-12">
                                <input id="searchTextField" type="text" class ="form-control" placeholder="Enter a location" autocomplete="on" runat="server" name ="exactcity"/>  
                                <input type="hidden" id="cityLat" name="cityLat" />
                                <input type="hidden" id="cityLng" name="cityLng" />

                                    @if ($errors->has('exactcity'))
                                        <span class="help-block">
                                            <strong> {{ $errors->first("exactcity")}} </strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                            <label for="name" class="col-md-4 control-label">Contact Number</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control" name="contact" value="{{ old('contact') }}" placeholder = "0925XXXXXXX" required autofocus>

                                    @if ($errors->has('contact'))
                                        <span class="help-block">
                                            <strong> {{$errors->first("contact")}} </strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                            <label for="name" class="col-md-4 control-label">Email Address</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control" name="email_address" value="{{ old('email_address')}}" required autofocus>

                                    @if ($errors->has('email_address'))
                                        <span class="help-block">
                                            <strong> {{$errors->first("email_address")}} </strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <ul class="list-inline pull-right">
                            <br>
                                <li><button type="button" class="btn btn-primary next-step">Save and continue</button></li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane" role="tabpanel" id="step2">
                        <div style ="margin-left:3%;margin-right:3%">
                            <div class="row">
                            <label for="name" class="col-md-4 control-label">Patient Directed</label>
                                <div class="col-md-12">
                                    <div class="radio">
                                      <label>
                                        <input type="radio" class = "reactive" name="reactive" value="true" required>
                                        Yes
                                      </label>
                                      <label>
                                        <input type="radio" class = "reactive" name="reactive"  value="false">
                                        No
                                      </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                              <label for="name" class="col-md-4 control-label">Blood Categories Available</label>
                              <div class="col-md-12">
                              <div class="checkbox">
                                <label>
                                  <input type="checkbox" name ="blood_bag_categories[]" value ="Whole Blood">
                                  Whole Blood
                                </label>
                                <label>
                                  <input type="checkbox" name ="blood_bag_categories[]" value ="Packed RBC">
                                  Packed RBC
                                </label>
                                <label>
                                  <input type="checkbox" name ="blood_bag_categories[]" value ="Washed RBC">
                                  Washed RBC
                                </label>
                                <label>
                                  <input type="checkbox" name ="blood_bag_categories[]" value ="Platelet" >
                                  Platelets
                                </label>
                                <label>
                                  <input type="checkbox" name ="blood_bag_categories[]" value ="Fresh Frozen Plasma">
                                  Fresh Frozen Plasma
                                </label>
                                <label>
                                  <input type="checkbox" name ="blood_bag_categories[]" value ="Cryoprecipitate">
                                  Cryoprecipitate
                                </label>
                              </div>
                              </div>
                            </div>
                            <div id ="blood_bag_brands" class="row">
                            <label for="name" class="col-md-12 control-label">Blood Bag Brands<a href ="#" title="Add Blood Bag Brand" id = "add_blood_bag" style="margin-left:3%"><i class="fa fa-plus"></i></a></label>
                            <div class="col-md-4">
                              <input type="text" class="form-control" name="bag_brand[0]" required autofocus>
                              <div class="checkbox">
                                <label>
                                  <input type="checkbox" name ="bag_qty[0][]" value ="450s">450s</label>
                                <label>
                                  <input type="checkbox" name ="bag_qty[0][]" value ="450d">
                                  450d
                                </label>
                                <label>
                                  <input type="checkbox" name ="bag_qty[0][]" value ="450t">
                                  450t
                                </label>
                                <label>
                                  <input type="checkbox" name ="bag_qty[0][]" value ="450q">
                                  450q
                                </label>
                              </div>
                            </div>
                            </div>
                        
                            <ul class="list-inline pull-right">
                            <br>
                                <li><button type="button" class="btn btn-default prev-step">Previous</button></li>
                                <li><button type="button" class="btn btn-primary btn-info-full next-step">Save and continue</button></li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane" role="tabpanel" id="step3">
                        <div style ="margin-left:3%;margin-right:3%">
                            <div class="row">
                            <label for="name" class="col-md-4 control-label">About us</label>
                                <div class="col-md-12">
                                    <textarea class="textarea form-control" name="about_us" placeholder="Description" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                            <label for="name" class="col-md-2 control-label">Social Media Links</label>
                                <div class ="row">
                                    <div class="col-md-12" style ="margin-bottom:2%">
                                        <label for="name" class="col-md-1 control-label text-right">Facebook</label>
                                        <div class ="col-md-10">
                                        <input type="text" class="form-control" name="facebook" value="{{old('facebook')}}" required >
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="col-md-12" style ="margin-bottom:2%">
                                        <label for="name" class="col-md-1 control-label text-right">Website</label>
                                        <div class ="col-md-10">
                                        <input type="text" class="form-control" name="website" value="{{old('website')}}" required >
                                        </div>
                                    </div>
                                    <div class="col-md-12" style ="margin-bottom:2%">
                                    
                                        <label for="name" class="col-md-1 control-label text-right">Twitter</label>
                                        <div class ="col-md-10">
                                        <input type="text" class="form-control" name="twitter" value="{{old('twitter')}}" required >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <ul class="list-inline pull-right">
                            <br>
                                <li><button type="button" class="btn btn-default prev-step">Previous</button></li>
                                <li><button type="button" class="btn btn-primary btn-info-full next-step">Save and continue</button></li>
                            </ul>
                        </div>
                    <div class="tab-pane" role="tabpanel" id="complete">
                        <div style ="margin-left:3%;margin-right:3%">
                            <div class="row">
                            <label for="name" class="col-md-4 control-label">Username</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control" name="username" value="{{old('username')}}" required autofocus>
                                </div>
                            <label for="name" class="col-md-4 control-label">Password</label>
                                <div class="col-md-12">
                                    <input type="password" class="form-control" name="password" value="{{old('password')}}" required autofocus>
                                </div>
                            </div>
                            <ul class="list-inline pull-right">
                            <br>
                                <li><button type="button" class="btn btn-default prev-step">Previous</button></li>
                                <li><button type="submit" class="btn btn-primary btn-info-full next-step">Complete Registration</button></li>
                            </ul>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div>
    </section>
   </div>
</div>

@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}"></script>
    <script src="{{ asset('theme/js/wizard.js') }}"></script>
    <script src="http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyAAnRpyGUpGwoIqAlWZgXPaMtrBoogMuWc" type="text/javascript"></script>
    <script type="text/javascript">
        $(".textarea").wysihtml5({
        "font-styles": true, 
        "emphasis": true, 
        "lists": true,
        "html": false,
        "link": false, 
        "image": false
        });
        var ctr = 1;

        $("#add_blood_bag").on("click", function()
        {
            // alert(ctr);
            // console.log(string);
            $("#blood_bag_brands").append($('<div>', {
                    class: 'col-md-4'
                    }).append(
                        $('<input>').attr({
                        type: 'text',
                        class: "form-control",
                        name: 'bag_brand['+ctr+']',
                        required: 'true'}))
                    .append($('<div>', {
                        class: 'checkbox'
                    }).append($('<label>')
                    .append($('<input>').attr({
                        type: 'checkbox',
                        value: '450s',
                        name: 'bag_qty['+ctr+'][]'
                    })).append('450s'))
                    .append($('<label>')
                    .append($('<input>').attr({
                        type: 'checkbox',
                        value: '450d',
                        name: 'bag_qty['+ctr+'][]'
                    })).append('450d'))
                    .append($('<label>')
                    .append($('<input>').attr({
                        type: 'checkbox',
                        value: '450t',
                        name: 'bag_qty['+ctr+'][]'
                    })).append('450t'))
                    .append($('<label>')
                    .append($('<input>').attr({
                        type: 'checkbox',
                        value: '450q',
                        name: 'bag_qty['+ctr+'][]'
                    })).append('450q'))
                    )
                );
            ctr++;        
            });

        initialize();
    function initialize() {
        var input = document.getElementById('searchTextField');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            document.getElementById('cityLat').value = place.geometry.location.lat();
            document.getElementById('cityLng').value = place.geometry.location.lng();
            //alert("This function is working!");
            //alert(place.name);
           // alert(place.address_components[0].long_name);
        });
    }
            //put another blood bag brand, required ang mga fuckers
    </script>
    @yield('js')
@stop
