YUI.add("recommend", function(Y)
{
    Y.one('body').append('<div id="recommend-desc-panel" class="hidden"></div>');
    YAHOO.EliteStar.getRecommend = function(type, objectid) {
        if (!objectid)
        {
            objectid = '';
        }
        if (!type)
        {
            type = '';
        }
        var cfg = {
            method: 'POST',
            data: {
                action: 'recommend',
                type: type,
                objectid: objectid
            }
        };
        
        var request = Y.io('/ajax/admin_action.php', cfg);
        Y.on('io:complete', function(id, o, args){
            if (id === request.id) {
                res = JSON.parse(o.responseText);
                if ('SUCCESS' === res.status) {
                    Y.one('#recommend-area').set('innerHTML', res.data.html);
                    Y.one('#recommend-area').removeClass('hidden');
                }
            }
        });


    }
    var showDetail = function(e) {
        e.preventDefault();
        var tmpNode = e.target.get('parentNode').get('parentNode').get('parentNode').one('.recommend-desc');
        var panel = Y.one('#recommend-desc-panel');
        panel.set('innerHTML', tmpNode.get('innerHTML'));
        panel.setStyle('top', (e.pageY - 50) + 'px');
        panel.setStyle('left', (e.pageX - 250) + 'px');
        panel.removeClass('hidden');
    };
    var removeRecommend = function(e) {
        e.target.get('parentNode').get('parentNode').remove();
        var firestHiddenItem = Y.one('#recommend-area .item.hidden');
        if (firestHiddenItem) {
            firestHiddenItem.removeClass('hidden')
        }
        if (Y.all('#recommend-area .item').size() == 0) {
            Y.one('#recommend-area').addClass('hidden');
        }
    }

    var sendMessage = function(e) {
        e.preventDefault();
        var cfg = {
            method: 'POST',
            sync: true,
            form: {
                id: 'message-panel-' + e.target.getAttribute('data-id'),
                useDisabled: true,
            }
        };
        request = Y.io('/ajax/messages.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            Y.one('#message-panel-' + e.target.getAttribute('data-id') + ' textarea').set('value', '');
            alert(res.data.message);
        } else {
            alert(res.message);
        }
    };

    Y.delegate('click', sendMessage, Y.one('#recommend-area'), 'form input[type="submit"]');
    Y.delegate('click', showDetail, Y.one('#recommend-area'), '.name-detail');
    Y.delegate('click', removeRecommend, Y.one('#recommend-area'), '.admin-close');
    Y.delegate('click', function(e) {
        Y.one('#recommend-desc-panel').addClass('hidden');
    }, Y.one('#recommend-desc-panel'), '.admin-close');

}, '0.0.1', {requires: ['base', 'node', 'scrollview', 'io-base', 'io-form']});
