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
            if (result.one('.listing-save_favorite')) {
                result.one('.listing-save_favorite').on('click', this._saveFavorite);
            }
            if (result.one('.message-panel')) {
                result.one('.message-panel input[type="submit"]').on('click', this._sendMessage);
                result.one('.listing-send_message').on('click', function(){
                    Y.one('#message-panel-board-' + cfg.resultId).removeClass('hidden');
                });
                result.one('.message-panel .listing-delete').on('click', function(){
                    Y.one('#message-panel-board-' + cfg.resultId).addClass('hidden');
                });
            }
        },

        _sendMessage: function(e) {
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
                Y.one('#message-panel-board-result-' + e.target.getAttribute('data-id')).addClass('hidden');
                alert(res.data.message);
            } else {
                alert(res.message);
            }
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

}, '0.0.1', {requires: ['base', 'node', 'io-base', 'io-form']});
