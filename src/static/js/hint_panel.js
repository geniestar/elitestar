YUI.add("hintpanel", function(Y) {

Y.one('body').append('<div id="hint-panel" class="hidden"></div>');

function showHint(e) {
    var hintText = e.target.getAttribute('data-hint');
    if (hintText) {
        var panel = Y.one('body #hint-panel');
        panel.set('innerHTML', hintText);
        panel.setStyle('left', (e.pageX + 10) + 'px');
        panel.setStyle('top', (e.pageY) + 'px');
        panel.removeClass('hidden');
    }

}

function hideHint(e) {
    Y.one('body #hint-panel').addClass('hidden');
}

Y.delegate('mouseover', showHint, Y.one('body'), '.hint');
Y.delegate('mouseout', hideHint, Y.one('body'), '.hint');

}, '0.0.1', {requires: ['base', 'node']});
