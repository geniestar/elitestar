YUI({
    modules: {
        backpacker: '/js/backpacker.js',
        hintpanel: '/js/hint_panel.js',
        alertdialog: '/js/alert_dialog.js',
    }
}).use('node', 'backpacker', 'hintpanel', 'alertdialog', function(Y) {

    var results = Y.all('.search-result');
    results.each(function(result) {
        var backpacker = new Y.EliteStar.backpacker({
            resultId: result.get('id')
        });
    });
});
