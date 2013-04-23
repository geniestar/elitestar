YUI({
    modules: {
        backpacker: '/js/backpacker.js',
    }
}).use('node', 'backpacker', function(Y) {

    var results = Y.all('.search-result');
    results.each(function(result) {
        var backpacker = new Y.EliteStar.backpacker({
            resultId: result.get('id')
        });
    });
});
