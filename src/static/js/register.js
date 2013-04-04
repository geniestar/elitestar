YUI({
    modules: {
        mapper: '/js/mapper.js'
    }
}).use('node', 'mapper', function(Y) {
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

    Y.one('#backpacker-btn').on('click', switchToBackpacker);
    Y.one('#houseowner-btn').on('click', switchToHouseowner);
    var mapper = new Y.EliteStar.Mapper({
        selector: '#map-all',
        itemsSelector: '.map-item',
        clickCallback: function(id) {
            var stateInfo = YAHOO.EliteStar.params.states[id];
console.log(stateInfo);
        }
    });
});
