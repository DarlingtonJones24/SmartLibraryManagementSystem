document.addEventListener('DOMContentLoaded', function () {
  loadDashboardCounts();
  loadNotificationCount();
  enableAjaxForms();
});

function loadDashboardCounts() {
  var loanCount = document.querySelector('[data-js="loan-count"]');
  var reservationCount = document.querySelector('[data-js="reservation-count"]');

  if (!loanCount && !reservationCount) {
    return;
  }

  getJson('/api/user/dashboard', function (data) {
    if (loanCount) {
      loanCount.textContent = String(data.loanCount || 0);
    }

    if (reservationCount) {
      reservationCount.textContent = String(data.reservationCount || 0);
    }
  });
}

function loadNotificationCount() {
  if (!document.querySelector('.user-notif')) {
    return;
  }

  getJson('/api/user/notifications', function (data) {
    updateNotificationBadge(data.count || 0);
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

function sendAjaxForm(form) {
  var apiUrl = getApiUrl(form);
  var data = getFormDataAsJson(form);

  if (apiUrl === '') {
    form.submit();
    return;
  }

  postJson(apiUrl, data, function (result) {
    if (result.success !== true) {
      showPageMessage(form, result.message || 'Unable to save your changes.', 'danger');
      return;
    }

    showPageMessage(form, result.message || 'Saved successfully.', 'success');
    removeCardFromList(form);
    loadDashboardCounts();
    loadNotificationCount();
  }, function (result) {
    showPageMessage(form, result && result.message ? result.message : 'Unable to save your changes.', 'danger');
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

  messageBox.innerHTML = buildAlertHtml(message, type);
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
  list.innerHTML = '<div class="card p-4 text-muted">' + escapeHtml(emptyMessage) + '</div>';
}
