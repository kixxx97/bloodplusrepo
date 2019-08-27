@extends('adminlte::page')

@section('title', 'Donate')

@section('content') 
@if (session('status'))
  <div id = "alertmsg" style="display:none">
    {{ session('status') }}
  </div>

@endif

<div class="row">
  <div class="col-xs-6 col-md-offset-3">
    <div class="box box-info  ">
      <div class="box-header">
        <center>
          <h3 class="box-title"><b>Complete Blood Donation</b></h3>
        </center>
      </div>

      <div class="box-body">
        <form role="form" method="POST" action="{{ url('/admin/donate/'.$donate->id.'/complete') }}">
        <div class = "row">
          <div class ="col-md-4">
          <label cl`ass="control-label">Donor Name:</label>
          <input type ="text" class ="form-control text-underline" value ="{{$donate->user->name()}}" readonly/>
          </div>
          <div class ="col-md-2">
          <label class="control-label">Blood Type:</label>
          <select name ="bloodType" class ="form-control text-underline">>
          @if($donate->user->bloodType)
          <option value = "{{$donate->user->bloodType}}" hidden selected>{{$donate->user->bloodType}}</option>
          @endif
          <option value ="A+">A+</option>
          <option value ="A-">A-</option>
          <option value ="B+">B+</option>
          <option value ="B-">B-</option>
          <option value ="AB+">AB+</option>
          <option value ="AB-">AB-</option>
          <option value ="O+">O+</option>
          <option value ="O-">O-</option>
          </select>
          </div>
          <div class ="col-md-2">
          <label class="control-label">Gender:</label>
          <input type ="text" class ="form-control text-underline" value ="{{$donate->user->gender}}" readonly/>
          </div>
        </div>
        <br> 
        <label class="control-label">Blood Bag Brand:</label>
        <div class="radio">
          @foreach(Auth::guard('web_admin')->user()->institute->settings['bloodbags'] as $key => $bloodbag)
          <label style ="margin-left: 1%;margin-right: 1%">
          @if(old('bag_type') == $key)
          <input type="radio" class ="blood_bag_brand" name="bag_type" value="{{$key}}" checked required>
            {{$key}}
          @else
          <input type="radio" class ="blood_bag_brand" name="bag_type" value="{{$key}}" required>
            {{$key}}
          @endif
          </label>
          @endforeach
        </div>
        <label class="control-label">Blood Bag Component:</label>
        <div class="radio">
          <!-- listen sa pag ilis sa blood bag brand, nya ajax(kuhaon ang ilahang na settan nga component para kani nga brand sa bag) then if wala i disable ang radio lang. -->
          <?php
          $bloodbags = Auth::guard('web_admin')->user()->institute->settings['bloodbags'];
          $components = collect($bloodbags)->first();
          ?>
            @foreach($components as $component)
              <label>
              @if(old('bag_component') == $component)
              <input type="radio" class="bag_component" name="bag_component" value="{{$component}}" checked required>
              {{$component}}
              @else
              <input type="radio" class="bag_component" name="bag_component" value="{{$component}}" required>
              {{$component}}
              @endif
              </label>
            @endforeach
          
        </div>
        <div class ="row">
          <div class ="col-md-4">
          <label class="control-label">Serial Number:</label>
          <input type ="number" class="form-control text-underline" name ="serial_number" placeholder="Input Here" />
          </div>
        </div>
        @if ($errors->has('serial_number'))
            <span class="help-block">
              <strong>{{ $errors->first('serial_number') }}</strong>
            </span>
          @endif
        {!! csrf_field() !!}
        <div class="form-group">
          <br>
          <label class="control-label">In Demand:</label>
          <div id ="updates">
            @forelse($updates as $update)
              <li>{{$update}}</li>
            @empty

            @endforelse
          </div>
        </div>
        <center>
        <input type="submit" value="Complete" class="btn btn-danger">
        <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
        </center>
        </form>
              
  	</div>
</div>
</div>
@stop

@section('js')
<script>
  $(document).ready(function() {

      $(".blood_bag_brand").on("click", function () {
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
        method: "post",
        url: "/admin/bloodbags/components",
        data: {'_token': CSRF_TOKEN,
               'bloodbag': $(this).val()
               },
        success: function(response) {
          $.each($(".bag_component"), function(index,value) 
          {
            if(response.includes($(this).val()))
            {
              $(this).attr("disabled",false);  
            }
            else
            {
              $(this).attr("disabled",true);  
            }
          });
        },
        error: function() {
            alert('An error occured.');
        }
        });
      });
        console.log($(this).val());
      });
      var message = document.getElementById('alertmsg').innerHTML;
      if(message != '')
      alert(message);
</script>
@stop