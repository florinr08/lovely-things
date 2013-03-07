<?php
include_once(HOME_DIR . 'scripts/headTagsSnippet.php');
echo showDOCTYPE();
echo headTagsSnippet('ThePo!ntC.O.C. - Log In', false);
?>
  <link rel="StyleSheet" href="<?php echo HOME_DIR; ?>css/login.css" type="text/css" />
  <script type="text/javascript">
  </script>
</head>
<!-- HEADTAGS -->
<?php
if(!correctLogin()) echo '<body onload="document.getElementById(\'username\').focus()">'.PHP_EOL;
else echo '<body>'.PHP_EOL;
?>

<!-- HEADER -->
<?php
include_once(HOME_DIR . 'scripts/headerSnippet.php');
echo headerSnippet();
?>
<!-- HEADER -->

<!-- CONTENT -->
<div id="content">
<div id="contentHolder" class="dotted">
<?php
include_once(HOME_DIR . 'scripts/navigationSnippet.php');
echo navigationSnippet();
?>
<div class="dottedBreak dotted"></div>
<h1>Log In</h1>
<div class="dottedBreak dotted"></div>
<?php
if (isset($_REQUEST['failed'])) {
  echo '<p>Login failed: wrong username or password?</p>', PHP_EOL;
}
if(!correctLogin()) { ?>
<form action="<?php echo HOME_DIR; ?>intermedLogin.php" method="post">
  <div>
    <div class="loginFormLabels">
      <span><label for="username">Username:</label></span><br />
      <span><label for="password">Password:</label></span><br />
    </div>
    <div class="loginFormFields">
      <input type="hidden" name="login" />
      <input type="text" name="username" id="username" class="field" placeholder="username" /><br />
      <input type="password" name="password" id="password" class="field" placeholder="password" /><br />
      <input type="checkbox" name="remember" id="remember" /><span><label for="remember">Always remember me</label></span><br />
      <input type="submit" value="Login" /><br />
    </div>
    <div class="break"></div>
  </div>
</form>
<?php }
else { ?>
<div>
  <p>You are logged in as <?php echo getCurrentFullName(); ?> ('<?php echo getCurrentUsername(); ?>').
  <a href="<?php echo HOME_DIR; ?>intermedLogin.php?logout">Logout</a>?</p>
</div>
<?php } ?>
<div class="dottedBreak dotted break"></div>
</div>
</div>
<!-- CONTENT -->

<!-- FOOTER -->
<?php
include_once(HOME_DIR . 'scripts/footerSnippet.php');
echo footerSnippet();
?>
<!-- FOOTER -->

</body>
</html>