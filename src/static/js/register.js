YUI({
    modules: {
        mapper: '/js/mapper.js',
        ecalendar: '/js/ecalendar.js',
        hintpanel: '/js/hint_panel.js',
        loginpanel: '/js/login_panel.js',
    }
}).use('node', 'mapper', 'ecalendar', 'hintpanel', 'loginpanel', function(Y) {
    var switchRole = function (role){
        var backpackerForm = Y.one('#backpacker-form-all');
        var houseownerForm = Y.one('#houseowner-form-all');
        if ('b' === role) {
            backpackerForm.removeClass('hidden');
            houseownerForm.addClass('hidden');
        } else {
            backpackerForm.addClass('hidden');
            houseownerForm.removeClass('hidden');
        }
    };

    var switchToBackpacker = function() {
        switchRole('b');
        Y.one('#register-form').removeClass('houseowner');
        Y.one('#register-form').addClass('backpacker');
        Y.one('#backpacker-btn').addClass('selected');
        Y.one('#houseowner-btn').removeClass('selected');
    }
    var switchToHouseowner = function() {
        switchRole('h')
        Y.one('#register-form').addClass('houseowner');
        Y.one('#register-form').removeClass('backpacker');
        Y.one('#backpacker-btn').removeClass('selected');
        Y.one('#houseowner-btn').addClass('selected');
    }

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
    Y.one('.registerform-login').on('click', function(){
        Y.one('.login-panel').removeClass('hidden');
    });
    Y.one('.login-panel .registerform-close').on('click', function(){
        Y.one('.login-panel').addClass('hidden');
    });
    Y.one('#backpacker-btn').on('click', switchToBackpacker);
    Y.one('#houseowner-btn').on('click', switchToHouseowner);
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
       e.parentNode.parentNode.removeChild(e.parentNode);
    }
    Y.one('.registerform-add').on('click', function(e){
        var tmpNode = Y.one('.des-template').cloneNode(10);
        Y.one('#more-description').append(tmpNode);
        tmpNode.removeClass('hidden');
    });

    var oldValue = '';
    Y.one('#user-form input[name="id"]').on('change', function(){
        var value = Y.one('#user-form input[name="id"]').get('value');
        var b = Y.one('#backpacker-form input[name="email"]');
        var h = Y.one('#houseowner-form input[name="email"]');
        if (!b.get('value') || b.get('value') === oldValue) {
            b.set('value', value);
        }
        if (!h.get('value') || h.get('value') === oldValue) {
            h.set('value', value);
        }
        oldValue = value;
    });

    Y.one('#backpacker-form input[name="email"]').on('change', function(){
        var b = Y.one('#backpacker-form input[name="email"]');
        if (!b.get('value')) {
            b.set('value', Y.one('#user-form input[name="id"]').get('value'));
        }
    });

    Y.one('#houseowner-form input[name="email"]').on('change', function(){
        var b = Y.one('#houseowner-form input[name="email"]');
        if (!b.get('value')) {
            b.set('value', Y.one('#user-form input[name="id"]').get('value'));
        }
    });

    var houseownerSubmit = function() {
        var inputs = Y.one('#backpacker-form').all('input');
        inputs.each(function(input) {
            input.remove();
        });
        var selects = Y.one('#backpacker-form').all('select');
        selects.each(function(select) {
            select.remove();
        });
    };

    var backpackerSubmit = function() {
        var inputs = Y.one('#houseowner-form').all('input');
        inputs.each(function(input) {
            input.remove();
        });
        var selects = Y.one('#houseowner-form').all('select');
        selects.each(function(select) {
            select.remove();
        });
    };
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
        var parentNode = Y.one('#' + formId + ' input[name="' + name + '"]').get('parentNode');
        if (parentNode.hasClass('input-set')) {
            parentNode.removeClass('invalid');
            if ('duration_end' !== name) {
                parentNode.one('.title').set('innerHTML', parentNode.one('.title').get('innerHTML').replace('*', ''));
            } else {
                parentNode.one('.sec-title').set('innerHTML', parentNode.one('.sec-title').get('innerHTML').replace('*', ''));
            }
        }
    }

    var markFieldAsInvalid = function(formId, name) {
        var parentNode = Y.one('#' + formId + ' input[name="' + name + '"]').get('parentNode');
        if ((parentNode.hasClass('input-set') && !parentNode.hasClass('invalid')) || 'duration_end' === name) {
            parentNode.addClass('invalid');
            if ('duration_end' !== name) {
                parentNode.one('.title').set('innerHTML', '*' + parentNode.one('.title').get('innerHTML'));
            } else {
                parentNode.one('.sec-title').set('innerHTML', '*' + parentNode.one('.sec-title').get('innerHTML'));
                parentNode.one('.sec-title').set('innerHTML', parentNode.one('.sec-title').get('innerHTML').replace('**', '*'));//prevent double *
            }
        }
    }

    var btns = Y.all('#register-form form input[type=submit]')
    btns.each(function(btn) {
        btn.on('click', function(e)  {
            e.preventDefault();
            var alertMessage = '';
            var checkOK = true;
            if ('b-submit' === e.target.get('id')) {
                if (!checkInput('user-form', ['name', 'id', 'password', 'retype_password'])) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_FILED_EMPTY;
                    checkOK = false;
                }
                if (!checkInput('backpacker-form', ['arrival_time', 'duration_start', 'duration_end', 'rent', 'email', 'phone'])) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_FILED_EMPTY;
                    checkOK = false;
                }
                if (!Y.one('#user-form input[name="password"]').get('value') || !Y.one('#user-form input[name="password"]').get('value') || (Y.one('#user-form input[name="password"]').get('value') !== Y.one('#user-form input[name="retype_password"]').get('value'))) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_PW_NOT_MATCH;
                    markFieldAsInvalid('user-form', 'password');
                    markFieldAsInvalid('user-form', 'retype_password');
                    checkOK = false;
                } else {
                    markFieldAsValid('user-form', 'password');
                    markFieldAsValid('user-form', 'retype_password');
                }
                if (Y.one('#user-form input[name="password"]').get('value').length < 8) {
                    markFieldAsInvalid('user-form', 'password');
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_PW_LENGTH;
                    checkOK = false;
                } else {
                    markFieldAsValid('user-form', 'password');
                }
                if (!checkEmailFormat('user-form', ['id'])) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_ID_EMAIL;
                    checkOK = false;
                }
                if (!checkEmailFormat('backpacker-form-all', ['email'])) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_EMAIL_FORMAT;
                    checkOK = false;
                }
                if (!Y.one('#backpacker-form-all input[name="agree"]').get('checked')) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_READ_TOS;
                    checkOK = false;
                }
                if (!checkOK)
                {
                    alert(alertMessage);
                    return false;
                }
                backpackerSubmit();
                e.target.get('form').submit();
            } else {
                if (!checkInput('user-form', ['name', 'id', 'password', 'retype_password'])) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_FILED_EMPTY;
                    checkOK = false;
                }
                if (!checkInput('houseowner-form', ['address', 'housename', 'duration_start', 'duration_end', 'rent', 'email', 'phone'])) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_FILED_EMPTY;
                    checkOK = false;
                }
                if (!Y.one('#user-form input[name="password"]').get('value') || !Y.one('#user-form input[name="password"]').get('value') || (Y.one('#user-form input[name="password"]').get('value') !== Y.one('#user-form input[name="retype_password"]').get('value'))) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_PW_NOT_MATCH;
                    markFieldAsInvalid('user-form', 'password');
                    markFieldAsInvalid('user-form', 'retype_password');
                    checkOK = false;
                } else {
                    markFieldAsValid('user-form', 'password');
                    markFieldAsValid('user-form', 'retype_password');
                }
                if (Y.one('#user-form input[name="password"]').get('value').length < 8) {
                    markFieldAsInvalid('user-form', 'password');
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_PW_LENGTH;
                    checkOK = false;
                } else {
                    markFieldAsValid('user-form', 'password');
                }
                if (!checkEmailFormat('user-form', ['id'])) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_ID_EMAIL;
                    checkOK = false;
                }
                if (!checkEmailFormat('houseowner-form-all', ['email'])) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_EMAIL_FORMAT;
                    checkOK = false;
                }
                if (!Y.one('#houseowner-form-all input[name="agree"]').get('checked')) {
                    alertMessage = alertMessage || YAHOO.EliteStar.lang.REG_READ_TOS;
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
                    houseownerSubmit();
                    e.target.get('form').submit();
                });
            }
            return false;
        });
    });
});
