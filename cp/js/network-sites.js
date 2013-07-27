var item_type = 'network-sites';

$(function()
{
    $('#network-sites')
    .tablesorter({
        cssHeader: 'headerSort',
        widgets: ['zebra'],
        headers: {0: {sorter: false}, 5: {sorter: false}}
    });


    $('a.cp-login')
    .click(function()
    {
        var url = $(this).attr('cpurl');
        var user = $(this).attr('cpuser');
        var pass = $(this).attr('cppass');

        $('#form-network-login input[name="cp_username"]').val(user);
        $('#form-network-login input[name="cp_password"]').val(pass);
        $('#form-network-login').attr('action', url).submit();

        return false;
    });


    $('img[src="images/edit-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one network site to edit');
            return;
        }

        $.overlay.show(document);
        $.ajax({data: 'r=_xNetworkSitesBulkEditShow&domain=' + selected.join(',')});
    });


    $('img[src="images/delete-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one network site to delete');
            return;
        }

        if( confirm('Are you sure you want to delete the selected network sites?') )
        {
            $.ajax({toolbarIcon: this,
                    data: 'r=_xNetworkSitesDeleteBulk&domain=' + selected.join(','),
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
});