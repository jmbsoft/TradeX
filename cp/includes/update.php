<?php
include 'global-header.php';
include 'global-menu.php';

require_once 'http.php';

$http = new HTTP();
if( $http->GET(URL_DOWNLOAD . '?get_versions=true') )
{
    $versions = unserialize($http->body);
}


?>

    <div class="centered-header">
      Update Your Installation
    </div>


    <?php if( !empty($http->error) ): ?>
    <div class="message-error ta-center" style="width: 900px; margin-left: auto; margin-right: auto;">
      Unable to connect to jmbsoft.com for update:
      <?php echo htmlspecialchars($http->error); ?>
    </div>
    <?php endif; ?>


    <div style="width: 800px;" class="block-center">

    <div id="no-new-version-message" class="message-warning d-none ta-center">No new version of TradeX is available at this time</div>
    <div id="new-version-message" class="message-notice d-none ta-center">A new version of TradeX is available!</div>

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

    <table class="item-table d-inline-block" cellpadding="4" cellspacing="0" style="width: auto; min-width: 0px; margin-left: 20px;" align="center">
      <thead>
        <tr>
          <td colspan="2" class="ta-center">Latest Version Available</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="ta-right fw-bold" style="padding-right: 4px; width: 180px;">Version Number</td>
          <td id="latest-version" class="ta-right" style="width: 180px;">Checking...</td>
        </tr>
        <tr>
          <td class="ta-right fw-bold" style="padding-right: 4px; width: 180px;">Release Date</td>
          <td id="latest-released" class="ta-right" style="width: 180px;">Checking...</td>
        </tr>
      </tbody>
    </table>

    </div>

    <div class="message-notice" style="width: 550px; margin: 10px auto; background-color: #F6E7FD; border-color: #C38DD4; color: #A25DBA;">
      Some servers do not allow 777 and 666 permissions for directories and files.  This is typically the case
      on servers where PHP is running as a CGI or using suPHP.  If your server is configured this way, check this box:

      <br /><br />

      <input type="checkbox" name="su" value="1" /> Use 755 permissions on directories and 644 permissions on files
    </div>


    <div class="block-center ta-center" style="width: 500px;">
      <span class="fw-bold" style="font-size: 105%;">Select the version you would like to install:</span>
      <select name="version" class="margin-top-bottom-10px">
        <?php echo form_options($versions); ?>
      </select>

      <button id="button-install">Install</button>
    </div>


    <div id="update-progress" class="message-notice ta-center va-middle d-none" style="width: 500px; margin-left: auto; margin-right: auto;">
      <img src="images/activity-16x16.gif"/>
      <span></span>
    </div>



<script type="text/javascript" language="JavaScript">
var installed = {timestamp: '<?php echo TIMESTAMP; ?>'};
$.getScript('http://www.jmbsoft.com/docs/tradex/version.js?' + Math.random(), function()
{
    $('#latest-version').text(latest.version);
    $('#latest-released').text(latest.released);

    if( latest.timestamp > installed.timestamp )
    {
        $('#new-version-message').show();
    }
    else
    {
        $('#no-new-version-message').show();
    }
});


$('#button-install')
.click(function()
{
    if( confirm('Are you sure you want to install this version?') )
    {
        $('#button-install').enable(false);
        downloadInstaller();
    }
});


function resetUpgrade()
{
    $('#update-progress').hide();
    $('#button-install').enable();
}


function downloadInstaller()
{
    $('#update-progress > span').text('Downloading installer from jmbsoft.com...');
    $('#update-progress').show();

    $.ajax({
        data: 'r=_xUpdateGetInstaller&version=' + escape($('select[name="version"]').val()),
        success: function(data)
        {
            if( data[JSON.KEY_STATUS] == JSON.STATUS_SUCCESS )
            {
                extractInstaller();
            }
            else
            {
                resetUpgrade();
            }
        },
        error: resetUpgrade
    });
}


function extractInstaller()
{
    $('#update-progress > span').text('Extracting files from the installer...');

    $.ajax({
        data: 'r=_xUpdateExtractInstaller&su=' + escape($('input[name="su"]').is(':checked') ? 1 : 0),
        success: function(data)
        {
            if( data[JSON.KEY_STATUS] == JSON.STATUS_SUCCESS )
            {
                runPatch();
            }
            else
            {
                resetUpgrade();
            }
        },
        error: resetUpgrade
    });
}


function runPatch()
{
    $('#update-progress > span').text('Patching installation...');

    $.ajax({
        data: 'r=_xPatch',
        success: function(data)
        {
            resetUpgrade();
            $('a[href="_xGlobalSettingsShow"]').click();
            $.growl('Your installation was just updated.  Please check the General Settings ' +
                    'dialog to see if any new settings have been added, and if so set them to ' +
                    'the values you wish to use', {timeout: 10000});
        },
        error: resetUpgrade
    });
}

</script>

<?php
include 'global-footer.php';
?>