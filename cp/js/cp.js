// Globals
var JSON = {STATUS_SUCCESS: 0,
            STATUS_WARNING: 1,
            STATUS_ERROR: 2,
            STATUS_LOGOUT: 3,
            KEY_STATUS: 'status',
            KEY_DIALOG: 'dialog',
            KEY_MESSAGE: 'message',
            KEY_ERRORS: 'errors',
            KEY_WARNINGS: 'warnings',
            KEY_HTML: 'html',
            KEY_JS: 'js',
            KEY_EVAL: 'eval',
            KEY_CODE: 'code',
            KEY_BODY: 'body',
            KEY_SUBJECT: 'subject',
            KEY_LOG: 'log',
            KEY_ROW: 'row',
            KEY_ITEM_ID: 'item_id',
            KEY_ITEM_TYPE: 'item_type',
            KEY_SEARCH: 'search',
            KEY_SEARCH_RESULTS: 'search_results',
            KEY_SAVED_SEARCH: 'saved_search',
            KEY_DIALOG_CLOSE: 'dialog_close'};


$(function()
{
    // Menu
    $('span.menu, span.menu > div span').menu();

    // Checkboxes
    $('.checkbox')
    .livequery(function()
    {
        $(this).checkbox();
    },
    function() {});

    // Initialize global XHR settings
    $.ajaxSetup({url: 'xhr.php', dataType: 'json', type: 'post', cache: false, timeout: 0});
    $(document).ajaxSend(globalAjaxSend);
    $(document).ajaxSuccess(globalAjaxSuccess);
    $(document).ajaxComplete(globalAjaxComplete);
    $(document).ajaxError(globalAjaxError);

    // Dialog links
    $('a.dialog')
    .livequery('click', function()
           {
               var data = $(this).attr('data');
               data = !data ? '' : data;

               $.overlay.show(document);
               $.ajax({data: 'r=' + $(this).attr('href') + data});
               return false;
           });

    // XHR links
    $('a.xhr')
    .livequery('click', function()
           {
               var data = $(this).attr('data');
               var conf_msg = $(this).attr('confirm');
               data = !data ? '' : data;

               if( !conf_msg || (conf_msg && confirm(conf_msg)) )
               {
                   $.ajax({link: this,
                           data: 'r=' + $(this).attr('href') + data});
               }

               return false;
           });

    // XHR forms
    $('form.xhr-form')
    .livequery(function()
    {
        $(this)
        .ajaxForm({
            beforeSubmit: function()
            {
                dialogButtonDisable();
            },
            success: function(data, status, $form)
            {
                $form.trigger('form-success', [data]);
            }
        });
    });

    // Check/un-check all checkbox
    $('thead input.check-all')
    .click(function()
    {
        var $table = $(this).parents('table');
        $('tbody td:first-child input:checkbox', $table).attr('checked', $(this).is(':checked'));
    });

    // Checkboxes
    $('tbody td:first-child input:checkbox')
    .click(function()
    {
        if( $(this).is(':checked') )
        {
            var $checkboxes = $('tbody td:first-child input:checkbox');

            if( $checkboxes.length == $checkboxes.filter(':checked').length )
            {
                $('thead input.check-all').attr('checked', true);
            }
        }
        else
        {
            $('thead input.check-all').attr('checked', false);
        }
    });
});

function fixOffPage($element, bottom)
{
    var offset = $element.offset();
    var scrollTop = $(document).scrollTop();
    var wh = $(window).height();
    var eh = $element.outerHeight();

    // Off the bottom
    if( offset.top + eh + 10 >= wh + scrollTop )
    {
        $element.css({bottom: bottom, top: 'auto'});
    }

    // Off the top
    else if( offset.top + scrollTop > wh )
    {
        $element.css({top: bottom, bottom: 'auto'});
    }
}

function getSelected()
{
    var selected = new Array();

    $('table tbody td:first-child input:checkbox:checked').each(function() { selected.push($(this).val()); });

    return selected;
}

function globalAjaxSend(e, xhr, settings)
{
    if( settings.toolbarIcon )
    {
        var $icon = $(settings.toolbarIcon);
        var offset = $icon.offset();

        $icon[0].$dimmer =
        $('<span class="toolbarDimmer"></span>')
        .appendTo('body')
        .css({top: offset.top + 'px', left: offset.left + 'px'});

        $icon.css({opacity: 0.3});
    }

    if( settings.link )
    {
        var $link = $(settings.link);

        $link[0].$dimmer =
        $('<div class="linkDimmer"></div>')
        .appendTo(settings.link.parentNode)
        .css({top: settings.link.offsetTop + 'px',
              left: settings.link.offsetLeft + 'px',
              width: $link.outerWidth() + 'px',
              height: $link.outerHeight() + 'px'});

        $link.css({opacity: 0.2});
    }
}

function globalAjaxComplete(e, xhr, settings)
{
    if( settings.toolbarIcon )
    {
        var $icon = $(settings.toolbarIcon);
        $icon[0].$dimmer.remove();
        $icon.css({opacity: 1});
    }

    if( settings.link )
    {
        var $link = $(settings.link);
        $link[0].$dimmer.remove();
        $link.css({opacity: 1});
    }
}

function globalAjaxSuccess(e, xhr, settings, data)
{
    if( settings.dataType == 'json' )
    {
        if( data[JSON.KEY_DIALOG] )
        {
            $.dialog.show(data[JSON.KEY_DIALOG]);
        }

        if( data[JSON.KEY_DIALOG_CLOSE] )
        {
            $.dialog.hide();
        }

        if( data[JSON.KEY_JS] )
        {
            $('head').append('<script language="JavaScipt" type="text/javascript">' + data[JSON.KEY_JS] + '<'+'/script>');
        }

        if( data[JSON.KEY_EVAL] )
        {
            eval(data[JSON.KEY_EVAL]);
        }

        switch(data[JSON.KEY_STATUS])
        {
            case JSON.STATUS_SUCCESS:
                dialogButtonEnable();

                if( data[JSON.KEY_MESSAGE] )
                {
                    $.growl(data[JSON.KEY_MESSAGE]);
                }

                if( data[JSON.KEY_ITEM_TYPE] && data[JSON.KEY_ITEM_TYPE] == item_type )
                {
                    if( data[JSON.KEY_ITEM_ID] )
                    {
                        if( data[JSON.KEY_ROW] )
                        {
                            $('table.item-table tbody tr[id="item-'+data[JSON.KEY_ITEM_ID]+'"]')
                            .replaceWith(data[JSON.KEY_ROW]);
                        }
                        else
                        {
                            $('table.item-table tbody tr[id="item-'+data[JSON.KEY_ITEM_ID]+'"]')
                            .remove();

                            $('#num-items')
                            .decrementText();
                        }
                    }
                    else if( data[JSON.KEY_ROW] )
                    {
                        $('table.item-table tbody')
                        .append(data[JSON.KEY_ROW]);

                        $('#num-items')
                        .incrementText();
                    }
                }

                break;

            case JSON.STATUS_WARNING:
                if( data[JSON.KEY_MESSAGE] )
                {
                    $.growl.warning(data[JSON.KEY_MESSAGE], data[JSON.KEY_WARNINGS]);
                }
                dialogButtonEnable();
                break;

            case JSON.STATUS_ERROR:
                $.growl.error(data[JSON.KEY_MESSAGE], data[JSON.KEY_ERRORS]);
                dialogButtonEnable();
                break;

            case JSON.STATUS_LOGOUT:
                $.dialog.show('<div class="message-logout">Your session has expired! Please <a href="index.php">click here</a> to login again.</div>');
                break;
        }
    }
}

function globalAjaxError(e, xhr, text, exception)
{
    var base_message = 'Ajax request failed!<br />';
    var detail_message = null;
    var extras = null;

    switch(text)
    {
        case 'error':
            detail_message = 'HTTP Status: ' + xhr.status + ' ' + xhr.statusText;
            extras = xhr.responseText;
            break;

        case 'parsererror':
            detail_message = 'Unable to parse return data as JSON';
            extras = xhr.responseText;
            break;

        default:
            if( exception )
            {
                detail_message = 'Exception: ' + exception.message;
            }
            break;
    }

    $.growl.error(base_message + detail_message, extras);
    dialogButtonEnable();
}

function dialogButtonDisable()
{
    $('#dialog-buttons input').enable(false);
    $('#dialog-buttons img').show();
}

function dialogButtonEnable()
{
    $('#dialog-buttons input').enable();
    $('#dialog-buttons img').hide();
}

function number_format(number, decimals, dec_point, thousands_sep)
{
    var n = number, prec = decimals;
    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);
    var sep = (typeof thousands_sep == "undefined") ? ',' : thousands_sep;
    var dec = (typeof dec_point == "undefined") ? '.' : dec_point;

    var s = (prec > 0) ? n.toFixed(prec) : Math.round(n).toFixed(prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;

    var abs = Math.abs(n).toFixed(prec);
    var _, i;

    if( abs >= 1000 )
    {
        _ = abs.split(/\D/);
        i = _[0].length % 3 || 3;

        _[0] = s.slice(0,i + (n < 0)) +
              _[0].slice(i).replace(/(\d{3})/g, sep+'$1');

        s = _.join(dec);
    }
    else
    {
        s = s.replace('.', dec);
    }

    return s;
}

(function($)
{
    $.fn.increment = function()
    {
        return this.each(function()
                         {
                            var current = parseInt($(this).val());
                            $(this).val(++current);
                         });
    };

    $.fn.decrement = function(selector)
    {
        return this.each(function()
                         {
                            var current = parseInt($(this).val());
                            $(this).val(--current);
                         });
    };

    $.fn.incrementText = function()
    {
        return this.each(function()
                         {
                            var current = parseInt($(this).text());
                            $(this).text(++current);
                         });
    };

    $.fn.decrementText = function(selector)
    {
        return this.each(function()
                         {
                            var current = parseInt($(this).text());
                            $(this).text(--current);
                         });
    };
})(jQuery);

