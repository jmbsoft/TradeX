<?php
include 'global-header.php';
include 'global-menu.php';
?>

<script language="JavaScript">
$(function()
{
    var dirty = false;
    var $last_loaded = null;

    // Prompt user if they did not save template changes
    window.onbeforeunload = function()
    {
        return (dirty ? 'The currently loaded template has not been saved' : undefined);
    };

    // Setup XHR to save template
    $('#template-form')
    .ajaxForm({toolbarIcon: 'img[src="images/save-32x32.png"]',
               success: function() { dirty = false; }});

    // Handle resizing window
    $(window)
    .resize(function()
    {
        var wh = $(window).height();
        var tbh = $('#toolbar').outerHeight() + 20;
        var offset = $('#file-list').offset();

        $('#file-list, #template-code')
        .css({height: wh - tbh - offset.top + 'px'});
    })
    .resize();

    // Mark template code as changed
    $('#template-code')
    .livequery('change', function()
    {
        dirty = true;
    });

    // Load a template
    $('#file-list > div')
    .click(function()
    {
        var $clicked = $('> span', this);
        var template = $clicked.text();

        if( !dirty || confirm('The currently loaded template has not been saved, are you sure you want to continue?') )
        {
            $('#template-code').remove();
            $('#template-loading').show();
            $('#template-loaded').hide();
            dirty = false;

            $.ajax({data: 'r=_xSiteTemplatesLoad&template=' + escape(template),
                    success: function(data)
                             {
                                 if( data[JSON.KEY_STATUS] == JSON.STATUS_SUCCESS )
                                 {
                                     $('<textarea name="template_code" id="template-code" wrap="off">' + data.code + '</textarea>')
                                     .prependTo('#template-form')
                                     .css({height: $('#file-list').innerHeight() + 'px'});

                                     $('#loaded-template').text(template);
                                     $('#template').val(template);
                                     $('#template-loading').hide();
                                     $('#template-loaded').show();

                                     $last_loaded = $clicked;
                                 }
                             }});
        }
    });


    // Reload a template
    $('img[src="images/reload-32x32.png"]')
    .click(function()
    {
        if( !$last_loaded )
        {
            alert('No template is currently loaded for editing');
            return;
        }

        $last_loaded.trigger('click');
    });


    // Save template
    $('img[src="images/save-32x32.png"]')
    .click(function()
    {
        if( !$('#template').val() )
        {
            alert('No template is currently loaded for editing');
            return;
        }

        $('#template-form').submit();
    });
});
</script>

    <div id="icon-menu-filter">
      <input type="text" size="16">
    </div>

    <table id="editor-table" cellpadding="0" cellspacing="0" align="center" width="90%">
      <tr>
        <td style="width: 20em;" valign="top">
          <div class="header">Select Template</div>

          <div id="file-list">
            <?php
            $templates = string_htmlspecialchars(dir_read_files(DIR_TEMPLATES, REGEX_SITE_TEMPLATES, true));
            foreach( $templates as $template ):
            ?>
            <div>
              <img src="images/source-16x16.png"/>
              <span style="padding-left: 4px;" title="<?php echo $template; ?>"><?php echo $template; ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </td>

        <td style="padding-left: 10px;" valign="top">
          <div id="template-loading" class="d-none"><img src="images/activity-32x32.gif" class="va-middle" /> <span class="va-middle">Loading template...</span></div>
          <div id="template-loaded" class="d-none">
            <div class="header">Template Code: <span id="loaded-template"></span></div>
            <form action="xhr.php" method="post" id="template-form">
              <textarea name="template_code" id="template-code" wrap="off"></textarea>
              <input type="hidden" name="template" id="template" value=""/>
              <input type="hidden" name="r" value="_xSiteTemplatesSave"/>
            </form>
          </div>
        </td>
      </tr>
    </table>

    <div id="toolbar">
      <div id="toolbar-content">
        <div id="toolbar-icons">
          <a href="_xSiteTemplatesReplaceShow" class="dialog" title="Search and Replace"><img src="images/search-replace-32x32.png" border="0" /></a>
          <img src="images/toolbar-separator-2x32.png"/>
          <img src="images/save-32x32.png" class="action" title="Save">
          <img src="images/reload-32x32.png" class="action" title="Refresh">
          <img src="images/toolbar-separator-2x32.png"/>
          <a href="docs/templates-site.html" title="Documentation" target="_blank"><img src="images/help-32x32.png" border="0" /></a>
        </div>
      </div>
    </div>

    <div id="toolbar-vspacer"></div>

<?php
include 'global-footer.php';
?>
