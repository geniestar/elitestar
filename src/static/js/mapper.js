YUI.add("mapper", function(Y)
{
    function Mapper(config) {
        Mapper.superclass.constructor.apply(this, arguments);
    }
    Mapper.NS = 'mapper';
    Mapper.NAME = 'mapper';
    Mapper.ATTRS = {
        clickCallback: null,
        selector: null,
        itemsSelector: null
    };

    Y.extend(Mapper, Y.Base, {
        initializer: function(cfg) {
            var items = Y.one(cfg.selector).all(cfg.itemsSelector);
            var clickCallback = cfg.clickCallback;
            var currentSelection = null;
            items.each(function (item) {
                item.on('click', function() {
                    if (this !== currentSelection) {
                        this.removeClass('unselected');
                        if (currentSelection) {
                            currentSelection.addClass('unselected');
                        }
                        currentSelection = this;
                        clickCallback(this.getAttribute('data-id'));
                    }
                });
            });
        }
    });

    Y.namespace("EliteStar");
    Y.EliteStar.Mapper = Mapper;

}, '0.0.1', {requires: ['base', 'node']});
