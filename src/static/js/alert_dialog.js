YUI.add("alertdialog", function(Y) {

Y.one('body').append('<div id="alert-dialog" class="hidden"><div class="message"></div><div class="btn-area"><div class="btn">' + YAHOO.EliteStar.lang.COMMON_OK + '</div></div><div class="listing-delete listing-sprite"></div></div>');
function tmpAlert(message) {
    var dialog = Y.one('#alert-dialog');
    dialog.one('.message').set('innerHTML', message);
    //dialog.setStyle('margin-top', '-' + dialog.get('offsetHeight')/2 + 'px');
    dialog.removeClass('hidden');
}

alert = tmpAlert; // wrap the alert function to show the same style of dialog
Y.one('#alert-dialog').one('.listing-delete').on('click', function(){
    Y.one('#alert-dialog').addClass('hidden'); 
});

Y.one('#alert-dialog').one('.btn').on('click', function(){
    Y.one('#alert-dialog').addClass('hidden'); 
});

Y.one('html').on('key', function(e) {
    var dialog = Y.one('#alert-dialog');
    if (!dialog.hasClass('hidden')) {
        dialog.addClass('hidden');
    }
}, 'enter');

}, '0.0.1', {requires: ['base', 'node', 'event-key']});
