document.addEventListener('DOMContentLoaded', function () {
  setupCatalogView();
});

function setupCatalogView() {
  var results = document.getElementById('catalog-results');
  var buttons = document.querySelectorAll('[data-view]');
  var storageKey = 'catalogView';
  var savedView = localStorage.getItem(storageKey);

  if (!results || buttons.length === 0) {
    return;
  }

  if (savedView !== 'grid') {
    savedView = 'list';
  }

  applyCatalogView(results, buttons, savedView);

  buttons.forEach(function (button) {
    button.addEventListener('click', function () {
      var selectedView = button.getAttribute('data-view');

      if (selectedView !== 'grid') {
        selectedView = 'list';
      }

      localStorage.setItem(storageKey, selectedView);
      applyCatalogView(results, buttons, selectedView);
    });
  });
}

function applyCatalogView(results, buttons, selectedView) {
  results.classList.remove('list');
  results.classList.remove('grid');
  results.classList.remove('d-flex');
  results.classList.remove('flex-column');

  if (selectedView === 'grid') {
    results.classList.add('grid');
  } else {
    results.classList.add('list');
    results.classList.add('d-flex');
    results.classList.add('flex-column');
  }

  buttons.forEach(function (button) {
    var buttonView = button.getAttribute('data-view');
    var isActive = buttonView === selectedView;

    button.classList.toggle('active', isActive);
  });
}

function buildAlertHtml(message, type) {
  return '<div class="alert alert-' + escapeHtml(type || 'info') + '">' + escapeHtml(message || '') + '</div>';
}

function escapeHtml(value) {
  var text = String(value || '');

  text = text.replace(/&/g, '&amp;');
  text = text.replace(/</g, '&lt;');
  text = text.replace(/>/g, '&gt;');
  text = text.replace(/"/g, '&quot;');
  text = text.replace(/'/g, '&#039;');

  return text;
}

function updateNotificationBadge(count) {
  var notificationArea = document.querySelector('.user-notif');
  var badge = null;

  if (!notificationArea) {
    return;
  }

  badge = notificationArea.querySelector('.notif-badge');
  count = parseInt(count, 10) || 0;

  if (count <= 0) {
    if (badge) {
      badge.remove();
    }

    return;
  }

  if (!badge) {
    badge = document.createElement('span');
    badge.className = 'notif-badge';
    notificationArea.appendChild(badge);
  }

  badge.textContent = String(count);
}

function getJson(url, onSuccess, onError) {
  fetch(url, {
    headers: {
      Accept: 'application/json'
    }
  })
    .then(function (response) {
      handleJsonResponse(response, onSuccess, onError);
    })
    .catch(function () {
      if (onError) {
        onError(null);
      }
    });
}

function postJson(url, data, onSuccess, onError) {
  fetch(url, {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
    .then(function (response) {
      handleJsonResponse(response, onSuccess, onError);
    })
    .catch(function () {
      if (onError) {
        onError(null);
      }
    });
}

function handleJsonResponse(response, onSuccess, onError) {
  response.json()
    .then(function (data) {
      if (response.ok) {
        if (onSuccess) {
          onSuccess(data);
        }

        return;
      }

      if (onError) {
        onError(data);
      }
    })
    .catch(function () {
      if (onError) {
        onError(null);
      }
    });
}
