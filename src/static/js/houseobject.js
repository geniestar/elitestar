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
            Y.delegate('click', this.showBigPic, Y.one('#' + cfg.resultId), '.pic-content ul li img');
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
                Y.one('#message-panel-board-result-' + e.target.getAttribute('data-id') + ' textarea').set('value', '');
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
                var liCount = Y.all('#' + resultId + ' .pic-content ul li').size();
                Y.one('#' + resultId + ' .theme-icon-arrow_left').on('click', function(e){
                    var scrollX = scrollView.get('scrollX') - 200;
                    if (scrollX < 0) {
                        scrollX = 0;
                    }
                    scrollView.scrollTo(scrollX, 0, 500, "ease-in");
                });
                Y.one('#' + resultId + ' .theme-icon-arrow_right').on('click', function(e){
                    var scrollX = scrollView.get('scrollX') + 200;
                    if (scrollX > 155*liCount - 492) {
                        scrollX = 155*liCount - 492;
                    }
                    scrollView.scrollTo(scrollX, 0, 500, "ease-in");
                });
            }
        },

        _initBigGoogleMap: function (resultId, address) {
            var geocoder;
            var map;
            var mapDomObject = Y.one('#big-map');
            function initialize() {
                geocoder = new google.maps.Geocoder();
                var myOptions = {
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                }
                map = new google.maps.Map(document.getElementById('big-map'), myOptions);
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
                            console.log(YAHOO.EliteStar.lang.SEARCH_RESULT_MAP_FAILURE + status);
                        }
                    });
                }
            }
            initialize();
        },

        _initGoogleMap: function(resultId) {
            var geocoder;
            var map;
            var mapDomObject = Y.one('#' + resultId + '-map');
            var address = mapDomObject.getAttribute('data-address');
            var self = this;
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
                               console.log(YAHOO.EliteStar.lang.SEARCH_RESULT_MAP_FAILURE + status);
                        }
                    });
                }
            }
            initialize();
            initBigMap = function() {
                var panel = Y.one('#img-panel');
                panel.removeClass('hidden');
                panel.one('#big-map').removeClass('hidden');
                panel.one('img').addClass('hidden');
                self._initBigGoogleMap(resultId, address);
            }
            Y.one('#' + resultId + '-map').on('click', initBigMap);
        },

        showBigPic: function (e) {
            var panel = Y.one('#img-panel');
            /*panel.setStyle('height', '');
            panel.setStyle('width', '');
            panel.setStyle('margin-left', '');
            panel.setStyle('margin-top', '');
            panel.one('.content-field').setStyle('line-height', '');*/
            panel.one('#big-map').addClass('hidden');
            panel.one('img.big-pic').removeClass('hidden');
            panel.one('img.big-pic').set('src', '/ugc/' + e.target.getAttribute('data-src'));
            /*
            panel.one('img.big-pic').on('load', function() {
                panel.setStyle('height', panel.one('img.big-pic').get('offsetHeight'));
                panel.setStyle('width', panel.one('img.big-pic').get('offsetWidth'));
                panel.setStyle('margin-left', '-' + panel.one('img.big-pic').get('offsetWidth')/2 + 'px');
                panel.setStyle('margin-top', '-' + panel.one('img.big-pic').get('offsetHeight')/2 + 'px');
                panel.one('.content-field').setStyle('line-height', panel.one('img.big-pic').get('offsetHeight'));
            });*/
            panel.removeClass('hidden');
        }
    });
   
    Y.namespace("EliteStar");
    Y.EliteStar.houseObject = houseObject;

}, '0.0.1', {requires: ['base', 'node', 'scrollview', 'io-base', 'io-form']});
