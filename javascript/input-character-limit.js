/**
 * Limit input length
 *
 * Stops typing in #keyword once it reaches 10 characters (Backspace still works).
 * Tip: the native `maxlength="10"` attribute does the same without JavaScript.
 */
jQuery(document).on('keydown', 'input#keyword', function (e) {
  var MAX_LENGTH = 10;
  var BACKSPACE = 8;

  if (jQuery(this).val().length >= MAX_LENGTH && e.keyCode !== BACKSPACE) {
    e.preventDefault();
  }
});
