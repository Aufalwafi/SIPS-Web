/**
 * SI PS ONLINE — Ultra-Simple Single-Row Centered Table Pagination with Integrated Filter Support
 * [← Prev] [ 1 ] / 5 [Next →] (Always horizontal, 1 row, centered)
 */
(function () {
    'use strict';

    // 1. Polyfill innerText for table rows and cells so custom page filters (row.innerText)
    // don't receive an empty string when rows are hidden by pagination in Chromium browsers.
    try {
        var origInnerTextDesc = Object.getOwnPropertyDescriptor(HTMLElement.prototype, 'innerText');
        if (origInnerTextDesc && origInnerTextDesc.get) {
            var origGet = origInnerTextDesc.get;
            [HTMLTableRowElement.prototype, HTMLTableCellElement.prototype].forEach(function (proto) {
                Object.defineProperty(proto, 'innerText', {
                    get: function () {
                        var val = origGet.call(this);
                        if ((val === '' || val === null || val === undefined) && this.textContent) {
                            return this.textContent;
                        }
                        return val;
                    },
                    set: origInnerTextDesc.set,
                    configurable: true
                });
            });
        }
    } catch (e) {
        // Fallback gracefully if browser restricts prototype property redefinition
    }

    // 2. Inject dedicated inline CSS for pagination bar and page-hiding
    if (!document.getElementById('sp-pagination-inline-style')) {
        var style = document.createElement('style');
        style.id = 'sp-pagination-inline-style';
        style.textContent = `
            tr.sp-page-hidden {
                display: none !important;
            }
            .simple-pagination-bar {
                display: flex !important;
                flex-direction: row !important;
                justify-content: center !important;
                align-items: center !important;
                width: 100% !important;
                box-sizing: border-box !important;
                padding: 12px 16px !important;
                margin: 0 auto !important;
                background: #fafbfc !important;
                border-top: 1px solid #e2e8f0 !important;
                border-radius: 0 0 8px 8px !important;
                text-align: center !important;
                white-space: nowrap !important;
            }
            .sp-actions {
                display: inline-flex !important;
                flex-direction: row !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 8px !important;
                margin: 0 auto !important;
                white-space: nowrap !important;
                flex-wrap: nowrap !important;
            }
            .sp-jump {
                display: inline-flex !important;
                flex-direction: row !important;
                align-items: center !important;
                gap: 5px !important;
                font-size: 12.5px !important;
                color: #475569 !important;
                white-space: nowrap !important;
                margin: 0 !important;
                padding: 0 2px !important;
            }
            .sp-input {
                display: inline-block !important;
                width: 46px !important;
                height: 30px !important;
                padding: 2px 4px !important;
                font-size: 13px !important;
                font-weight: 700 !important;
                text-align: center !important;
                color: #1e293b !important;
                background: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 5px !important;
                outline: none !important;
                box-sizing: border-box !important;
                margin: 0 !important;
            }
            .sp-input:focus {
                border-color: #2e7d32 !important;
                box-shadow: 0 0 0 2px rgba(46, 125, 50, 0.2) !important;
            }
            .sp-total {
                font-size: 12.5px !important;
                color: #64748b !important;
                font-weight: 600 !important;
                white-space: nowrap !important;
            }
            .sp-btn {
                display: inline-flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 5px 12px !important;
                font-size: 12px !important;
                font-weight: 600 !important;
                color: #334155 !important;
                background: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 5px !important;
                cursor: pointer !important;
                height: 30px !important;
                line-height: 1 !important;
                white-space: nowrap !important;
                margin: 0 !important;
                transition: all 0.15s ease !important;
            }
            .sp-btn:hover:not(:disabled) {
                background: #f1f5f9 !important;
                color: #0f172a !important;
                border-color: #94a3b8 !important;
            }
            .sp-btn:disabled {
                opacity: 0.35 !important;
                cursor: not-allowed !important;
                background: #f8fafc !important;
                border-color: #e2e8f0 !important;
            }
        `;
        document.head.appendChild(style);
    }

    function isDataRow(tr) {
        if (!tr || tr.children.length === 0) return false;
        var firstCell = tr.children[0];
        if (firstCell && firstCell.getAttribute('colspan') && parseInt(firstCell.getAttribute('colspan'), 10) >= 3) {
            return false;
        }
        return true;
    }

    function initTablePagination(table) {
        if (!table || table.dataset.paginationInitialized === 'true') return;

        if (table.classList.contains('profile-table') || 
            table.classList.contains('info-table') || 
            table.classList.contains('no-paginate') ||
            table.classList.contains('key-value-table')) {
            return;
        }

        var tbody = table.querySelector('tbody');
        if (!tbody) return;

        var allDataRows = Array.from(tbody.querySelectorAll('tr')).filter(isDataRow);
        if (allDataRows.length <= 10 && !table.hasAttribute('data-force-paginate')) {
            return;
        }

        table.dataset.paginationInitialized = 'true';

        var pageSize = 10;
        var currentPage = 1;

        // Create minimalist 1-row pagination bar
        var wrapper = document.createElement('div');
        wrapper.className = 'simple-pagination-bar';
        wrapper.innerHTML = `
            <div class="sp-actions">
                <button type="button" class="sp-btn sp-prev">&larr; Prev</button>
                <div class="sp-jump">
                    <input type="number" class="sp-input" min="1" value="1" aria-label="Nomor Halaman">
                    <span class="sp-total">/ 1</span>
                </div>
                <button type="button" class="sp-btn sp-next">Next &rarr;</button>
            </div>
        `;

        var tableParent = table.parentElement;
        if (tableParent && (tableParent.classList.contains('table-responsive') || tableParent.style.overflowX === 'auto' || tableParent.classList.contains('table-wrap') || tableParent.style.overflow === 'auto')) {
            tableParent.after(wrapper);
        } else {
            table.after(wrapper);
        }

        var btnPrev = wrapper.querySelector('.sp-prev');
        var btnNext = wrapper.querySelector('.sp-next');
        var pageInput = wrapper.querySelector('.sp-input');
        var totalSpan = wrapper.querySelector('.sp-total');

        function isRowActive(tr) {
            if (!isDataRow(tr)) return false;
            // Explicitly hidden by search filter
            if (tr.dataset.filterHidden === 'true') return false;
            if (tr.dataset.customHidden === 'true') return false;
            if (tr.getAttribute('data-hidden-by-filter') === 'true') return false;
            // If display is 'none' and NOT because of our pagination class, a page script hid it
            if (tr.style.display === 'none' && !tr.classList.contains('sp-page-hidden')) return false;
            return true;
        }

        function getActiveRows() {
            return Array.from(tbody.querySelectorAll('tr')).filter(isRowActive);
        }

        function render() {
            var activeRows = getActiveRows();
            var total = activeRows.length;
            var totalPages = Math.max(1, Math.ceil(total / pageSize));

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            var startIdx = (currentPage - 1) * pageSize;
            var endIdx = startIdx + pageSize;

            var currentRows = Array.from(tbody.querySelectorAll('tr')).filter(isDataRow);
            currentRows.forEach(function (tr) {
                var pos = activeRows.indexOf(tr);
                if (pos === -1) {
                    // Row is filtered out by search/filter criteria
                    tr.classList.add('sp-page-hidden');
                } else if (pos >= startIdx && pos < endIdx) {
                    // Row is active and belongs to the current page
                    tr.classList.remove('sp-page-hidden');
                    tr.style.display = '';
                } else {
                    // Row is active but belongs to a different page
                    tr.classList.add('sp-page-hidden');
                }
            });

            if (total <= 10) {
                wrapper.style.display = 'none';
            } else {
                wrapper.style.display = 'flex';
            }

            pageInput.value = currentPage;
            pageInput.max = totalPages;
            totalSpan.textContent = '/ ' + totalPages;

            btnPrev.disabled = (currentPage <= 1);
            btnNext.disabled = (currentPage >= totalPages);
        }

        btnPrev.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                render();
            }
        });

        btnNext.addEventListener('click', function () {
            var totalPages = Math.ceil(getActiveRows().length / pageSize);
            if (currentPage < totalPages) {
                currentPage++;
                render();
            }
        });

        pageInput.addEventListener('change', function () {
            var val = parseInt(this.value, 10);
            var totalPages = Math.ceil(getActiveRows().length / pageSize);
            if (isNaN(val) || val < 1) val = 1;
            if (val > totalPages) val = totalPages;
            currentPage = val;
            render();
        });

        pageInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.blur();
            }
        });

        // Public method to force pagination refresh on this table
        table.refreshPagination = function (resetPage) {
            if (resetPage !== false) currentPage = 1;
            render();
        };

        // Watch search/filter controls on the page to automatically sync pagination
        var filterHandler = function () {
            // Small debounce to let custom keyup/input scripts finish setting row visibility
            setTimeout(function () {
                currentPage = 1;
                render();
            }, 20);
        };

        var searchControls = document.querySelectorAll('input[type="text"], input[type="search"], select');
        searchControls.forEach(function (input) {
            if (input === pageInput) return;
            input.addEventListener('input', filterHandler);
            input.addEventListener('change', filterHandler);
            input.addEventListener('keyup', filterHandler);
        });

        render();
    }

    // Global helper so custom scripts can explicitly trigger pagination update
    window.refreshTablePagination = function (tableElement) {
        if (tableElement && typeof tableElement.refreshPagination === 'function') {
            tableElement.refreshPagination();
        } else {
            document.querySelectorAll('table').forEach(function (t) {
                if (typeof t.refreshPagination === 'function') {
                    t.refreshPagination();
                }
            });
        }
    };

    function autoInit() {
        document.querySelectorAll('table').forEach(initTablePagination);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInit);
    } else {
        autoInit();
    }
})();
