(function () {
  loadDashboardCounts();
  loadNotificationCount();
  enableAjaxForms();
})();

function loadDashboardCounts() {
  var loanCount = document.querySelector('[data-js="loan-count"]');
  var reservationCount = document.querySelector('[data-js="reservation-count"]');

  if (!loanCount && !reservationCount) {
    return;
  }

  fetch('/api/user/dashboard', {
    headers: {
      Accept: 'application/json'
    }
  })
    .then(function (response) {
      return response.json().then(function (data) {
        return {
          ok: response.ok,
          data: data
        };
      });
    })
    .then(function (result) {
      if (!result.ok || !result.data) {
        return;
      }

      if (loanCount) {
        loanCount.textContent = String(result.data.loanCount || 0);
      }

      if (reservationCount) {
        reservationCount.textContent = String(result.data.reservationCount || 0);
      }
    })
    .catch(function () {
    });
}

function loadNotificationCount() {
  if (!document.querySelector('.user-notif')) {
    return;
  }

  fetch('/api/user/notifications', {
    headers: {
      Accept: 'application/json'
    }
  })
    .then(function (response) {
      return response.json().then(function (data) {
        return {
          ok: response.ok,
          data: data
        };
      });
    })
    .then(function (result) {
      if (!result.ok || !result.data || !window.libraryApp) {
        return;
      }

      window.libraryApp.updateNotificationBadge(result.data.count || 0);
    })
    .catch(function () {
    });
}

function enableAjaxForms() {
  var forms = document.querySelectorAll('[data-js="ajax-action-form"]');
  var index = 0;

  for (index = 0; index < forms.length; index++) {
    forms[index].addEventListener('submit', function (event) {
      event.preventDefault();
      sendAjaxForm(this);
    });
  }
}

// Basic AJAX POST so loans and reservations can be updated without refreshing.
function sendAjaxForm(form) {
  var apiUrl = getApiUrl(form);
  var data = getFormDataAsJson(form);

  if (apiUrl === '') {
    form.submit();
    return;
  }

  fetch(apiUrl, {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
    .then(function (response) {
      return response.json().then(function (json) {
        return {
          ok: response.ok,
          data: json
        };
      });
    })
    .then(function (result) {
      if (!result.ok || !result.data || result.data.success !== true) {
        showPageMessage(form, result.data && result.data.message ? result.data.message : 'Unable to save your changes.', 'danger');
        return;
      }

      showPageMessage(form, result.data.message || 'Saved successfully.', 'success');
      removeCardFromList(form);
      loadDashboardCounts();
      loadNotificationCount();
    })
    .catch(function () {
      showPageMessage(form, 'Unable to save your changes.', 'danger');
    });
}

function getApiUrl(form) {
  var action = form.getAttribute('action') || '';

  if (action === '/loan/return') {
    return '/api/user/loan/return';
  }

  if (action === '/reserve/cancel') {
    return '/api/user/reservation/cancel';
  }

  return '';
}

function getFormDataAsJson(form) {
  var formData = new FormData(form);
  var data = {};

  formData.forEach(function (value, key) {
    data[key] = value;
  });

  return data;
}

function showPageMessage(form, message, type) {
  var container = form.closest('.container');
  var messageBox = null;

  if (!container) {
    return;
  }

  messageBox = container.querySelector('[data-js="page-message"]');

  if (!messageBox) {
    return;
  }

  messageBox.innerHTML = window.libraryApp.buildAlertHtml(message, type);
}

function removeCardFromList(form) {
  var card = form.closest('.card');
  var list = form.closest('[data-js="ajax-list"]');
  var emptyMessage = '';

  if (card) {
    card.remove();
  }

  if (!list) {
    return;
  }

  if (list.querySelectorAll('.card').length > 0) {
    return;
  }

  emptyMessage = list.getAttribute('data-empty-message') || 'No items found.';
  list.innerHTML = '<div class="card p-4 text-muted">' + window.libraryApp.escapeHtml(emptyMessage) + '</div>';
}
