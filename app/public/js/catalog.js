document.addEventListener('DOMContentLoaded', function () {
  var form = document.querySelector('[data-js="catalog-search-form"]');
  var input = document.querySelector('[data-js="catalog-search-input"]');
  var results = document.querySelector('[data-js="catalog-live-results"]');
  var timerId = 0;

  if (!form || !input || !results) {
    return;
  }

  input.addEventListener('input', function () {
    var query = input.value.trim();
    window.clearTimeout(timerId);

    if (query.length < 2) {
      results.innerHTML = '';
      return;
    }

    timerId = window.setTimeout(function () {
      loadBooks(query, results);
    }, 300);
  });
});

function loadBooks(query, results) {
  getJson('/api/books?q=' + encodeURIComponent(query), function (data) {
    renderLiveBookResults(data.books || [], results);
  }, function () {
    results.innerHTML = buildAlertHtml('Unable to load live search results.', 'warning');
  });
}

function renderLiveBookResults(books, results) {
  var html = '';
  var index = 0;
  var maxItems = books.length < 5 ? books.length : 5;

  if (maxItems === 0) {
    results.innerHTML = buildAlertHtml('No matching books found.', 'info');
    return;
  }

  html += '<div class="card">';
  html += '<div class="card-body">';
  html += '<div class="fw-semibold mb-2">Live search results</div>';
  html += '<div class="list-group list-group-flush">';

  for (index = 0; index < maxItems; index++) {
    html += '<a class="list-group-item list-group-item-action" href="/books/' + books[index].id + '">';
    html += '<div class="fw-semibold">' + escapeHtml(books[index].title) + '</div>';
    html += '<div class="small text-muted">' + escapeHtml(books[index].author) + '</div>';
    html += '</a>';
  }

  html += '</div>';
  html += '</div>';
  html += '</div>';

  results.innerHTML = html;
}
