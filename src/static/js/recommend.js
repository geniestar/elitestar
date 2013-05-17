YUI.add("recommend", function(Y)
{
    YAHOO.EliteStar.getRecommend = function(type, objectid) {
        if (!objectid)
        {
            objectid = '';
        }
        var cfg = {
            method: 'POST',
            sync: true,
            data: {
                action: 'recommend',
                type: type,
                objectid: objectid
            }
        };
        request = Y.io('/ajax/admin_action.php', cfg);
        res = JSON.parse(request.responseText);
        if ('SUCCESS' === res.status) {
            Y.one('#recommend-area').set('innerHTML', res.data.html);
        }
    }
}, '0.0.1', {requires: ['base', 'node', 'scrollview', 'io-base', 'io-form']});
