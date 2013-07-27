var selected_sites = null;
var selected_settings = null;
var selected_trades = null;
var in_progress = false;
var total_sites = 0;
var sync_errors = false;

$(function()
{
    // Make selectable
    $('div.selectable-container')
    .selectable();


    // Select all settings
    $('#select-settings-all')
    .click(function()
    {
        $('#select-settings').selectable('all');
    });


    // Deselect all settings
    $('#select-settings-none')
    .click(function()
    {
        $('#select-settings').selectable('none');
    });


    // Select all trades
    $('#select-trades-all')
    .click(function()
    {
        $('#select-trades').selectable('all');
        $('.selectable-trades-checkboxes input[type="checkbox"]').attr('checked', true);
    });


    // Deselect all trades
    $('#select-trades-none')
    .click(function()
    {
        $('#select-trades').selectable('none');
        $('.selectable-trades-checkboxes input[type="checkbox"]').attr('checked', false);
    });


    // Select trades by category
    $('.selectable-trades-checkboxes input[type="checkbox"]')
    .click(function()
    {
        var $checkboxes = $(this).parents('div.selectable-checkboxes-container').find('input:checked');
        var categories = [];

        $checkboxes.each(function(i, cb)
        {
            categories.push($(cb).attr('value'));
        });

        $('#select-trades')
        .selectable(
            'multi_matching',
            $(this).attr('name'),
            categories
        );
    });



    // Select all sites
    $('#select-sites-all')
    .click(function()
    {
        $('#select-sites').selectable('all');
        $('.selectable-sites-checkboxes input[type="checkbox"]').attr('checked', true);
    });


    // Deselect all sites
    $('#select-sites-none')
    .click(function()
    {
        $('#select-sites').selectable('none');
        $('.selectable-sites-checkboxes input[type="checkbox"]').attr('checked', false);
    });


    // Select sites by category or owner
    $('.selectable-sites-checkboxes input[type="checkbox"]')
    .click(function()
    {
        $('#select-sites')
        .selectable($(this).attr('checked') ? 'matching' : 'unmatching', $(this).attr('name'), $(this).attr('value'));
    });


    // Syncing trades, show trade selection
    $('span[value="trades"]')
    .bind('selected', function(evt, is_selected)
    {
        if( is_selected )
        {
            $('#sync-trades').show();
        }
        else
        {
            $('#sync-trades').hide();
        }
    });


    // Start sync
    $('img[src="images/sync-32x32.png"]')
    .click(function()
    {
        if( in_progress )
        {
            alert('Syncing is currently in progress!');
            return;
        }

        selected_sites = $('#select-sites').selectable('selected');
        total_sites = selected_sites.length;
        if( total_sites < 1 )
        {
            alert('Please select at least one site to sync');
            return;
        }

        selected_settings = $('#select-settings').selectable('selected');
        if( selected_settings.length < 1 )
        {
            alert('Please select at least one setting to sync');
            return;
        }

        selected_trades = $('#select-trades').selectable('selected');

        startSync();
    });
});

function startSync()
{
    total_sites = selected_sites.length;

    $('#sync-progress').show();
    $('#sync-current').show();
    $('#sync-complete').html('');
    $('#sync-num-total').html(total_sites);

    $('fieldset.sync-hide').hide();

    sync_errors = false;
    in_progress = true;
    syncNext(true);
}

function syncNext(cache)
{
    if( selected_sites.length > 0 )
    {
        var domain = selected_sites.shift();

        $('#sync-site').html(domain);
        $('#sync-num-done').html(total_sites - selected_sites.length);

        $.ajax({
            data: 'r=_xNetworkSync&domain=' + domain + (cache ? '&cache=1&settings=' + escape(selected_settings.join(',')) + '&trades=' + escape(selected_trades.join(',')) : ''),
            success: function(data)
            {
                switch(data[JSON.KEY_STATUS])
                {
                    case JSON.STATUS_SUCCESS:
                        $('#sync-complete')
                        .prepend('<div class="sync-success">' + domain + ' sync successful!</div>');

                        break;

                    case JSON.STATUS_WARNING:
                        sync_errors = true;
                        $('#sync-complete')
                        .prepend('<div class="sync-failure">' + domain + ' sync failed: ' + data.response + '</div>');
                        break;
                }
            },
            complete: function()
            {
                syncNext(false);
            }
        });
    }
    else
    {
        in_progress = false;
        $('fieldset.sync-hide').show();
        $('#sync-current').hide();
        $('#select-sites-none').click();
        $('#select-settings-none').click();
        $('#select-trades-none').click();

        if( !sync_errors )
        {
            $('#sync-progress').hide();
            $.growl('Syncing has been completed successfully!');
        }
        else
        {
            $.growl.warning('Syncing has been finished, however some errors were encountered.  See the Results output for details.');
        }
    }
}

(function($)
{
    // Plugin definition
    $.fn.selectable = function(options)
    {
        switch(typeof options)
        {
            case 'string':
                return eval(options + '.apply(this, arguments);');

            case 'object':
            case 'undefined':
                this
                .children('span')
                .click(function()
                {
                    $(this)
                    .toggleClass('selected')
                    .trigger('selected', [$(this).hasClass('selected')]);
                });
                break;
        }
    }

    function selected()
    {
        var selected = new Array();

        this
        .children('span.selected')
        .each(function()
        {
            selected.push($(this).attr('value'));
        });

        return selected;
    }

    function multi_matching(fnc, attr, values)
    {
        this
        .children('span')
        .removeClass('selected')
        .each(function(i, span)
        {
            var categories = $(this).attr(attr);

            $.each(values, function(i, value)
            {
                if( categories.indexOf(',' + value + ',') != -1 )
                {
                    $(span).addClass('selected');
                    return false;
                }
            });
        });
    }

    function matching(fnc, attr, value)
    {
        this
        .children('span['+attr+'="'+value+'"]')
        .addClass('selected');
    }

    function unmatching(fnc, attr, value)
    {
        this
        .children('span['+attr+'="'+value+'"]')
        .removeClass('selected');
    }

    function all()
    {
        this
        .children('span')
        .removeClass('selected')
        .click();
    }

    function none()
    {
        this
        .children('span')
        .addClass('selected')
        .click();
    }

})(jQuery);
