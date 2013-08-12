<?php
include 'global-header.php';
include 'global-menu.php';
?>

    <div class="centered-header">
      TradeX Version Information <P>
	  
	  
	      <table class="item-table d-inline-block" cellpadding="4" cellspacing="0" style="width: auto; min-width: 0px; margin-left: 14px;" align="center">
      <thead>
        <tr>
          <td colspan="2" class="ta-center">Installed Version</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="ta-right fw-bold" style="padding-right: 4px; width: 180px;">Version Number</td>
          <td class="ta-right" style="width: 180px;"><?php echo VERSION; ?></td>
        </tr>
        <tr>
          <td class="ta-right fw-bold" style="padding-right: 4px; width: 180px;">Release Date</td>
          <td class="ta-right" style="width: 180px;"><?php echo RELEASED; ?></td>
        </tr>
      </tbody>
    </table>
	
	<P>
	
	<iframe src="http://www.unofficialjmbsupport.com/iframenews.html" style="width: 95%; margin-left: 10px; margin-right: 10px;" frameborder="0"></iframe>
	  
	  
    </div>

<?php
include 'global-footer.php';
?>