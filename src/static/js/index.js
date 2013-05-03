YUI().use('node', 'event', function(Y) {
    Y.one('#find-house-btn').on('mouseover', function(){
        Y.one('#welcome-board').set('className', 'find-house'); 
        Y.one('#login #footer').set('className', 'find-house');
        Y.one('.register-now-main-btn').addClass('hidden');
        Y.one('form').removeClass('hidden');
    });
    Y.one('#find-customer-btn').on('mouseover', function(){
        Y.one('#welcome-board').set('className', 'find-customer'); 
        Y.one('#login #footer').set('className', 'find-customer'); 
        Y.one('.register-now-main-btn').addClass('hidden');
        Y.one('form').removeClass('hidden');
    });
    Y.one('#register-now-btn').on('mouseover', function(){
        Y.one('#welcome-board').set('className', 'register-now'); 
        Y.one('#login #footer').set('className', 'register-now'); 
        Y.one('.register-now-main-btn').removeClass('hidden');
        Y.one('form').addClass('hidden');
    });
})
