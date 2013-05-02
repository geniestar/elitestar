YUI({
    modules: {
        houseobject: '/js/houseobject.js',
        hintpanel: '/js/hint_panel.js',
    }
}).use('node', 'houseobject', 'hintpanel', function(Y) {

    var results = Y.all('.search-result');
    results.each(function(result) {
        var houseObject = new Y.EliteStar.houseObject({
            resultId: result.get('id')
        });
    result});
    Y.one('#img-panel .listing-delete').on('click', function(){
        Y.one('#img-panel').addClass('hidden');
    });

});
