<!DOCTYPE html>
<html>
<head>
	<title>Verify BloodPlus</title>
</head>
<body>
	<h1>Hi there! Thank you for registering to BloodPlus. </h1>
	To verify your account, click this <a href ="{{url('/verifyemail/'.$user->email_token)}}">link</a> to verify your account.<br>
	or<br>
	This<a href="{{url('/verifyemail/'.$user->email_token)}}">{{url('/verifyemail/'.$user->email_token)}}</a><br>

	----------------------------------------------------------------------------------------------------------------------------------<br>
This message is automatically generated by BloodPlusPH Email Tool.<br>
If you have received this email in error, please notify us through this email: bloodplusph@gmail.com
</body>
</html>