(function($)
{
    var calendar_initialized = false;
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var days = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
    var one_minute = 60 * 1000;
    var one_hour = one_minute * 60;
    var one_day = one_hour * 24;
    var one_week = one_day * 7;
    var buddy = null;
    var hovering = false;

    // Plugin definition
    $.fn.calendar = function(options)
    {
        var global_opts = $.extend({}, $.fn.calendar.defaults, options);

        if( !calendar_initialized )
        {
            initializeCalendar();
            calendar_initialized = true;
        }

        this.each(function()
                  {
                      this.opts = global_opts;
                      $(this).after('&nbsp;<img src="images/calendar-22x22.png" class="calendar-icon" title="Calendar" />');
                      $(this).bind('keydown', function(e) { if( e.keyCode == 9 ) { hideCalendar(); }});
                  });

        $('.calendar-icon').click($.fn.calendar.toggleCalendar);

        return this;
    };


    // Toggle visibility of the calendar
    $.fn.calendar.toggleCalendar = function()
    {
        if( buddy )
        {
            hideCalendar();
        }
        else
        {
            $(this).prev('input[class=datepicker], input[class=datetimepicker]').each(showCalendar);
        }
    };


    // Show the calendar
    function showCalendar()
    {
        // Don't redisplay
        if( this == buddy )
        {
            return;
        }

        buddy = this;
        var pos = $(this).offset();
        var height = $(this).outerHeight();

        // See if the input field contains a date already
        var input = $(this).val();
        var matches = null;
        var selected = null;
        if( (matches = input.match(/(\d\d\d\d)-(\d\d)-(\d\d)/)) != null )
        {
            $('#cal_year option[value='+matches[1]+']').attr('selected', 'selected');
            $('#cal_month option[value='+matches[2]+']').attr('selected', 'selected');

            selected = matches[1] + '-' + matches[2] + '-' + matches[3];

            if( !this.opts.notime && (matches = input.match(/(\d\d):(\d\d):(\d\d)/)) != null )
            {
                $('#cal_hour option[value='+matches[1]+']').attr('selected', 'selected');
                $('#cal_minute option[value='+matches[2]+']').attr('selected', 'selected');
                $('#cal_second option[value='+matches[3]+']').attr('selected', 'selected');
            }
        }
        else
        {
            setToday();
        }

        updateCalendar();

        $('#cal')
        .css({top: pos.top + height + 5 + 'px', left: pos.left + 'px'})
        .show();

        if( this.opts.notime )
        {
            $('tr.time').hide();
        }
        else
        {
            $('tr.time').show();
        }

        if( selected )
        {
            $('#date-'+selected).removeClass('today').addClass('selected');
        }

        $(document).bind('mousedown', function(e)
                                    {
                                        e = $.event.fix(e);

                                        if( e.target.id != 'cal' && $(e.target).parents('#cal').length == 0 )
                                        {
                                            hideCalendar();
                                        }
                                    });
    };


    // Hide the calendar
    function hideCalendar()
    {
        buddy = null;
        $(document).unbind('mousedown');
        $('#cal').hide();
    };


    // Set the calendar to view today
    function setToday()
    {
        var today = new Date();
        var month = today.getMonth()+1;
        if( month < 10 ) month = '0' + month;

        $('#cal_year option[value='+today.getFullYear()+']').attr('selected', 'selected');
        $('#cal_month option[value='+month+']').attr('selected', 'selected');

        $('#cal_hour option[value=12]').attr('selected', 'selected');
        $('#cal_minute option[value=00]').attr('selected', 'selected');
        $('#cal_second option[value=00]').attr('selected', 'selected');
    };


    // Initialize the calendar markup
    function initializeCalendar()
    {
        var now = new Date();
        var year = now.getFullYear();
        var month = now.getMonth();
        var html_buffer = '<div id="cal" style="display: none;">' +
                          '<table align="center" cellspacing="0">' +
                          '<tr>' +
                          '<td>' +
                          '<span class="img calendar-prev-16x16 click prev"></span>' +
                          '</td>' +
                          '<td colspan="5" align="center">' +
                          '<select id="cal_month">';

        for( var i = 0; i < months.length; i++ )
        {
            var value = i + 1;
            if( value < 10 ) value = '0' + value;
            html_buffer += '<option value="'+value+'"'+(i == month ? ' selected="selected"' : '')+'>'+months[i]+'</option>';
        }

        html_buffer += '</select> <select id="cal_year">';

        for( var i = year - 100; i < year + 25; i++ )
        {
            html_buffer += '<option value="'+i+'"'+(i == year ? ' selected="selected"' : '')+'>'+i+'</option>';
        }

        html_buffer += '</select>' +
                       '</td>' +
                       '<td align="right">' +
                       '<span class="img calendar-next-16x16 click next"></span>' +
                       '</td>' +
                       '</tr>' +
                       '<tr class="days">';

        for( var i = 0; i < days.length; i++ )
        {
            html_buffer += '<td';

            if( i == 0 )
            {
                html_buffer += ' class="sunday"';
            }
            else if( i == 6 )
            {
                html_buffer += ' class="saturday"';
            }

            html_buffer += '>' + days[i] + '</td>';
        }

        html_buffer += '</tr>' +
                       '<tr class="time">' +
                       '<td colspan="7" align="center">' +
                       '<select id="cal_hour">';

        for( var i = 0; i < 24; i++ )
        {
            if( i < 10 ) i = '0' + i;
            html_buffer += '<option value="'+i+'"'+(i == 12 ? ' selected="selected"' : '')+'>'+i+'</option>';
        }

        html_buffer += '</select>:<select id="cal_minute">';

        for( var i = 0; i < 60; i++ )
        {
            if( i < 10 ) i = '0' + i;
            html_buffer += '<option value="'+i+'">'+i+'</option>';
        }

        html_buffer += '</select>:<select id="cal_second">';

        for( var i = 0; i < 60; i++ )
        {
            if( i < 10 ) i = '0' + i;
            html_buffer += '<option value="'+i+'">'+i+'</option>';
        }

        html_buffer += '</select></td></tr>' +
                       '</table>' +
                       '</div>';

        $('body').append(html_buffer);

        $('#cal').hover(function() { hovering = true }, function() { hovering = false });
        $('#cal_month').bind('change', updateCalendar);
        $('#cal_year').bind('change', updateCalendar);
        $('#cal span.prev').click(function() { jumpMonth(false); });
        $('#cal span.next').click(function() { jumpMonth(true); });

        $('#cal_month').trigger('change');
    };


    // Update the calendar when the month or year change
    function updateCalendar()
    {
        var today = new Date();
        var selected_month = $('#cal_month').val();
        var selected_year = $('#cal_year').val();

        if( selected_month.charAt(0) == '0' )
            selected_month = selected_month.substr(1);

        selected_month = parseInt(selected_month);

        var start_date = new Date(selected_year, selected_month - 1, 1, 12);
        var days_in_month = new Date(start_date.getFullYear(), start_date.getMonth(), 0).getDate();

        var day_of_week = start_date.getDay();
        if( day_of_week == 0 )
        {
            day_of_week = 7;
        }
        start_date.setTime(start_date.getTime() - (day_of_week * one_day));

        var cur_date = new Date();
        var html_buffer = '<tr class="numbers">';
        for( var i = 0; i < 42; i++ )
        {
            cur_date.setTime(start_date.getTime() + (i * one_day));
            var month = (cur_date.getMonth()+1);
            if( month < 10 ) month = '0' + month;
            var date = cur_date.getDate();
            if( date < 10 ) date = '0' + date;
            var full_date = cur_date.getFullYear() + '-' + month + '-' + date;
            html_buffer += '<td id="date-'+full_date+'" class="' +
                           (cur_date.getMonth()+1 != selected_month ? ' other-month' : '') +
                           '">'+cur_date.getDate()+'</td>';

            if( (i + 1) % 7 == 0 )
            {
                html_buffer += '</tr><tr class="numbers">';
            }
        }

        $('#cal tr.numbers').unbind().remove();
        $('#cal tr.days').after(html_buffer);
        $('#cal tr.numbers > td').click(dateClicked);

        var month = (today.getMonth()+1);
        if( month < 10 ) month = '0' + month;
        var date = today.getDate();
        if( date < 10 ) date = '0' + date;

        $('#date-'+today.getFullYear() + '-' + month + '-' + date).addClass('today');
    };


    // A date was clicked on
    function dateClicked()
    {
        var now = new Date();
        var time = $('#cal_hour').val() + ':' + $('#cal_minute').val() + ':' + $('#cal_second').val();
        var matches = this.id.match(/(\d\d\d\d)-(\d\d)-(\d\d)/);
        var datetime = matches[1] + '-' + matches[2] + '-' + matches[3] + ($('tr.time:visible').length ? ' ' + time : '');

        if( buddy )
        {
            $(buddy).val(datetime);
        }

        hideCalendar();
    };


    // Jump forward or back one month
    function jumpMonth(forward)
    {
        var selected_month = $('#cal_month').val();
        var selected_year = parseInt($('#cal_year').val());

        if( selected_month.charAt(0) == '0' )
            selected_month = selected_month.substr(1);

        selected_month = parseInt(selected_month);

        // Adjust year
        if( forward )
        {
            if( selected_month == 12 )
            {
                selected_year++;
                selected_month = 0;
            }

            selected_month++;
        }
        else
        {
            if( selected_month == 1 )
            {
                selected_year--;
                selected_month = 13;
            }

            selected_month--;
        }

        if( selected_month < 10 ) selected_month = '0' + selected_month;

        $('#cal_year option[value='+selected_year+']').attr('selected', 'selected');
        $('#cal_month option[value='+selected_month+']').attr('selected', 'selected');
        $('#cal_month').trigger('change');

        return false;
    };

    // Default values
    $.fn.calendar.defaults =
    {
        notime: false
    };

})(jQuery);





(function($)
{

    $.fn.center = function(container, direction, animate)
    {
        var w = (container == document) ? $(window).width() : $(container).outerWidth();
        var h = (container == document) ? $(window).height() : $(container).outerHeight();
        var sl = (container == document) ? $(document).scrollLeft() : 0;
        var st = (container == document) ? $(document).scrollTop() : 0;

        return this.each(function()
                         {
                             var o = container == document || $(this).parents()[0] == $(container)[0] ? {top: 0, left: 0} : $(container).offset();
                             var oh = $(this).outerHeight();
                             var ow = $(this).outerWidth();
                             var l = Math.max(0, ((w - ow) / 2) + sl) + o.left;
                             var t = Math.max(0, ((h - oh) / 2) + st) + o.top;
                             var styles = {top: t + 'px', left: l + 'px'};

                             switch(direction)
                             {
                                 case 'vertical':
                                    styles = {top: t + 'px'};
                                    break;

                                 case 'horizontal':
                                    styles = {left: l + 'px'};
                                    break;
                             }

                             if( animate )
                             {
                                 $(this).animate(styles, 'slow');
                             }
                             else
                             {
                                 $(this).css(styles);
                             }
                         });
    };

})(jQuery);




