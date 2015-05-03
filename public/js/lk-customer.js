window.onload = function() {
  var dom = init_dom_for_lk_page();

  dom['forms'] = {
    balance_refill: {
      self: document.getElementById('balance-refill-form'),
      fee: document.getElementById('balance-refill-form-fee'),
      msgs: {
        fee: document.getElementById('balance-refill-form-fee-msgs'),
        server: document.getElementById('balance-refill-form-server-msgs')
      }
    },
    add_order: {
      self: document.getElementById('add-order-form'),
      title: document.getElementById('add-order-form-title'),
      description: document.getElementById('add-order-form-description'),
      price: document.getElementById('add-order-form-price'),
      msgs: {
        title: document.getElementById('add-order-form-title-msgs'),
        price: document.getElementById('add-order-form-price-msgs'),
        server: document.getElementById('add-order-form-server-msgs'),
      }
    }
  };

  dom.forms.balance_refill.self.onsubmit = function(event) {
    event.preventDefault();

    var form = this;
    submit_form(form, function() {
      var response = JSON.parse(this.response);

      show_form_errors(response.msgs, dom.forms.balance_refill.msgs);
      if (response['type'] === 'ok') {
        // Update balance
        var fee = parseFloat(dom.forms.balance_refill.fee.value);
        var cur_balance = parseFloat(dom.labels.balance.textContent);
        var new_balance = fee + cur_balance;

        dom.labels.balance.textContent = new_balance.toFixed(2);

        form.reset();
        remove_form_errors(response.msgs, dom.forms.balance_refill.msgs);
      }
    });

    return false;
  };

  dom.forms.add_order.self.onsubmit = function(event) {
    event.preventDefault();

    var form = this;
    submit_form(form, function() {
      var response = JSON.parse(this.response);

      console.log(response);

      show_form_errors(response.msgs, dom.forms.add_order.msgs);

      if (response.type === 'ok') {
        // Manually create new order in DOM list
        var li_node = document.createElement('li');

        var title_node = document.createElement('div');
        title_node.className = 'order-title';
        title_node.textContent = dom.forms.add_order.title.value.trim();

        var time_node = document.createElement('div');
        time_node.className = 'order-time';
        time_node.textContent = get_formatted_current_time();

        var description_node = document.createElement('div');
        description_node.className = 'order-description';
        description_node.textContent = dom.forms.add_order.description.value.trim();

        var meta_node = document.createElement('div');
        meta_node.className = 'order-meta';

        var price_val = parseFloat(dom.forms.add_order.price.value.trim());
        var price_node = document.createElement('div');
        price_node.className = 'order-price';
        price_node.textContent = price_val.toFixed(2) + ' руб.';

        meta_node.appendChild(price_node);
        li_node.appendChild(title_node);
        li_node.appendChild(time_node);
        li_node.appendChild(description_node);
        li_node.appendChild(meta_node);
        dom.blocks.pending_orders_list.appendChild(li_node);

        // Update pending orders counter
        var pending_orders_cnt = parseInt(dom.labels.pending_orders_cnt.textContent);
        dom.labels.pending_orders_cnt.textContent = pending_orders_cnt + 1;

        form.reset();
        remove_form_errors(response.msgs, dom.forms.add_order.msgs);
      }
    });

    return false;
  }
}