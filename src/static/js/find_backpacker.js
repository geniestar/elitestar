YUI({
    modules: {
        backpacker: '/js/backpacker.js',
        hintpanel: '/js/hint_panel.js',
    }
}).use('node', 'backpacker', 'hintpanel', function(Y) {

    var results = Y.all('.search-result');
    results.each(function(result) {
        var backpacker = new Y.EliteStar.backpacker({
            resultId: result.get('id')
        });
    });
});
