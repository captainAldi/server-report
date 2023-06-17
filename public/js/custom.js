// Custom Function

// For Logout
function logoutUser() {
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, Log Out!'
  }).then((result) => {
    if (result.isConfirmed) {
      $('#logout-form').submit()
    }
  })
}

// For Dark Mode
var toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
var currentTheme = localStorage.getItem('theme');
var mainHeader = document.querySelector('.main-header');

if (currentTheme) {
  if (currentTheme === 'dark') {
    if (!document.body.classList.contains('dark-mode')) {
      document.body.classList.add("dark-mode");
    }
    if (mainHeader.classList.contains('navbar-light')) {
      mainHeader.classList.add('navbar-dark');
      mainHeader.classList.remove('navbar-light');
    }
    toggleSwitch.checked = true;
  }
}

function switchTheme(e) {
  if (e.target.checked) {
    if (!document.body.classList.contains('dark-mode')) {
      document.body.classList.add("dark-mode");
    }
    if (mainHeader.classList.contains('navbar-light')) {
      mainHeader.classList.add('navbar-dark');
      mainHeader.classList.remove('navbar-light');
    }
    localStorage.setItem('theme', 'dark');
  } else {
    if (document.body.classList.contains('dark-mode')) {
      document.body.classList.remove("dark-mode");
    }
    if (mainHeader.classList.contains('navbar-dark')) {
      mainHeader.classList.add('navbar-light');
      mainHeader.classList.remove('navbar-dark');
    }
    localStorage.setItem('theme', 'light');
  }
}

toggleSwitch.addEventListener('change', switchTheme, false);