(function($)
{
    $.fn.menu = function()
    {
        return this.each(function()
                         {
                             $(this)
                             .hover(function()
                                    {
                                        $(this).children().show();
                                    },
                                    function()
                                    {
                                        $(this).children().hide();
                                    });
                         });
    };
})(jQuery);


(function($)
{
    $.fn.updatefield = function()
    {
        return this.each(function()
                         {
                             var $input = $('> input[type=hidden]', this);
                             var is_checkbox = this.tagName.toLowerCase() == 'span';

                             if( parseInt($input.val()) )
                             {
                                 $(this).addClass('updating');
                             }

                             $(this)
                             .mousedown(function(e)
                                        {
                                            if( !is_checkbox || e.shiftKey )
                                            {
                                                var value = parseInt($input.val());

                                                $input.val(value ? 0 : 1).trigger('change', e);
                                                $(this).toggleClass('updating');
                                            }

                                            return false;
                                        })
                             .bind('selectstart', function() { return false; });
                         });
    };
})(jQuery);



(function($)
{
    $.fn.checkbox = function()
    {
        return this.each(function()
                         {
                             var $input = $('input[type=hidden]', this);

                             if( parseInt($input.val()) )
                             {
                                 $(this).addClass('checked');
                             }

                             $(this)
                             .mousedown(function(e)
                                        {
                                            if( !e.shiftKey )
                                            {
                                                var value = parseInt($input.val());

                                                $input.val(value ? 0 : 1).trigger('change', e);
                                                $(this).toggleClass('checked');
                                            }

                                            //return false;
                                        })
                             .bind('selectstart', function() { return false; });
                         });
    };
})(jQuery);




(function($)
{
    var $dialog = null;
    var options = {};
    var defaults = {id: 'dialog',
                    closeId: 'dialog-close',
                    cancelId: 'dialog-button-cancel',
                    headerId: 'dialog-header',
                    contentId: 'dialog-content',
                    panelId: 'dialog-panel'};


    $.dialog = {

        show: function(content, params)
              {
                  options = $.extend({}, defaults, params);

                  $.overlay
                  .show(document);

                  if( $dialog == null )
                  {
                      $dialog = $('<div id="' + options.id + '"></div>').appendTo('body');
                  }

                  $dialog
                  .html(content)
                  .draggable({handle: '#' + options.headerId, containment: 'document'})
                  .show();

                  var width = $('#' + options.panelId).attr('dwidth');
                  $dialog.css({width: width ? width : 'auto'});

                  $('#' + options.panelId)
                  .css({maxHeight: $(window).height() - 200 + 'px'});

                  $dialog
                  .center(document);

                  $.overlay
                  .finished(document);

                  $('#' + options.closeId + ', #' + options.cancelId)
                  .click($.dialog.hide);

                  $(window)
                  .resize(resize)
                  .scroll(scroll);

                  $('#' + options.panelId)
                  .trigger('dialog-visible');
              },

        hide: function()
              {
                  $('#' + options.panelId)
                  .trigger('dialog-closing');

                  if( $dialog )
                  {
                      $dialog
                      .html('')
                      .draggable('destroy')
                      .hide();
                  }

                  $.overlay
                  .hide(document);

                  $('#' + options.closeId + ', #' + options.cancelId)
                  .unbind('click', $.dialog.hide);

                  $('#' + options.contentId)
                  .remove();

                  $(window)
                  .unbind('resize', resize)
                  .unbind('scroll', scroll);

                  $('#' + options.panelId)
                  .trigger('dialog-closed');
              }
    };

    function resize()
    {
        $('#' + options.panelId).css({maxHeight: $(window).height() - 200 + 'px'});
        $dialog.center(document);
    }

    function scroll()
    {
        $dialog.center(document, 'vertical');
    }
})(jQuery);




(function($)
{
    var optionsStore = 'options';
    var warningClass = 'warning';
    var errorClass = 'error';
    var defaults = {cls: '',
                    timeout: 3500,
                    messageCls: 'message',
                    closeCls: 'close',
                    containerId: 'growl-container',
                    showDuration: 250,
                    hideDuration: 500,
                    paddingTop: 8,
                    paddingBottom: 8,
                    marginBottom: 18};

    $.growl = function(message, params)
              {
                  var options = $.extend({}, defaults, params);

                  // Create the container div if it does not already exist
                  if( $('#'+options.containerId).length == 0 )
                  {
                      $('<div id="' + options.containerId + '"></div>')
                      .appendTo('body');
                  }

                  var html = '<div>' +
                             '<div class="' + options.closeCls + '">x</div>' +
                             '<div class="' + options.messageCls + '">' + message + '</div>' +
                             '</div>';

                  var $notice = $(html)
                                .appendTo('#'+options.containerId)
                                .addClass(options.cls)
                                .animate({opacity: 1,
                                          paddingTop: options.paddingTop,
                                          paddingBottom: options.paddingBottom,
                                          marginBottom: options.marginBottom},
                                         options.showDuration);

                 $('.' + options.messageCls, $notice)
                 .animate({height: 'show'}, options.showDuration);

                 // If a timeout has been specified, setup to automatically remove the message
                 var timeout = null;
                 if( options.timeout > 0 )
                 {
                     timeout = setTimeout(function()
                                          {
                                              remove.apply($notice);
                                          },
                                          options.timeout);
                 }

                 // Handle clicks on the X (close)
                 $('.' + options.closeCls, $notice)
                 .click(function()
                        {
                            clearTimeout(timeout);
                            remove.apply($notice);
                        });

                 // Store options
                 $notice.data(optionsStore, options);
              };


    $.growl.error = function(message, errors)
                    {
                        $.growl(formatMessage(message, errors), {cls: errorClass, timeout: 15000});
                    };

    $.growl.warning = function(message, warnings)
                      {
                          $.growl(formatMessage(message, warnings), {cls: warningClass, timeout: 10000});
                      };

    function formatMessage(message, extras)
    {
        if( typeof extras == 'object' )
        {
            message += ':<ul><li>' + extras.join('</li><li>') + '</li></ul>';
        }
        else if( typeof extras == 'string' )
        {
            message += '<xmp>' + extras + '</xmp>';
        }

        return message;
    }

    function remove()
    {
        var $notice = this;
        var options = $notice.data(optionsStore);
        var $message = $('.' + options.messageCls, $notice);

        // IE doesn't change the opacity of the close div
        if( $.browser.msie )
        {
            $('.' + options.closeCls, $notice)
            .animate({height: 'hide', opacity: 0}, options.hideDuration);
        }

        $message
        .animate({height: 'hide'}, options.hideDuration);

        $notice
        .animate({opacity: 0,
                  paddingTop: 0,
                  paddingBottom: 0,
                  marginBottom: 0},
                 options.hideDuration,
                 'swing',
                 function()
                 {
                     $notice.remove();
                 });
    }

})(jQuery);




(function($)
{

    // Plugin definition
    $.fn.iconmenu = function()
    {
        $('.icon-menu-overflow').bind('DOMMouseScroll mousewheel', mousewheel);

        return this.each(initialize);
    };


    // Initialize
    function initialize()
    {
        var menu_id = $(this).attr('menu');

        if( menu_id )
        {
            var css_top = $(menu_id).css('top');
            var $menu = $(menu_id);

            $(this)
            .hoverIntent(function()
                   {
                       var offset = $(this).offset();
                       var scrollTop = $(document).scrollTop();
                       var wh = $(window).height();

                       $menu
                       .appendTo(this);

                       var menu_height = $(menu_id).outerHeight();

                       $menu
                       .css(offset.top + menu_height + 10 >= wh + scrollTop ? {bottom: css_top, top: null} : {top: css_top, bottom: null})
                       .show();
                   },
                   function()
                   {
                       $(menu_id)
                       .hide()
                       .appendTo('body');
                   });
        }
        else
        {
            $(this)
            .hoverIntent(function()
                   {
                       $('div.icon-menu')
                       .hide();

                       $('div.icon-menu', this)
                       .show();

                       $(document)
                       .keydown(keydown)
                       .keypress(keypress);
                   },
                   function()
                   {
                       $('div.icon-menu', this)
                       .hide()
                       .appendTo('body');

                       $(document)
                       .unbind('keydown', keydown)
                       .unbind('keypress', keypress);
                   });
        }
    }



    function keydown(evt)
    {
        var $el = $('div.icon-menu:visible'),
            el = $el[0],
            stop = true;

        el.buffer = el.buffer == undefined ? '' : el.buffer;

        switch(evt.keyCode)
        {
            case 27: // Escape
                el.buffer = '';
                //$('div.icon-menu', el).removeClass('filtered');
                $(el).removeClass('filtered');
                $('div.icon-menu-overflow > div', el).show();
                $('div.icon-menu-overflow > p', el).hide();
                $('#icon-menu-filter').hide();
                break;

            case 8: // Backspace
            case 46: // Delete
                clearTimeout(el.timeout);
                el.buffer = el.buffer.substr(0, el.buffer.length - 1);
                el.timeout = setTimeout(function() { filter.apply(el) }, 500);
                break;

            case 9: // Tab
            case 13: // Enter
                break;

            default:
                stop = false;
                break;
        }

        $('#icon-menu-filter input').val(el.buffer);

        if( stop )
        {
            evt.stopPropagation();
            return false;
        }
    }



    function keypress(evt)
    {
        var $el = $('div.icon-menu:visible'),
            el = $el[0];

        el.buffer = el.buffer == undefined ? '' : el.buffer;

        $('#icon-menu-filter:hidden')
        .appendTo($el)
        .center($el)
        .show();

        switch( evt.keyCode )
        {
            case 27: // Escape
                $('#icon-menu-filter').hide();
            case 8: // Backspace
            case 9: // Tab
            case 13: // Enter
            case 46: // Delete
                break;

            default:
                clearTimeout(el.timeout);
                el.buffer += String.fromCharCode($.browser.msie ? evt.keyCode : evt.charCode);
                $('#icon-menu-filter input').val(el.buffer);
                el.timeout = setTimeout(function() { filter.apply(el) }, 500);
                break;
        }

        evt.stopPropagation();
        return false;
    }



    // Clear the filter
    function filter()
    {
        $(this).addClass('filtered');
        $('#icon-menu-filter').hide();
        $('div.icon-menu-overflow', this).scrollTop(0);
        $('div.icon-menu-overflow > p', this).hide();
        $('div.icon-menu-overflow > div', this).hide();

        if( $('div.icon-menu-overflow > div:containsi("' + this.buffer + '")', this).show().length < 1 )
        {
            $('div.icon-menu-overflow > p', this).show();
        }

        this.buffer = '';
    }


    // Handle mousewheel on context menu
    function mousewheel(evt)
    {
        var wheel_delta = $.browser.mozilla ? -evt.detail : evt.wheelDelta;

        $(this).scrollTop($(this).scrollTop() + (wheel_delta > 0 ? -54 : 54));

        evt.stopPropagation();

        return false;
    }
})(jQuery);

