YUI({
    modules: {
        mapper: '/js/mapper.js',
        ecalendar: '/js/ecalendar.js'
    }
}).use('node', 'mapper', 'ecalendar', function(Y) {
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
        if (YAHOO.EliteStar.params['positionDesc'][e.value]) {
            var inputSet = e.parentNode;
            document.getElementById(inputSet.id + '-desc').setAttribute('class', '');
            var moreDesc = document.getElementById(inputSet.id + '-desc');
            var titleDiv = moreDesc.getElementsByTagName('div')[0];
            titleDiv.innerHTML = YAHOO.EliteStar.params['positionDesc'][e.value];
        } else {
            var inputSet = e.parentNode;
            document.getElementById(inputSet.id + '-desc').setAttribute('class', 'hidden');
        }
    }
    var moreDescriptionCount = 1;
    Y.one('.registerform-add').on('click', function(e){
        var tmpNode = Y.one('.des-template').cloneNode(10);
        Y.one('#more-description').append(tmpNode.set('id','more-des-' + moreDescriptionCount));
        tmpNode = Y.one('#more-des-' + moreDescriptionCount);
        tmpNode.one('select[name="position-tmpl"]').set('name', 'position-' + moreDescriptionCount);
        tmpNode.one('select[name="vehicle-tmpl"]').set('name', 'vehicle-' + moreDescriptionCount);
        tmpNode.one('input[name="km-tmpl"]').set('name', 'km-' + moreDescriptionCount);
        tmpNode.one('input[name="mins-tmpl"]').set('name', 'mins-' + moreDescriptionCount);
        tmpNode.one('input[name="desc-tmpl"]').set('name', 'desc-' + moreDescriptionCount);
        tmpNode.one('#more-des-tmpl-desc').set('id', 'more-des-' + moreDescriptionCount + '-desc');
        tmpNode.removeClass('hidden');
        moreDescriptionCount++;
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

    var btns = Y.all('#register-form form input[type=submit]')
    btns.each(function(btn) {
        btn.on('click', function(e)  {
            e.preventDefault();
            if ('b-submit' === e.target.get('id')) {
                backpackerSubmit();
            } else {
                houseownerSubmit();
            }
            e.target.get('form').submit();
            return false;
        });
    });
});
