@extends('adminlte::page')

@section('title', 'Donate')

@section('content')


<div class="row">
  <div class="col-xs-6 col-md-offset-3">
    <div class="box box-info  ">
      <div class="box-header">
        <center>
        <h3 class="box-title">Complete Screening of Blood Bag</h3>
        </center>
      </div>

      <div class="box-body">
        @if($single)
        <form role="form" method="POST" action="{{ url('/admin/bloodbags/'.$screenedBloodBags->id.'/stage') }}">
        <div class = "row">
          <div class ="col-md-4">
          <label class="control-label">Serial Number:</label>
          <input type ="text" class ="form-control text-underline" value ="{{$screenedBloodBags->serial_number}}" readonly/>
          </div>
          <div class ="col-md-4">
          <label class="control-label">Donor Name:</label>
          <input type ="text" class ="form-control text-underline" value ="{{$screenedBloodBags->donation->user->name()}}" readonly/>
          </div>
          <div class ="col-md-2">
          <label class="control-label">Blood Type:</label>
          <input type ="text" class ="form-control text-underline" value ="{{$screenedBloodBags->donation->user->bloodType}}" readonly/>
          </div>
          <div class ="col-md-2">
          <label class="control-label">Blood Bag:</label>
          <input type ="text" class ="form-control text-underline" value ="{{$screenedBloodBags->bag_type}}" readonly/>
          </div>
          
        </div>
        @else
        <form role="form" method="POST" action="{{ url('/admin/bloodbags/stage') }}">
          @forelse($screenedBloodBags as $bloodBag)
          <input type = "hidden" name ="bloodbag[]" value ="{{$bloodBag->id}}">
          @empty
          @endforelse
          <div class = "row">
          <div class ="col-md-4">
          <label class="control-label">Serial Number:</label>
          <select id ="changeDonor" class ="form-control">
          @forelse($screenedBloodBags as $bloodBag)
          <option value ="{{$bloodBag->id}}" data-name="{{$bloodBag->donation->user->name()}}" data-type="{{$bloodBag->donation->user->bloodType}}" data-brand="{{$bloodBag->bag_type}}">{{$bloodBag->serial_number}}</option>
          @empty
          @endforelse
          </select>
          </div>
          <div class ="col-md-4">
          <label  class="control-label">Donor Name:</label>
          <input id ="donor_name" type ="text" class ="form-control text-underline" value ="{{$firstScreen->donation->user->name()}}" readonly/>
          </div>
          <div class ="col-md-2">
          <label class="control-label">Blood Type:</label>
          <input id ="donor_type" type ="text" class ="form-control text-underline" value ="{{$firstScreen->donation->user->bloodType}}" readonly/>
          </div>
          <div class ="col-md-2">
          <label  class="control-label">Blood Bag:</label>
          <input id ="donor_bag" type ="text" class ="form-control text-underline" value ="{{$firstScreen->bag_type}}" readonly/>
          </div>
          
        </div>
          
        @endif
        <br>
        <h4>Is the blood reactive?</h4>
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
        
        <h4>Medical Complication<h4>
        <div class="radio">

            <select name="diagnose" class="form-control  diagnose">
            <option value='' disabled hidden selected> Select Complication</option>
            <option value="HIV Positive">HIV Postive</option>
            <option value="Hepatitis B">Hepatitis B</option>
            <option value="Hepatitis C">Hepatitis C</option>
            <option value="Malari">Malaria</option>
            <option value="Syphilis">Syphilis</option><!-- 
            <!-- 
            <option value="Cryoprecipitate">Cryoprecipitate</option>
            <option value="resh Frozen Plasma">Fresh Frozen Plasma</option> -->
            </select>
            </div>

      <!--     <label>
            <input type="radio" class = "diagnose" name="diagnose" value="Hepatitis B" >
            Hepatitis B
          </label>
          <label>
            <input type="radio" class = "diagnose" name="diagnose" value="Hepatitis C" >
            Hepatitis C
          </label>
          <label>
            <input type="radio" class = "diagnose" name="diagnose" value="Disease 3" >
            Hepatitis C
          </label>
          <label>
            <input type="radio" class = "diagnose" name="diagnose" value="Disease 4" >
            Hepatitis C
          </label>
          <label>
            <input type="radio" class = "diagnose" name="diagnose" value="Disease 5" >
            Hepatitis C
          </label>
          <label>
            <input type="radio" class = "diagnose" name="diagnose" value="Disease 6" >
            Hepatitis C
          </label>
 -->
        <!-- if 450q pili siya -->
    
        <!-- endif -->

        {!! csrf_field() !!}

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
    <script type="text/javascript" src="{{asset('theme/js/DataTables/media/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('theme/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('theme/js/dashboard.js') }}"></script>

    <script> 
      $(document).ready(function() {
        $(".reactive").change(function () {
          if(this.value == 'true')
          {
            $(".diagnose").attr("disabled",false);  
          }
          else
          {
            $(".diagnose").attr("disabled",true);
            $(".diagnose").prop("selectedIndex", 0);
          }
        });
        $("#changeDonor").change(function() {
          var name = $(this).find(':selected').data('name');
          var type = $(this).find(':selected').data('type');
          var brand = $(this).find(':selected').data('brand');
          $("#donor_name").val(name);
          $("#donor_type").val(type);
          $("#donor_bag").val(brand);
        });
      });
    </script>
@stop