(function($) {
    $.fn.hoverIntent = function(f,g) {
        // default configuration options
        var cfg = {
            sensitivity: 5,
            interval: 50,
            timeout: 0
        };
        // override configuration options with user supplied object
        cfg = $.extend(cfg, g ? { over: f, out: g } : f );

        // instantiate variables
        // cX, cY = current X and Y position of mouse, updated by mousemove event
        // pX, pY = previous X and Y position of mouse, set by mouseover and polling interval
        var cX, cY, pX, pY;

        // A private function for getting mouse position
        var track = function(ev) {
            cX = ev.pageX;
            cY = ev.pageY;
        };

        // A private function for comparing current and previous mouse position
        var compare = function(ev,ob) {
            ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
            // compare mouse positions to see if they've crossed the threshold
            if ( ( Math.abs(pX-cX) + Math.abs(pY-cY) ) < cfg.sensitivity ) {
                $(ob).unbind("mousemove",track);
                // set hoverIntent state to true (so mouseOut can be called)
                ob.hoverIntent_s = 1;
                return cfg.over.apply(ob,[ev]);
            } else {
                // set previous coordinates for next time
                pX = cX; pY = cY;
                // use self-calling timeout, guarantees intervals are spaced out properly (avoids JavaScript timer bugs)
                ob.hoverIntent_t = setTimeout( function(){compare(ev, ob);} , cfg.interval );
            }
        };

        // A private function for delaying the mouseOut function
        var delay = function(ev,ob) {
            ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
            ob.hoverIntent_s = 0;
            return cfg.out.apply(ob,[ev]);
        };

        // A private function for handling mouse 'hovering'
        var handleHover = function(e) {
            // next three lines copied from jQuery.hover, ignore children onMouseOver/onMouseOut
            var p = (e.type == "mouseover" ? e.fromElement : e.toElement) || e.relatedTarget;
            while ( p && p != this )
            {
                try
                {
                    p = p.parentNode;
                }
                catch(e)
                {
                    if( Object.prototype.toString.call(parent) == '[object XULElement]' )
                    {
                        p = this;
                    }
                    else
                    {
                        p = null;
                    } /*JMB Software*/ /*parent = this;*/
                }
            }
            if ( p == this ) { return false; }

            // copy objects to be passed into t (required for event object to be passed in IE)
            var ev = jQuery.extend({},e);
            var ob = this;

            // cancel hoverIntent timer if it exists
            if (ob.hoverIntent_t) { ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t); }

            // else e.type == "onmouseover"
            if (e.type == "mouseover") {
                // set "previous" X and Y position based on initial entry point
                pX = ev.pageX; pY = ev.pageY;
                // update "current" X and Y position based on mousemove
                $(ob).bind("mousemove",track);
                // start polling interval (self-calling timeout) to compare mouse coordinates over time
                if (ob.hoverIntent_s != 1) { ob.hoverIntent_t = setTimeout( function(){compare(ev,ob);} , cfg.interval );}

            // else e.type == "onmouseout"
            } else {
                // unbind expensive mousemove event
                $(ob).unbind("mousemove",track);
                // if hoverIntent state is true, then call the mouseOut function after the specified delay
                if (ob.hoverIntent_s == 1) { ob.hoverIntent_t = setTimeout( function(){delay(ev,ob);} , cfg.timeout );}
            }
        };

        // bind the function to the two event listeners
        return this.mouseover(handleHover).mouseout(handleHover);
    };
})(jQuery);





(function($)
{
    var storeName = 'overlay';
    var defaults = {cls: 'overlay',
                    zIndex: 12,
                    opacity: 0.85,
                    activity: true,
                    activityCls: 'overlay-activity',
                    activityIcon: 'images/activity-32x32.gif',
                    activityHeight: 32,
                    activityWidth: 32,
                    activityAlt: 'Processing...',
                    innerHtml: ''};

    $.overlay = {

        show: function(el, params)
              {
                  var $el = $(el);
                  var $overlay = $el.data(storeName);
                  var options = $.extend({}, defaults, params);

                  if( typeof $overlay != 'object' )
                  {
                      var prependTo = (el == document) ? 'body' : $el,
                          height = (el == document) ? $el.height() : $el.outerHeight(),
                          width  = (el == document) ? $el.width() : $el.outerWidth();
                          html   = '<div class="' + options.cls + '">' +
                                   '<img src="' + options.activityIcon +'" ' +
                                        'width="' + options.activityWidth + '" ' +
                                        'height="' + options.activityHeight + '" ' +
                                        'alt="' + options.activityAlt + '" ' +
                                        'title="' + options.activityAlt + '" ' +
                                        'class="' + options.activityCls + '" />' +
                                   options.innerHtml +
                                   '</div>';

                      $overlay = $(html)
                                 .prependTo(prependTo)
                                 .css({zIndex: options.zIndex,
                                       opacity: options.opacity,
                                       width: width + 'px',
                                       height: height + 'px',
                                       lineHeight: height + 'px'})
                                 .show();

                      $el.data(storeName, $overlay);
                  }
                  else
                  {
                      $overlay.show();
                  }

                  // Show activity icon
                  if( options.activity )
                  {
                      $('img', $overlay).center(document).show();
                  }
                  else
                  {
                      $('img', $overlay).hide();
                  }

                  // Handle resize and scroll if overlay element is document
                  if( el == document )
                  {
                      $(window)
                      .resize(resize)
                      .scroll(scroll);
                  }
              },

        hide: function(el, destroy)
              {
                  var $el = $(el);
                  var $overlay = $el.data(storeName);

                  if( $overlay )
                  {
                      $overlay.hide();

                      if( destroy )
                      {
                          $overlay.removeData(storeName);
                          $overlay.remove();
                      }

                      // Handle resize and scroll if overlay element is document
                      if( el == document )
                      {
                          $(window)
                          .unbind('resize', resize)
                          .unbind('scroll', scroll);
                      }
                  }
              },

        processing: function(el)
                    {
                        var $overlay = $(el).data(storeName);
                        $('img', $overlay).center(el).show();
                    },

        finished: function(el)
                  {
                      var $overlay = $(el).data(storeName);
                      $('img', $overlay).hide();
                  }
    };

    function resize()
    {
        var $overlay = $(document).data(storeName);

        $overlay.hide();
        $overlay.css({width: $(document).width() + 'px', height: $(document).height() + 'px'}).show();
        $('img', $overlay).center(document);
    }

    function scroll()
    {
        var $overlay = $(document).data(storeName);
        $('img', $overlay).center(document);
    }

})(jQuery);


