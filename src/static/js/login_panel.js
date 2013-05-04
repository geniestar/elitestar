YUI.add("loginpanel", function(Y) {
if (Y.one('.registerform-login')) {
    Y.one('.registerform-login').on('click', function(){
        Y.one('.login-panel').removeClass('hidden');
    });
}

if (Y.one('.btn-container.nonLogin')) {
    Y.one('.btn-right').on('click', function(){
        Y.one('.login-panel').removeClass('hidden');
    });
}

Y.one('.login-panel .registerform-close').on('click', function(){
    Y.one('.login-panel').addClass('hidden');
});

}, '0.0.1', {requires: ['base', 'node']});
