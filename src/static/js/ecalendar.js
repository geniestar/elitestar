YUI.add("ecalendar", function(Y)
{
    function ECalendar(config) {
        ECalendar.superclass.constructor.apply(this, arguments);
    }
    ECalendar.NS = 'ecalendar';
    ECalendar.NAME = 'calender';
    ECalendar.ATTRS = {
        textSelector: null,
        selector: null,
        id: null
    };

    Y.extend(ECalendar, Y.Base, {
        initializer: function(cfg) {
            var ecalendar = null;
            var calendarNode = null;
            var selector = cfg.selector;
            var textSelector = cfg.textSelector;
            var id = cfg.id;
            var dateFormat = cfg.dateFormat;
            var dtdate = Y.DataType.Date;
            var calendarNode = null;
            Y.one(selector).on('click', function() {
                if (!ecalendar) {
                    calendarNode = Y.Node.create('<div id="' + id + '" class="calendar yui3-skin-sam panel"></div>');
                    Y.one(selector).get('parentNode').append(calendarNode);
                    ecalendar = new Y.Calendar({
                        contentBox: '#' + id,
                        width:'200px',
                        showPrevMonth: true,
                        showNextMonth: true,
                        date: new Date()
                    }).render();
                    
                    ecalendar.on("selectionChange", function (ev) {
                        var newDate = ev.newSelection[0];
                        Y.one(textSelector).set('value', dtdate.format(newDate, {format:dateFormat}));
                        calendarNode.addClass('hidden');
                    });  
                } else {
                    if (calendarNode.hasClass('hidden')) {
                        calendarNode.removeClass('hidden');
                    } else {
                        calendarNode.addClass('hidden')
                    }
                }
            });
        }
    });
    
    Y.namespace("EliteStar");
    Y.EliteStar.ECalendar = ECalendar;

}, '0.0.1', {requires: ['base', 'node', 'calendar', 'datatype-date']});
