YUI({
    modules: {
        mapper: '/js/mapper.js',
        ecalendar: '/js/ecalendar.js'
    }
}).use('node', 'mapper', 'ecalendar', 'event-delegate', 'io-base', function(Y) {
    var replaceAllSuburbs = function(selector, id) {
        var select = Y.one(selector);
        var options = select.all('option');
        options.each(function(option) {
            option.remove();
        });
        var stateInfo = YAHOO.EliteStar.params.states[id];
        /*default*/
        var newOption = Y.Node.create('<option value=""></option>');
        select.append(newOption);
        for (var i in stateInfo.suburbs) {
            var newOption = Y.Node.create('<option value=' + i + '>' + stateInfo.suburbs[i] + '</option>');
            select.append(newOption);
        }
    }
    var mapper = new Y.EliteStar.Mapper({
        selector: '#map-all',
        itemsSelector: '.map-item',
        clickCallback: function(id) {
            var stateInfo = YAHOO.EliteStar.params.states[id];
            Y.one('#search-menu input[name="state"]').set('value', stateInfo.id);
            replaceAllSuburbs('#search-menu .city-selection', id);
        }
    });

    Y.one('#search-btn').on('click', function(e) {
        Y.one('#search-form').submit();
    });

    var startCalendar = new Y.EliteStar.ECalendar({
        selector: '#search-form #cal-btn-start',
        textSelector: '#search-form input[name="ds"]',
        id: 'cal-btn-start-cal',
        dateFormat: '%Y/%b/%d'
    });
    var endCalendar = new Y.EliteStar.ECalendar({
        selector: '#search-form #cal-btn-end',
        textSelector: '#search-form input[name="de"]',
        id: 'cal-btn-end-cal',
        dateFormat: '%Y/%b/%d'
    });
    var deleteFavorite = function(e) {
        e.target.getAttribute('data-id');
        var cfg = {
            method: 'POST',
            sync: true,
            data: {
                id: e.target.getAttribute('data-id'),
                role: e.target.getAttribute('data-role'),
                action: 'delete'
            }
        };
        request = Y.io('/ajax/favorite.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            alert(res.data.message);
            e.target.get('parentNode').remove();
        } else {
            alert(res.message);
        }
    }
    Y.delegate('click', deleteFavorite, Y.one('#favorites .favorites'), '.listing-delete');
});
