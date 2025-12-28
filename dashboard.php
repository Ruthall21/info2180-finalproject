<?php
require 'sess.php';
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dolphin CRM</title>
<link rel="stylesheet" href="styles.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="app-body">


<header class="header">
  <h1>Dolphin CRM</h1>
</header>

<div class="container">
  
  <aside class="sidebar">
    <nav>
      <a href="#" class="active" data-page="dashboardContent">Home</a>
      <a href="#" data-page="addContact">New Contact</a>
      <?php if($_SESSION['role']==='Admin'): ?>
        <a href="#" data-page="viewUsers">Users</a>
      <?php endif; ?>
      <hr>
      <a href="logout.php">Logout</a>
    </nav>
  </aside>

  
  <div class="main-content" id="main-content">
    
  </div>
</div>

<script>

function loadPage(page) {
  $.ajax({
    url: page + '.php',
    method: 'GET',
    success: function(data) {
      $('#main-content').html(data);
    }
  });
}


loadPage('newUser'); 


$('.sidebar a').click(function(e){
  e.preventDefault();
  $('.sidebar a').removeClass('active');
  $(this).addClass('active');
  const page = $(this).data('page');
  loadPage(page);
});

</script>

<script>
$(document).on('click', '.ajax-link', function (e) {
  e.preventDefault();

  const page = $(this).data('page');

  
  
  $('.sidebar a').removeClass('active');
  $('.sidebar a[data-page="' + page + '"]').addClass('active');

  loadPage(page);
});
</script>

</body>
</html>

