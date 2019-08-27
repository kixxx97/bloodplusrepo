@extends('landing.master')
@section('title','BloodPlus')

@section('content')
@if (session('status'))
  <div id = "alertmsg" style="display:none">
  {{ session('status') }}
  </div>
@endif
    <header id="home">
        <div class="container">
            <div class="row">
                <div class="col-sm-5">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1 class="bighead">You can save 3 lives.</h1>
                            <h1>BloodPlus is a web and mobile application that connect patients that urgently needs blood and blood donors. </h1>
                            <a href="{{ url('whoweare') }}" class="btn btn-default-home btn-xl ">About Us</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5 col-sm-offset-1">
                    <div class="device-container">
                        <div class="device-mockup iphone6_plus portrait white">
                            <div class="device">
                                <div class="screen">
                                    <!-- Demo image for screen mockup, you can put an image here, some HTML, an animation, video, or anything else! -->
                                    <img src="{{asset('landing/img/bg-hand.png')}}" class="img-responsive" style="position: relative; bottom: 0px;" alt="">
                                </div>
                                <div class="button">
                                    <!-- You can hook the "home button" to some JavaScript events or just remove it -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
  <section id ="carousel">
      <h2 class="sr-headings2">Blood Crowdfunding Project</h2>
    <div class="row" style ="margin-left: 10px">
    @forelse($cards as $card)
      <div class="col-md-3">
      <div class="example-2 card">
        <div class="wrapper" style ="background: url('{{$card->picture}}') center/cover no-repeat">
          <div class="header">
            <div class="date">
              <span class="day">{{$card->date_start->format('F')}}</span>
              <span class="month">{{$card->date_start->format('d')}}</span>
              <span class="year">{{$card->date_start->format('Y')}}</span>
            </div>
          </div>
          <div class="data">
            <div class="content">
              <span class="author">{{$card->initiated->name()}}</span>
              <h1 class="title"><a href="#">{{$card->name}}</a></h1>
              <div class="progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="70"
                aria-valuemin="0" aria-valuemax="100" style="width:70%">
                  <span class="">70% Complete</span>
                </div>
              </div>
              <div class ="text">
                <a href="{{url('/register')}}" class="button">Join Us!</a>
              </div>
              <a href="#" class="button">Join Us!</a>
            </div>
          </div>
      </div>
    </div>
   </div>
  @empty
      <h3 class="sr-headings2">There is no blood crowdfunding activities yet!</h3>
    @endforelse

  </div>
  <br><br>
      <h2 class="sr-headings2">Blood Awareness Drives</h2>
    <div class="row" style ="margin-left: 10px">
    @forelse($eventCards as $card)
      <div class="col-md-3">
      <div class="example-2 card">
        <div class="wrapper" style ="background: url('{{$card->picture}}') center/cover no-repeat">
          <div class="header">
            <div class="date">
              <span class="day">{{$card->date_start->format('F')}}</span>
              <span class="month">{{$card->date_start->format('d')}}</span>
              <span class="year">{{$card->date_start->format('Y')}}</span>
            </div>
          </div>
          <div class="data">
            <div class="content">
              <span class="author">{{$card->initiated->name()}}</span>
              <h1 class="title"><a href="#">{{$card->name}}</a></h1>
              <div>
                  {!! $card->description !!}
              </div>
              <div class ="text">
                <a href="{{url('/register')}}" class="button">Join Us!</a>
              </div>
              <a href="#" class="button">Join Us!</a>
            </div>
          </div>
      </div>
    </div>
   </div>
  @empty
      <h3 class="sr-headings2">There is no blood crowdfunding activities yet!</h3>
    @endforelse
    
  </div>
    </section>
  </div>
    <section id="services">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 text-center">
            <h2 class="section-heading">Partners</h2>
            <hr class="sr-headings2" id="hrpartners">
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-6 text-center">
            <div class="service-box">
              <i class="fa fa-4x usjr text-primary sr-icons"> </i>
              <h3>University of San Jose - Recoletos</h3>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 text-center">
            <div class="service-box">
              <i class="fa fa-4x prc text-primary sr-icons"></i>
              <h3>Philippine Red Cross <br> Cebu Chapter</h3>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 text-center">
            <div class="service-box">
              <i class="fa fa-4x smart text-primary sr-icons"></i>
              <h3>Smart SWEEP</h3>
              <!-- <p class="text-muted">We update dependencies to keep things fresh.</p> -->
            </div>
          </div>

        </div>
      </div>
    </section>

    

    <footer id="myFooter">
        <div class="container">
            <div class="row">
                <div class="col-sm-3">

   
                <h2 class="logo"><a href="#"> LOGO </a></h2>
                </div>

                <div class="col-sm-2">
                    <h5>Get started</h5>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Sign up</a></li>
                    </ul>
                </div>
                <div class="col-sm-2">
                    <h5>About us</h5>
                    <ul>
                        <li><a href="#">Mission</a></li>
                        <li><a href="#">Vision</a></li>
                        <li><a href="#">Team</a></li>
                    </ul>
                </div>
                <div class="col-sm-2">
                    <h5>Services</h5>
                    <ul>
                        <li><a href="#">Request</a></li>
                        <li><a href="#">Donate</a></li>
                        <li><a href="#">Join Campaign</a></li>
                    </ul>
                </div>
                <div class="col-sm-3">
                    <div class="social-networks">
                        <a href="#" class="twitter"><i class="fa fa-twitter"></i></a>
                        <a href="#" class="facebook"><i class="fa fa-facebook"></i></a>
                        <a href="#" class="google"><i class="fa fa-instagram"></i></a>
                    </div>
                    <button type="button" class="btn btn-default-footer">Contact us</button>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <p>© 2016 BloodPlus PH </p>
        </div>
    </footer> 

    <!-- Bootstrap core JavaScript -->
  

  @stop
@section('additional_js')
<script>
$(document).ready(function() {
  var message = document.getElementById('alertmsg').innerHTML;
      if(message != '')
      alert(message);
    });
</script>
@stop
