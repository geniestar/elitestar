YUI({
    modules: {
        houseobject: '/js/houseobject.js',
    }
}).use('node', 'houseobject', function(Y) {

    var results = Y.all('.search-result');
    results.each(function(result) {
        var houseObject = new Y.EliteStar.houseObject({
            resultId: result.get('id')
        });
    result});

});
