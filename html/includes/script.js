

$('#addform').ready(function(){
  $('.tape_to').keyup(function(){
    //var starttape = $('.tape_from').val();
    var starttape = 1;
    var currentTape = 0;
    var inputboxes = $("#add_multi_labels").html('');
    var NumofTapes = $('.tape_to').val() ;
    $('#number_of_tapes').text(NumofTapes);
    for (var i=0;i<NumofTapes;i++) {
      currentTape = parseInt(starttape)+i;
      inputboxes.append("<input type='text' name='label" + currentTape + "' placeholder='Label for " + currentTape + "' /><br />");
    }
  });

});
//
$(document).ready(function(){

    $('#view_tapes').DataTable( {
        "order": [[ 0, "desc" ]]
    } );
    $('#containers').DataTable( {
        "order": [[ 0, "desc" ]]
    } );
    $('#container_types').DataTable( {
        "order": [[ 0, "desc" ]]
    } );
    $('#tapes_in_backupset').DataTable( {
        "order": [[ 0, "desc" ]]
    } );
    $('#curr_tapes').DataTable( {
        "order": [[ 0, "desc" ]]
    } );
    $('#edit_tapes_table').DataTable( {
        "order": [[ 1, "desc" ]],
        "bSort": false,
        "searching": false
        
    } );
    $('#edit_container').DataTable( {
        "order": [[ 1, "desc" ]],
        "bSort": false,
        "searching": false
    } );


    
    var dates = $( "#from, #to" ).datepicker({
    defaultDate: "+1w",
    dateFormat: 'yy-mm-dd',
    changeMonth: true,
    numberOfMonths: 1,
    onSelect: function( selectedDate ) {
      var option = this.id == "from" ? "minDate" : "maxDate",
      instance = $( this ).data( "datepicker" ),
      date = $.datepicker.parseDate(
        instance.settings.dateFormat ||
        $.datepicker._defaults.dateFormat,
        selectedDate, instance.settings );
          dates.not( this ).datepicker( "option", option, date );
    }
  });

  $('#checkall').click(function(){
    $(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
  });

  $(document).click(function() {
    $("#log").html( $(".multiedit:checkbox:checked").length + " records checked" );
  });

  $("#datepicker").datepicker({dateFormat: 'yy-mm-dd'});
  
  $(".iframe_add").fancybox({
    'width': 450,
    'height': 420,
    'overlayOpacity': 0.6,
    'scrolling': false,
    'transitionIn': 'none',
    'transitionOut': 'none',
    'type': 'iframe'
  });
  


});