YUI.add("loginpanel", function(Y) {
if (Y.one('.registerform-login')) {
    Y.one('.registerform-login').on('click', function(){
        Y.one('.login-panel').removeClass('hidden');
    });
}

if (Y.one('.btn-container.nonLogin')) {
    Y.one('.btn-right').on('click', function(){
        var loginPanel = Y.one('.login-panel');
        loginPanel.removeClass('hidden');
        loginPanel.setStyle('top', '');
        loginPanel.setStyle('left', '');
    });
}


var loginBtns = Y.all('.need-sign-in-btn');
loginBtns.each(function(loginBtn) {
    loginBtn.on('click', function(e) {
        var loginPanel = Y.one('.login-panel');
        loginPanel.removeClass('hidden');
        loginPanel.setStyle('position', 'absolute');
        loginPanel.setStyle('top', (e.pageY + 20) + 'px');
        loginPanel.setStyle('left', (e.pageX + 120) + 'px');
        
    });
});

Y.one('.login-panel .registerform-close').on('click', function(){
    Y.one('.login-panel').addClass('hidden');
});

}, '0.0.1', {requires: ['base', 'node']});
