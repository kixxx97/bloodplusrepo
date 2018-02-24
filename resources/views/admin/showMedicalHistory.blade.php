@extends('adminlte::page')

@section('title', 'Donate')


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
  </div>
      <!-- /.box-header -->
      <div class="box-body">
      <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
      <form action ="{{url('admin/donate/'.$donate->id).'/'.$medicalHistory->id.'/remark'}}" method ="post">
        {{csrf_field()}}
      <div class="tab-content"> 
        <div class="tab-pane active" id = "tab_1">
          <div id = "app" class="box-body table-responsive no-padding">
            <table class ="table" >
        <thead>
          <tr>
          <th class="col-xs-1">No.</th>
          <th class="col-sm-6">Questions</th>
          <th class="col-sm-1">Yes</th>
          <th class="col-sm-1">No</th>
          <th class="col-sm-2">Remarks</th>
        </tr>
        </thead>
        @if($medicalHistory->medical_history == null)
        <tbody>
          <tr>
          <td align="center">&nbsp</td>
          <td></td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">1</td>
          <td>Do you feel well and healthy today?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">2</td>
          <td>In the past <b>4 WEEKS</b> have you taken any medications and/or vaccination?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">3</td>
          <td>In the last <b>3 DAYS</b> have you taken aspirin??</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td><b>IN THE PAST 3 MONTHS HAVE YOU:</b></td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">4</td>
          <td><b>Donated whole blood, platelets or plasma?</b></td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td><b>IN THE PAST 12 MONTHS HAVE YOU:</b></td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">5</td>
          <td>Received blood, blood products and/or had tissue/organ transplant or graft</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">6</td>
          <td>Had surgical operation or dental extraction?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">7</td>
          <td>Had a tattoo applied, ear and body piercing, acupuncture, needle stick injury or accidental contact with blood?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">8</td>
          <td>Had sexual contact with high risks and individuals or in exchange for material or monetary gain</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">9</td>
          <td>Engaged in unprotected, unsafe or casual sex</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">10</td>
          <td>Had jaundice/hepatitis/ personal contact with person who had hepatitis?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">11</td>
          <td>Been incarcerated, jailed or imprisoned?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">12</td>
          <td>Spent time or have relatives in the United Kingdom or Europe?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td><b>HAVE YOU EVER:</b></td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">13</td>
          <td>Travelled or lived-in outside of your place of residence of the Philippines?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">14</td>
          <td>Taken prohibited drugs, (orally, by nose or by injection)</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">15</td>
          <td>Used clotting factor concentrates?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">16</td>
          <td>Had a positive test for the HIV, Hepatitis virus, Syphilis and Malaria?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">17</td>
          <td>Had Malaria or hepatitis in the past?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">18</td>
          <td>Had or was treated for genital wart, syphilis, gonorrhea or other sexually transmitted diseases?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">19</td>
          <td><b>HAD ANY OF THE FOLLOWING</b></td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td>Cancer, blood disease or bleeding disorder [ haemophilia ]?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td>Heart disease/surgery, rheumatic fever of chest pains?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td>Kidney disease, thyroid disease, diabetes, epilepsy?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td>Chicken pox and/or cold sores?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td>Any other chronic medical condition or surgical operations?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">20</td>
          <td>Have you recently had rash and/or fever? Was/were this/these also associated with arthralgia or arthritis or conjunctivitis?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">20</td>
          <td>Have you recently had rash and/or fever? Was/were this/these also associated with arthralgia or arthritis or conjunctivitis?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td><b>IN THE PAST 6 MONTHS HAVE YOU:</b></td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">21</td>
          <td>Been to any places in the Philippines or countries infected with ZIKA Virus?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">22</td>
          <td>Had sexual contact with a person who was confirmed to have ZIKA virus infection?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">23</td>
          <td>Had sexual contact with a eprson who has been to any places in the Philippines or countries infected with ZIKA Virus?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">24</td>
          <td>Are you giving blood only because you want to be tested for HIV / Aids virus or Hepatitis virus?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">25</td>
          <td>Are you <b>aware</b> that an HIV / Hepatitis infected person can still transmit the virus, despite a negative HIV/ Hepatitis test?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">26</td>
          <td>Have you within the last <b>12 HOURS</b> had taken liquor, beer or any drinks with alcohol ?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">27</td>
          <td>Have you even been refused as a blood donor or told not to donate blood for any reasons?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td align="center">FOR FEMALE DONORS ONLY: </td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">28</td>
          <td>Are you currently pregnant?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">29</td>
          <td>Have you even been pregnant? </td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">30</td>
          <td>When was your last delivery?</td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">31</td>
          <td>Did you have an abortion in the past <b>1 YEAR</b></td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          <td align="center">32</td>
          <td>When was your last menstrual period? <b>&nbsp&nbspDate:</b> </td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
        </tr>
        <tr>
          
        </tr>
        @else
          <tr>
          <td align="center">&nbsp</td>
          <td></td>
          <td align="center"></td>
          <td align="center"></td>
          <td></td>
          </tr>
          @foreach($medicalHistory->medical_history as $key => $data)
            @if(!is_Numeric($key))
            <tr>
              @if($key == 'Had any of the following')
              <td align ="center">{{$count++}}</td>
              @else
              <td>&nbsp</td>
              @endif
              <td><b>{{$key}}</b></td>
              <td></td>
              <td></td>
              <td></td> 
            </tr>
            @endif
            @foreach($data as $question => $answer)
            <tr>
              @if($key != 'Had any of the following' || is_Numeric($key))
              <td align="center">{{$count++}}</td>
              @else
              <td align="center"></td>
              @endif
              <td>{{$question}}</td>
              @if($answer['answers'] == 'no')
              <td align="center"></td>
              <td align="center">No</td>
              @else
              <td align="center">Yes</td>
              <td align="center"></td>
              @endif
              <td><input type="text" class="table-no-borders-td" value ="{{$answer['remarks']}}"> </td>
            </tr>
            @endforeach

          @endforeach
        @endif  

      </tbody>
      </table>
            <br>
            <div>
            <center>
              <button type="submit" class="btn btn-danger" name = "remark" value ="true">Passed</button>
              <button type="submit" class="btn btn-danger" name = "remark" value ="false">Failed</button>
              <a href='{{url("/admin/donate/".$donate->id."/view/pdf")}}'>
              <button type="button" class="btn btn-danger pull-right" name = "remark" >View PDF</button>
              </a>
            </center>
            </div>
            </form>
            </br>
          </div>
        </div>
      </div>
      </ul>
      </div>
      </div>
    </div>
</div>
</div>
@stop
@section('css')
<style>
 .table td, .table thead, .table th{

  border: 1px solid black !important;
 }
 .table > thead > tr > th {
  text-align: center;
 }
 .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th
 {
  padding-top: 0px;
  padding-bottom: 0px;
 }
 .table-no-borders-td {
  display: block; 
  padding: 0; 
  margin: 0; 
  border: 0; 
  width: 100%;
 }
</style>
@stop
@section('js')
      <script src="{{ asset('theme/js/dashboard.js') }}"></script>
       
@stop