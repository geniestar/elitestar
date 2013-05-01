YUI({
    modules: {
        mapper: '/js/mapper.js',
        ecalendar: '/js/ecalendar.js'
    }
}).use('node', 'event', 'io', 'io-form', 'mapper', 'ecalendar', 'scrollview', function(Y) {
var selectedObjectBtn = null;
var replaceAllSuburbs = function(selector, id) {
    var select = Y.one(selector);
    var options = select.all('option');
    options.each(function(option) {
        option.remove();
    });
    var stateInfo = YAHOO.EliteStar.params.states[id];
    for (var i in stateInfo.suburbs) {
        var newOption = Y.Node.create('<option value=' + i + '>' + stateInfo.suburbs[i] + '</option>');
        select.append(newOption);
    }
}
if ('settings' === YAHOO.EliteStar.params.type ) {
if ('houseowner' === YAHOO.EliteStar.params.role) {
    initScrollView =  function() {
        if (Y.one('#houseobject-selector .container').hasClass('is-scroll')) {
            var scrollView = new Y.ScrollView({
                id: 'scrollview-selector',
                srcNode : '#houseobject-selector .container',
                width : 504,
            }); 
            scrollView.render();
            Y.one('#houseobject-selector .admin-arrow-left').on('click', function(e){
                var scrollX = scrollView.get('scrollX') - 200;
                if (scrollX < 0) {
                    scrollX = 0;
                }
                scrollView.scrollTo(scrollX, 0, 500, "ease-in");
            });
            Y.one('#houseobject-selector .admin-arrow-right').on('click', function(e){
                scrollView.scrollTo(scrollView.get('scrollX') + 200, 0, 500, "ease-in");
            });
        }
    };
    Y.one('.admin-tab-settings').on('click', function(e){
        var serviceTab = Y.one('.admin-tab-service');
        serviceTab.removeClass('selected');
        Y.one('#' + serviceTab.getAttribute('data-id')).addClass('hidden');
        var settingsTab = Y.one('.admin-tab-settings');
        settingsTab.addClass('selected');
        Y.one('#' + settingsTab.getAttribute('data-id')).removeClass('hidden');
        if ('' === Y.one('#houseobject').get('innerHTML')) {
            Y.one('#form-submit').addClass('hidden');
        }
    });
    Y.one('.admin-tab-service').on('click', function(e){
        var settingsTab = Y.one('.admin-tab-settings');
        settingsTab.removeClass('selected');
        Y.one('#' + settingsTab.getAttribute('data-id')).addClass('hidden');
        var serviceTab = Y.one('.admin-tab-service');
        serviceTab.addClass('selected');
        Y.one('#' + serviceTab.getAttribute('data-id')).removeClass('hidden');
        Y.one('#form-submit').removeClass('hidden');
    });
    
    var setupForm = function() {
        var hStartCalendar = new Y.EliteStar.ECalendar({
            selector: '#houseowner-form #cal-btn-house-start',
            textSelector: '#houseowner-form input[name="duration_start"]',
            id: 'cal-btn-house-start-cal',
            dateFormat: '%Y/%b/%d'
        });
    
        var hEndCalendar = new Y.EliteStar.ECalendar({
            selector: '#houseowner-form #cal-btn-house-end',
            textSelector: '#houseowner-form input[name="duration_end"]',
            id: 'cal-btn-house-end-cal',
            dateFormat: '%Y/%b/%d'
        });
        
        Y.one('#houseowner-form').one('select[name="state"]').on('change', function(e) {
            var id = e.target.get('value');
            replaceAllSuburbs('#houseowner-form .city-selection', id);
        });

        Y.one('.registerform-add').on('click', function(e){
            var tmpNode = Y.one('.des-template').cloneNode(10);
            Y.one('#more-description').append(tmpNode);
            tmpNode.removeClass('hidden');
        });
    };

    var objectIdToBeDeleted = null;
    var currentSelectedId = null;
    var getForm = function(e) {
        if (e.target.hasClass('admin-close_big')) {
            return;
        }
        e.preventDefault(); 
        if (selectedObjectBtn) {
            selectedObjectBtn.removeClass('selected');
        }
        if (!e.target.hasClass('houseobject-selector-item'))
        {
            selectedObjectBtn = e.target.get('parentNode');
        } else {
            selectedObjectBtn = e.target;
        }
        selectedObjectBtn.addClass('selected');
        var cfg = {
            method: 'POST',
            sync: true,
            data: {
                action: 'get-form',
                objectid: e.target.getAttribute('data-id'),
            }
        };
        request = Y.io('/ajax/admin_action.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            currentSelectedId = e.target.getAttribute('data-id');
            Y.one('#houseobject').set('innerHTML', res.data.html);
            Y.one('#form-submit').removeClass('hidden');
            setupForm();
        }
    }
    
    var deleteObject = function (e) {
        var cfg = {
            method: 'POST',
            sync: true,
            data: {
                action: 'delete-object',
                objectid: objectIdToBeDeleted,
            }
        };
        request = Y.io('/ajax/admin_action.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            alert(res.data.message);
            Y.one('#houseobject-selector-' + objectIdToBeDeleted).remove();
            Y.one('.delete-dialog').addClass('hidden');
            if (currentSelectedId == objectIdToBeDeleted) {
                Y.one('#houseobject').set('innerHTML', '');
                Y.one('#form-submit').addClass('hidden');
            }
        }else {
            alert(res.data.message);
        }
    }

    var deletePhoto = function (e) {
        var cfg = {
            method: 'POST',
            sync: true,
            data: {
                action: 'delete-photo',
                objectid: e.target.getAttribute('data-object-id'),
                photoid: e.target.getAttribute('data-photo-id'),
            }
        };
        request = Y.io('/ajax/admin_action.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            alert(res.data.message);
            e.target.get('parentNode').addClass('hidden');
        }else {
            alert(res.data.message);
        }
    }

    YAHOO.EliteStar.onPositionChange = function(e) {
        var inputSet = e.parentNode;
        var descNameSet = inputSet.getElementsByTagName('div')[6];
        var descNameTitle = descNameSet.getElementsByTagName('div')[0];
        if (YAHOO.EliteStar.params['positionDesc'][e.value]) {
            descNameSet.setAttribute('class', '');
            descNameTitle.innerHTML = YAHOO.EliteStar.params['positionDesc'][e.value];
        } else {
            descNameSet.setAttribute('class', 'hidden');
        }
    }

    YAHOO.EliteStar.onDeleteClick = function(e) {
       e.parentNode.remove(); 
    }
    Y.one('#houseobject-add').on('click', function(e){
        var cfg = {
            method: 'POST',
            sync: true,
            data: {
                action: 'get-form',
            }
        };
        request = Y.io('/ajax/admin_action.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            Y.one('#houseobject').set('innerHTML', res.data.html);
            setupForm();
        }
    });
    initScrollView();
    Y.delegate('click', getForm, Y.one('#houseobject-selector'), '.houseobject-selector-item');
    Y.delegate('click', function(e) {
        Y.one('.delete-dialog').removeClass('hidden');
        objectIdToBeDeleted = e.target.getAttribute('data-id');
    }, Y.one('#houseobject-selector'), '.houseobject-selector-item .admin-close_big');
    Y.delegate('click', deletePhoto, Y.one('#ajax-role-form'), '.houseobject-photo-item .admin-close_big');
    Y.one('#houseobject-selector .btn-area .btn-cancel').on('click', function() {
        Y.one('.delete-dialog').addClass('hidden');
    });
    Y.one('#close-delete-dialog-btn').on('click', function() {
        Y.one('.delete-dialog').addClass('hidden');
    });
    Y.one('#houseobject-selector .btn-area .btn-delete').on('click', deleteObject);

} else {
    var mapper = new Y.EliteStar.Mapper({
        selector: '#map-all',
        itemsSelector: '.map-item',
        clickCallback: function(id) {
            var stateInfo = YAHOO.EliteStar.params.states[id];
            Y.one('#backpacker-form input[name="state"]').set('value', stateInfo.id);
            replaceAllSuburbs('#backpacker-form .city-selection', id);
        },
        defaultSelected: true
    });
    var bArrivalCalendar = new Y.EliteStar.ECalendar({
        selector: '#backpacker-form #cal-btn-arrival',
        textSelector: '#backpacker-form input[name="arrival_time"]',
        id: 'cal-btn-arrival-cal',
        dateFormat: '%Y/%b/%d'
    });
    var bStartCalendar = new Y.EliteStar.ECalendar({
        selector: '#backpacker-form #cal-btn-start',
        textSelector: '#backpacker-form input[name="duration_start"]',
        id: 'cal-btn-start-cal',
        dateFormat: '%Y/%b/%d'
    });
    var bEndCalendar = new Y.EliteStar.ECalendar({
        selector: '#backpacker-form #cal-btn-end',
        textSelector: '#backpacker-form input[name="duration_end"]',
        id: 'cal-btn-end-cal',
        dateFormat: '%Y/%b/%d'
    });
    
}
}
else if ('messages' === YAHOO.EliteStar.params.type)
{
    var getMessages = function(e) {
        e.preventDefault();
        var cfg = {
            method: 'POST',
            sync: true,
            data: {
                action: 'get-messages',
                start: startMessage,
            }
        };
        request = Y.io('/ajax/admin_action.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            Y.one('#messages').append(res.data.html);
            startMessage += res.data.count;
            if (res.data.count < 5) {
                previousBtn.addClass('hidden');
            }
        }
    }
    
    var sendReply = function(e) {
        e.preventDefault();
        var cfg = {
            method: 'POST',
            sync: true,
            form: {
                id: 'reply-form-' + e.target.getAttribute('data-id'),
                useDisabled: true,
            }
        };
        request = Y.io('/ajax/admin_action.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            Y.one('#message-' + e.target.getAttribute('data-id') + ' .container').append(res.data.html);
            alert(res.data.message);
            Y.one('#message-' + e.target.getAttribute('data-id') + ' input[type=text]').set('value', '');
        } else {
            alert(res.message);
        }
        return false;
    }
    
    var deleteMessage = function(e) {
        e.preventDefault();
        var cfg = {
            method: 'POST',
            sync: true,
            data: {
                messageId: e.target.getAttribute('data-id'),
                action: 'delete-messages',
            }
        };
        request = Y.io('/ajax/admin_action.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            alert(res.data.message);
            Y.one('#message-' + e.target.getAttribute('data-id')).remove();
        }
    }

    var startMessage = 0; //default;
    var previousBtn = Y.one('#previous-messages-btn');
    if (previousBtn) {
        startMessage = 5; //get 2nd page
        previousBtn.on('click', getMessages);
    }
    Y.delegate('submit', sendReply, Y.one('#messages'), 'form');
    Y.delegate('click', deleteMessage, Y.one('#messages'), '.admin-close');
} else if ('suggestion' === YAHOO.EliteStar.params.type) {
    var sendSuggestion = function(e) {
        e.preventDefault();
        var cfg = {
            method: 'POST',
            sync: true,
            form: {
                id: 'suggestion-form',
                useDisabled: true,
            }
        };
        request = Y.io('/ajax/admin_action.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            alert(res.data.message);
            Y.one('#suggestion textarea').set('value', '');
        } else {
            alert(res.message);
        }
        return false;
    }
    Y.delegate('submit', sendSuggestion, Y.one('#suggestion'), 'form');
} else if ('profits' === YAHOO.EliteStar.params.type) {
    function caculateProfits() {
        var total = 0;
        var houseobjects = Y.all('.house-object');
        houseobjects.each(function(houseobject) {
            var durationStart = houseobject.one('input[name="duration_start"]').get('value');
            var durationEnd = houseobject.one('input[name="duration_end"]').get('value');
            var tenants = parseInt(houseobject.one('input[name="tenants"]').get('value'), 10);
            var rent = parseInt(houseobject.one('input[name="rent"]').get('value'), 10);
            if (durationStart && durationEnd && tenants && rent) {
                var durationStartTime = (new Date(durationStart)).getTime()/1000;
                var durationEndTime = (new Date(durationEnd)).getTime()/1000;
                var oneMinRent = rent/(7*24*60);
                var durationMins = (durationEndTime - durationStartTime)/60;
                var subTotal = Math.floor(durationMins * tenants * oneMinRent);
                houseobject.one('input[name="amount"]').set('value', subTotal);
                total += subTotal;
            }
        });
        Y.one('.total-area input').set('value', total);
    }
    function initHouseObjects() {
        var houseobjects = Y.all('.house-object');
        houseobjects.each(function(houseobject) {
            var hStartCalendar = new Y.EliteStar.ECalendar({
                selector: '#houseobject-' + houseobject.getAttribute('data-id') + ' .calendar-start-btn',
                textSelector: '#houseobject-' + houseobject.getAttribute('data-id') + ' input[name="duration_start"]',
                id: 'houseobject-cal-start-' + houseobject.getAttribute('data-id'),
                dateFormat: '%Y/%b/%d'
            });
    
            var hEndCalendar = new Y.EliteStar.ECalendar({
                selector: '#houseobject-' + houseobject.getAttribute('data-id') + ' .calendar-end-btn',
                textSelector: '#houseobject-' + houseobject.getAttribute('data-id') + ' input[name="duration_end"]',
                id: 'houseobject-cal-end-' + houseobject.getAttribute('data-id'),
                dateFormat: '%Y/%b/%d'
            });
        });
    }
    caculateProfits();
    initHouseObjects();
    Y.delegate('change', caculateProfits, Y.one('#profits'), 'input');
}
});
