<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<h1>Login</h1>

  @if($errors->any())
   @foreach($errors->all() as $error)
    <p style="color:red;">{{ $error }}</p>
   @endforeach
  @endif

  @if(Session::has('error'))
   <p style="color:red;">{{ Session::get('error') }}</p>
  @endif
  
  <form action="{{ route('userLogin') }}" method="POST">
   @csrf

   <input type="email" name="email" placeholder="Enter email" id="">
   <br><br>
   <input type="password" name="password" placeholder="Enter password" id="">
   <br><br>
   <input type="submit" value="Login">
  </form>