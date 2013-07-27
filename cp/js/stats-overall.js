var g_xml = null;

$(function()
{
    $('.icon-menu-container')
    .iconmenu();


    var table_pos = $('table#trade-stats')
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
        table_pos = $('table#trade-stats')
                    .offset();

        $thead
        .css({/*top: table_pos.top + 'px', */left: table_pos.left + 'px'});
    });


    var cookie_trades = $.cookie(COOKIE_NAME_TRADES) != null ? $.cookie(COOKIE_NAME_TRADES).split('|') : null;
    var cookie_system = $.cookie(COOKIE_NAME_SYSTEM) != null ? $.cookie(COOKIE_NAME_SYSTEM).split('|') : null;

    $('#trade-stats')
    .tablesorter({
        textExtraction: STATS_HOURLY ? statsHourlyTextExtraction : 'simple',
        cssHeader: 'headerSort',
        widgets: ['zebra'],
        headers: {0: {sorter: false}},
        sortList: cookie_trades != null ? [[cookie_trades[0],cookie_trades[1]]] : null
    })
    .bind('sortColumn', function(e, sortColumn, sortDirection)
    {
        $.cookie(COOKIE_NAME_TRADES, sortColumn + '|' + sortDirection, {expires: 365});
    });


    $('#system-stats')
    .tablesorter({
        textExtraction: STATS_HOURLY ? statsHourlyTextExtraction : 'simple',
        cssHeader: 'headerSort',
        widgets: ['zebra'],
        headers: {0: {sorter: false}},
        sortList: cookie_system != null ? [[cookie_system[0],cookie_system[1]]] : null
    })
    .bind('sortColumn', function(e, sortColumn, sortDirection)
    {
        $.cookie(COOKIE_NAME_SYSTEM, sortColumn + '|' + sortDirection, {expires: 365});
    });




    $('img[src="images/edit-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one trade to edit');
            return;
        }

        $.overlay.show(document);
        $.ajax({data: 'r=_xTradesBulkEditShow&domain=' + selected.join(',')});
    });


    $('img[src="images/delete-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one trade');
            return;
        }

        $.overlay.show(document);
        $.ajax({data: 'r=_xTradesDeleteShow&domain=' + selected.join(',')});
    });


    $('img[src="images/email-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one trade');
            return;
        }

        $.overlay.show(document);
        $.ajax({data: 'r=_xTradesEmailShow&domain=' + selected.join(',')});
    });


    $('img[src="images/reset-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one trade');
            return;
        }

        if( confirm('Are you sure you want to reset the stats for the selected trades?') )
        {
            $.ajax({toolbarIcon: this,
                    data: 'r=_xTradesReset&domain=' + selected.join(',')});
        }
    });

    $('img[src="images/enable-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one trade');
            return;
        }

        if( confirm('Are you sure you want to enable the selected trades?') )
        {
            $.ajax({toolbarIcon: this,
                    data: 'r=_xTradesEnable&domain=' + selected.join(',')});
        }
    });


    $('img[src="images/disable-32x32.png"]')
    .click(function()
    {
        var selected = getSelected();

        if( selected.length < 1 )
        {
            alert('Please select at least one trade');
            return;
        }

        if( confirm('Are you sure you want to disable the selected trades?') )
        {
            $.ajax({toolbarIcon: this,
                    data: 'r=_xTradesDisable&domain=' + selected.join(',')});
        }
    });


    $('#trade-action-menu > div > div, #system-action-menu > div > div')
    .click(function()
    {
        var fnc = $(this).attr('fnc');
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
    });


    $('span.trade-info-container')
    .hoverIntent(function()
    {
        //var $container = $(this);
        var $box = $('div.trade-info', this);

        if( $box.html() == '' )
        {
            $box.siblings('img').attr('src', 'images/activity-16x16.gif');

            $.ajax({
                data: 'r=_xTradesInfoBox&trade=' + $box.attr('trade'),
                success: function(data)
                {
                    $box
                    .html(data[JSON.KEY_HTML])
                    .css('visibility', 'hidden')
                    .show();

                    setTimeout(function()
                    {
                        fixOffPage($box, '8px');

                        $box
                        .css('visibility', 'visible')
                        .siblings('img')
                        .attr('src', 'images/info-16x16.png');
                    }, 250);
                }
            });
        }
        else
        {
            $box
            .css('visibility', 'hidden')
            .show();

            fixOffPage($box, '8px');

            $box
            .css('visibility', 'visible');
        }
    },
    function()
    {
        $('div.trade-info', this).hide();
    });


    $('img.refresh-thumbs')
    .livequery('click', function()
    {
        var icon = this;

        $(icon)
        .hide()
        .after('<img src="images/activity-16x16.gif" />');

        $.ajax({
            data: 'r=_xGrabThumbs&trade=' + escape($(this).attr('trade')),
            success: function(data)
            {
                $('span.trade-thumbs[trade="' + data[JSON.KEY_ITEM_ID] + '"]')
                .html(data[JSON.KEY_HTML]);

                fixOffPage($(icon).parents('div.trade-info'), '8px');
            },
            complete: function()
            {
                $(icon)
                .show()
                .siblings('img')
                .remove();
            }
        });
    });

});


// Text extraction for hourly stats rows
function statsHourlyTextExtraction(node)
{
    if( node.className.search(/triint/) != -1 )
    {
        return node.childNodes[1].innerHTML;
    }
    else
    {
        return node.innerHTML;
    }
}


// Mark trades as deleted
function markDeleted(domains)
{
    if( typeof domains == 'string' )
    {
        domains = domains.split(',');
    }

    $.each(domains, function(i, domain)
    {
        $('table#trade-stats tbody tr[id="item-'+domain+'"]').remove();
    });

    $('table#trade-stats')
    .trigger('update')
    .trigger('sorton');
}


// Mark trades as reset
function markReset(domains)
{
    if( typeof domains == 'string' )
    {
        domains = domains.split(',');
    }

    $.each(domains, function(i, domain)
    {
        var $tr = $('table.item-table tbody tr[id="item-'+domain+'"]');

        $('td.int', $tr).text(0);
        $('td.pct', $tr).text('0%');
        $('td.qly > div', $tr).css({width: '0%'}).attr('title', '0%');

        // Hourly
        $('td.triint', $tr).attr('title', 'Prod: 0%, Trade Prod: 0%');
        $('td.triint > div', $tr).text(0);
    });

    $('table#trade-stats')
    .trigger('update')
    .trigger('sorton');

    $('table#system-stats')
    .trigger('update')
    .trigger('sorton');
}


// Mark trades as enabled
function markEnabled(domains)
{
    if( typeof domains == 'string' )
    {
        domains = domains.split(',');
    }

    $.each(domains, function(i, domain)
    {
        $('table#trade-stats tbody tr[id="item-'+domain+'"] > td:eq(1) > a')
        .attr('title', 'Active')
        .removeClass('unconfirmed new autostopped disabled')
        .addClass('active');
    });
}


// Mark trades as disabled
function markDisabled(domains)
{
    if( typeof domains == 'string' )
    {
        domains = domains.split(',');
    }

    $.each(domains, function(i, domain)
    {
        $('table#trade-stats tbody tr[id="item-'+domain+'"] > td:eq(1) > a')
        .attr('title', 'Disabled')
        .removeClass('unconfirmed new active autostopped')
        .addClass('disabled');
    });
}