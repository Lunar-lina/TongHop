<?php require('./includes/Header.php')?>
<div style="position: relative;">
    <img src="images/6876899.jpg" style="z-index:-1;border-radius: 0px 0px 10px 10px;object-fit: cover;" height='300hv'
      width='100%' max-width='100%' max-height='100%' id="target-section" />
    <div class="Content"
      style="text-align:center;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">

      <?php if (!isset($_COOKIE["Login-Status"]) || $_COOKIE["Login-Status"] != "1"): ?>
        <h1>Nothing to see here...</h1>
        <p>You need to Have an account.</p>
        <button style="border: #000 solid 0px; background-color: #00000000;">
          <div class="interest" onclick="window.location.href='login.php'">Login</div>
        </button>
        <button style="border: #000 solid 0px; background-color: #00000000;">
          <div class="interest" onclick="window.location.href='register.php'">Sign Up</div>
        </button>
      <?php else: ?>
        <h1>Settings</h1>
        <p>Welcome! You Can Change Your Account Settings Here</p>
      <?php endif; ?>
    </div>
  </div>
<?php require('./includes/Footer.php')?>
