(function(window, $){

	$.fn.mediaView = function(options){

		var settings = $.extend({

			viewHeight: 426,
			canvasColor: '#111',
			captionColor: '#fff'

		}, options);

		$(this).each(function(){

			//selectors
			var mvGallery = $(this);
			var mvCaption = mvGallery.find('.mv-caption');
			var mvCanvas = mvGallery.find('.mv-canvas');
			var mvCanvasInner = mvCanvas.find('.mv-canvas-inner');
			var mvLoader = mvGallery.find('.mv-loader');
			var mvImageCanvas = mvCanvasInner.find('.mv-photo');
			var mvIframeCanvasWrapper = mvCanvasInner.find('.mv-iframe-wrapper');
			var mvIframeCanvas = mvIframeCanvasWrapper.find('.mv-iframe');
			var viewFullButton = mvGallery.find('a.mv-view-full');
			var viewThumbsButton = mvGallery.find('a.mv-view-thumbs');
			var esCarousel	= mvGallery.find('div.mv-carousel-wrapper');
			var items = esCarousel.find('li');

			//data

			//total number of items
			var itemsCount = items.length;
			//index of the current item
			var current	= 0;
			//mode : carousel || fullview
			var mode = 'carousel';
			//control if one image is being loaded
			var anim = false;

			//add full and thumb toggle click events
			addViewModes();

			//modify canvas wrapper
			modifyImageWrapper();

			//initialize the carousel
			initCarousel();

			//show first item
			showImage(items.eq(0));

			//////////////////////////////
			//////////functions///////////
			//////////////////////////////

			//show and hide carousel click events
			function addViewModes(){

				viewFullButton.click(function(){

					if(mode === 'carousel')
						esCarousel.elastislide('destroy');

					esCarousel.slideUp();
					viewFullButton.addClass('mv-view-selected');
					viewThumbsButton.removeClass('mv-view-selected');
					mode = 'fullview';
					return false;

				});

				viewThumbsButton.click(function(){

					initCarousel();
					viewThumbsButton.addClass('mv-view-selected');
					viewFullButton.removeClass('mv-view-selected');
					mode = 'carousel';
					return false;

				});

			}

			//customize canvas wrapper
			function modifyImageWrapper(){

				mvCanvas.css('backgroundColor', settings.canvasColor);
				mvCaption.css('color', settings.captionColor);

				if(itemsCount > 1) {

					// add touchwipe events on the large image wrapper
					mvCanvasInner.touchwipe({

						wipeLeft: function() {
							navigate('right');
							navigate('right');
						},
						wipeRight: function() {
							navigate('left');
						},
						preventDefaultEvents: false

					});

					$(document).on('keyup', function(e){

						if (e.keyCode == 39)
							navigate('right');
						else if (e.keyCode == 37)
							navigate('left');

					});

				}

			}

			//show an image or video on canvas
			function showImage(item){

				//hide canvases for the reset
				mvImageCanvas.addClass('mv-no-transition').removeClass('visible');
				mvIframeCanvasWrapper.removeClass('visible');
				mvIframeCanvas.attr('src', '');
				mvCaption.hide().empty();

				//show loader
				mvLoader.show();

				items.removeClass('selected');
				item.addClass('selected');

				//get source data
				var thumb = item.find('a');
				var largesrc = thumb.data('large');
				var title = thumb.data('description');
				var type = thumb.data('type');

				//if content type is iframe
				if(type == 'iframe'){

					mvImageCanvas.hide();
					mvIframeCanvasWrapper.show();

					//set iframe src
					mvIframeCanvas.attr('src', largesrc);

					//hide loader
					mvLoader.hide();

					//show content
					mvIframeCanvasWrapper.addClass('visible');
						
					//show caption if needed
					if(title)
						mvCaption.show().html('<p>' + title + '</p>');

					//fix for videos not setting current for some reason
					current	= item.index();

					//move carousel
					if(mode === 'carousel'){
						esCarousel.elastislide('reload');
						esCarousel.elastislide('setCurrent', current);
					}

					anim = false;

				//if content type is image
				}else{

					$('<img/>').on('load', function() {

						mvIframeCanvasWrapper.hide();
						mvImageCanvas.show();
						

						//mvImageCanvas.attr('src', '');

						//set image src
						mvImageCanvas.css('max-height', settings.viewHeight + 'px').attr('src', largesrc);
					
						//hide loader
						mvLoader.hide();

						mvImageCanvas.removeClass('mv-no-transition').addClass('visible');

						//show caption if needed
						if(title)
							mvCaption.html('<p>' + title + '</p>').show();

						//move carousel
						if(mode === 'carousel'){
							esCarousel.elastislide('reload');
							esCarousel.elastislide('setCurrent', current);
						}

						anim = false;


					}).attr('src', largesrc);

				}

				return false;

			}

			//init carousel
			function initCarousel(){

				esCarousel.slideDown().elastislide({

					imageW: 65,
					onClick: function(item){

						if(anim) 
							return false;
							
						anim = true;

						//on click show image
						showImage(item);

						//change current
						current	= item.index();

					}

				});

				//set elastislide's current to current
				esCarousel.elastislide('setCurrent', current);

			}

			//navigate thumbnail choices
			function navigate(dir){

				if(anim) 
					return false;

				anim = true;

				if(dir === 'right'){

					if(current + 1 >= itemsCount)
						current = 0;
					else
						++current;

				}else if(dir === 'left') {

					if(current - 1 < 0)
						current = itemsCount - 1;
					else
						--current;

				}

				showImage(items.eq(current));

			}

		});

	};

	// http://www.netcu.de/jquery-touchwipe-iphone-ipad-library
	$.fn.touchwipe = function(settings) {

		var config = {
			min_move_x: 20,
			min_move_y: 20,
			wipeLeft: function() { },
			wipeRight: function() { },
			wipeUp: function() { },
			wipeDown: function() { },
			preventDefaultEvents: true
		};

		if(settings) 
			$.extend(config, settings);

		this.each(function() {

			var startX;
			var startY;
			var isMoving = false;

			function cancelTouch() {

				this.removeEventListener('touchmove', onTouchMove);
				startX = null;
				isMoving = false;

			}

			function onTouchMove(e) {

				if(config.preventDefaultEvents) 
					e.preventDefault();
			
				if(isMoving) {

					var x = e.touches[0].pageX;
					var y = e.touches[0].pageY;
					var dx = startX - x;
					var dy = startY - y;

					if(Math.abs(dx) >= config.min_move_x){

						cancelTouch();

						if(dx > 0)
							config.wipeLeft();
						else
							config.wipeRight();
						
					}else if(Math.abs(dy) >= config.min_move_y){

						cancelTouch();

						if(dy > 0) 
							config.wipeDown();
						else 
							config.wipeUp();
						
					}

				}

			}

			function onTouchStart(e)
			{

				if (e.touches.length == 1) {

					startX = e.touches[0].pageX;
					startY = e.touches[0].pageY;
					isMoving = true;
					this.addEventListener('touchmove', onTouchMove, false);

				}

			}

			if('ontouchstart' in document.documentElement) 
				this.addEventListener('touchstart', onTouchStart, false);
			
		});

		return this;

	};

	$.elastislide = function(options, element){

		this.$el = $(element);
		this._init(options);

	};

	$.elastislide.defaults 	= {

		speed		: 450,	// animation speed
		easing		: '',	// animation easing effect
		imageW		: 65,	// the images width
		margin		: 3,	// image margin right
		border		: 3,	// image border
		minItems	: 1,	// the minimum number of items to show when we resize the window, this will make sure minItems are always shown
		current		: 0,	// index of the current item when we resize the window, the carousel will make sure this item is visible
		onClick		: function() { return false; } // click item callback

    };

	$.elastislide.prototype = {

		_init: function( options ) {

			this.options = $.extend(true, {}, $.elastislide.defaults, options);

			// <ul>
			this.$slider = this.$el.find('ul');

			// <li>
			this.items	= this.$slider.children('li');

			// total number of elements / images
			this.itemsCount	= this.items.length;

			// cache the <ul>'s parent, since we will eventually need to recalculate its width on window resize
			this.esCarousel = this.$slider.parent();

			// validate options
			this._validateOptions();

			// set sizes and initialize some vars...
			this._configure();

			// add navigation buttons
			this._addControls();

			// initialize the events
			this._initEvents();

			// show the <ul>
			this.$slider.show();

			// slide to current's position
			this._slideToCurrent(false);

		},
		_validateOptions: function() {

			if(this.options.speed < 0)
				this.options.speed = 450;
			if(this.options.margin < 0)
				this.options.margin = 4;
			if(this.options.minItems < 1 || this.options.minItems > this.itemsCount)
				this.options.minItems = 1;
			if(this.options.current > this.itemsCount - 1)
				this.options.current = 0;

		},
		_configure: function() {

			// current item's index
			this.current = this.options.current;

			// the ul's parent's (div.es-carousel) width is the "visible" width
			this.visibleWidth = this.esCarousel.width();

			// test to see if we need to initially resize the items
			if(this.visibleWidth < this.options.minItems * (this.options.imageW + 2 * this.options.border) + (this.options.minItems - 1) * this.options.margin) {

				this._setDim((this.visibleWidth - (this.options.minItems - 1) * this.options.margin) / this.options.minItems);
				this._setCurrentValues();

				// how many items fit with the current width
				this.fitCount	= this.options.minItems;

			}
			else {
				this._setDim();
				this._setCurrentValues();
			}

			// set the <ul> width
			this.$slider.css({
				width	: this.sliderW
			});

		},
		_setDim: function(elW) {

			// <li> style
			this.items.css({
				marginRight: this.options.margin,
				width: (elW) ? elW : this.options.imageW + 2 * this.options.border
			});

		},
		_setCurrentValues: function() {

			// the total space occupied by one item
			this.itemW = this.items.outerWidth(true);

			// total width of the slider / <ul>
			// this will eventually change on window resize
			this.sliderW = this.itemW * this.itemsCount;

			// the ul parent's (div.es-carousel) width is the "visible" width
			this.visibleWidth = this.esCarousel.width();

			// how many items fit with the current width
			this.fitCount = Math.floor(this.visibleWidth / this.itemW);

		},
		_addControls: function() {

			this.$navNext = $('<span class="es-nav-next">Next</span>');
			this.$navPrev = $('<span class="es-nav-prev">Previous</span>');

		},
		_toggleControls: function( dir, status ) {//to remove

			// show / hide navigation buttons
			if( dir && status ) {
				if( status === 1 )
					( dir === 'right' ) ? this.$navNext.show() : this.$navPrev.show();
				else
					( dir === 'right' ) ? this.$navNext.hide() : this.$navPrev.hide();
			}
			else if( this.current === this.itemsCount - 1 || this.fitCount >= this.itemsCount )
					this.$navNext.hide();

		},
		_initEvents: function() {

			var instance = this;

			// window resize
			$(window).on('resize.elastislide', function(event) {

				instance._reload();

				// slide to the current element
				clearTimeout(instance.resetTimeout);
				instance.resetTimeout	= setTimeout(function() {
					instance._slideToCurrent();
				}, 200);

			});

			// navigation buttons events
			this.$navNext.on('click.elastislide', function( event ) {
				instance._slide('right');
			});

			this.$navPrev.on('click.elastislide', function( event ) {
				instance._slide('left');
			});

			// item click event
			this.$slider.on('click.elastislide', 'li', function( event ) {
				instance.options.onClick( $(this) );
				return false;
			});

			// touch events
			instance.$slider.touchwipe({
				wipeLeft: function() {
					instance._slide('right');
				},
				wipeRight: function() {
					instance._slide('left');
				}
			});

		},
		reload: function(callback) {

			this._reload();

			if(callback)
				callback.call();

		},
		_reload: function() {

			var instance = this;

			// set values again
			instance._setCurrentValues();

			// need to resize items
			if(instance.visibleWidth < instance.options.minItems * ( instance.options.imageW + 2 * instance.options.border ) + ( instance.options.minItems - 1 ) * instance.options.margin) {

				instance._setDim( ( instance.visibleWidth - ( instance.options.minItems - 1 ) * instance.options.margin ) / instance.options.minItems );
				instance._setCurrentValues();
				instance.fitCount = instance.options.minItems;

			}
			else{

				instance._setDim();
				instance._setCurrentValues();

			}

			instance.$slider.css({
				width: instance.sliderW + 10 // TODO: +10px seems to solve a firefox "bug" :S
			});

		},
		_slide: function( dir, val, anim, callback ) {

			// current margin left
			var ml = parseFloat(this.$slider.css('margin-left'));

			// val is just passed when we want an exact value for the margin left (used in the _slideToCurrent function)
			if(val === undefined) {

				// how much to slide?
				var amount	= this.fitCount * this.itemW, val;
				amount = amount + this.itemW;

				if(amount < 0) 
					return false;

				// make sure not to leave a space between the last item / first item and the end / beggining of the slider available width
				if(dir === 'right' && this.sliderW - (Math.abs(ml) + amount) < this.visibleWidth) {

					amount	= this.sliderW - (Math.abs(ml) + this.visibleWidth) - this.options.margin; // decrease the margin left

				}
				else if(dir === 'left' && Math.abs(ml) - amount < 0) {

					amount	= Math.abs(ml);
				
				}
				else {

					var fml; // future margin left

					(dir === 'right')
						? fml = Math.abs(ml) + this.options.margin + Math.abs(amount)
						: fml = Math.abs(ml) - this.options.margin - Math.abs(amount);

				}

				(dir === 'right') ? val = '-=' + amount : val = '+=' + amount;

			}
			else {

				if(Math.max(this.sliderW, this.visibleWidth) - fml < this.visibleWidth) {
					
					val	=- (Math.max(this.sliderW, this.visibleWidth) - this.visibleWidth);

					if(val !== 0)
						val += this.options.margin;	// decrease the margin left if not on the first position

					fml	= Math.abs(val);

				}
				else
				{

					if(val !== 0)
						val = val + this.visibleWidth * 0.35 + this.options.margin;

				}

			}

			$.fn.applyStyle = (anim === undefined) ? $.fn.animate : $.fn.css;

			var sliderCSS = { marginLeft : val };

			var instance = this;

			this.$slider.stop().applyStyle(sliderCSS, $.extend(true, [], { duration : this.options.speed, easing : this.options.easing, complete : function() {

				if(callback) 
					callback.call();

			} } ) );

		},
		_slideToCurrent: function(anim) {

			// how much to slide?
			var amount	= this.current * this.itemW;
			this._slide('', -amount, anim);

		},
		add: function($newelems, callback) {

			// adds new items to the carousel
			this.items = this.items.add($newelems);
			this.itemsCount = this.items.length;

			this._setDim();
			this._setCurrentValues();
			this.$slider.css({
				width	: this.sliderW
			});
			this._slideToCurrent();

			if (callback) 
				callback.call($newelems);

		},
		setCurrent: function(idx, callback) {

			this.current = idx;

			var ml = Math.abs(parseFloat(this.$slider.css('margin-left')));
			var posR = ml + this.visibleWidth;
			var fml	= Math.abs(this.current * this.itemW);

			//the last bit makes sure there is no tiny thumbs on right of slider, by sliding them over when clicking on earlier thumbs
			if(fml + this.itemW > posR || fml < ml + this.itemW || posR - fml < 200 || fml + this.itemW > ml)
				this._slideToCurrent();

			if(callback) 
				callback.call();

		},
		destroy: function(callback) {

			this._destroy(callback);

		},
		_destroy: function(callback) {

			this.$el.off('.elastislide').removeData('elastislide');
			$(window).off('.elastislide');

			if(callback) 
				callback.call();

		}

	};

	$.fn.elastislide = function( options ) {

		if ( typeof options === 'string' ) {

			var args = Array.prototype.slice.call( arguments, 1 );

			this.each(function() {

				var instance = $.data( this, 'elastislide' );

				if ( !instance ) {

					console.log('cannot call methods on elastislide prior to initialization; attempted to call method "' + options + '"');
					return;

				}

				if (!$.isFunction( instance[options] ) || options.charAt(0) === "_") {

					console.log('no such method "' + options + '" for elastislide instance');
					return;

				}

				instance[ options ].apply( instance, args );

			});

		}else{

			this.each(function() {

				var instance = $.data( this, 'elastislide' );

				if(!instance) 
					$.data( this, 'elastislide', new $.elastislide( options, this ) );
				
			});

		}

		return this;

	};

})(window, jQuery);