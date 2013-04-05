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
        }
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
    var hCalendar = new Y.EliteStar.ECalendar({
        selector: '#houseowner-form #cal-btn-house',
        textSelector: '#houseowner-form input[name="duration_custom_define"]',
        id: 'cal-btn-house-cal',
        dateFormat: '%Y/%b/%d'
    });
    
    Y.one('#houseowner-form').one('select[name="state"]').on('change', function(e) {
        var id = e.target.get('value');
        replaceAllSuburbs('#houseowner-form .city-selection', id);
    });
    var moreDescriptionCount = 1;
    Y.one('.registerform-add').on('click', function(e){
        var tmpNode = Y.one('.des-template').cloneNode(10);
        Y.one('#more-description').append(tmpNode.set('id','more-des-' + moreDescriptionCount));
        tmpNode = Y.one('#more-des-' + moreDescriptionCount);
        tmpNode.one('select[name="position-tmpl"]').set('name', 'position-' + moreDescriptionCount);
        tmpNode.one('select[name="vehicle-tmpl"]').set('name', 'vehicle-' + moreDescriptionCount);
        tmpNode.one('input[name="km-tmpl"]').set('name', 'km-' + moreDescriptionCount);
        tmpNode.one('input[name="mins-tmpl"]').set('name', 'mins-' + moreDescriptionCount);
        tmpNode.removeClass('hidden');
        moreDescriptionCount++;
    });
});
