window.onload = function() {
  var dom = {
    forms: {
      login: {
        selfs: document.querySelectorAll('.login-form')
      }
    }
  };

  for (var i = 0; i < dom.forms.login.selfs.length; i++) {
    dom.forms.login.selfs[i].onsubmit = function() {
      var form = this;
      var msgs = {
        email: form.querySelector('.login-form-email-msgs'),
        server: form.querySelector('.login-form-server-msgs')
      };

      Utils.submit_form(form, function(responseText) {
        var response = JSON.parse(responseText);
        if (response.type === 'ok') {
          window.location.reload();
        }
      });
      return false;
    };
  };
};