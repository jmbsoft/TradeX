var unknowns = null;
var total_unknowns = 0;
var item_type = null;

$(function()
{
    $('#shown-items').text($('#network-stats tbody tr').length);

    $('.icon-menu-container')
    .iconmenu();

     var table_pos = $('table#network-stats')
                     .offset();

    var $thead = $('#thead-float')
                 .css({/*top: table_pos.top + 'px', */left: table_pos.left + 'px'});


    $($.browser.msie ? window : document)
    .scroll(function()
    {
        var st = $(this).scrollTop();

        if( st > table_pos.top )
        {
            $thead
            //.css({top: table_pos.top + st - table_pos.top  + 'px'})
            .show();
        }
        else
        {
            $thead
            .hide();
        }
    });


    $(window)
    .resize(function()
    {
        table_pos = $('table#network-stats')
                    .offset();

        $thead
        .css({/*top: table_pos.top + 'px', */left: table_pos.left + 'px'});
    });


    $('#network-stats')
    .tablesorter({
        cssHeader: 'headerSort',
        widgets: ['zebra'],
        headers: {0: {sorter: false}}
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


    $('#site-action-menu > div > div')
    .click(function()
    {
        var fnc = $(this).attr('fnc');

        if( !fnc )
        {
            switch( $(this).attr('js') )
            {
                case 'refresh':
                    startProcessUnknowns($(this).parents('tr').each(function() { markUnknown($(this)); }).get());
                    break;
            }
        }
        else
        {
            var conf = $(this).attr('confirm');
            var domain = $(this).parents('tr').attr('id').replace(/^item-/, '');

            if( fnc.match(/Show$/) )
            {
                $.overlay.show(document);
            }

            if( !conf || confirm(conf) )
            {
                $.ajax({data: 'r='+fnc+'&domain=' + domain});
            }
        }
    });


    // Refresh stats
    $('img[src="images/reload-32x32.png"]')
    .click(function()
    {
        startProcessUnknowns($('#network-stats tbody tr').each(function() { markUnknown($(this)); }).get());
    });


    // Delete site
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

    startProcessUnknowns($('#network-stats tbody tr.unknown').get());
});

function startProcessUnknowns(input)
{
    unknowns = input;
    total_unknowns = unknowns.length;
    markUnknown($('tfoot tr'), true);
    processUnknowns();
}

function processUnknowns()
{
    if( unknowns.length > 0 )
    {
        var $tr = $(unknowns.shift());
        var domain = $tr.attr('id').replace(/^item-/, '');

        $('#stats-loading-notice').show();
        $('#stats-loading-site').html($('span.site-info', $tr).attr('domain'));
        $('#stats-loading-current').html(total_unknowns - unknowns.length);
        $('#stats-loading-total').html(total_unknowns);

        $.ajax({
            data: 'r=_xNetworkStatsGet&domain=' + domain,
            success: function(data)
            {
                switch(data[JSON.KEY_STATUS])
                {
                    case JSON.STATUS_SUCCESS:
                        updateStatsRow($tr, data.response);
                        break;

                    case JSON.STATUS_WARNING:
                        $tr
                        .children('td:nth-child(3)')
                        .attr('colspan', 14)
                        .html(data.response)
                        .addClass('error')
                        .nextAll()
                        .hide();
                        break;
                }
            },
            complete: function()
            {
                processUnknowns();
            }
        });
    }
    else
    {
        $('#stats-loading-notice')
        .hide();

        // Load total stats
        $.ajax({
            data: 'r=_xNetworkStatsTotalGet',
            success: function(data)
            {
                updateStatsRow($('tfoot tr'), data.response, true);
            }
        });

        $('table#network-stats')
        .trigger('update')
        .trigger('sorton');
    }
}


function updateStatsRow($tr, response, total)
{
    var i = total ? 2 : 3;

    $tr.children('td:nth-child('+(i++)+')').html(number_format(response.i_raw_60));
    $tr.children('td:nth-child('+(i++)+')').html(number_format(response.o_raw_60));
    $tr.children('td:nth-child('+(i++)+')').html(number_format(response.c_raw_60));

    $tr.children('td:nth-child('+i+')').children('div.quality-good').css({width: response.i_ctry_g_pct_60 + '%'}).attr('title', response.i_ctry_g_pct_60 + '%');
    $tr.children('td:nth-child('+i+')').children('div.quality-normal').css({width: response.i_ctry_n_pct_60 + '%'}).attr('title', response.i_ctry_n_pct_60 + '%');
    $tr.children('td:nth-child('+(i++)+')').children('div.quality-bad').css({width: response.i_ctry_b_pct_60 + '%'}).attr('title', response.i_ctry_b_pct_60 + '%');

    $tr.children('td:nth-child('+(i++)+')').html(response.prod_60 + '%');
    $tr.children('td:nth-child('+(i++)+')').html(response.return_60 + '%');

    $tr.children('td:nth-child('+(i++)+')').html(number_format(response.i_raw_24));
    $tr.children('td:nth-child('+(i++)+')').html(number_format(response.o_raw_24));
    $tr.children('td:nth-child('+(i++)+')').html(number_format(response.c_raw_24));

    $tr.children('td:nth-child('+i+')').children('div.quality-good').css({width: response.i_ctry_g_pct_24 + '%'}).attr('title', response.i_ctry_g_pct_24 + '%');
    $tr.children('td:nth-child('+i+')').children('div.quality-normal').css({width: response.i_ctry_n_pct_24 + '%'}).attr('title', response.i_ctry_n_pct_24 + '%');
    $tr.children('td:nth-child('+(i++)+')').children('div.quality-bad').css({width: response.i_ctry_b_pct_24 + '%'}).attr('title', response.i_ctry_b_pct_24 + '%');

    $tr.children('td:nth-child('+(i++)+')').html(number_format(response.i_uniq_24));

    $tr.children('td:nth-child('+(i++)+')').html(response.prod_24 + '%');
    $tr.children('td:nth-child('+(i++)+')').html(response.skim_24 + '%');
    $tr.children('td:nth-child('+(i++)+')').html(response.return_24 + '%');
}


function markUnknown($tr, total)
{
    var unknown = '--';

    if( !total )
    {
        $tr
        .children('td:nth-child(3)')
        .removeAttr('colspan')
        .removeClass('error')
        .nextAll()
        .show();
    }

    var i = total ? 2 : 3;

    $tr.children('td:nth-child('+(i++)+')').html(unknown);
    $tr.children('td:nth-child('+(i++)+')').html(unknown);
    $tr.children('td:nth-child('+(i++)+')').html(unknown);

    $tr.children('td:nth-child('+i+')').children('div.quality-good').css({width: 0 + '%'}).attr('title', unknown + '%');
    $tr.children('td:nth-child('+i+')').children('div.quality-normal').css({width: 0 + '%'}).attr('title', unknown + '%');
    $tr.children('td:nth-child('+(i++)+')').children('div.quality-bad').css({width: 0 + '%'}).attr('title', unknown + '%');

    $tr.children('td:nth-child('+(i++)+')').html(unknown + '%');
    $tr.children('td:nth-child('+(i++)+')').html(unknown + '%');


    $tr.children('td:nth-child('+(i++)+')').html(unknown);
    $tr.children('td:nth-child('+(i++)+')').html(unknown);
    $tr.children('td:nth-child('+(i++)+')').html(unknown);

    $tr.children('td:nth-child('+i+')').children('div.quality-good').css({width: 0 + '%'}).attr('title', unknown + '%');
    $tr.children('td:nth-child('+i+')').children('div.quality-normal').css({width: 0 + '%'}).attr('title', unknown + '%');
    $tr.children('td:nth-child('+(i++)+')').children('div.quality-bad').css({width: 0 + '%'}).attr('title', unknown + '%');

    $tr.children('td:nth-child('+(i++)+')').html(unknown);

    $tr.children('td:nth-child('+(i++)+')').html(unknown + '%');
    $tr.children('td:nth-child('+(i++)+')').html(unknown + '%');
    $tr.children('td:nth-child('+(i++)+')').html(unknown + '%');
}
