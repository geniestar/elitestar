YUI.add("backpacker", function(Y)
{
    function backpacker(config) {
        backpacker.superclass.constructor.apply(this, arguments);
    }
    backpacker.NS = 'backpacker';
    backpacker.NAME = 'backpacker';
    backpacker.ATTRS = {
        resultId: null,
    };

    Y.extend(backpacker, Y.Base, {
        initializer: function(cfg) {
            var thisObject = this;
            var result = Y.one('#' + cfg.resultId);
            result.one('.listing-save_favorite').on('click', this._saveFavorite);
        },
        
        _saveFavorite: function(e) {
            e.target.getAttribute('data-id');
            var cfg = {
                method: 'POST',
                sync: true,
                data: {
                    id: e.target.getAttribute('data-id'),
                    role: 1,
                    action: 'add'
                }
            };
            request = Y.io('/ajax/favorite.php', cfg);
            res = JSON.parse(request.responseText);
            if ('SUCCESS' === res.status) {
                Y.one('#favorites .favorites').append(res.data.html);
                alert(res.data.message);
            } else {
                alert(res.message);
            }
        }
    });
    
    Y.namespace("EliteStar");
    Y.EliteStar.backpacker = backpacker;

}, '0.0.1', {requires: ['base', 'node', 'io-base']});