(function($) {

$.extend($.fn, {
    livequery: function(type, fn, fn2) {
        var self = this, q;

        // Handle different call patterns
        if ($.isFunction(type))
            fn2 = fn, fn = type, type = undefined;

        // See if Live Query already exists
        $.each( $.livequery.queries, function(i, query) {
            if ( self.selector == query.selector && self.context == query.context &&
                type == query.type && (!fn || fn.$lqguid == query.fn.$lqguid) && (!fn2 || fn2.$lqguid == query.fn2.$lqguid) )
                    // Found the query, exit the each loop
                    return (q = query) && false;
        });

        // Create new Live Query if it wasn't found
        q = q || new $.livequery(this.selector, this.context, type, fn, fn2);

        // Make sure it is running
        q.stopped = false;

        // Run it immediately for the first time
        q.run();

        // Contnue the chain
        return this;
    },

    expire: function(type, fn, fn2) {
        var self = this;

        // Handle different call patterns
        if ($.isFunction(type))
            fn2 = fn, fn = type, type = undefined;

        // Find the Live Query based on arguments and stop it
        $.each( $.livequery.queries, function(i, query) {
            if ( self.selector == query.selector && self.context == query.context &&
                (!type || type == query.type) && (!fn || fn.$lqguid == query.fn.$lqguid) && (!fn2 || fn2.$lqguid == query.fn2.$lqguid) && !this.stopped )
                    $.livequery.stop(query.id);
        });

        // Continue the chain
        return this;
    }
});

$.livequery = function(selector, context, type, fn, fn2) {
    this.selector = selector;
    this.context  = context;
    this.type     = type;
    this.fn       = fn;
    this.fn2      = fn2;
    this.elements = [];
    this.stopped  = false;

    // The id is the index of the Live Query in $.livequery.queries
    this.id = $.livequery.queries.push(this)-1;

    // Mark the functions for matching later on
    fn.$lqguid = fn.$lqguid || $.livequery.guid++;
    if (fn2) fn2.$lqguid = fn2.$lqguid || $.livequery.guid++;

    // Return the Live Query
    return this;
};

$.livequery.prototype = {
    stop: function() {
        var query = this;

        if ( this.type )
            // Unbind all bound events
            this.elements.unbind(this.type, this.fn);
        else if (this.fn2)
            // Call the second function for all matched elements
            this.elements.each(function(i, el) {
                query.fn2.apply(el);
            });

        // Clear out matched elements
        this.elements = [];

        // Stop the Live Query from running until restarted
        this.stopped = true;
    },

    run: function() {
        // Short-circuit if stopped
        if ( this.stopped ) return;
        var query = this;

        var oEls = this.elements,
            els  = $(this.selector, this.context),
            nEls = els.not(oEls);

        // Set elements to the latest set of matched elements
        this.elements = els;

        if (this.type) {
            // Bind events to newly matched elements
            nEls.bind(this.type, this.fn);

            // Unbind events to elements no longer matched
            if (oEls.length > 0)
                $.each(oEls, function(i, el) {
                    if ( $.inArray(el, els) < 0 )
                        $.event.remove(el, query.type, query.fn);
                });
        }
        else {
            // Call the first function for newly matched elements
            nEls.each(function() {
                query.fn.apply(this);
            });

            // Call the second function for elements no longer matched
            if ( this.fn2 && oEls.length > 0 )
                $.each(oEls, function(i, el) {
                    if ( $.inArray(el, els) < 0 )
                        query.fn2.apply(el);
                });
        }
    }
};

$.extend($.livequery, {
    guid: 0,
    queries: [],
    queue: [],
    running: false,
    timeout: null,

    checkQueue: function() {
        if ( $.livequery.running && $.livequery.queue.length ) {
            var length = $.livequery.queue.length;
            // Run each Live Query currently in the queue
            while ( length-- )
                $.livequery.queries[ $.livequery.queue.shift() ].run();
        }
    },

    pause: function() {
        // Don't run anymore Live Queries until restarted
        $.livequery.running = false;
    },

    play: function() {
        // Restart Live Queries
        $.livequery.running = true;
        // Request a run of the Live Queries
        $.livequery.run();
    },

    registerPlugin: function() {
        $.each( arguments, function(i,n) {
            // Short-circuit if the method doesn't exist
            if (!$.fn[n]) return;

            // Save a reference to the original method
            var old = $.fn[n];

            // Create a new method
            $.fn[n] = function() {
                // Call the original method
                var r = old.apply(this, arguments);

                // Request a run of the Live Queries
                $.livequery.run();

                // Return the original methods result
                return r;
            }
        });
    },

    run: function(id) {
        if (id != undefined) {
            // Put the particular Live Query in the queue if it doesn't already exist
            if ( $.inArray(id, $.livequery.queue) < 0 )
                $.livequery.queue.push( id );
        }
        else
            // Put each Live Query in the queue if it doesn't already exist
            $.each( $.livequery.queries, function(id) {
                if ( $.inArray(id, $.livequery.queue) < 0 )
                    $.livequery.queue.push( id );
            });

        // Clear timeout if it already exists
        if ($.livequery.timeout) clearTimeout($.livequery.timeout);
        // Create a timeout to check the queue and actually run the Live Queries
        $.livequery.timeout = setTimeout($.livequery.checkQueue, 20);
    },

    stop: function(id) {
        if (id != undefined)
            // Stop are particular Live Query
            $.livequery.queries[ id ].stop();
        else
            // Stop all Live Queries
            $.each( $.livequery.queries, function(id) {
                $.livequery.queries[ id ].stop();
            });
    }
});

// Register core DOM manipulation methods
$.livequery.registerPlugin('append', 'prepend', 'after', 'before', 'wrap', 'attr', 'removeAttr', 'addClass', 'removeClass', 'toggleClass', 'empty', 'remove');

// Run Live Queries when the Document is ready
$(function() { $.livequery.play(); });

})(jQuery);

