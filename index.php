<!DOCTYPE html>
<html>
<head>
<title>Inventory Management</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="design/css/framework.css">
<link rel="shortcut icon" href="design/images/favicon/favicon-16x16.png" type="image/x-icon">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inconsolata">
<style>
body, html {
  height: 100%;
  font-family: "Inconsolata", sans-serif;
}

.bgimg {
  position: relative;
  background-position: center 65%;
  background-size: cover;
  background-image: url("design/images/pixelcut-export.jpeg");
  min-height: 75%;
}

.bgimg::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 0;
}

.me-display-middle {
  z-index: 1;
}

.KEYFEATURES {
  display: none;
}

::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.2);
  border-radius: 4px;
  border: 2px solid transparent;
  background-clip: padding-box;
}

::-webkit-scrollbar-thumb:hover {
  background-color: rgba(0, 0, 0, 0.3);
}
</style>
</head>
<body>

<div class="me-top">
  <div class="me-row me-padding me-black">
    <div class="me-col s3">
      <a href="#" class="me-button me-block me-black" style="width: 150px; margin-left: 100px;">HOME</a>
    </div>
    <div class="me-col s3">
      <a href="#about" class="me-button me-block me-black" style="width: 150px; margin-left: 100px;">ABOUT</a>
    </div>
    <div class="me-col s3">
      <a href="#KEYFEATURES" class="me-button me-block me-black" style="width: 150px; margin-left: 100px;">KEY FEATURES</a>
    </div>
    <div class="me-col s3">
      <a href="sign-in.php" class="me-button me-block me-black" style="border: 1px solid white; width: 150px; margin-left: 100px;">SIGN-IN</a>
    </div>
  </div>
</div>

<header class="bgimg me-display-container me-grayscale-min" id="home">
  <div class="me-display-middle me-center">
    <span class="me-text-white" style="font-size:90px">Inventory<br>Management</span>
  </div>
</header>

<div class="me-sand me-grayscale me-large">
<div class="me-container" id="about">
  <div class="me-content" style="max-width:700px">
    <h5 class="me-center me-padding-64"><span class="me-tag me-wide">ABOUT THE SYSTEM</span></h5>
    <p>Our Inventory Management System is a comprehensive solution for businesses to manage inventory, orders, and invoices. With real-time tracking, detailed reporting, and user-friendly interfaces, it streamlines operations for both administrators and regular users.</p>
  </div>
</div>

<div class="me-container" id="KEYFEATURES">
  <div class="me-content" style="max-width:700px">
    <h5 class="me-center me-padding-48"><span class="me-tag me-wide">Key Features</span></h5>
  
    <div class="me-row me-center me-card me-padding">
      <a href="javascript:void(0)" onclick="openKEYFEATURES(event, 'userfeatures');" id="myLink">
        <div class="me-col s6 tablink">User Features</div>
      </a>
      <a href="javascript:void(0)" onclick="openKEYFEATURES(event, 'adminfeatures');">
        <div class="me-col s6 tablink">Admin Features</div>
      </a>
    </div>

    <div id="userfeatures" class="me-container KEYFEATURES me-padding-48 me-card">
      <h5>Dashboard</h5>
      <p class="me-text-grey">Interactive dashboard with real-time statistics, charts, and inventory metrics</p>

      <h5>Product Management</h5>
      <p class="me-text-grey">Add, edit, and manage products with detailed tracking of stock levels</p>

      <h5>Order Management</h5>
      <p class="me-text-grey">Create and track orders with automatic stock updates</p>

      <h5>Invoice System</h5>
      <p class="me-text-grey">Generate and manage invoices with payment tracking</p>

      <h5>Reporting Tools</h5>
      <p class="me-text-grey">Generate comprehensive reports for products, orders, and invoices</p>

      <h5>Recycle Bin</h5>
      <p class="me-text-grey">Safely manage deleted products with restore capabilities</p>
    </div>

    <div id="adminfeatures" class="me-container KEYFEATURES me-padding-48 me-card">
      <h5>User Management</h5>
      <p class="me-text-grey">Complete control over user accounts and permissions</p>

      <h5>Provider Management</h5>
      <p class="me-text-grey">Manage supplier information and relationships</p>

      <h5>Admin Dashboard</h5>
      <p class="me-text-grey">Advanced analytics and system-wide monitoring tools</p>
    </div>  
  </div>
</div>
</div>

<footer class="me-center me-light-grey me-padding-48 me-large">
  <p>Made by <a href="https://github.com/migi-gluttony" title="me.CSS" target="_blank" class="me-hover-text-green">@me</a></p>
</footer>

<script>
function openKEYFEATURES(evt, KEYFEATURESName) {
  var i, x, tablinks;
  x = document.getElementsByClassName("KEYFEATURES");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < x.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" me-dark-grey", "");
  }
  document.getElementById(KEYFEATURESName).style.display = "block";
  evt.currentTarget.firstElementChild.className += " me-dark-grey";
}
document.getElementById("myLink").click();
</script>

</body>
</html>