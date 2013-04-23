YUI.add("houseobject", function(Y)
{
    function houseObject(config) {
        houseObject.superclass.constructor.apply(this, arguments);
    }
    houseObject.NS = 'houseobject';
    houseObject.NAME = 'houseobject';
    houseObject.ATTRS = {
        resultId: null,
    };

    Y.extend(houseObject, Y.Base, {
        initedScrollview: false,
        initedGoogleMap: false,
        initializer: function(cfg) {
            var thisObject = this;
            var result = Y.one('#' + cfg.resultId);
            result.one('.detail-btn').on('click', function() {
                result.one('.combined-content').removeClass('hidden');
                result.one('.pic-content').addClass('hidden');
                result.one('.map-content').addClass('hidden');
                result.one('.detail-btn').addClass('selected');
                result.one('.pic-btn').removeClass('selected');
                result.one('.map-btn').removeClass('selected');
            });
            result.one('.pic-btn').on('click', function() {
                result.one('.combined-content').addClass('hidden');
                result.one('.pic-content').removeClass('hidden');
                result.one('.map-content').addClass('hidden');
                result.one('.detail-btn').removeClass('selected');
                result.one('.pic-btn').addClass('selected');
                result.one('.map-btn').removeClass('selected');
                if (!thisObject.initedScrollview) {
                    thisObject._initScrollView(cfg.resultId);
                    thisObject.initedScrollview = true;
                }
            });
            result.one('.map-btn').on('click', function() {
                result.one('.combined-content').addClass('hidden');
                result.one('.pic-content').addClass('hidden');
                result.one('.map-content').removeClass('hidden');
                result.one('.detail-btn').removeClass('selected');
                result.one('.pic-btn').removeClass('selected');
                result.one('.map-btn').addClass('selected');
                if (!thisObject.initedGoogleMap) {
                    thisObject._initGoogleMap(cfg.resultId);
                    thisObject.initedGoogleMap = true;
                }
            });
            result.one('.listing-save_favorite').on('click', this._saveFavorite);
        },
        
        _saveFavorite: function(e) {
            e.target.getAttribute('data-id');
            var cfg = {
                method: 'POST',
                sync: true,
                data: {
                    id: e.target.getAttribute('data-id'),
                    role: 0,
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
        },

        _initScrollView: function(resultId) {
            if (Y.one('#' + resultId + ' .pic-content').hasClass('is-scroll')) {
                var scrollView = new Y.ScrollView({
                    id: 'scrollview-' + resultId,
                    srcNode : '#' + resultId + ' .pics ul',
                    width : 492,
                }); 
                scrollView.render();
                Y.one('#' + resultId + ' .theme-icon-arrow_left').on('click', function(e){
                    var scrollX = scrollView.get('scrollX') - 200;
                    if (scrollX < 0) {
                        scrollX = 0;
                    }
                    scrollView.scrollTo(scrollX, 0, 500, "ease-in");
                });
                Y.one('#' + resultId + ' .theme-icon-arrow_right').on('click', function(e){
                    scrollView.scrollTo(scrollView.get('scrollX') + 200, 0, 500, "ease-in");
                });
            }
        },

        _initGoogleMap: function(resultId) {
            var geocoder;
            var map;
            var mapDomObject = Y.one('#' + resultId + '-map');
            var address = mapDomObject.getAttribute('data-address');
            function initialize() {
                geocoder = new google.maps.Geocoder();
                var myOptions = {
                zoom: 10,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                disableDefaultUI: true
                }
                map = new google.maps.Map(document.getElementById(resultId + '-map'), myOptions);
                codeAddress();
            }
            function codeAddress() {
                if (geocoder) {
                    geocoder.geocode({ 'address': address }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            map.setCenter(results[0].geometry.location);
                            var marker = new google.maps.Marker({
                                map: map,
                                position: results[0].geometry.location
                            });
                        } else {
                            alert("Geocode was not successful for the following reason: " + status);
                        }
                    });
                }
            }
            initialize();
        }
    });
    
    Y.namespace("EliteStar");
    Y.EliteStar.houseObject = houseObject;

}, '0.0.1', {requires: ['base', 'node', 'scrollview', 'io-base']});
