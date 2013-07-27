<?php
include 'global-header.php';
?>

    <form method="post" action="index.php">

      <fieldset class="margin-top-10px p-relative" style="width: 400px; margin: 10px auto;">
        <legend><img src="images/logo-94x32.png"/></legend>

        <?php if( isset($auth_error) ): ?>
        <div class="message-warning">
          <?php echo $auth_error; ?>
        </div>
        <?php endif; ?>

        <img src="images/key-64x64.png" class="p-absolute" style="left: 15px;"/>

        <div class="field">
          <label>Username:</label>
          <span><input type="text" name="cp_username" value="" size="25"></span>
        </div>

        <div class="field">
          <label>Password:</label>
          <span><input type="password" name="cp_password" value="" size="25"></span>
        </div>


        <div class="field">
          <label></label>
          <span>
            <input type="submit" value="Log In">
          </span>
        </div>

      </fieldset>

      <input type="hidden" name="r" value="_xIndexShow"/>
    </form>

<?php
include 'global-footer.php';
?>