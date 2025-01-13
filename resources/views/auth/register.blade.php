<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>INSPINIA | Register</title>
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
  <link href="{{ asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
  <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body class="gray-bg">
  <div class="middle-box loginscreen animated fadeInDown text-center">
    <div>
      <div>
        <img src="{{ asset('img/regis-removebg-preview.png') }}" alt="dashboard" class="img-fluid float-right">
      </div>
      <h3>Register to Cermat</h3>
      <p>Create account to see it in action.</p>

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form class="m-t" role="form" method="POST" action="{{ route('post.register') }}">
        @csrf
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Name" name="name" required>
        </div>
        <div class="form-group">
          <input type="email" class="form-control" placeholder="Email" name="email" required>
        </div>
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Phone Number" name="phone_number" required>
        </div>
        <!-- Province Dropdown -->
        <div class="form-group">
          <select class="form-control" id="province" name="province" required>
            <option value="">Pilih Provinsi</option>
          </select>
        </div>
        <!-- Regency Dropdown -->
        <div class="form-group">
          <select class="form-control" id="regency" name="regency" required disabled>
            <option value="">Pilih Kabupaten/Kota</option>
          </select>
        </div>
        <div class="form-group">
          <input type="password" class="form-control" placeholder="Password" name="password" required>
        </div>
        <div class="form-group">
          <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation"
            required>
        </div>
        <div class="form-group">
          <div class="checkbox i-checks">
            <label>
              <input type="checkbox" name="terms" required><i></i> Agree to the terms and policy
            </label>
          </div>
        </div>
        <button type="submit" class="btn btn-primary full-width m-b block">Register</button>

        <p class="text-muted text-center"><small>Already have an account?</small></p>
        <a class="btn btn-sm btn-white btn-block" href="{{ route('login') }}">Login</a>
      </form>
      <p class="m-t"> <small>Inspinia web app framework based on Bootstrap 3 &copy; 2014</small> </p>
    </div>
  </div>

  <!-- Mainly scripts -->
  <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
  <script src="{{ asset('js/popper.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.js') }}"></script>
  <!-- iCheck -->
  <script src="{{ asset('js/plugins/iCheck/icheck.min.js') }}"></script>

  <!-- Add Province/Regency Script -->
  <script>
    $(document).ready(function() {
      // Initialize iCheck
      $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
      });

      // Fetch Provinces
      fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
        .then(response => response.json())
        .then(provinces => {
          const provinceSelect = document.getElementById('province');
          provinces.forEach(province => {
            const option = document.createElement('option');
            option.value = province.name; // Changed from province.id to province.name
            option.textContent = province.name;
            option.dataset.id = province.id; // Store ID in data attribute for reference
            provinceSelect.appendChild(option);
          });
        })
        .catch(error => console.error('Error fetching provinces:', error));

      // Handle Province Change
      $('#province').change(function() {
        const selectedOption = this.options[this.selectedIndex];
        const provinceId = selectedOption.dataset.id; // Get ID from data attribute
        const regencySelect = $('#regency');

        // Clear existing regencies
        regencySelect.empty().append('<option value="">Select Regency</option>');

        if (provinceId) {
          // Enable regency select
          regencySelect.prop('disabled', false);

          // Fetch Regencies for selected province
          fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
            .then(response => response.json())
            .then(regencies => {
              regencies.forEach(regency => {
                const option = document.createElement('option');
                option.value = regency.name; // Changed from regency.id to regency.name
                option.textContent = regency.name;
                regencySelect.append(option);
              });
            })
            .catch(error => console.error('Error fetching regencies:', error));
        } else {
          // If no province is selected, disable regency select
          regencySelect.prop('disabled', true);
        }
      });
    });
  </script>
</body>

</html>
