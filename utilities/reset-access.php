<?php

require_once 'includes/functions.php';
require_once 'global-header.php';

$password = get_random_password();
file_write(FILE_CP_USER, 'administrator|' . sha1($password));
cp_session_cleanup(true);
?>

    <div class="block-center margin-top-bottom-10px" style="width: 550px;">
      Your TradeX control panel login information has been set and is listed below.
      Please bookmark the control panel and write down both the username and password
      for safe keeping!

      <br /><br />

      <a href="index.php">TradeX Control Panel</a><br />
      <b>Username:</b> administrator<br />
      <b>Password:</b> <?php echo $password; ?>
    </div>

<?php
require_once 'global-footer.php';
?>