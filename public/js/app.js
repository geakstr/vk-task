(function() {
  window.onload = function() {
    var dom = {
      blocks: {
        pending_orders_list: document.querySelector('.pending-orders .orders-list'),
        completed_orders_list: document.querySelector('.completed-orders .orders-list')
      },
      labels: {
        balance: document.querySelector('.profile-balance'),
        pending_orders_cnt: document.querySelector('.pending-orders-cnt'),
        completed_orders_cnt: document.querySelector('.completed-orders-cnt'),
      },
      forms: {
        login: {
          form: document.querySelector('.login-form'),
          email: document.querySelector('.login-form .email'),
          msgs: {
            email: document.querySelector('.login-form .email-msgs')
          }
        },
        add_order: {
          form: document.querySelector('.add-order-form'),
          title: document.querySelector('.add-order-form .order-title'),
          description: document.querySelector('.add-order-form .order-description'),
          price: document.querySelector('.add-order-form .order-price'),
          msgs: {
            title: document.querySelector('.add-order-form .title-msgs'),
            price: document.querySelector('.add-order-form .price-msgs'),
            server: document.querySelector('.add-order-form .server-msgs')
          }
        },
        perform_order: {
          forms: document.querySelectorAll('.perform-order-form')
        }
      }
    };

    if (dom.forms.login.form !== null) {
      dom.forms.login.form.onsubmit = function(event) {
        event.preventDefault();
        submit_form(this, function() {
          var json = JSON.parse(this.response);
          show_form_errors(json.msgs, dom.forms.login.msgs);

          if (json.type === 'ok') {
            location.reload();
          }
        });
        return false;
      };
    }

    if (dom.forms.perform_order.form !== null && dom.forms.perform_order.forms.length !== 0) {
      for (var i = 0; i < dom.forms.perform_order.forms.length; i++) {
        dom.forms.perform_order.forms[i].onsubmit = function(event) {
          event.preventDefault();
          var form = this;
          submit_form(form, function() {
            var li = document.createElement('li');

            var title = document.createElement('div');
            title.className = 'order-title';
            title.textContent = form.querySelector('.order-title').textContent.trim();

            var time = document.createElement('div');
            time.className = 'order-time';
            time.textContent = get_formatted_current_time();

            var description = document.createElement('div');
            description.className = 'order-description';
            description.textContent = form.querySelector('.order-description').textContent.trim();

            var meta = document.createElement('div');
            meta.className = 'order-meta';

            var price_val = parseFloat(form.querySelector('.order-price').textContent.trim());
            var price = document.createElement('div');
            price.className = 'order-price';
            price.textContent = price_val + ' руб.';

            meta.appendChild(price);
            li.appendChild(title);
            li.appendChild(time);
            li.appendChild(description);
            li.appendChild(meta);
            dom.blocks.completed_orders_list.appendChild(li);

            form.parentNode.removeChild(form);

            dom.labels.balance.textContent = (parseFloat(dom.labels.balance.textContent) + price_val).toFixed(2);
            dom.labels.pending_orders_cnt.textContent = parseInt(dom.labels.pending_orders_cnt.textContent) - 1;
            dom.labels.completed_orders_cnt.textContent = parseInt(dom.labels.completed_orders_cnt.textContent) + 1;
          });
          return false;
        }
      }
    }

    if (dom.forms.add_order.form !== null) {
      dom.forms.add_order.form.onsubmit = function(event) {
        event.preventDefault();
        var form = this;
        submit_form(form, function() {
          var json = JSON.parse(this.response);

          show_form_errors(json.msgs, dom.forms.add_order.msgs);

          if (json.type === 'ok') {
            var li = document.createElement('li');

            var title = document.createElement('div');
            title.className = 'order-title';
            title.textContent = dom.forms.add_order.title.value.trim();

            var time = document.createElement('div');
            time.className = 'order-time';
            time.textContent = get_formatted_current_time();

            var description = document.createElement('div');
            description.className = 'order-description';
            description.textContent = dom.forms.add_order.description.value.trim();

            var meta = document.createElement('div');
            meta.className = 'order-meta';

            var price = document.createElement('div');
            price.className = 'order-price';
            price.textContent = dom.forms.add_order.price.value.trim() + ' руб.';

            meta.appendChild(price);
            li.appendChild(title);
            li.appendChild(time);
            li.appendChild(description);
            li.appendChild(meta);
            dom.blocks.pending_orders_list.appendChild(li);


            form.reset();
            remove_form_errors(json.msgs, dom.forms.add_order.msgs);
          }
        });
        return false;
      }
    }
  }
})();