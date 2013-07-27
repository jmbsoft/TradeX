var item_type = 'toplists';

$(function()
{
    $('img[src="images/delete-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one toplist to delete');
            return;
        }

        if( confirm('Are you sure you want to delete the selected toplists?') )
        {
            $.ajax({toolbarIcon: this,
                    data: 'r=_xToplistsDeleteBulk&toplist_id=' + selected.join(','),
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

    $('img[src="images/build-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one toplist to build');
            return;
        }

        if( confirm('Are you sure you want to build the selected toplists?') )
        {
            $.ajax({toolbarIcon: this,
                    data: 'r=_xToplistsBuild&toplist_id=' + selected.join(',')});
        }
    });

});