YUI().use('node', 'event', function(Y) {
    Y.one('#find-house-btn').on('click', function(){
        Y.one('#welcome-board').set('className', 'find-house'); 
        Y.one('#login footer').set('className', 'find-house'); 
    });
    Y.one('#find-customer-btn').on('click', function(){
        Y.one('#welcome-board').set('className', 'find-customer'); 
        Y.one('#login footer').set('className', 'find-customer'); 
    });
    Y.one('#register-now-btn').on('click', function(){
        Y.one('#welcome-board').set('className', 'register-now'); 
        Y.one('#login footer').set('className', 'register-now'); 
    });
})
