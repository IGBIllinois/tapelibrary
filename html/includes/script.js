
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
    var text = "";
    text += "<table class='table'>";
    text += ("<tr><th>Tape ID</th><th>Tape Label</th></tr>");
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
      text +=("<tr><td>"+currentTapeString+":</td><td>"+ "<input type='text' name='tape_label" + i + "' value='' placeholder='Label for " + currentTapeString + "' /><br />");
      text +=("<input type='hidden' name='tape_id"+i+"' value='"+currentTapeString+"'></td></tr>");
    }

    text += ("</table>");
    inputboxes.append(text);
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
        "order": [[ 0, "asc" ]],
        "pageLength": 50,
        "lengthChange": false
    } );
    $('#containers').DataTable( {
        "order": [[ 0, "asc" ]],
        "pageLength": 50,
        "lengthChange": false
    } );
    $('#container_types').DataTable( {
        "order": [[ 0, "asc" ]],
        "pageLength": 50,
        "lengthChange": false
    } );
    $('#tape_types').DataTable( {
        "order": [[ 0, "asc" ]],
        "pageLength": 50,
        "lengthChange": false
    } );
    $('#tapes_in_backupset').DataTable( {
        "order": [[ 0, "asc" ]],
        "pageLength": 50,
        "lengthChange": false
    } );
    $('#curr_tapes').DataTable( {
        "order": [[ 0, "asc" ]],
        "pageLength": 50,
        "lengthChange": false
    } );
    $('#edit_tapes_table').DataTable( {
        "order": [[ 1, "asc" ]],
        "bSort": false,
        "searching": false,
        "pageLength": 50,
        "lengthChange": false
        
    } );
    $('#edit_container').DataTable( {
        "order": [[ 1, "asc" ]],
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

function changeAllCheckedLocations(source, checkbox_name, id_name) {
    //containers = document.getElementsByName('tape_container');
    //alert("numLocations = "+containers.length);
    //for(var i=0, n=containers.length; i<n; i++) {
    //    containers[i].value = source.value;
    //}
    checkboxes = document.getElementsByName(checkbox_name+'[]');
    //alert("numCheckboxes = "+checkboxes.length);
        for(var i=0, n=checkboxes.length;i<n;i++) {
            //alert("i = "+i + ":"+checkboxes[i].checked);
            if(checkboxes[i].checked) {
                var id = checkboxes[i].id;
                //alert("id = "+id);
                //alert("newLoc="+id_name+id);
                newLoc = document.getElementById(id_name+"_"+id);
                //alert("newVal = "+source.value);
                if(newLoc != null) {
                    newLoc.value = source.value;
                }
            }
  }
}

function format_backupset_table(tape_array) {
    html = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<th>Name</th><th>Type</th><th>Location</th>';
        for(i=0; i<tape_array.length; i++) {
            html += '<tr><td>'+tape_array[i][0]+"</td>"+
                    '<td>'+tape_array[i][1]+"</td>"+
                    '<td>'+tape_array[i][2]+"</td></tr>";
        }

    html += '</table>';
    return html;
}

