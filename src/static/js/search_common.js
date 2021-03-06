YUI({
    modules: {
        mapper: '/js/mapper.js',
        ecalendar: '/js/ecalendar.js',
        houseobject: '/js/houseobject.js',
        backpacker: '/js/backpacker.js',
        loginpanel: '/js/login_panel.js',
        alertdialog: '/js/alert_dialog.js',
        state: '/js/state.js',
    }
}).use('node', 'mapper', 'ecalendar', 'event-delegate', 'io-base', 'houseobject', 'backpacker', 'loginpanel', 'alertdialog','state', function(Y) {
    var replaceAllSuburbs = function(selector, id) {
        var select = Y.one(selector);
        var options = select.all('option');
        options.each(function(option) {
            option.remove();
        });

        var stateInfo = YAHOO.EliteStar.params.states(id);
        /*default*/
        var newOption = Y.Node.create('<option value="">' + YAHOO.EliteStar.lang.COMMON_CHOOSE_A_SUBURB + '</option>');
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
            var stateInfo = YAHOO.EliteStar.params.states(id);
            Y.one('#search-menu input[name="state"]').set('value', stateInfo.id);
            replaceAllSuburbs('#search-menu .city-selection', id);
        }
    });

    Y.one('#search-btn').on('click', function(e) {
        Y.one('#search-form').submit();
    });

    Y.one('#search-btn-sortbar').on('click', function(e) {
        Y.one('#search-form-sortbar').submit();
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
            var favs = Y.all('.favorite-set .title');
            if (!favs.size()) {
                Y.one('.no-favs').removeClass('hidden');
            }
        } else {
            alert(res.message);
        }
    }
    var getFavorite = function(e) {
        e.target.getAttribute('data-id');
        var cfg = {
            method: 'POST',
            sync: true,
            data: {
                id: e.target.getAttribute('data-id'),
                role: e.target.getAttribute('data-role'),
            }
        };
        request = Y.io('/ajax/search_result.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            var result = Y.one('#result-' + e.target.getAttribute('data-id')); 
            if (result) {
                result.remove(); //delete the node if result already exist
            }
            if (0 == e.target.getAttribute('data-role')) {
                Y.one('#favorite-container').set('innerHTML', res.data.html);
                var houseObject = new Y.EliteStar.houseObject({
                    resultId: 'result-' + e.target.getAttribute('data-id') 
                });
                Y.one('#favorite-container').set('className', 'contain-h')
            } else {
                Y.one('#favorite-container').set('innerHTML', res.data.html);
                var backpacker = new Y.EliteStar.backpacker({
                    resultId: 'result-' + e.target.getAttribute('data-id') 
                });
                Y.one('#favorite-container').set('className', 'contain-b')
            }
        } else {
            alert(res.message);
        }
    }

    Y.delegate('click', deleteFavorite, Y.one('#favorites .favorites'), '.listing-delete');
    Y.delegate('click', getFavorite, Y.one('#favorites .favorites'), 'a');
    if (Y.one('.extension-link')) {
        Y.one('#menu .btn-right').on('mouseover', function() {
            Y.one('.extension-link').removeClass('hidden');
            Y.one('#menu .btn-right .btn-container').addClass('mouseover');
        });
        Y.one('#menu .btn-right').on('mouseout', function() {
            Y.one('.extension-link').addClass('hidden');
            Y.one('#menu .btn-right .btn-container').removeClass('mouseover');
        });
        Y.one('.extension-link').one('mouseout', function() {
            Y.one('.extension-link').addClass('hidden');
            Y.one('#menu .btn-right .btn-container').removeClass('mouseover');
        });
        Y.one('.extension-link').one('mouseover', function() {
            Y.one('.extension-link').removeClass('hidden');
            Y.one('#menu .btn-right .btn-container').addClass('mouseover');
        });
    }

    var menuBtns = Y.all('#menu .btn-container ');
    menuBtns.each(function(btn){
        btn.on('mouseover', function() {
            btn.addClass('mouseover');
        });
        btn.on('mouseout', function() {
            btn.removeClass('mouseover');
        });
    });
    
    var unreadAlert = Y.one('#unread-alert');
    var unreadInterval;
    var checkUnreadMessage = function() {
        var cfg = {
            method: 'POST',
            data: {
                action: 'check-unread-messages',
            }
        };
        var request = Y.io('/ajax/messages.php', cfg);
        Y.on('io:complete', function(id, o, args){
            if (id === request.id) {
                if (!o.responseText) {
                    clearInterval(unreadInterval);
                    return;
                }
                res = JSON.parse(o.responseText);
                if ('SUCCESS' === res.status) {
                    if (res.data.unreadCount > 0) {
                        unreadAlert.addClass('alert-open');
                    }
                }
            }
        });
    }
    checkUnreadMessage();
    unreadInterval = setInterval(checkUnreadMessage, 3000);
});
