/**
 * KnifeInformer State Knife Laws Hub — Frontend JS
 *
 * Handles:
 * 1. Hub search + filter (client-side, no API call needed)
 * 2. Compare tool (fetches from REST API)
 *
 * Zero dependencies. Vanilla JS.
 */

(function () {
    'use strict';

    // ============================================================
    // 1. Hub Search & Filter
    // ============================================================

    var searchInput = document.getElementById('klh-search');
    var stateGrid = document.getElementById('klh-state-grid');
    var noResults = document.getElementById('klh-no-results');
    var filterToggle = document.querySelector('.klh-filter-toggle');
    var filtersPanel = document.getElementById('klh-filters-panel');
    var clearFiltersBtn = document.querySelector('.klh-filters__clear');

    if (searchInput && stateGrid) {
        initHub();
    }

    function initHub() {
        var cards = stateGrid.querySelectorAll('.klh-state-card');

        // Search
        searchInput.addEventListener('input', function () {
            applyFilters(cards);
        });

        // Filter toggle
        if (filterToggle && filtersPanel) {
            filterToggle.addEventListener('click', function () {
                var expanded = filterToggle.getAttribute('aria-expanded') === 'true';
                filterToggle.setAttribute('aria-expanded', String(!expanded));
                filtersPanel.hidden = expanded;
            });
        }

        // Filter radios
        var radios = filtersPanel ? filtersPanel.querySelectorAll('input[type="radio"]') : [];
        for (var i = 0; i < radios.length; i++) {
            radios[i].addEventListener('change', function () {
                applyFilters(cards);
                updateFilterCount();
            });
        }

        // Clear filters
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function () {
                var radios = filtersPanel.querySelectorAll('input[type="radio"]');
                for (var i = 0; i < radios.length; i++) {
                    radios[i].checked = radios[i].value === '';
                }
                applyFilters(cards);
                updateFilterCount();
            });
        }
    }

    function applyFilters(cards) {
        var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        var filters = getActiveFilters();
        var visibleCount = 0;

        for (var i = 0; i < cards.length; i++) {
            var card = cards[i];
            var matchesSearch = true;
            var matchesFilters = true;

            // Search match
            if (query) {
                var stateName = (card.getAttribute('data-state') || '').toLowerCase();
                var abbr = (card.getAttribute('data-abbr') || '').toLowerCase();
                matchesSearch = stateName.indexOf(query) !== -1 || abbr.indexOf(query) !== -1;
            }

            // Filter match
            if (matchesFilters && filters.preemption !== '') {
                matchesFilters = card.getAttribute('data-preemption') === filters.preemption;
            }
            if (matchesFilters && filters.open_carry !== '') {
                matchesFilters = card.getAttribute('data-open-carry') === filters.open_carry;
            }
            if (matchesFilters && filters.concealed_carry !== '') {
                matchesFilters = card.getAttribute('data-concealed-carry') === filters.concealed_carry;
            }
            if (matchesFilters && filters.switchblade !== '') {
                matchesFilters = card.getAttribute('data-switchblade') === filters.switchblade;
            }
            if (matchesFilters && filters.automatic !== '') {
                matchesFilters = card.getAttribute('data-automatic') === filters.automatic;
            }
            if (matchesFilters && filters.blade_limit !== '') {
                var hasLimit = card.getAttribute('data-blade-limit');
                matchesFilters = hasLimit === filters.blade_limit;
            }

            var visible = matchesSearch && matchesFilters;
            card.hidden = !visible;
            if (visible) visibleCount++;
        }

        if (noResults) {
            noResults.hidden = visibleCount > 0;
        }
    }

    function getActiveFilters() {
        if (!filtersPanel) return { preemption: '', open_carry: '', concealed_carry: '', switchblade: '', automatic: '', blade_limit: '' };

        return {
            preemption: getRadioValue('f_preemption'),
            open_carry: getRadioValue('f_open_carry'),
            concealed_carry: getRadioValue('f_concealed_carry'),
            switchblade: getRadioValue('f_switchblade'),
            automatic: getRadioValue('f_automatic'),
            blade_limit: getRadioValue('f_blade_limit'),
        };
    }

    function getRadioValue(name) {
        var checked = filtersPanel.querySelector('input[name="' + name + '"]:checked');
        return checked ? checked.value : '';
    }

    function updateFilterCount() {
        if (!filterToggle) return;
        var filters = getActiveFilters();
        var count = 0;
        for (var key in filters) {
            if (filters[key] !== '') count++;
        }
        var countEl = filterToggle.querySelector('.klh-filter-toggle__count');
        if (countEl) {
            countEl.textContent = count;
            countEl.hidden = count === 0;
        }
    }

    // ============================================================
    // 2. Compare Tool
    // ============================================================

    var compareForm = document.getElementById('klh-compare-form');
    var compareResults = document.getElementById('klh-compare-results');
    var compareLoading = document.getElementById('klh-compare-loading');
    var compareTableWrap = document.getElementById('klh-compare-table-wrap');
    var compareError = document.getElementById('klh-compare-error');

    if (compareForm) {
        initCompare();
    }

    function initCompare() {
        // Pre-fill from URL params
        var params = new URLSearchParams(window.location.search);
        var statesParam = params.get('states');
        if (statesParam) {
            var abbrs = statesParam.split(',');
            var selA = document.getElementById('klh-state-a');
            var selB = document.getElementById('klh-state-b');
            if (abbrs[0] && selA) selA.value = abbrs[0].toUpperCase();
            if (abbrs[1] && selB) selB.value = abbrs[1].toUpperCase();
        }

        compareForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var stateA = document.getElementById('klh-state-a').value;
            var stateB = document.getElementById('klh-state-b').value;

            if (!stateA || !stateB) {
                showCompareError('Please select two states.');
                return;
            }
            if (stateA === stateB) {
                showCompareError('Please select two different states.');
                return;
            }

            fetchComparison(stateA, stateB);
        });
    }

    function fetchComparison(a, b) {
        compareResults.hidden = false;
        compareLoading.hidden = false;
        compareTableWrap.innerHTML = '';
        compareError.hidden = true;

        // Update URL
        var url = new URL(window.location);
        url.searchParams.set('states', a + ',' + b);
        history.replaceState(null, '', url);

        var endpoint = (window.klhData ? window.klhData.restUrl : '/wp-json/klh/v1/') + 'compare?states=' + encodeURIComponent(a + ',' + b);

        fetch(endpoint, {
            headers: { 'X-WP-Nonce': window.klhData ? window.klhData.nonce : '' }
        })
            .then(function (res) {
                if (!res.ok) throw new Error('Failed to load comparison data.');
                return res.json();
            })
            .then(function (data) {
                compareLoading.hidden = true;
                renderComparison(data);
            })
            .catch(function (err) {
                compareLoading.hidden = true;
                showCompareError(err.message || 'Something went wrong.');
            });
    }

    function renderComparison(data) {
        var states = data.states;
        var rows = data.rows;

        // === Desktop table ===
        var html = '<table class="klh-compare-table">';
        html += '<thead><tr><th></th>';
        for (var s = 0; s < states.length; s++) {
            html += '<th><a href="' + escHtml(states[s].permalink) + '">' + escHtml(states[s].state) + '</a></th>';
        }
        html += '</tr></thead><tbody>';

        for (var r = 0; r < rows.length; r++) {
            var row = rows[r];
            html += '<tr>';
            html += '<td class="klh-compare-table__row-label">' + escHtml(row.label) + '</td>';
            for (var c = 0; c < row.states.length; c++) {
                html += '<td>' + renderCompareCell(row, row.states[c]) + '</td>';
            }
            html += '</tr>';
        }

        html += '</tbody></table>';

        // === Mobile stacked layout ===
        html += '<div class="klh-compare-stacked">';
        for (var r2 = 0; r2 < rows.length; r2++) {
            var row2 = rows[r2];
            html += '<div class="klh-compare-stacked__row">';
            html += '<div class="klh-compare-stacked__label">' + escHtml(row2.label) + '</div>';
            html += '<div class="klh-compare-stacked__values">';
            for (var c2 = 0; c2 < row2.states.length; c2++) {
                var st = row2.states[c2];
                html += '<div class="klh-compare-stacked__state">';
                html += '<strong>' + escHtml(st.state_name) + '</strong>';
                html += '<span>' + renderCompareValue(row2, st) + '</span>';
                html += '</div>';
            }
            html += '</div></div>';
        }
        html += '</div>';

        compareTableWrap.innerHTML = html;

        // Wire up detail toggles
        var toggles = compareTableWrap.querySelectorAll('.klh-compare-detail-toggle');
        for (var t = 0; t < toggles.length; t++) {
            toggles[t].addEventListener('click', function () {
                var target = this.nextElementSibling;
                if (target) {
                    target.hidden = !target.hidden;
                    this.textContent = target.hidden ? 'Show details' : 'Hide details';
                }
            });
        }
    }

    function renderCompareCell(row, stateEntry) {
        var valueHtml = renderCompareValue(row, stateEntry);
        var detailsHtml = '';

        if (stateEntry.details) {
            detailsHtml += '<button class="klh-compare-detail-toggle" type="button">Show details</button>';
            detailsHtml += '<div class="klh-compare-detail" hidden>' + escHtml(stateEntry.details);

            if (stateEntry.statutes && stateEntry.statutes.length) {
                detailsHtml += '<br>';
                for (var i = 0; i < stateEntry.statutes.length; i++) {
                    var stat = stateEntry.statutes[i];
                    if (stat.url) {
                        detailsHtml += '<a href="' + escHtml(stat.url) + '" target="_blank" rel="noopener">' + escHtml(stat.code_section) + '</a>';
                    } else {
                        detailsHtml += escHtml(stat.code_section);
                    }
                    if (i < stateEntry.statutes.length - 1) detailsHtml += ', ';
                }
            }

            detailsHtml += '</div>';
        }

        return valueHtml + detailsHtml;
    }

    function renderCompareValue(row, stateEntry) {
        var val = stateEntry.value;

        if (row.key === 'blade_length') {
            if (val === null || val === undefined) {
                return '<span class="klh-badge klh-badge--yes">No limit</span>';
            }
            return escHtml(val + ' inches');
        }

        if (row.key === 'restricted_locations') {
            return val ? escHtml(val) : '<em>No data</em>';
        }

        // Tri-state boolean
        if (val === true) return '<span class="klh-badge klh-badge--yes">Yes</span>';
        if (val === false) return '<span class="klh-badge klh-badge--no">No</span>';
        return '<span class="klh-badge klh-badge--unclear">Unclear</span>';
    }

    function showCompareError(msg) {
        if (compareError) {
            compareError.textContent = msg;
            compareError.hidden = false;
        }
    }

    function escHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(String(str)));
        return div.innerHTML;
    }

})();
