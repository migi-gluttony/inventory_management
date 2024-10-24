<!DOCTYPE html>
<html>
<head>
<title>inventory management</title>
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
  background-color: rgba(0, 0, 0, 0.5); /* Dark overlay */
  z-index: 0; /* Place behind the text */
}

.me-display-middle {
  z-index: 1; /* Ensure text is above the overlay */
}

.KEYFEATURES {
  display: none;
}
/* scroll bar style */

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
}/*# sourceMappingURL=style.css.map */
</style>
</head>
<body>

<!-- Links (sit on top) -->
<div class="me-top">
  <div class="me-row me-padding me-black">
    
    <div class="me-col s3">
      <a href="#" class="me-button me-block me-black" style=" width: 150px; margin-left: 100px ;">HOME</a>
    </div>
    <div class="me-col s3">
      <a href="#about" class="me-button me-block me-black" style=" width: 150px; margin-left: 100px ;">ABOUT</a>
    </div>
    <div class="me-col s3">
      <a href="#KEYFEATURES" class="me-button me-block me-black" style=" width: 150px; margin-left: 100px ;">KEY FEATURES</a>
    </div>
    <div class="me-col s3">
      <a href="sign-in.php" class="me-button me-block me-black" style="border: 1px solid white; width: 150px; margin-left: 100px ;">SIGN-IN</a>
    </div>
  </div>
</div>

<!-- Header with image -->
<header class="bgimg me-display-container me-grayscale-min" id="home">
  <div class="me-display-middle me-center">
    <span class="me-text-white" style="font-size:90px">inventory<br>management</span>
  </div>
</header>


<!-- Add a background color and large text to the whole page -->
<div class="me-sand me-grayscale me-large">

<!-- About Container -->
<div class="me-container" id="about">
  <div class="me-content" style="max-width:700px">
    <h5 class="me-center me-padding-64"><span class="me-tag me-wide">ABOUT THE WEBSITE</span></h5>
    <p>&nbsp Our Inventory Management System is an easy-to-use platform designed to help businesses efficiently manage their inventory, users, and providers. It offers a streamlined experience for keeping track of products, monitoring key statistics, and managing user and provider information, all in one convenient place.</p>
  </div>
</div>

<!-- KEYFEATURES Container -->
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
      <p class="me-text-grey">The main page displays key statistics and recent activities.</p><br>
    
      <h5>Products Management</h5>
      <p class="me-text-grey">Users can view, edit, or delete products. Deleted products are moved to a "Deleted Products" page, where they can be restored or permanently deleted.</p><br>
    
      <h5>Add Products</h5>
      <p class="me-text-grey">Users can add new products to the inventory with ease. Providers are pre-defined by the admin for easy selection.</p><br>
    
      <h5>Deleted Products</h5>
      <p class="me-text-grey">This page allows users to manage removed products, with options to either restore them or permanently delete them.      </p><br>
    
      <h5>Reports</h5>
      <p class="me-text-grey">Users can generate and download PDF reports of products, deleted products, users, and providers.</p>
    </div>

    <div id="adminfeatures" class="me-container KEYFEATURES me-padding-48 me-card">
      <h5>User Management</h5>
      <p class="me-text-grey">Admins have full control over user accounts, including adding, editing, and deleting users.</p><br>
    
      <h5>Provider Management</h5>
      <p class="me-text-grey">Admins can also manage the list of providers for the inventory system.</p><br>
    </div>  
  </div>
</div>
</div>

<!-- Footer -->
<footer class="me-center me-light-grey me-padding-48 me-large">
  <p>made by <a href="https://github.com/migi-gluttony" title="me.CSS" target="_blank" class="me-hover-text-green">@me</a></p>
</footer>

<script>
// Tabbed KEYFEATURES
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
