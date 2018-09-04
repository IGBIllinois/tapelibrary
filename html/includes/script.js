
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
var current = -1;
function set_new_tapes() {

    if($('.tape_from').val() != 0 && ($('.tape_to').val() == 0 || !jQuery.isNumeric($('.tape_to').val()))) {
        // If "From" field is not empty, and "To" field is empty or contains alphabetical characters...
        var i = 0;
        var starttape = $('.tape_from').val();
        var text = "";
        var inputboxes = $("#add_multi_labels").html('');
        text += "<table class='table'>";
        text += ("<tr><th>Tape ID</th><th>Tape Label</th></tr>");
        var currentTapeString = starttape;
        text +=("<tr><td>"+currentTapeString+":</td><td>"+ "<input type='text' name='tape_label" + i + "' value='' placeholder='Label for " + currentTapeString + "' /><br />");
        text +=("<input type='hidden' name='tape_id"+i+"' value='"+currentTapeString+"'></td></tr>");
        inputboxes.append(text);
    } else if($('.tape_to').val() != 0 && $('.tape_from').val() != 0) {
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
    



  $('#checkall').click(function(){
    $(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
  });

  $(document).click(function() {
    $("#log").html( $(".multiedit:checkbox:checked").length + " records checked" );
  });

  
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

// Hides or shows an element from a set, given the element name and div name
// Currently used to show the proper drop-down menus to select the proper
// potential parent locations for tapes or containers. A series of drop-down menus
// is created, and when a type or tape library object is selected, only the 
// correct corresponding menu for its parent types is displayed.
// 
// 'current' is the id of the currently displayed menu
// 
// 
// @param element: The element from which to get the new value. Typically an id
//                 corresponding to which menu to display
// @param div: The name of the <div> tag to hide or show.

function hide(element, div) {

    var value = document.getElementsByName(element)[0].value;

    document.getElementById(div+value).style.visibility = "visible";
    if(current != -1) {
        document.getElementById(div+current).style.visibility = "collapse";
    }
    current = value;

    return;
}

function showText(id) {
    var x = document.getElementById(id);
    var y = document.getElementById(id+"-orig");
    if (x.style.display === "none") {
        x.style.display = "block";
        y.style.display = "none";
    } else {
        x.style.display = "none";
        y.style.display = "block";
    }
} 
