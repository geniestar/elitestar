YUI({
    modules: {
        mapper: '/js/mapper.js',
        ecalendar: '/js/ecalendar.js',
        hintpanel: '/js/hint_panel.js',
    }
}).use('node', 'mapper', 'ecalendar', 'hintpanel', function(Y) {
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
    }
    var switchToHouseowner = function() {
        switchRole('h')
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
       e.parentNode.remove(); 
    }
    Y.one('.registerform-add').on('click', function(e){
        var tmpNode = Y.one('.des-template').cloneNode(10);
        Y.one('#more-description').append(tmpNode);
        tmpNode.removeClass('hidden');
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
        for (var key in checkArray) {
            var checkItem = checkArray[key];
            console.log(checkItem);
            if ('' === Y.one('#' + formId + ' input[name="' + checkItem + '"]').get('value')) {
                return false;
            }
        }
        return true;
    }
    var btns = Y.all('#register-form form input[type=submit]')
    btns.each(function(btn) {
        btn.on('click', function(e)  {
            e.preventDefault();
            if ('b-submit' === e.target.get('id')) {
                if (!checkInput('user-form', ['name', 'id', 'password', 'retype_password'])) {
                    alert(YAHOO.EliteStar.lang.REG_FILED_EMPTY);
                    return false;
                }
                if (!checkInput('backpacker-form', ['arrival_time', 'duration_start', 'duration_end', 'rent', 'email', 'phone'])) {
                    alert(YAHOO.EliteStar.lang.REG_FILED_EMPTY);
                    return false;
                }
                if (Y.one('#user-form input[name="password"]').get('value') !== Y.one('#user-form input[name="retype_password"]').get('value')) {
                    alert(YAHOO.EliteStar.lang.REG_PW_NOT_MATCH);
                    return false;
                }
                if (Y.one('#user-form input[name="password"]').get('value').length < 8) {
                    alert(YAHOO.EliteStar.lang.REG_PW_LENGTH);
                    return false;
                }
                if (!Y.one('#backpacker-form-all input[name="agree"]').get('checked')) {
                    alert(YAHOO.EliteStar.lang.REG_READ_TOS);
                    return false;
                }
                backpackerSubmit();
            } else {
                if (!checkInput('user-form', ['name', 'id', 'password', 'retype_password'])) {
                    alert(YAHOO.EliteStar.lang.REG_FILED_EMPTY);
                    return false;
                }
                if (!checkInput('houseowner-form', ['address', 'housename', 'duration_start', 'duration_end', 'rent', 'email', 'phone'])) {
                    alert(YAHOO.EliteStar.lang.REG_FILED_EMPTY);
                    return false;
                }
                if (Y.one('#user-form input[name="password"]').get('value') !== Y.one('#user-form input[name="retype_password"]').get('value')) {
                    alert(YAHOO.EliteStar.lang.REG_PW_NOT_MATCH);
                    return false;
                }
                if (Y.one('#user-form input[name="password"]').get('value').length < 8) {
                    alert(YAHOO.EliteStar.lang.REG_PW_LENGTH);
                    return false;
                }
                if (!Y.one('#houseowner-form-all input[name="agree"]').get('checked')) {
                    alert(YAHOO.EliteStar.lang.REG_READ_TOS);
                    return false;
                }
                houseownerSubmit();
            }
            e.target.get('form').submit();
            return false;
        });
    });
});
