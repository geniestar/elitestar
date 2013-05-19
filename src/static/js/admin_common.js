YUI({
    modules: {
        mapper: '/js/mapper.js',
        ecalendar: '/js/ecalendar.js',
        hintpanel: '/js/hint_panel.js',
        recommend: '/js/recommend.js',
        alertdialog: '/js/alert_dialog.js',
    }
}).use('node', 'event', 'io', 'io-form', 'mapper', 'ecalendar', 'scrollview', 'hintpanel', 'cookie', 'querystring', 'node-event-simulate', 'recommend', 'alertdialog', function(Y) {
var action;
var tab;
var objectId;
if (location.search) {
    var params = Y.QueryString.parse(location.search.substr(1)); //cut the ?
    action = params.action;
    tab = params.tab;
    objectId = params.objectId;
}
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
var showDialog = Y.Cookie.get("a", Number);
if (showDialog) {
    alert(YAHOO.EliteStar.lang.ADMIN_SAVED_IMFORMATION);
    Y.Cookie.set("a", 0);
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
            var liCount = Y.all('#houseobject-selector .container .houseobject-selector-item').size();
            Y.one('#houseobject-selector .admin-arrow-left').on('click', function(e){
                var scrollX = scrollView.get('scrollX') - 200;
                if (scrollX < 0) {
                    scrollX = 0;
                }
                scrollView.scrollTo(scrollX, 0, 500, "ease-in");
            });
            Y.one('#houseobject-selector .admin-arrow-right').on('click', function(e){
                var scrollX = scrollView.get('scrollX') + 200;
                if (scrollX > 165*liCount + 165 - 504) {
                    scrollX = 165*liCount + 165 - 504;
                }
                scrollView.scrollTo(scrollX, 0, 500, "ease-in");
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
        Y.one('input[name="tab"]').set('value', 'settings');
        Y.one('#recommend-area').set('innerHTML', '');
        Y.one('#recommend-desc-panel').addClass('hidden');
        if (Y.one('.houseobject-selector-item.selected')) {
              YAHOO.EliteStar.getRecommend('settings', Y.one('.houseobject-selector-item.selected').getAttribute('data-id'))
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
        Y.one('input[name="tab"]').set('value', 'service');
        YAHOO.EliteStar.getRecommend('service');
        Y.one('#recommend-area').set('innerHTML', '');
        Y.one('#recommend-desc-panel').addClass('hidden');
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
            data: {
                action: 'get-form',
                objectid: e.target.getAttribute('data-id'),
            }
        };

        var request = Y.io('/ajax/admin_action.php', cfg);
        Y.on('io:complete', function(id, o, args){
            if (id === request.id) {
                res = JSON.parse(o.responseText);
                if ('SUCCESS' === res.status) {
                    currentSelectedId = e.target.getAttribute('data-id');
                    Y.one('#houseobject').set('innerHTML', res.data.html);
                    Y.one('#form-submit').removeClass('hidden');
                    setupForm();
                    YAHOO.EliteStar.getRecommend('settings', e.target.getAttribute('data-id'))
                }
            }
        });
    }
    
    var deleteObject = function (e) {
        var cfg = {
            method: 'POST',
            data: {
                action: 'delete-object',
                objectid: objectIdToBeDeleted,
            }
        };
        var request = Y.io('/ajax/admin_action.php', cfg);
        Y.on('io:complete', function(id, o, args){
            if (id === request.id) {
                res = JSON.parse(o.responseText);
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
        });
    }

    var deletePhoto = function (e) {
        var cfg = {
            method: 'POST',
            data: {
                action: 'delete-photo',
                objectid: e.target.getAttribute('data-object-id'),
                photoid: e.target.getAttribute('data-photo-id'),
            }
        };
        var request = Y.io('/ajax/admin_action.php', cfg);
        Y.on('io:complete', function(id, o, args){
            if (id === request.id) {
                res = JSON.parse(o.responseText);
                if ('SUCCESS' === res.status) {
                    alert(res.data.message);
                    e.target.get('parentNode').addClass('hidden');
                }else {
                    alert(res.data.message);
                }
            }
        });
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
       e.parentNode.parentNode.removeChild(e.parentNode);
    }
    Y.one('#houseobject-add').on('click', function(e){
        var cfg = {
            method: 'POST',
            data: {
                action: 'get-form',
            }
        };
        var request = Y.io('/ajax/admin_action.php', cfg);
        Y.on('io:complete', function(id, o, args){
            if (id === request.id) {
                res = JSON.parse(o.responseText);
                if ('SUCCESS' === res.status) {
                    Y.one('#houseobject').set('innerHTML', res.data.html);
                    setupForm();
                    Y.one('#form-submit').removeClass('hidden');
                    Y.one('.houseobject-selector-item.selected').removeClass('selected')
                }
            }
        });
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

    /* swith to the tab*/
    if (tab) {
        var settingsTab = Y.one('.admin-tab-settings');
        var serviceTab = Y.one('.admin-tab-service');
        if (tab === 'service') {
            settingsTab.removeClass('selected');
            serviceTab.addClass('selected');
            Y.one('#ajax-role-form').addClass('hidden');
            Y.one('#form-service').removeClass('hidden');
            Y.one('input[name="tab"]').set('value', 'service');
            YAHOO.EliteStar.getRecommend('service');
        } else {
            settingsTab.addClass('selected');
            serviceTab.removeClass('selected');
            Y.one('#ajax-role-form').removeClass('hidden');
            Y.one('#form-service').addClass('hidden');
            if (objectId) {
                var houseObject = Y.one('#houseobject-selector-' + objectId);
                if (houseObject) {
                    houseObject.simulate('click');
                }
            } else if (objectId === '') {
                if (Y.one('.houseobject-selector-item')) {
                    Y.one('.houseobject-selector-item').simulate('click');
                }
            }
            Y.one('input[name="tab"]').set('value', 'settings');
            //YAHOO.EliteStar.getRecommend('settings', objectId);
        }
        Y.one('#form-submit').removeClass('hidden');
    } else {
        //YAHOO.EliteStar.getRecommend('settings');
    }

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
    
    YAHOO.EliteStar.getRecommend();
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
                start: startMessage[e.target.getAttribute('data-id')],
                talker: e.target.getAttribute('data-id')
            }
        };
        request = Y.io('/ajax/messages.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            var container = e.target.get('parentNode').get('parentNode').one('.container');
            var oHtml = container.get('innerHTML');
            container.set('innerHTML', res.data.html + oHtml);
            startMessage[e.target.getAttribute('data-id')] += res.data.count;
            
            if (res.data.count < 5) {
                var previousBtn = container.get('parentNode').one('#previous-messages-btn');
                previousBtn.addClass('hidden');
            }
        }
        
    }
    
    var sendMessage = function(e) {
        e.preventDefault();
        var cfg = {
            method: 'POST',
            sync: true,
            form: {
                id: 'message-form-' + e.target.getAttribute('data-id'),
                useDisabled: true,
            }
        };
        request = Y.io('/ajax/messages.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            e.target.get('parentNode').get('parentNode').get('parentNode').one('.container').append(res.data.html);
            //alert(res.data.message);
            e.target.get('parentNode').one('textarea').set('value', '');
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
                talker: e.target.getAttribute('data-id'),
                action: 'delete-messages',
            }
        };
        request = Y.io('/ajax/messages.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            alert(res.data.message);
            e.target.get('parentNode').remove();
        }
    }

    var getUnreadMessage = function() {
        var cfg = {
            method: 'POST',
            data: {
                action: 'get-unread-messages',
            }
        };
        var request = Y.io('/ajax/messages.php', cfg);
        Y.on('io:complete', function(id, o, args){
            if (id === request.id) {
                res = JSON.parse(o.responseText);
                if ('SUCCESS' === res.status) {
                    for(var key in res.data.unreads) {
                        var container = Y.one('.container[data-id="' + key +'"]');
                        if (!container) {
                            window.location.reload(); //just reload the page if message area not exist
                            return;
                        }
                        container.append(res.data.unreads[key]);
                    }
                }
            }
        });
    }
    setInterval(getUnreadMessage, 3000);
    var startMessage = []; //default;
    var messages = Y.all('.message');
    messages.each(function(message) {
        if (message.getAttribute('data-count') > 5) {
            startMessage[message.getAttribute('data-id')] = 5;
            var previousBtn = message.one('#previous-messages-btn');
            previousBtn.on('click', getMessages);
        }
    });
    Y.delegate('click', sendMessage, Y.one('#messages'), 'form input[type="submit"]');
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
        var monthMapping = {
            'Jan': '01',
            'Feb': '02',
            'Mar': '03',
            'Apr': '04',
            'May': '05',
            'Jun': '06',
            'Jul': '07',
            'Aug': '08',
            'Sep': '09',
            'Oct': '10',
            'Nov': '11',
            'Dec': '12'
        }
        function parseDate(dateString) {
            dateArray = dateString.split('/');
            dateArray[1] = monthMapping[dateArray[1]];
            return dateArray.join('-')
        }
        houseobjects.each(function(houseobject) {
            var durationStart = houseobject.one('input[name="duration_start"]').get('value');
            var durationEnd = houseobject.one('input[name="duration_end"]').get('value');
            var tenants = parseInt(houseobject.one('input[name="tenants"]').get('value'), 10);
            var rent = parseInt(houseobject.one('input[name="rent"]').get('value'), 10);
            if (durationStart && durationEnd && tenants && rent) {
                var durationStartTime = (new Date(parseDate(durationStart))).getTime()/1000;
                var durationEndTime = (new Date(parseDate(durationEnd))).getTime()/1000;
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
    //Y.delegate('change', caculateProfits, Y.one('#profits'), 'input');
    var inputs = Y.all('#profits input');
    inputs.each(function(input) {
        input.on('change', caculateProfits)
    });
}

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

    var menuBtns = Y.all('#menu .btn-container ');
    menuBtns.each(function(btn){
        btn.on('mouseover', function() {
            btn.addClass('mouseover');
        });
        btn.on('mouseout', function() {
            btn.removeClass('mouseover');
        });
    });
    
    /* checker part*/
    
    var checkInput = function(formId, checkArray) {
        var checkOK = true;
        for (var key in checkArray) {
            var checkItem = checkArray[key];
            if ('' === Y.one('#' + formId + ' input[name="' + checkItem + '"]').get('value')) {
                markFieldAsInvalid(formId, checkItem);
                checkOK = false;
            } else {
                markFieldAsValid(formId, checkItem);
            }
        }
        return checkOK;
    }

    var checkUserId = function() {
        var cfg = {
            method: 'POST',
            sync: true,
            data: {
                action: 'check-user',
                id: Y.one('#user-form input[name="id"]').get('value'),
            }
        };
        request = Y.io('/ajax/admin_action.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            return true;
        } else {
            return false;
        }
    }

    var checkEmailFormat = function(formId, checkArray) {
        var checkOK = true;
        for (var key in checkArray) {
            var checkItem = checkArray[key];
            var value = Y.one('#' + formId + ' input[name="' + checkItem + '"]').get('value');
            var pattern = /^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})*$/
            var matchArray = pattern.exec(value);
            if (!value || !matchArray) {
                markFieldAsInvalid(formId, checkItem);
                checkOK = false;
            } else {
                markFieldAsValid(formId, checkItem);
            }
        }
        return checkOK;
    }

    var checkAddress = function(cb) {
        var address = Y.one('#houseowner-form input[name="address"]').get('value');
        var state = Y.one('#houseowner-form select[name="state"]').get('value');
        var city = Y.one('#houseowner-form select[name="city"]').get('value');
        var stateName = YAHOO.EliteStar.params.states[state].name;
        var cityName = YAHOO.EliteStar.params.states[state].suburbs[city];
        var geocoder = new google.maps.Geocoder();
        if (geocoder) {
            geocoder.geocode({ 'address': stateName + ',' + cityName + ',' + address }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    markFieldAsValid('houseowner-form', 'address');
                    cb(true);
                } else {
                    markFieldAsInvalid('houseowner-form', 'address');
                    cb(false);
                }
            });
        }
        return true; //unable to check, just pass that;
    }

    var markFieldAsValid = function(formId, name) {
        /*var parentNode = Y.one('#' + formId + ' input[name="' + name + '"]').get('parentNode');
        if (parentNode.hasClass('input-set')) {
            parentNode.removeClass('invalid');
            if ('duration_end' !== name) {
                parentNode.one('.title').set('innerHTML', parentNode.one('.title').get('innerHTML').replace('*', ''));
            } else {
                parentNode.one('.sec-title').set('innerHTML', parentNode.one('.sec-title').get('innerHTML').replace('*', ''));
            }
        }*/
        Y.one('#' + formId + ' input[name="' + name + '"]').removeClass('invalid-input');
    }

    var markFieldAsInvalid = function(formId, name) {
        /*
        var parentNode = Y.one('#' + formId + ' input[name="' + name + '"]').get('parentNode');
        if ((parentNode.hasClass('input-set') && !parentNode.hasClass('invalid')) || 'duration_end' === name) {
            parentNode.addClass('invalid');
            if ('duration_end' !== name) {
                parentNode.one('.title').set('innerHTML', '*' + parentNode.one('.title').get('innerHTML'));
            } else {
                parentNode.one('.sec-title').set('innerHTML', '*' + parentNode.one('.sec-title').get('innerHTML'));
                parentNode.one('.sec-title').set('innerHTML', parentNode.one('.sec-title').get('innerHTML').replace('**', '*'));//prevent double *
            }
        }*/
        Y.one('#' + formId + ' input[name="' + name + '"]').addClass('invalid-input');
    }
    var btn = Y.one('input[type=submit]');
    if (btn) {
    btn.on('click', function(e){
        var checkOK = true;
        if ('settings' === YAHOO.EliteStar.params.type ) {
            e.preventDefault();
            var alertMessage = '';
            var role = Y.one('input[name="role"]').get('value');
            if (role == 0) {
                if (Y.one('.admin-tab.selected').getAttribute('data-id') === 'ajax-role-form') {
                    if (!checkInput('houseowner-form', ['address', 'housename', 'duration_start', 'duration_end', 'rent'])) {
                        alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_FILED_EMPTY;
                        checkOK = false;
                    }
                    checkAddress(function(addressOK){
                        if (!addressOK) {
                            alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_ADDRESS_WRONG;
                            checkOK = false;
                        }
                        if (!checkOK)
                        {
                            alert(alertMessage);
                            return false;
                        }
                        e.target.get('form').submit();
                    });
                } else {
                    e.target.get('form').submit();
                }
            } else {
                if (!checkInput('backpacker-form', ['arrival_time', 'duration_start', 'duration_end', 'rent'])) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_FILED_EMPTY;
                    checkOK = false;
                }
                if (!checkOK)
                {
                    alert(alertMessage);
                    return false;
                }
                e.target.get('form').submit();
            }
            return false;
        } else if ('basic' === YAHOO.EliteStar.params.type) {
            e.preventDefault();
                if (!checkInput('user-form', ['email', 'phone'])) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_FILED_EMPTY;
                    checkOK = false;
                }
                if (Y.one('input[name="password"]').get('value') && Y.one('input[name="retype_password"]').get('value') && (Y.one('input[name="password"]').get('value') !== Y.one('input[name="retype_password"]').get('value'))) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_PW_NOT_MATCH;
                    markFieldAsInvalid('user-form', 'password');
                    markFieldAsInvalid('user-form', 'retype_password');
                    checkOK = false;
                } else {
                    if (Y.one('#user-form input[name="password"]').get('value') && Y.one('#user-form input[name="password"]').get('value').length < 8) {
                        markFieldAsInvalid('user-form', 'password');
                        alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_PW_LENGTH;
                        checkOK = false;
                    } else {
                        markFieldAsValid('user-form', 'password');
                    }
                }
                if (!checkEmailFormat('user-form', ['email'])) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_EMAIL_FORMAT;
                    checkOK = false;
                }
                if (!checkOK)
                {
                    alert(alertMessage);
                    return false;
                }
                e.target.get('form').submit();
        }
    });
    }
});
