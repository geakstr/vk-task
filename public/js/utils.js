function submit_form(form, callback) {
  var elems = form.elements;
  var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');

  var params = '';
  for (var i = 0, value; i < elems.length; i++) {
    if (elems[i].tagName == 'SELECT') {
      value = elems[i].options[elems[i].selectedIndex].value;
    } else {
      value = elems[i].value;
    }
    params += elems[i].name + '=' + encodeURIComponent(value) + '&';
  }

  xmlhttp.onload = callback.bind(xmlhttp);

  xmlhttp.open('POST', form.action, true);
  xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xmlhttp.send(params);
}

function show_form_errors(msgs_txts_obj, msgs_nodes_obj) {
  // Remove old text from nodes
  remove_form_errors(msgs_txts_obj, msgs_nodes_obj);

  // Insert new text if exist
  for (var msgs_txts_prop in msgs_txts_obj) {
    if (msgs_txts_obj.hasOwnProperty(msgs_txts_prop)) {
      var msgs_txts = msgs_txts_obj[msgs_txts_prop];
      for (var i = 0; i < msgs_txts.length; i++) {
        var li = document.createElement('li');
        li.appendChild(document.createTextNode(msgs_txts[i]));
        msgs_nodes_obj[msgs_txts_prop].appendChild(li);
      }
    }
  }
}

function remove_form_errors(msgs_txts_obj, msgs_nodes_obj) {
  for (var msgs_nodes_prop in msgs_nodes_obj) {
    if (msgs_nodes_obj.hasOwnProperty(msgs_nodes_prop)) {
      var msg_node = msgs_nodes_obj[msgs_nodes_prop];
      while (msg_node.firstChild) {
        msg_node.removeChild(msg_node.firstChild);
      }
    }
  }
}

function get_formatted_current_time() {
  var now = new Date();
  var date = now.getDate().toString();
  if (date.length === 1) {
    date = '0' + date;
  }
  var month = (now.getMonth() + 1).toString();
  if (month.length === 1) {
    month = '0' + month;
  }
  var year = now.getFullYear().toString();
  var hours = now.getHours().toString();
  if (hours.length === 1) {
    hours = '0' + hours;
  }
  var mins = now.getMinutes().toString();
  if (mins.length === 1) {
    mins = '0' + mins;
  }
  return date + '.' + month + '.' + year + ' ' + hours + ':' + mins;
}