<?php
###############################################################################
# Copyright 2015 Univeristy of Florida. All rights reserved.
# This file is part of the redcap-extras project.
# Use of this source code is governed by the license found in the LICENSE file.
###############################################################################

/**
 * Complex Validation Hook
 *
 * Allows for the additional of valid options outside of the min-max range for
 * a field.
 *
 * For example, if the question were something like "In which month was the
 * subject born?" The field note could read "1-12, or 88 if unknown." Simply
 * use the 1-12 range and decorate additional valid options in the Note like
 * this:
 *
 *   1-12, or <span class=valid>88</span> if unknown.
 */

return function ($project_id, $record, $instrument, $event_id, $group_id)
{
?>
<script>
/*
 * Finds additional valid options outside of min-max ranges.
 *
 * Searches for field notes that have children elements of class "valid". For
 * all that are found, store the original REDCap validation (found in the
 * "onblur" event callback) and replace it with a custom one.
 *
 * If the user enters a value not found in the list of additional valid
 * options, the original REDCap validation is called.
 */
$( "input" ).filter(function( index, element ) {
  return $(element).siblings(".note:has('.valid')").length > 0;
})
.each(function( index, element ) {
  var additionalOptions = $(element).siblings(".note").find('.valid');
  var original = element.onblur;
  element.onblur = null;
  $(element).on( "blur", function( evt ) {
    var val = this.value;
    var matches = additionalOptions.filter(function( index, element ) {
      return $(element).text() == val;
    });
    if ( matches.length == 0 ) {
      $(this).trigger( "original", original );
    }
  }).on( "original", original );
});

</script>
<?php
};
