/**
 * @file
 * Javascript functionality.
 */
(function ($, Drupal) {
  /**
    * Calculate total.
    */
  function calculate(id) {
    var amount = $('#edit-prod-'+id).val();
    var set = $('#edit-set-'+id+' span').text();
    var price = $('#edit-price-'+id+' .product-price').text();
    //return Math.round(eval("amount * set * price")*10)/10;
    return round(eval("amount * set * price"), 2);
  }

  /**
    * Rounding help.
    */
  function round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
  }

  /* First time when the page is loaded calculate all sum values. */
  $( ".multistep-form-one .form-number" ).each(function( index ) {
    var id = $(this).attr("id").substring(10);
    var total = calculate(id);
    $('#edit-total-'+id+' .product-total').text(total);
  });

  /* Every time amount is changed in form calulate new sum. */
  $('.multistep-form-one .form-number').bind('keyup mouseup', function () {
    var id = $(this).attr("id").substring(10);
    var total = calculate(id);
    $('#edit-total-'+id+' .product-total').text(total);
  });
})(jQuery, Drupal);
