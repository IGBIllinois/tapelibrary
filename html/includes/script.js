
        /*
$('#addform').ready(function(){
  $('.tape_to').keyup(function(){
    var starttape = $('.tape_from').val();
    //var starttape = 1;
    var currentTape = 0;
    var inputboxes = $("#add_multi_labels").html('');
    var NumofTapes = $('.tape_to').val() ;
    $('#number_of_tapes').text(NumofTapes);
    for (var i=0;i<NumofTapes;i++) {
      currentTape = parseInt(starttape)+i;
      inputboxes.append("<input type='text' name='label" + currentTape + "' placeholder='Label for " + currentTape + "' value='"+currentTape+"' /><br />");
    }
  });

})*/

function set_new_tapes() {

if($('.tape_to').val() != 0 && $('.tape_from').val() != 0) {
    var starttape = $('.tape_from').val();
    var currentTape = 0;
    var length = 0;
    var inputboxes = $("#add_multi_labels").html('');
    var NumofTapes = $('.tape_to').val() - $('.tape_from').val() + 1;
    $('#number_of_tapes').text(NumofTapes);
    
    for (var i=0;i<NumofTapes;i++) {
      if(starttape.startsWith("0")) {
          length = starttape.length;
          //alert("length = "+length);
      }
      //alert("length = "+length);
      currentTape = parseInt(starttape, 10)+i;
      var currentTapeString = currentTape.toString();
      //alert("currentTape length = "+currentTapeString.length);
      if(length > 0) {
          // pad with zeroes
          while(currentTapeString.length < length) {
              currentTapeString = "0"+currentTapeString;
          }
      }
      inputboxes.append(" Tape: "+ "<input type='text' name='label" + i + "' value='"+currentTapeString+"' placeholder='Label for " + currentTapeString + "' /><br />");
    }
    } else {
        // clear
        var inputboxes = $("#add_multi_labels").html('');
        inputboxes.append("");
    }
}

$('#addform').ready(function(){
    set_new_tapes();
  $('.tape_to').focusout(function(){
      set_new_tapes();
  });
  $('.tape_from').focusout(function(){
    set_new_tapes();
  });
});
//
$(document).ready(function(){

    $('#view_tapes').DataTable( {
        "order": [[ 0, "desc" ]],
        "pageLength": 50,
        "lengthChange": false
    } );
    $('#containers').DataTable( {
        "order": [[ 0, "desc" ]],
        "pageLength": 50,
        "lengthChange": false
    } );
    $('#container_types').DataTable( {
        "order": [[ 0, "desc" ]],
        "pageLength": 50,
        "lengthChange": false
    } );
    $('#tapes_in_backupset').DataTable( {
        "order": [[ 0, "desc" ]],
        "pageLength": 50,
        "lengthChange": false
    } );
    $('#curr_tapes').DataTable( {
        "order": [[ 0, "desc" ]],
        "pageLength": 50,
        "lengthChange": false
    } );
    $('#edit_tapes_table').DataTable( {
        "order": [[ 1, "desc" ]],
        "bSort": false,
        "searching": false,
        "pageLength": 50,
        "lengthChange": false
        
    } );
    $('#edit_container').DataTable( {
        "order": [[ 1, "desc" ]],
        "bSort": false,
        "searching": false,
        "pageLength": 50,
        "lengthChange": false
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


function toggleAll(source, name) {
  checkboxes = document.getElementsByName(name+'[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}


function disable(id) {
    document.getElementById(id).disabled = true;
}

function enable(id) {
    document.getElementById(id).disabled = false;
}

function toggle(id) {
    if(document.getElementById(id).disabled ) {
        enable(id);
    } else {
        disable(id);
    }

}

