(function() {
  window.onload = function() {
    var dom = {
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
          title: document.querySelector('.add-order-form .title'),
          description: document.querySelector('.add-order-form .description'),
          price: document.querySelector('.add-order-form .price'),
          msgs: {
            title: document.querySelector('.add-order-form .title-msgs'),
            price: document.querySelector('.add-order-form .price-msgs'),
            server: document.querySelector('.add-order-form .server-msgs')
          }
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

    if (dom.forms.add_order.form !== null) {
      dom.forms.add_order.form.onsubmit = function(event) {
        event.preventDefault();
        submit_form(this, function() {
          show_form_errors(JSON.parse(this.response).msgs, dom.forms.add_order.msgs);
        });
        return false;
      }
    }
  }
})();