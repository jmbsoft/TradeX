var item_type = 'skim-schemes';

$(function()
{
    $('img[src="images/delete-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one skim scheme to delete');
            return;
        }

        if( confirm('Are you sure you want to delete the selected skim schemes?') )
        {
            $.ajax({toolbarIcon: this,
                    data: 'r=_xSkimSchemesDeleteBulk&scheme=' + selected.join(','),
                    success: function()
                             {
                                 $.each(selected, function(index, item)
                                                  {
                                                      $('table.item-table tbody tr[id="item-'+item+'"]')
                                                      .remove();

                                                      $('#num-items')
                                                      .decrementText();
                                                  });
                             }});
        }
    });

    $('img[src="images/save-32x32.png"]')
    .click(function()
    {
        $('#skim-schemes-form').ajaxSubmit({toolbarIcon: this});
    });

});