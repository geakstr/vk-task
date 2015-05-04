window.onload = function() {
  var dom = init_dom_for_lk_page();

  dom['forms'] = {
    work_order: {
      selfs: document.querySelectorAll('.work-order-form')
    }
  };

  for (var i = 0; i < dom.forms.work_order.selfs.length; i++) {
    dom.forms.work_order.selfs[i].onsubmit = function(event) {
      event.preventDefault();
      var form = this;
      Utils.submit_form(form, function() {
        var response = JSON.parse(this.responseText);
        if (response['type'] === 'ok') {
          var li = document.createElement('li');

          var title = document.createElement('div');
          title.className = 'order-title';
          title.textContent = form.querySelector('.order-title').textContent.trim();

          var time = document.createElement('div');
          time.className = 'order-time';
          time.textContent = Utils.get_formatted_current_time();

          var description = document.createElement('div');
          description.className = 'order-description';
          description.textContent = form.querySelector('.order-description').textContent.trim();

          var meta = document.createElement('div');
          meta.className = 'order-meta';

          var price_val = parseFloat(form.querySelector('.order-price').textContent.trim()).toFixed(2);
          var price = document.createElement('div');
          price.className = 'order-price';
          price.textContent = '+' + price_val + ' руб.';

          meta.appendChild(price);
          li.appendChild(title);
          li.appendChild(time);
          li.appendChild(description);
          li.appendChild(meta);
          dom.blocks.completed_orders_list.insertBefore(li, dom.blocks.completed_orders_list.firstChild);

          form.parentNode.removeChild(form);

          var new_balance = parseFloat(dom.labels.balance.textContent) + parseFloat(price_val);
          dom.labels.balance.textContent = new_balance.toFixed(2);
          dom.labels.pending_orders_cnt.textContent = parseInt(dom.labels.pending_orders_cnt.textContent) - 1;
          dom.labels.completed_orders_cnt.textContent = parseInt(dom.labels.completed_orders_cnt.textContent) + 1;
        }
      });
      return false;
    }
  }
}