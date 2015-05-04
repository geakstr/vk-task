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
        server: document.getElementById('add-order-form-server-msgs')
      }
    },
    delete_order: {
      selfs: document.querySelectorAll('.order-delete')
    }
  };

  dom['labels']['sub_balance'] = document.getElementById('profile-sub-balance');
  dom['labels']['worker_fee'] = document.getElementById('add-order-form-worker-fee');

  dom.forms.balance_refill.self.onsubmit = function(event) {
    event.preventDefault();

    var form = this;
    Utils.submit_form(form, function() {
      var response = JSON.parse(this.responseText);

      Utils.show_form_errors(response.msgs, dom.forms.balance_refill.msgs);
      if (response['type'] === 'ok') {
        // Update balance
        var fee = parseFloat(dom.forms.balance_refill.fee.value);
        var cur_balance = parseFloat(dom.labels.balance.textContent);
        var new_balance = fee + cur_balance;

        var new_sub_balance = parseFloat(dom.labels.sub_balance.textContent) + fee;

        dom.labels.balance.textContent = new_balance.toFixed(2);
        dom.labels.sub_balance.textContent = new_sub_balance.toFixed(2);

        form.reset();
        Utils.remove_form_errors(response.msgs, dom.forms.balance_refill.msgs);
      }
    });

    return false;
  };

  dom.forms.add_order.price.oninput = function(event) {
    var fee = parseFloat(this.value) * 0.90;
    if (isNaN(fee)) {
      dom.labels.worker_fee.textContent = '— руб.';
    } else {
      dom.labels.worker_fee.textContent = fee.toFixed(2) + ' руб.';
    }

  };

  dom.forms.add_order.self.onsubmit = function(event) {
    event.preventDefault();

    var form = this;
    Utils.submit_form(form, function() {
      var response = JSON.parse(this.responseText);

      Utils.show_form_errors(response.msgs, dom.forms.add_order.msgs);

      if (response.type === 'ok') {
        // Manually create new order in DOM list
        var li_node = document.createElement('li');

        // Create delete button form
        var delete_node_form = document.createElement('form');
        delete_node_form.className = 'order-delete';
        delete_node_form.action = '/actions/orders/delete';
        delete_node_form.method = 'post';
        delete_node_form.onsubmit = delete_order_event;
        var delete_node_id = document.createElement('input');
        delete_node_id.type = 'hidden';
        delete_node_id.name = 'id';
        delete_node_id.value = response.params.order_id;
        var delete_node_submit = document.createElement('input');
        delete_node_submit.type = 'submit';
        delete_node_submit.value = '×';
        delete_node_form.appendChild(delete_node_id);
        delete_node_form.appendChild(delete_node_submit);

        var title_node = document.createElement('div');
        title_node.className = 'order-title';
        title_node.textContent = dom.forms.add_order.title.value.trim();

        var time_node = document.createElement('div');
        time_node.className = 'order-time';
        time_node.textContent = Utils.get_formatted_current_time();

        var description_node = document.createElement('div');
        description_node.className = 'order-description';
        description_node.textContent = dom.forms.add_order.description.value.trim();

        var price_val = parseFloat(dom.forms.add_order.price.value.trim());
        var price_node = document.createElement('div');
        price_node.className = 'order-price';
        price_node.textContent = price_val.toFixed(2) + ' руб.';

        var meta_node = document.createElement('div');
        meta_node.className = 'order-meta';
        meta_node.appendChild(price_node);

        li_node.appendChild(delete_node_form);
        li_node.appendChild(title_node);
        li_node.appendChild(time_node);
        li_node.appendChild(description_node);
        li_node.appendChild(meta_node);

        dom.blocks.pending_orders_list.appendChild(li_node);

        // Update pending orders counter
        var pending_orders_cnt = parseInt(dom.labels.pending_orders_cnt.textContent);
        dom.labels.pending_orders_cnt.textContent = pending_orders_cnt + 1;

        var new_sub_balance = parseFloat(dom.labels.sub_balance.textContent) - price_val;
        dom.labels.sub_balance.textContent = new_sub_balance.toFixed(2);

        dom.labels.worker_fee.textContent = '— руб.';

        form.reset();
        Utils.remove_form_errors(response.msgs, dom.forms.add_order.msgs);
      }
    });

    return false;
  }

  for (var i = 0; i < dom.forms.delete_order.selfs.length; i++) {
    dom.forms.delete_order.selfs[i].onsubmit = delete_order_event;
  }

  function delete_order_event(event) {
    event.preventDefault();

    var form = this;
    Utils.submit_form(form, function() {
      var response = JSON.parse(this.responseText);

      if (response.type === 'ok') {
        var parent_li = form.parentNode;
        parent_li.parentNode.removeChild(parent_li);

        var pending_orders_cnt = parseInt(dom.labels.pending_orders_cnt.textContent);
        dom.labels.pending_orders_cnt.textContent = pending_orders_cnt - 1;

        var price = parseFloat(parent_li.querySelector('.order-price').textContent.trim());
        var new_sub_balance = parseFloat(dom.labels.sub_balance.textContent) + price;
        dom.labels.sub_balance.textContent = new_sub_balance.toFixed(2);
      }
    });

    return false;
  }
};