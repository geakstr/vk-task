window.onload = function() {
  var dom = {
    forms: {
      login: {
        self: document.getElementById('login-form'),
        msgs: {
          email: document.getElementById('login-form-email-msgs'),
          server: document.getElementById('login-form-server-msgs')
        }
      },
    }
  };

  dom.forms.login.self.onsubmit = function(event) {
    event.preventDefault();

    var form = this;
    submit_form(form, function() {
      var response = JSON.parse(this.response);
      show_form_errors(response.msgs, dom.forms.login.msgs);

      if (response.type === 'ok') {
        location.reload();
      }
    });
    return false;
  };
};