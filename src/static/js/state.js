YUI.add("state", function(Y) {
var states = [];

YAHOO.EliteStar.params.states = function(id) {
    //sync to get state info
    if (!states[id]) {
        var cfg = {
            method: 'POST',
            sync: true,
            data: {
                state: id
            }
        };
        request = Y.io('/ajax/states.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            states[id] = res.data.state;
        } else {
        }
    }
    return states[id];
}

/*async get all the state*/
setTimeout(function(){
        var cfg = {
            method: 'POST',
            data: {
            }
        };
        var request = Y.io('/ajax/states.php', cfg);
        Y.on('io:complete', function(id, o, args){
            if (id === request.id) {
                res = JSON.parse(o.responseText);
                if ('SUCCESS' === res.status) {
                    states = res.data.states;
                }
            }
        });
}, 1000);

}, '0.0.1', {requires: ['base', 'node', 'io-base']});
