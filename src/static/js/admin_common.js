YUI({
    modules: {
        mapper: '/js/mapper.js',
        ecalendar: '/js/ecalendar.js'
    }
}).use('node', 'event', 'io', 'io-form', 'mapper', 'ecalendar', function(Y) {
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
    Y.one('.admin-tab-settings').on('click', function(e){
        var serviceTab = Y.one('.admin-tab-service');
        serviceTab.removeClass('selected');
        Y.one('#' + serviceTab.getAttribute('data-id')).addClass('hidden');
        var settingsTab = Y.one('.admin-tab-settings');
        settingsTab.addClass('selected');
        Y.one('#' + settingsTab.getAttribute('data-id')).removeClass('hidden');
    });
    Y.one('.admin-tab-service').on('click', function(e){
        var settingsTab = Y.one('.admin-tab-settings');
        settingsTab.removeClass('selected');
        Y.one('#' + settingsTab.getAttribute('data-id')).addClass('hidden');
        var serviceTab = Y.one('.admin-tab-service');
        serviceTab.addClass('selected');
        Y.one('#' + serviceTab.getAttribute('data-id')).removeClass('hidden');
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

    var getForm = function(e) {
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
            Y.one('#houseobject').set('innerHTML', res.data.html);
            setupForm();
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
    Y.delegate('click', getForm, Y.one('#houseobject-selector'), '.houseobject-selector-item');
    Y.delegate('click', deletePhoto, Y.one('#ajax-role-form'), '.houseobject-photo-item .admin-close_big');

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
}
});
