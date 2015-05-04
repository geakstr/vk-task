if (typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, '');
  }
}

if (!document.querySelectorAll) {
  document.querySelectorAll = function(selectors) {
    var style = document.createElement('style'),
      elements = [],
      element;
    document.documentElement.firstChild.appendChild(style);
    document._qsa = [];

    style.styleSheet.cssText = selectors + '{x-qsa:expression(document._qsa && document._qsa.push(this))}';
    window.scrollBy(0, 0);
    style.parentNode.removeChild(style);

    while (document._qsa.length) {
      element = document._qsa.shift();
      element.style.removeAttribute('x-qsa');
      elements.push(element);
    }
    document._qsa = null;
    return elements;
  };
}

if (!document.querySelector) {
  document.querySelector = function(selectors) {
    var elements = document.querySelectorAll(selectors);
    return (elements.length) ? elements[0] : null;
  };
}

if (Object.defineProperty && Object.getOwnPropertyDescriptor && Object.getOwnPropertyDescriptor(Element.prototype, "textContent") && !Object.getOwnPropertyDescriptor(Element.prototype, "textContent").get) {
  (function() {
    var innerText = Object.getOwnPropertyDescriptor(Element.prototype, "innerText");
    Object.defineProperty(Element.prototype, "textContent", {
      get: function() {
        return innerText.get.call(this);
      },
      set: function(s) {
        return innerText.set.call(this, s);
      }
    });
  })();
}

var Utils = {
  submit_form: function submit_form(form, callback) {
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

    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4) {
        callback(xmlhttp.responseText);
      }
    }

    xmlhttp.open('POST', form.action, true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(params);
  },

  show_form_errors: function show_form_errors(msgs_txts_obj, msgs_nodes_obj) {
    // Remove old text from nodes
    Utils.remove_form_errors(msgs_txts_obj, msgs_nodes_obj);

    // Insert new text if exist
    for (var msgs_txts_prop in msgs_txts_obj) {
      if (msgs_txts_obj.hasOwnProperty(msgs_txts_prop)) {
        var msgs_txts = msgs_txts_obj[msgs_txts_prop];
        for (var i = 0; i < msgs_txts.length; i++) {
          var li = document.createElement('li');
          li.textContent = msgs_txts[i];
          msgs_nodes_obj[msgs_txts_prop].appendChild(li);
        }
      }
    }
  },

  remove_form_errors: function remove_form_errors(msgs_txts_obj, msgs_nodes_obj) {
    for (var msgs_nodes_prop in msgs_nodes_obj) {
      if (msgs_nodes_obj.hasOwnProperty(msgs_nodes_prop)) {
        var msg_node = msgs_nodes_obj[msgs_nodes_prop];
        while (msg_node.firstChild) {
          msg_node.removeChild(msg_node.firstChild);
        }
      }
    }
  },

  get_formatted_current_time: function get_formatted_current_time() {
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
};