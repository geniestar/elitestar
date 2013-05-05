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
        itemsSelector: null,
        defaultSelected: null
    };

    Y.extend(Mapper, Y.Base, {
        initializer: function(cfg) {
            var items = Y.one(cfg.selector).all(cfg.itemsSelector);
            var clickCallback = cfg.clickCallback;
            var currentSelectrion = null;
            if (true || cfg.defaultSelected) {
                states = Y.all(cfg.itemsSelector);
                states.each(function(state) {
                    if (!state.hasClass('unselected')) {
                        currentSelection = state;
                    }
                });
            } else {
                var currentSelection = null;
            }
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
                item.on('mouseover', function(){
                    if (this !== currentSelection) {
                        this.removeClass('unselected');
                    }
                });
                item.on('mouseout', function(){
                    if (this !== currentSelection) {
                        this.addClass('unselected');
                    }
                });
            });
        }
    });

    Y.namespace("EliteStar");
    Y.EliteStar.Mapper = Mapper;

}, '0.0.1', {requires: ['base', 'node']});
