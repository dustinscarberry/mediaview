import '../css/admin.scss';

var mvAdmin = {};

mvAdmin.initialize = function($) {
  mvAdmin.setupEvents($);
};

mvAdmin.setupEvents = function($) {
  $(function() {
    $('div.updated, div.e-message').delay(3000).queue(function(){$(this).remove();});

    $('.mv-sortable-table tbody').sortable({
      placeholder: 'mv-sortable-placeholder',
      handle: '.mv-sortable-handle',
      opacity: .8,
      containment: 'parent',
      stop: function(event, ui) {
        var ordering = $('.mv-sortable-table tbody').sortable('toArray').toString();

        var data = {
          action: 'mv_orderupdate',
          order: ordering
        };

        $.post(ajaxurl, data, function(response) {});
      }
    }).disableSelection();

    $('.mv-delete-shortcode').click(function() {
      if (!confirm(mvJSData.translations.confirmShortcodeDelete))
        return false;

      var item = $(this).parents('tr');
      var key = item.attr('id');

      var data = {
        action: 'mv_deleteshortcode',
        key: key,
        nonce: mvJSData.nonces.deleteShortcode
      };

      $.post(ajaxurl, data, function(response) {
        if (response)
          item.fadeOut(400, function(){ item.remove(); });
      });

      return false;
    });

    $('#resetColor').click(function() {
      $('#vimeoControlColor').val('#00adef');
      return false;
    });

    $('#mvSaveOptions').click(function() {
      var errors = false;

      $('#mvSaveOptionsFrm input').removeClass('mv-error-field');

      if ($('#vimeoControlColor').val() == '') {
        $('#vimeoControlColor').addClass('mv-error-field');
        errors = true;
      }

      if (errors)
        return false;
    });

    $('.mv-delete-item').click(function() {
      if (!confirm(mvJSData.translations.confirmItemDelete))
        return false;

      var item = $(this).parents('tr');
      var key = item.attr('id');

      var data = {
        action: 'mv_deleteitem',
        key: key,
        nonce: mvJSData.nonces.deleteItem
      };

      $.post(ajaxurl, data, function(response) {
        if (response)
          item.fadeOut(400, function(){ item.remove(); });
      });

      return false;
    });

    $('#mediaType').change(function() {
      var type = this.value;

      //hide all boxes by default
      $('#vimeoURLBox, #youtubeURLBox, #photoURLBox, #resBox').css('display', 'none');

      if (type == 'vimeo')
        $('#vimeoURLBox').css('display', 'block');
      else if (type == 'youtube')
        $('#youtubeURLBox, #resBox').css('display', 'block');
      else if (type == 'photo')
        $('#photoURLBox').css('display', 'block');
    });

    $('#vimeoURLInput, #youtubeURLInput, #photoURLInput').on('propertychange keyup input paste', function() {
      if (this.value == '')
        $('#mvSaveMedia').attr('disabled', 'disabled');
      else
        $('#mvSaveMedia').removeAttr('disabled');
    });

    var _custom_media = true;
    var _orig_send_attachment = wp.media.editor.send.attachment;

    $('#photoURLInput').click(function(e) {
      var button = $(this);
      var id = button.attr('id').replace('_button', '');
      _custom_media = true;

      wp.media.editor.send.attachment = function(props, attachment) {
        if (_custom_media) {
          if (attachment.sizes.large)
            $('#' + id).val(attachment.sizes.large.url);
          else
            $('#' + id).val(attachment.sizes.full.url);

          if ($('#' + id).val() == '')
            $('#mvSaveMedia').attr('disabled', 'disabled');
          else
            $('#mvSaveMedia').removeAttr('disabled');
        } else
          return _orig_send_attachment.apply(this, [props, attachment]);
      };

      wp.media.editor.open(button);
      return false;
    });

    $('.add_media').on('click', function() {
      _custom_media = false;
    });

    $('#mvSaveMedia').click(function() {
      var errors = false;

      $('#mvSaveMediaFrm input').removeClass('mv-error-field');

      if ($('#mediaType').val() == '') {
        $('#mediaType').addClass('mv-error-field');
        errors = true;
      }

      if ($('#mediaType').val() == 'Vimeo' && $('#vimeoURLInput').val() == '') {
        $('#vimeoURLInput').addClass('mv-error-field');
        errors = true;
      }

      if ($('#mediaType').val() == 'Youtube' && $('#youtubeURLInput').val() == '') {
        $('#youtubeURLInput').addClass('mv-error-field');
        errors = true;
      }

      if ($('#mediaType').val() == 'Photo' && $('#photoURLInput').val() == '') {
        $('#photoURLInput').addClass('mv-error-field');
        errors = true;
      }

      if (errors)
        return false;
    });

    $('#resetHeight').click(function() {
      $('#displayHeight').val('400');
      return false;
    });

    $('#resetCanvasColor').click(function() {
      $('#canvasColor').val('#111');
      return false;
    });

    $('#resetCaptionColor').click(function() {
      $('#captionColor').val('#fff');
      return false;
    });

    $('#mvSaveShortcodeEdit').click(function() {
      var errors = false;

      $('#mvSaveShortcodeEditFrm input').removeClass('mv-error-field');

      if ($('#shortName').val() == '') {
        $('#shortName').addClass('mv-error-field');
        errors = true;
      }

      if ($('#displayHeight').val() == '') {
        $('#displayHeight').addClass('mv-error-field');
        errors = true;
      }

      if ($('#canvasColor').val() == '') {
        $('#canvasColor').addClass('mv-error-field');
        errors = true;
      }

      if ($('#captionColor').val() == '') {
        $('#captionColor').addClass('mv-error-field');
        errors = true;
      }

      if (errors)
        return false;
    });

    $('#mvSaveShortcode').click(function() {
      var errors = false;

      $('#mvSaveShortcodeFrm input').removeClass('mv-error-field');

      if ($('#shortName').val() == '') {
        $('#shortName').addClass('mv-error-field');
        errors = true;
      }

      if ($('#displayHeight').val() == '') {
        $('#displayHeight').addClass('mv-error-field');
        errors = true;
      }

      if ($('#canvasColor').val() == '') {
        $('#canvasColor').addClass('mv-error-field');
        errors = true;
      }

      if ($('#captionColor').val() == '') {
        $('#captionColor').addClass('mv-error-field');
        errors = true;
      }

      if (errors)
        return false;
    });
  });
};

(function($){mvAdmin.initialize($);})(jQuery);