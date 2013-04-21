YUI.add("houseobject", function(Y)
{
    function houseObject(config) {
        houseObject.superclass.constructor.apply(this, arguments);
    }
    houseObject.NS = 'houseobject';
    houseObject.NAME = 'houseobject';
    houseObject.ATTRS = {
        selector: null,
    };

    Y.extend(houseObject, Y.Base, {
        initializer: function(cfg) {
            var result = Y.one(cfg.selector);
        }
    });
    
    Y.namespace("EliteStar");
    Y.EliteStar.houseObject = houseObject;

}, '0.0.1', {requires: ['base', 'node']});
