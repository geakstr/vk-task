window.onload = function() {
  var dom = {
    forms: {
      login: {
        selfs: document.querySelectorAll('.login-form')
      }
    }
  };

  for (var i = 0; i < dom.forms.login.selfs.length; i++) {
    dom.forms.login.selfs[i].onsubmit = function(event) {
      event.preventDefault();

      var form = this;
      var msgs = {
        email: form.querySelector('.login-form-email-msgs'),
        server: form.querySelector('.login-form-server-msgs')
      };

      Utils.submit_form(form, function() {
        var response = JSON.parse(this.response);
        if (response.type === 'ok') {
          location.reload();
        }
      });
      return false;
    };
  };
};