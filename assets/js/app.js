import '../css/app.scss';

var mvFrontend = {};

mvFrontend.initialize = function($) {
  mvFrontend.setupEvents($);
};

mvFrontend.setupEvents = function($) {
  $(function() {
    $('.mv-gallery').each(function() {
      var self = $(this);

      $(this).mediaView({
        viewHeight: self.data('viewheight'),
        canvasColor: self.data('canvascolor'),
        captionColor: self.data('captioncolor')
      });
    });
  });
};

(function($){mvFrontend.initialize($);})(jQuery);
