<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<p id="message_error" style="color:red;"></p>
<p id="message_success" style="color:green;"></p>

<form method="post" id="verificationForm">
 @csrf 
 <input type="hidden" name="email" value="{{ $email }}">
 <input type="number" name="otp" placeholder="Enter OTP" required>
 <br><br>
 <input type="submit" value="Verify">
</form>

<p class="time"></p>

<button id="resendOtpVerification">Resend Verifcation OTP</button>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<script>
 $(document).ready(function(){
  $('#verificationForm').submit(function(e){
   e.preventDefault();

   var formData = $(this).serialize();

   $.ajax({
    url:"{{ route('verificationOtp') }}",
    type:"POST",
    data: formData,
    success:function(res){
     if(res.success){
      alert(res.msg);
      console.log(res.msg);
      window.open("/", "_self");
     }
     else{
      $('#message_error').text(res.msg);
      setTimeout(() => {
       $('#message_error').text('');
      }, 3000);
     }
    }
   });
  });

  $('#resendOtpVerification').click(function(){
   $(this).text("Wait.....");
   var userMail = @json($email);

   $.ajax({
     url:"{{ route('resendOtp') }}", // Add a comma here
     type:"GET",
     data: {email:userMail},
     success:function(res){
      $('#resendOtpVerification').text('Resend Verification OTP');
      if(res.success){
       timer();
       $('#message_success').text(res.msg);
       setTimeout(() => {
         $('#message_success').text('');
       }, 3000);
      }
      else{
       $('#message_error').text(res.msg);
       setTimeout(() => {
        $('#message_error').text(res.msg);
       }, 3000);
      }
     }
   })
  })
 })
</script>