;(function($) {


$.fn.ajaxSubmit = function(options) {
    // fast fail if nothing selected (http://dev.jquery.com/ticket/2752)
    if (!this.length) {
        log('ajaxSubmit: skipping submit process - no element selected');
        return this;
    }

    if (typeof options == 'function')
        options = { success: options };

    var url = $.trim(this.attr('action'));
    if (url) {
        // clean url (don't include hash vaue)
        url = (url.match(/^([^#]+)/)||[])[1];
       }
       url = url || window.location.href || '';

    options = $.extend({
        $form: this,    // JMB Software
        url:  url,
        type: this.attr('method') || 'GET'
    }, options || {});

    // hook for manipulating the form data before it is extracted;
    // convenient for use with rich editors like tinyMCE or FCKEditor
    var veto = {};
    this.trigger('form-pre-serialize', [this, options, veto]);
    if (veto.veto) {
        log('ajaxSubmit: submit vetoed via form-pre-serialize trigger');
        return this;
    }

    // provide opportunity to alter form data before it is serialized
    if (options.beforeSerialize && options.beforeSerialize(this, options) === false) {
        log('ajaxSubmit: submit aborted via beforeSerialize callback');
        return this;
    }

    var a = this.formToArray(options.semantic);
    if (options.data) {
        options.extraData = options.data;
        for (var n in options.data) {
          if(options.data[n] instanceof Array) {
            for (var k in options.data[n])
              a.push( { name: n, value: options.data[n][k] } );
          }
          else
             a.push( { name: n, value: options.data[n] } );
        }
    }

    // give pre-submit callback an opportunity to abort the submit
    if (options.beforeSubmit && options.beforeSubmit(a, this, options) === false) {
        log('ajaxSubmit: submit aborted via beforeSubmit callback');
        return this;
    }

    // fire vetoable 'validate' event
    this.trigger('form-submit-validate', [a, this, options, veto]);
    if (veto.veto) {
        log('ajaxSubmit: submit vetoed via form-submit-validate trigger');
        return this;
    }

    var q = $.param(a);

    if (options.type.toUpperCase() == 'GET') {
        options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + q;
        options.data = null;  // data is null for 'get'
    }
    else
        options.data = q; // data is the query string for 'post'

    var $form = this, callbacks = [];
    if (options.resetForm) callbacks.push(function() { $form.resetForm(); });
    if (options.clearForm) callbacks.push(function() { $form.clearForm(); });

    // perform a load on the target only if dataType is not provided
    if (!options.dataType && options.target) {
        var oldSuccess = options.success || function(){};
        callbacks.push(function(data) {
            $(options.target).html(data).each(oldSuccess, arguments);
        });
    }
    else if (options.success)
        callbacks.push(options.success);

    options.success = function(data, status) {
        for (var i=0, max=callbacks.length; i < max; i++)
            callbacks[i].apply(options, [data, status, $form]);
    };

    // are there files to upload?
    var files = $('input:file', this).fieldValue();
    var found = false;
    for (var j=0; j < files.length; j++)
        if (files[j])
            found = true;

    var multipart = false;
//    var mp = 'multipart/form-data';
//    multipart = ($form.attr('enctype') == mp || $form.attr('encoding') == mp);

    // options.iframe allows user to force iframe mode
   if (options.iframe || found || multipart) {
       // hack to fix Safari hang (thanks to Tim Molendijk for this)
       // see:  http://groups.google.com/group/jquery-dev/browse_thread/thread/36395b7ab510dd5d
       if (options.closeKeepAlive)
           $.get(options.closeKeepAlive, fileUpload);
       else
           fileUpload();
       }
   else
   {
       $.ajax(options);
   }

    // fire 'notify' event
    this.trigger('form-submit-notify', [this, options]);
    return this;


    // private function for handling file uploads (hat tip to YAHOO!)
    function fileUpload() {
        var form = $form[0];

        if ($(':input[name=submit]', form).length) {
            alert('Error: Form elements must not be named "submit".');
            return;
        }

        var opts = $.extend({}, $.ajaxSettings, options);
        var s = $.extend(true, {}, $.extend(true, {}, $.ajaxSettings), opts);

        var id = 'jqFormIO' + (new Date().getTime());
        var $io = $('<iframe id="' + id + '" name="' + id + '" src="about:blank" />');
        var io = $io[0];

        $io.css({ position: 'absolute', top: '-1000px', left: '-1000px' });

        var xhr = { // mock object
            aborted: 0,
            responseText: null,
            responseXML: null,
            status: 0,
            statusText: 'n/a',
            getAllResponseHeaders: function() {},
            getResponseHeader: function() {},
            setRequestHeader: function() {},
            abort: function() {
                this.aborted = 1;
                $io.attr('src','about:blank'); // abort op in progress
            }
        };

        var g = opts.global;
        // trigger ajax global events so that activity/block indicators work like normal
        if (g && ! $.active++) $.event.trigger("ajaxStart");
        if (g) $.event.trigger("ajaxSend", [xhr, opts]);

        if (s.beforeSend && s.beforeSend(xhr, s) === false) {
            s.global && $.active--;
            return;
        }
        if (xhr.aborted)
            return;

        var cbInvoked = 0;
        var timedOut = 0;

        // add submitting element to data if we know it
        var sub = form.clk;
        if (sub) {
            var n = sub.name;
            if (n && !sub.disabled) {
                options.extraData = options.extraData || {};
                options.extraData[n] = sub.value;
                if (sub.type == "image") {
                    options.extraData[name+'.x'] = form.clk_x;
                    options.extraData[name+'.y'] = form.clk_y;
                }
            }
        }

        // take a breath so that pending repaints get some cpu time before the upload starts
        setTimeout(function() {
            // make sure form attrs are set
            var t = $form.attr('target'), a = $form.attr('action');

            // update form attrs in IE friendly way
            form.setAttribute('target',id);
            if (form.getAttribute('method') != 'POST')
                form.setAttribute('method', 'POST');
            if (form.getAttribute('action') != opts.url)
                form.setAttribute('action', opts.url);

            // ie borks in some cases when setting encoding
            if (! options.skipEncodingOverride) {
                $form.attr({
                    encoding: 'multipart/form-data',
                    enctype:  'multipart/form-data'
                });
            }

            // support timout
            if (opts.timeout)
                setTimeout(function() { timedOut = true; cb(); }, opts.timeout);

            // add "extra" data to form if provided in options
            var extraInputs = [];
            try {
                if (options.extraData)
                    for (var n in options.extraData)
                        extraInputs.push(
                            $('<input type="hidden" name="'+n+'" value="'+options.extraData[n]+'" />')
                                .appendTo(form)[0]);

                // add iframe to doc and submit the form
                $io.appendTo('body');
                io.attachEvent ? io.attachEvent('onload', cb) : io.addEventListener('load', cb, false);
                form.submit();
            }
            finally {
                // reset attrs and remove "extra" input elements
                form.setAttribute('action',a);
                t ? form.setAttribute('target', t) : $form.removeAttr('target');
                $(extraInputs).remove();
            }
        }, 10);

        var domCheckCount = 50;

        function cb() {
            if (cbInvoked++) return;

            io.detachEvent ? io.detachEvent('onload', cb) : io.removeEventListener('load', cb, false);

            var ok = true;
            try {
                if (timedOut) throw 'timeout';
                // extract the server response from the iframe
                var data, doc;

                doc = io.contentWindow ? io.contentWindow.document : io.contentDocument ? io.contentDocument : io.document;

                var isXml = opts.dataType == 'xml' || doc.XMLDocument || $.isXMLDoc(doc);
                log('isXml='+isXml);
                if (!isXml && (doc.body == null || doc.body.innerHTML == '')) {
                     if (--domCheckCount) {
                        // in some browsers (Opera) the iframe DOM is not always traversable when
                        // the onload callback fires, so we loop a bit to accommodate
                        cbInvoked = 0;
                        setTimeout(cb, 100);
                        return;
                    }
                    log('Could not access iframe DOM after 50 tries.');
                    return;
                }

                xhr.responseText = doc.body ? doc.body.innerHTML : null;
                xhr.responseXML = doc.XMLDocument ? doc.XMLDocument : doc;
                xhr.getResponseHeader = function(header){
                    var headers = {'content-type': opts.dataType};
                    return headers[header];
                };

                if (opts.dataType == 'json' || opts.dataType == 'script') {
                    // see if user embedded response in textarea
                    var ta = doc.getElementsByTagName('textarea')[0];
                    if (ta)
                        xhr.responseText = ta.value;
                    else {
                        // account for browsers injecting pre around json response
                        var pre = doc.getElementsByTagName('pre')[0];
                        if (pre)
                            xhr.responseText = pre.innerHTML;
                    }
                }
                else if (opts.dataType == 'xml' && !xhr.responseXML && xhr.responseText != null) {
                    xhr.responseXML = toXml(xhr.responseText);
                }
                data = $.httpData(xhr, opts.dataType);
            }
            catch(e){
                ok = false;
                $.handleError(opts, xhr, 'error', e);
            }

            // ordering of these callbacks/triggers is odd, but that's how $.ajax does it
            if (ok) {
                opts.success(data, 'success');
                if (g) $.event.trigger("ajaxSuccess", [xhr, opts]);
            }
            if (g) $.event.trigger("ajaxComplete", [xhr, opts]);
            if (g && ! --$.active) $.event.trigger("ajaxStop");
            if (opts.complete) opts.complete(xhr, ok ? 'success' : 'error');

            // clean up
            setTimeout(function() {
                $io.remove();
                xhr.responseXML = null;
            }, 100);
        };

        function toXml(s, doc) {
            if (window.ActiveXObject) {
                doc = new ActiveXObject('Microsoft.XMLDOM');
                doc.async = 'false';
                doc.loadXML(s);
            }
            else
                doc = (new DOMParser()).parseFromString(s, 'text/xml');
            return (doc && doc.documentElement && doc.documentElement.tagName != 'parsererror') ? doc : null;
        };
    };
};

$.fn.ajaxForm = function(options) {
    return this.ajaxFormUnbind().bind('submit.form-plugin', function() {
        $(this).ajaxSubmit(options);
        return false;
    }).bind('click.form-plugin', function(e) {
        var $el = $(e.target);
        if (!($el.is(":submit,input:image"))) {
            return;
        }
        var form = this;
        form.clk = e.target;
        if (e.target.type == 'image') {
            if (e.offsetX != undefined) {
                form.clk_x = e.offsetX;
                form.clk_y = e.offsetY;
            } else if (typeof $.fn.offset == 'function') { // try to use dimensions plugin
                var offset = $el.offset();
                form.clk_x = e.pageX - offset.left;
                form.clk_y = e.pageY - offset.top;
            } else {
                form.clk_x = e.pageX - e.target.offsetLeft;
                form.clk_y = e.pageY - e.target.offsetTop;
            }
        }
        // clear form vars
        setTimeout(function() { form.clk = form.clk_x = form.clk_y = null; }, 10);
    });
};

// ajaxFormUnbind unbinds the event handlers that were bound by ajaxForm
$.fn.ajaxFormUnbind = function() {
    return this.unbind('submit.form-plugin click.form-plugin');
};

$.fn.formToArray = function(semantic) {
    var a = [];
    if (this.length == 0) return a;

    var form = this[0];
    var els = semantic ? form.getElementsByTagName('*') : form.elements;
    if (!els) return a;
    for(var i=0, max=els.length; i < max; i++) {
        var el = els[i];
        var n = el.name;
        if (!n) continue;

        if (semantic && form.clk && el.type == "image") {
            // handle image inputs on the fly when semantic == true
            if(!el.disabled && form.clk == el) {
                a.push({name: n, value: $(el).val()});
                a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
            }
            continue;
        }

        var v = $.fieldValue(el, true);
        if (v && v.constructor == Array) {
            for(var j=0, jmax=v.length; j < jmax; j++)
                a.push({name: n, value: v[j]});
        }
        else if (v !== null && typeof v != 'undefined')
            a.push({name: n, value: v});
    }

    if (!semantic && form.clk) {
        // input type=='image' are not found in elements array! handle it here
        var $input = $(form.clk), input = $input[0], n = input.name;
        if (n && !input.disabled && input.type == 'image') {
            a.push({name: n, value: $input.val()});
            a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
        }
    }
    return a;
};

$.fn.formSerialize = function(semantic) {
    //hand off to jQuery.param for proper encoding
    return $.param(this.formToArray(semantic));
};

$.fn.fieldSerialize = function(successful) {
    var a = [];
    this.each(function() {
        var n = this.name;
        if (!n) return;
        var v = $.fieldValue(this, successful);
        if (v && v.constructor == Array) {
            for (var i=0,max=v.length; i < max; i++)
                a.push({name: n, value: v[i]});
        }
        else if (v !== null && typeof v != 'undefined')
            a.push({name: this.name, value: v});
    });
    //hand off to jQuery.param for proper encoding
    return $.param(a);
};

$.fn.fieldValue = function(successful) {
    for (var val=[], i=0, max=this.length; i < max; i++) {
        var el = this[i];
        var v = $.fieldValue(el, successful);
        if (v === null || typeof v == 'undefined' || (v.constructor == Array && !v.length))
            continue;
        v.constructor == Array ? $.merge(val, v) : val.push(v);
    }
    return val;
};

$.fieldValue = function(el, successful) {
    var n = el.name, t = el.type, tag = el.tagName.toLowerCase();
    if (typeof successful == 'undefined') successful = true;

    if (successful && (!n || el.disabled || t == 'reset' || t == 'button' ||
        (t == 'checkbox' || t == 'radio') && !el.checked ||
        (t == 'submit' || t == 'image') && el.form && el.form.clk != el ||
        tag == 'select' && el.selectedIndex == -1))
            return null;

    if (tag == 'select') {
        var index = el.selectedIndex;
        if (index < 0) return null;
        var a = [], ops = el.options;
        var one = (t == 'select-one');
        var max = (one ? index+1 : ops.length);
        for(var i=(one ? index : 0); i < max; i++) {
            var op = ops[i];
            if (op.selected) {
                var v = op.value;
                if (!v) // extra pain for IE...
                    v = (op.attributes && op.attributes['value'] && !(op.attributes['value'].specified)) ? op.text : op.value;
                if (one) return v;
                a.push(v);
            }
        }
        return a;
    }
    return el.value;
};

$.fn.clearForm = function() {
    return this.each(function() {
        $('input,select,textarea', this).clearFields();
    });
};

$.fn.clearFields = $.fn.clearInputs = function() {
    return this.each(function() {
        var t = this.type, tag = this.tagName.toLowerCase();
        if (t == 'text' || t == 'password' || tag == 'textarea')
            this.value = '';
        else if (t == 'checkbox' || t == 'radio')
            this.checked = false;
        else if (tag == 'select')
            this.selectedIndex = -1;
    });
};

$.fn.resetForm = function() {
    return this.each(function() {
        // guard against an input with the name of 'reset'
        // note that IE reports the reset function as an 'object'
        if (typeof this.reset == 'function' || (typeof this.reset == 'object' && !this.reset.nodeType))
            this.reset();
    });
};

$.fn.enable = function(b) {
    if (b == undefined) b = true;
    return this.each(function() {
        this.disabled = !b;
    });
};

$.fn.selected = function(select) {
    if (select == undefined) select = true;
    return this.each(function() {
        var t = this.type;
        if (t == 'checkbox' || t == 'radio')
            this.checked = select;
        else if (this.tagName.toLowerCase() == 'option') {
            var $sel = $(this).parent('select');
            if (select && $sel[0] && $sel[0].type == 'select-one') {
                // deselect all other options
                $sel.find('option').selected(false);
            }
            this.selected = select;
        }
    });
};

// helper fn for console logging
// set $.fn.ajaxSubmit.debug to true to enable debug logging
function log() {
    if ($.fn.ajaxSubmit.debug )
    {
        alert('[jquery.form] ' + Array.prototype.join.call(arguments,''));
    }
};

})(jQuery);

(function($) {
    $.extend({
        tablesorter: new function() {

            var parsers = [], widgets = [];

            this.defaults = {
                cssHeader: "header",
                cssAsc: "headerSortUp",
                cssDesc: "headerSortDown",
                sortInitialOrder: "asc",
                sortMultiSortKey: "shiftKey",
                sortForce: null,
                sortAppend: null,
                textExtraction: "simple",
                parsers: {},
                widgets: [],
                widgetZebra: {css: ["even","odd"]},
                headers: {},
                widthFixed: false,
                cancelSelection: true,
                sortList: [],
                headerList: [],
                dateFormat: "us",
                decimal: '.',
                debug: false
            };

            function benchmark(s,d) {
                log(s + "," + (new Date().getTime() - d.getTime()) + "ms");
            }

            this.benchmark = benchmark;

            function log(s) {
                alert(s);
            }

            function buildParserCache(table,$headers) {

                if(table.config.debug) { var parsersDebug = ""; }

                var rows = table.tBodies[0].rows;

                if(table.tBodies[0].rows[0]) {

                    var list = [], cells = rows[0].cells, l = cells.length;

                    for (var i=0;i < l; i++) {
                        var p = false;

                        if($.metadata && ($($headers[i]).metadata() && $($headers[i]).metadata().sorter)  ) {

                            p = getParserById($($headers[i]).metadata().sorter);

                        } else if((table.config.headers[i] && table.config.headers[i].sorter)) {

                            p = getParserById(table.config.headers[i].sorter);
                        }
                        if(!p) {
                            p = detectParserForColumn(table,cells[i]);
                        }

                        if(table.config.debug) { parsersDebug += "column:" + i + " parser:" +p.id + "\n"; }

                        list.push(p);
                    }
                }

                if(table.config.debug) { log(parsersDebug); }

                return list;
            };

            function detectParserForColumn(table,node) {
                var l = parsers.length;
                for(var i=1; i < l; i++) {
                    if(parsers[i].is($.trim(getElementText(table.config,node)),table,node)) {
                        return parsers[i];
                    }
                }
                // 0 is always the generic parser (text)
                return parsers[0];
            }

            function getParserById(name) {
                var l = parsers.length;
                for(var i=0; i < l; i++) {
                    if(parsers[i].id.toLowerCase() == name.toLowerCase()) {
                        return parsers[i];
                    }
                }
                return false;
            }

            function buildCache(table) {

                if(table.config.debug) { var cacheTime = new Date(); }


                var totalRows = (table.tBodies[0] && table.tBodies[0].rows.length) || 0,
                    totalCells = (table.tBodies[0].rows[0] && table.tBodies[0].rows[0].cells.length) || 0,
                    parsers = table.config.parsers,
                    cache = {row: [], normalized: []};

                    for (var i=0;i < totalRows; ++i) {

                        var c = table.tBodies[0].rows[i], cols = [];

                        cache.row.push($(c));

                        for(var j=0; j < totalCells; ++j) {
                            cols.push(parsers[j].format(getElementText(table.config,c.cells[j]),table,c.cells[j]));
                        }

                        cols.push(i); // add position for rowCache
                        cache.normalized.push(cols);
                        cols = null;
                    };

                if(table.config.debug) { benchmark("Building cache for " + totalRows + " rows:", cacheTime); }

                return cache;
            };

            function getElementText(config,node) {

                if(!node) return "";

                var t = "";

                if(config.textExtraction == "simple") {
                    if(node.childNodes[0] && node.childNodes[0].hasChildNodes()) {
                        t = node.childNodes[0].innerHTML;
                    } else {
                        t = node.innerHTML;
                    }
                } else {
                    if(typeof(config.textExtraction) == "function") {
                        t = config.textExtraction(node);
                    } else {
                        t = $(node).text();
                    }
                }
                return t;
            }

            function appendToTable(table,cache) {

                if(table.config.debug) {var appendTime = new Date()}

                var c = cache,
                    r = c.row,
                    n= c.normalized,
                    totalRows = n.length,
                    checkCell = (n[0].length-1),
                    tableBody = $(table.tBodies[0]),
                    rows = [];

                for (var i=0;i < totalRows; i++) {
                    rows.push(r[n[i][checkCell]]);
                    if(!table.config.appender) {

                        var o = r[n[i][checkCell]];
                        var l = o.length;
                        for(var j=0; j < l; j++) {

                            tableBody[0].appendChild(o[j]);

                        }

                        //tableBody.append(r[n[i][checkCell]]);
                    }
                }

                if(table.config.appender) {

                    table.config.appender(table,rows);
                }

                rows = null;

                if(table.config.debug) { benchmark("Rebuilt table:", appendTime); }

                //apply table widgets
                applyWidget(table);

                // trigger sortend
                setTimeout(function() {
                    $(table).trigger("sortEnd");
                },0);

            };

            function buildHeaders(table) {

                if(table.config.debug) { var time = new Date(); }

                var meta = ($.metadata) ? true : false, tableHeadersRows = [];

                for(var i = 0; i < table.tHead.rows.length; i++) { tableHeadersRows[i]=0; };

                $tableHeaders = $("thead th",table);

                $tableHeaders.each(function(index) {

                    this.count = 0;
                    this.column = index;
                    this.order = formatSortingOrder(table.config.sortInitialOrder);

                    if(checkHeaderMetadata(this) || checkHeaderOptions(table,index)) this.sortDisabled = true;

                    if(!this.sortDisabled) {
                        $(this).addClass(table.config.cssHeader);
                    }

                    // add cell to headerList
                    table.config.headerList[index]= this;
                });

                if(table.config.debug) { benchmark("Built headers:", time); log($tableHeaders); }

                return $tableHeaders;

            };

               function checkCellColSpan(table, rows, row) {
                var arr = [], r = table.tHead.rows, c = r[row].cells;

                for(var i=0; i < c.length; i++) {
                    var cell = c[i];

                    if ( cell.colSpan > 1) {
                        arr = arr.concat(checkCellColSpan(table, headerArr,row++));
                    } else  {
                        if(table.tHead.length == 1 || (cell.rowSpan > 1 || !r[row+1])) {
                            arr.push(cell);
                        }
                        //headerArr[row] = (i+row);
                    }
                }
                return arr;
            };

            function checkHeaderMetadata(cell) {
                if(($.metadata) && ($(cell).metadata().sorter === false)) { return true; };
                return false;
            }

            function checkHeaderOptions(table,i) {
                if((table.config.headers[i]) && (table.config.headers[i].sorter === false)) { return true; };
                return false;
            }

            function applyWidget(table) {
                var c = table.config.widgets;
                var l = c.length;
                for(var i=0; i < l; i++) {

                    getWidgetById(c[i]).format(table);
                }

            }

            function getWidgetById(name) {
                var l = widgets.length;
                for(var i=0; i < l; i++) {
                    if(widgets[i].id.toLowerCase() == name.toLowerCase() ) {
                        return widgets[i];
                    }
                }
            };

            function formatSortingOrder(v) {

                if(typeof(v) != "Number") {
                    i = (v.toLowerCase() == "desc") ? 1 : 0;
                } else {
                    i = (v == (0 || 1)) ? v : 0;
                }
                return i;
            }

            function isValueInArray(v, a) {
                var l = a.length;
                for(var i=0; i < l; i++) {
                    if(a[i][0] == v) {
                        return true;
                    }
                }
                return false;
            }

            function setHeadersCss(table,$headers, list, css) {
                // remove all header information
                $headers.removeClass(css[0]).removeClass(css[1]);

                var h = [];
                $headers.each(function(offset) {
                        if(!this.sortDisabled) {
                            h[this.column] = $(this);
                        }
                });

                var l = list.length;
                for(var i=0; i < l; i++) {
                    h[list[i][0]].addClass(css[list[i][1]]);
                }
            }

            function fixColumnWidth(table,$headers) {
                var c = table.config;
                if(c.widthFixed) {
                    var colgroup = $('<colgroup>');
                    $("tr:first td",table.tBodies[0]).each(function() {
                        colgroup.append($('<col>').css('width',$(this).width()));
                    });
                    $(table).prepend(colgroup);
                };
            }

            function updateHeaderSortCount(table,sortList) {
                var c = table.config, l = sortList.length;
                for(var i=0; i < l; i++) {
                    var s = sortList[i], o = c.headerList[s[0]];
                    o.count = s[1];
                    o.count++;
                }
            }

            function multisort(table,sortList,cache) {

                if(table.config.debug) { var sortTime = new Date(); }

                var dynamicExp = "var sortWrapper = function(a,b) {", l = sortList.length;

                for(var i=0; i < l; i++) {

                    var c = sortList[i][0];
                    var order = sortList[i][1];
                    var s = (getCachedSortType(table.config.parsers,c) == "text") ? ((order == 0) ? "sortText" : "sortTextDesc") : ((order == 0) ? "sortNumeric" : "sortNumericDesc");

                    var e = "e" + i;

                    dynamicExp += "var " + e + " = " + s + "(a[" + c + "],b[" + c + "]); ";
                    dynamicExp += "if(" + e + ") { return " + e + "; } ";
                    dynamicExp += "else { ";
                }

                // if value is the same keep orignal order
                var orgOrderCol = cache.normalized[0].length - 1;
                dynamicExp += "return a[" + orgOrderCol + "]-b[" + orgOrderCol + "];";

                for(var i=0; i < l; i++) {
                    dynamicExp += "}; ";
                }

                dynamicExp += "return 0; ";
                dynamicExp += "}; ";

                eval(dynamicExp);

                cache.normalized.sort(sortWrapper);

                if(table.config.debug) { benchmark("Sorting on " + sortList.toString() + " and dir " + order+ " time:", sortTime); }

                return cache;
            };

            function sortText(a,b) {
                //return ((a < b) ? -1 : ((a > b) ? 1 : 0));
                function chunkify(t) { var tz = [], x = 0, y = -1, n = 0, i, j; while (i = (j = t.charAt(x++)).charCodeAt(0)) { var m = (i == 46 || (i >=48 && i <= 57)); if (m !== n) { tz[++y] = ""; n = m; } tz[y] += j; } return tz; } var aa = chunkify(a); var bb = chunkify(b); for (x = 0; aa[x] && bb[x]; x++) { if (aa[x] !== bb[x]) { var c = Number(aa[x]), d = Number(bb[x]); if (c == aa[x] && d == bb[x]) { return c - d; } else return (aa[x] > bb[x]) ? 1 : -1; } } return aa.length - bb.length;
            };

            function sortTextDesc(a,b) {
                //return ((b < a) ? -1 : ((b > a) ? 1 : 0));
                function chunkify(t) { var tz = [], x = 0, y = -1, n = 0, i, j; while (i = (j = t.charAt(x++)).charCodeAt(0)) { var m = (i == 46 || (i >=48 && i <= 57)); if (m !== n) { tz[++y] = ""; n = m; } tz[y] += j; } return tz; } var aa = chunkify(b); var bb = chunkify(a); for (x = 0; aa[x] && bb[x]; x++) { if (aa[x] !== bb[x]) { var c = Number(aa[x]), d = Number(bb[x]); if (c == aa[x] && d == bb[x]) { return c - d; } else return (aa[x] > bb[x]) ? 1 : -1; } } return aa.length - bb.length;
            };

             function sortNumeric(a,b) {
                return a-b;
            };

            function sortNumericDesc(a,b) {
                return b-a;
            };

            function getCachedSortType(parsers,i) {
                return parsers[i].type;
            };

            this.construct = function(settings) {

                return this.each(function() {

                    if(!this.tHead || !this.tBodies) return;

                    var $this, $document,$headers, cache, config, shiftDown = 0, sortOrder;

                    this.config = {};

                    config = $.extend(this.config, $.tablesorter.defaults, settings);

                    // store common expression for speed
                    $this = $(this);

                    // build headers
                    $headers = buildHeaders(this);

                    // try to auto detect column type, and store in tables config
                    this.config.parsers = buildParserCache(this,$headers);


                    // build the cache for the tbody cells
                    cache = buildCache(this);

                    // get the css class names, could be done else where.
                    var sortCSS = [config.cssDesc,config.cssAsc];

                    // fixate columns if the users supplies the fixedWidth option
                    fixColumnWidth(this);

                    // apply event handling to headers
                    // this is to big, perhaps break it out?
                    $headers.click(function(e) {

                        $this.trigger("sortStart");

                        var totalRows = ($this[0].tBodies[0] && $this[0].tBodies[0].rows.length) || 0;

                        if(!this.sortDisabled && totalRows > 0) {


                            // store exp, for speed
                            var $cell = $(this);

                            // get current column index
                            var i = this.column;

                            // get current column sort order
                            //this.order = this.count++ % 2;
                            this.order = ++this.count % 2;

                            $this.trigger("sortColumn", [this.column, this.order]);

                            // user only whants to sort on one column
                            if(!e[config.sortMultiSortKey]) {

                                // flush the sort list
                                config.sortList = [];

                                if(config.sortForce != null) {
                                    var a = config.sortForce;
                                    for(var j=0; j < a.length; j++) {
                                        if(a[j][0] != i) {
                                            config.sortList.push(a[j]);
                                        }
                                    }
                                }

                                // add column to sort list
                                config.sortList.push([i,this.order]);

                            // multi column sorting
                            } else {
                                // the user has clicked on an all ready sortet column.
                                if(isValueInArray(i,config.sortList)) {

                                    // revers the sorting direction for all tables.
                                    for(var j=0; j < config.sortList.length; j++) {
                                        var s = config.sortList[j], o = config.headerList[s[0]];
                                        if(s[0] == i) {
                                            o.count = s[1];
                                            o.count++;
                                            s[1] = o.count % 2;
                                        }
                                    }
                                } else {
                                    // add column to sort list array
                                    config.sortList.push([i,this.order]);
                                }
                            };
                            setTimeout(function() {
                                //set css for headers
                                setHeadersCss($this[0],$headers,config.sortList,sortCSS);
                                appendToTable($this[0],multisort($this[0],config.sortList,cache));
                            },1);
                            // stop normal event by returning false
                            return false;
                        }
                    // cancel selection
                    }).mousedown(function() {
                        if(config.cancelSelection) {
                            this.onselectstart = function() {return false};
                            return false;
                        }
                    });

                    // apply easy methods that trigger binded events
                    $this.bind("update",function() {

                        // rebuild parsers.
                        this.config.parsers = buildParserCache(this,$headers);

                        // rebuild the cache map
                        cache = buildCache(this);

                    }).bind("sorton",function(e,list) {

                        $(this).trigger("sortStart");

                        if( list != undefined )
                        {
                            config.sortList = list;
                        }

                        // update and store the sortlist
                        var sortList = config.sortList;

                        // update header count index
                        updateHeaderSortCount(this,sortList);

                        //set css for headers
                        setHeadersCss(this,$headers,sortList,sortCSS);


                        // sort the table and append it to the dom
                        appendToTable(this,multisort(this,sortList,cache));
                    }).bind("appendCache",function() {

                        appendToTable(this,cache);

                    }).bind("applyWidgetId",function(e,id) {

                        getWidgetById(id).format(this);

                    }).bind("applyWidgets",function() {
                        // apply widgets
                        applyWidget(this);
                    });

                    if($.metadata && ($(this).metadata() && $(this).metadata().sortlist)) {
                        config.sortList = $(this).metadata().sortlist;
                    }
                    // if user has supplied a sort list to constructor.
                    if(config.sortList != null && config.sortList.length > 0) {
                        $this.trigger("sorton",[config.sortList]);
                    }

                    // apply widgets
                    applyWidget(this);
                });
            };

            this.addParser = function(parser) {
                var l = parsers.length, a = true;
                for(var i=0; i < l; i++) {
                    if(parsers[i].id.toLowerCase() == parser.id.toLowerCase()) {
                        a = false;
                    }
                }
                if(a) { parsers.push(parser); };
            };

            this.addWidget = function(widget) {
                widgets.push(widget);
            };

            this.formatFloat = function(s) {
                var i = parseFloat(s);
                return (isNaN(i)) ? 0 : i;
            };
            this.formatInt = function(s) {
                var i = parseInt(s);
                return (isNaN(i)) ? 0 : i;
            };

            this.isDigit = function(s,config) {
                var DECIMAL = '\\' + config.decimal;
                var exp = '/(^0$)|(^[+]?0(' + DECIMAL +'0+)?$)|(^([-+]?[1-9][0-9]*)$)|(^([-+]?((0?|[1-9][0-9]*)' + DECIMAL +'(0*[1-9][0-9]*)))$)|(^[-+]?[1-9]+[0-9]*' + DECIMAL +'0+$)/';
                return RegExp(exp).test($.trim(s));
            };

            this.clearTableBody = function(table) {
                if($.browser.msie) {
                    function empty() {
                        while ( this.firstChild ) this.removeChild( this.firstChild );
                    }
                    empty.apply(table.tBodies[0]);
                } else {
                    table.tBodies[0].innerHTML = "";
                }
            };
        }
    });

    // extend plugin scope
    $.fn.extend({
        tablesorter: $.tablesorter.construct
    });

    var ts = $.tablesorter;

    // add default parsers
    ts.addParser({
        id: "text",
        is: function(s) {
            return true;
        },
        format: function(s) {
            return $.trim(s.toLowerCase());
        },
        type: "text"
    });

    ts.addParser({
        id: "commaDigit",
        is: function(s, table)
        {
            var c = table.config;
            return jQuery.tablesorter.isDigit(s.replace(/,/g, ""), c);
        },
        format: function(s)
        {
            return jQuery.tablesorter.formatFloat(s.replace(/,/g, ""));
        },
        type: "numeric"
    });

    ts.addParser({
        id: "digit",
        is: function(s,table) {
            var c = table.config;
            return $.tablesorter.isDigit(s.replace(/,/g, ""),c);
        },
        format: function(s) {
            return $.tablesorter.formatFloat(s.replace(/,/g, ""));
        },
        type: "numeric"
    });

    ts.addParser({
        id: "currency",
        is: function(s) {
            return /^[£$€?.]/.test(s);
        },
        format: function(s) {
            return $.tablesorter.formatFloat(s.replace(new RegExp(/[^0-9.]/g),""));
        },
        type: "numeric"
    });

    ts.addParser({
        id: "ipAddress",
        is: function(s) {
            return /^\d{2,3}[\.]\d{2,3}[\.]\d{2,3}[\.]\d{2,3}$/.test(s);
        },
        format: function(s) {
            var a = s.split("."), r = "", l = a.length;
            for(var i = 0; i < l; i++) {
                var item = a[i];
                   if(item.length == 2) {
                    r += "0" + item;
                   } else {
                    r += item;
                   }
            }
            return $.tablesorter.formatFloat(r);
        },
        type: "numeric"
    });

    ts.addParser({
        id: "url",
        is: function(s) {
            return /^(https?|ftp|file):\/\/$/.test(s);
        },
        format: function(s) {
            return jQuery.trim(s.replace(new RegExp(/(https?|ftp|file):\/\//),''));
        },
        type: "text"
    });

    ts.addParser({
        id: "isoDate",
        is: function(s) {
            return /^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/.test(s);
        },
        format: function(s) {
            return $.tablesorter.formatFloat((s != "") ? new Date(s.replace(new RegExp(/-/g),"/")).getTime() : "0");
        },
        type: "numeric"
    });

    ts.addParser({
        id: "percent",
        is: function(s) {
            return /\%$/.test($.trim(s));
        },
        format: function(s) {
            return $.tablesorter.formatFloat(s.replace(/,/g, "").replace(new RegExp(/%/g),""));
        },
        type: "numeric"
    });

    ts.addParser({
        id: "usLongDate",
        is: function(s) {
            return s.match(new RegExp(/^[A-Za-z]{3,10}\.? [0-9]{1,2}, ([0-9]{4}|'?[0-9]{2}) (([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(AM|PM)))$/));
        },
        format: function(s) {
            return $.tablesorter.formatFloat(new Date(s).getTime());
        },
        type: "numeric"
    });

    ts.addParser({
        id: "shortDate",
        is: function(s) {
            return /\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}/.test(s);
        },
        format: function(s,table) {
            var c = table.config;
            s = s.replace(/\-/g,"/");
            if(c.dateFormat == "us") {
                // reformat the string in ISO format
                s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, "$3/$1/$2");
            } else if(c.dateFormat == "uk") {
                //reformat the string in ISO format
                s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, "$3/$2/$1");
            } else if(c.dateFormat == "dd/mm/yy" || c.dateFormat == "dd-mm-yy") {
                s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2})/, "$1/$2/$3");
            }
            return $.tablesorter.formatFloat(new Date(s).getTime());
        },
        type: "numeric"
    });

    ts.addParser({
        id: "time",
        is: function(s) {
            return /^(([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(am|pm)))$/.test(s);
        },
        format: function(s) {
            return $.tablesorter.formatFloat(new Date("2000/01/01 " + s).getTime());
        },
      type: "numeric"
    });


    ts.addParser({
        id: "metadata",
        is: function(s) {
            return false;
        },
        format: function(s,table,cell) {
            var c = table.config, p = (!c.parserMetadataName) ? 'sortValue' : c.parserMetadataName;
            return $(cell).metadata()[p];
        },
      type: "numeric"
    });


    // add default widgets
    ts.addWidget({
        id: "zebra",
        format: function(table) {
            if(table.config.debug) { var time = new Date(); }
            $("tr:visible",table.tBodies[0])
            .filter(':even')
            .removeClass(table.config.widgetZebra.css[1]).addClass(table.config.widgetZebra.css[0])
            .end().filter(':odd')
            .removeClass(table.config.widgetZebra.css[0]).addClass(table.config.widgetZebra.css[1]);
            if(table.config.debug) { $.tablesorter.benchmark("Applying Zebra widget", time); }
        }
    });
})(jQuery);



jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};