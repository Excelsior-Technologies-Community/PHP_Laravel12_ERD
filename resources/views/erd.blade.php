<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel ERD - Database Explorer</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-primary: #0a0f1a;
            --bg-secondary: rgba(15, 25, 40, 0.8);
            --bg-glass: rgba(20, 30, 50, 0.5);
            --bg-card: rgba(30, 50, 80, 0.3);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-primary: #e8edf5;
            --text-secondary: rgba(180, 195, 230, 0.7);
            --text-muted: rgba(120, 140, 180, 0.5);
            --accent: #10b981;
            --accent-glow: rgba(16, 185, 129, 0.25);
            --accent-dim: rgba(16, 185, 129, 0.12);
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --shadow: 0 8px 32px rgba(0,0,0,0.3);
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-theme="light"] {
            --bg-primary: #f0f4f8;
            --bg-secondary: rgba(255, 255, 255, 0.7);
            --bg-glass: rgba(255, 255, 255, 0.5);
            --bg-card: rgba(255, 255, 255, 0.6);
            --border-color: rgba(0, 0, 0, 0.06);
            --text-primary: #0a1628;
            --text-secondary: rgba(30, 50, 90, 0.65);
            --text-muted: rgba(60, 80, 130, 0.45);
            --shadow: 0 8px 32px rgba(0,0,0,0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: var(--transition);
            min-height: 100vh;
        }

        .header {
            background: var(--bg-secondary);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header h1 i { color: var(--accent); }

        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn {
            padding: 8px 18px;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: #059669;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--bg-glass);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            border-color: var(--accent);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .theme-btn {
            width: 40px;
            height: 40px;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            background: var(--bg-glass);
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-btn:hover {
            border-color: var(--accent);
            color: var(--text-primary);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 16px 20px;
            transition: var(--transition);
        }

        .stat-card:hover {
            border-color: rgba(16,185,129,0.2);
            transform: translateY(-2px);
        }

        .stat-card .label {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            margin-top: 4px;
        }

        .search-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 16px 20px;
            margin-bottom: 20px;
        }

        .search-box {
            display: flex;
            gap: 12px;
        }

        .search-box input {
            flex: 1;
            padding: 10px 16px;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            background: var(--bg-glass);
            color: var(--text-primary);
            font-size: 14px;
            outline: none;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .search-box input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
        }

        .search-box input::placeholder {
            color: var(--text-muted);
        }

        .filters {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 5px 14px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            background: var(--bg-glass);
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .filter-btn:hover {
            border-color: var(--accent);
            color: var(--text-primary);
        }

        .filter-btn.active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .tables-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            overflow: hidden;
            transition: var(--transition);
            cursor: pointer;
        }

        .table-card:hover {
            transform: translateY(-4px);
            border-color: rgba(16,185,129,0.2);
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        }

        .table-card.highlighted {
            border-color: var(--accent);
            box-shadow: 0 0 30px rgba(16,185,129,0.15);
        }

        .table-header {
            padding: 14px 18px;
            background: var(--bg-glass);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-name {
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-name i { color: var(--accent); }

        .table-badge {
            font-size: 11px;
            padding: 2px 10px;
            border-radius: 20px;
            background: var(--accent-dim);
            color: var(--accent);
        }

        .table-body {
            padding: 14px 18px;
        }

        .column-list {
            list-style: none;
        }

        .column-list li {
            padding: 6px 0;
            border-bottom: 1px solid var(--border-color);
            font-family: 'Courier New', monospace;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .column-list li:last-child {
            border-bottom: none;
        }

        .column-type {
            font-size: 10px;
            color: var(--text-muted);
            background: var(--bg-glass);
            padding: 1px 8px;
            border-radius: 10px;
            margin-left: auto;
        }

        .pk-badge {
            color: var(--warning);
            font-weight: 700;
            font-size: 10px;
        }

        .fk-badge {
            color: var(--accent);
            font-weight: 700;
            font-size: 10px;
        }

        .relations-info {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid var(--border-color);
            font-size: 12px;
            color: var(--text-secondary);
        }

        .relations-info i { color: var(--accent); }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(8px);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: var(--bg-secondary);
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
            max-width: 700px;
            width: 92%;
            max-height: 85vh;
            overflow: auto;
            padding: 28px;
            animation: modalIn 0.3s ease;
        }

        @keyframes modalIn {
            from { transform: scale(0.9) translateY(20px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header h2 {
            font-size: 20px;
            font-weight: 700;
        }

        .modal-close {
            font-size: 28px;
            cursor: pointer;
            color: var(--text-muted);
            transition: var(--transition);
            background: none;
            border: none;
        }

        .modal-close:hover {
            color: var(--danger);
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .modal-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
        }

        .modal-stat {
            background: var(--bg-glass);
            padding: 10px 14px;
            border-radius: var(--radius);
            text-align: center;
        }

        .modal-stat .label {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .modal-stat .value {
            font-size: 18px;
            font-weight: 700;
            margin-top: 2px;
        }

        .modal-section {
            margin-top: 8px;
        }

        .modal-section h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .modal-section table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .modal-section th,
        .modal-section td {
            padding: 6px 10px;
            border: 1px solid var(--border-color);
            text-align: left;
        }

        .modal-section th {
            background: var(--bg-glass);
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }

        .diagram-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 20px;
            margin-bottom: 20px;
            display: none;
            overflow-x: auto;
        }

        .diagram-container.active {
            display: block;
        }

        .diagram-container canvas {
            width: 100%;
            height: auto;
            background: var(--bg-glass);
            border-radius: var(--radius);
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: var(--text-muted);
            font-size: 12px;
            border-top: 1px solid var(--border-color);
            margin-top: 20px;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .loading-spinner.active {
            display: block;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--border-color);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 12px 20px;
            border-radius: var(--radius);
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(16px);
            color: var(--text-primary);
            font-size: 13px;
            z-index: 200;
            transform: translateX(120%);
            transition: transform 0.4s ease;
            max-width: 350px;
        }

        .toast.show {
            transform: translateX(0);
        }

        @media (max-width: 768px) {
            .header { padding: 12px 16px; flex-direction: column; align-items: stretch; }
            .header-actions { justify-content: flex-end; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .tables-grid { grid-template-columns: 1fr; }
            .modal-content { padding: 20px; max-width: 95%; }
        }
    </style>
</head>
<body>

<div class="header">
    <h1><i class="fas fa-database"></i> Database Explorer</h1>
    <div class="header-actions">
        <button class="theme-btn" id="themeToggle" title="Toggle Theme">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>
        <button class="btn btn-secondary" id="diagramToggle">
            <i class="fas fa-project-diagram"></i> Diagram
        </button>
        <a href="{{ route('erd.export') }}" class="btn btn-primary">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
</div>

<div class="container">

    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Total Tables</div>
            <div class="value">{{ $stats['total_tables'] }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Total Columns</div>
            <div class="value">{{ $stats['total_columns'] }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Relationships</div>
            <div class="value">{{ $stats['total_relationships'] }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Total Records</div>
            <div class="value">{{ number_format($stats['total_rows']) }}</div>
        </div>
    </div>

    <div class="diagram-container" id="diagramContainer">
        <canvas id="diagramCanvas"></canvas>
    </div>

    <div class="search-section">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search table or column name...">
        </div>
        <div class="filters">
            <button class="filter-btn active" data-filter="all">All Tables</button>
            <button class="filter-btn" data-filter="relations">With Relations</button>
            <button class="filter-btn" data-filter="no-relations">Without Relations</button>
        </div>
    </div>

    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
        <p style="margin-top: 10px;">Loading tables...</p>
    </div>

    <div class="tables-grid" id="tablesGrid">
        @forelse($tables as $table)
            <div class="table-card" data-table="{{ $table['name'] }}" data-relations="{{ count($table['foreign_keys']) > 0 ? 1 : 0 }}">
                <div class="table-header">
                    <span class="table-name">
                        <i class="fas fa-table"></i>
                        {{ $table['name'] }}
                    </span>
                    <span class="table-badge">{{ number_format($table['row_count']) }} rows</span>
                </div>
                <div class="table-body">
                    <ul class="column-list">
                        @foreach($table['column_details'] as $column)
                            <li>
                                {{ $column['name'] }}
                                <span class="column-type">{{ $column['data_type'] }}</span>
                                @if($column['is_primary'])
                                    <span class="pk-badge"><i class="fas fa-key"></i> PK</span>
                                @endif
                                @if($column['is_foreign'])
                                    <span class="fk-badge"><i class="fas fa-link"></i> FK</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    @if(count($table['foreign_keys']) > 0)
                        <div class="relations-info">
                            <i class="fas fa-link"></i>
                            @foreach($table['foreign_keys'] as $fk)
                                {{ $fk->COLUMN_NAME }} → {{ $fk->REFERENCED_TABLE_NAME }}
                                @if(!$loop->last), @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-database"></i>
                <p>No tables found. Run <code>php artisan migrate</code> first.</p>
            </div>
        @endforelse
    </div>

    <div class="footer">
        <i class="fas fa-code"></i> Laravel ERD • Smart Database Relationship Visualizer
    </div>
</div>

<div class="modal-overlay" id="modalOverlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Table Details</h2>
            <button class="modal-close" id="modalClose">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <div class="loading-spinner active" id="modalLoading">
                <div class="spinner"></div>
            </div>
            <div id="modalContent" style="display: none;"></div>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
    const html = document.documentElement;
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');

    themeToggle.addEventListener('click', () => {
        const isDark = html.getAttribute('data-theme') === 'dark';
        html.setAttribute('data-theme', isDark ? 'light' : 'dark');
        themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        localStorage.setItem('theme', isDark ? 'light' : 'dark');
    });

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        html.setAttribute('data-theme', savedTheme);
        themeIcon.className = savedTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
    }

    const searchInput = document.getElementById('searchInput');
    const tables = document.querySelectorAll('.table-card');
    const filterBtns = document.querySelectorAll('.filter-btn');

    searchInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        tables.forEach(table => {
            const text = table.textContent.toLowerCase();
            table.style.display = text.includes(value) ? '' : 'none';
        });
    });

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            tables.forEach(table => {
                const relations = parseInt(table.dataset.relations) || 0;
                if (filter === 'all') {
                    table.style.display = '';
                } else if (filter === 'relations') {
                    table.style.display = relations > 0 ? '' : 'none';
                } else if (filter === 'no-relations') {
                    table.style.display = relations === 0 ? '' : 'none';
                }
            });
        });
    });

    const modalOverlay = document.getElementById('modalOverlay');
    const modalBody = document.getElementById('modalBody');
    const modalContent = document.getElementById('modalContent');
    const modalLoading = document.getElementById('modalLoading');
    const modalTitle = document.getElementById('modalTitle');

    async function showTableDetails(tableName) {
        modalOverlay.classList.add('active');
        modalLoading.classList.add('active');
        modalContent.style.display = 'none';
        modalTitle.textContent = '📊 ' + tableName;

        try {
            const response = await fetch('/table-details/' + tableName);
            const data = await response.json();

            modalLoading.classList.remove('active');
            modalContent.style.display = 'block';

            let html = `
                <div class="modal-stats">
                    <div class="modal-stat">
                        <div class="label">Rows</div>
                        <div class="value">${data.row_count.toLocaleString()}</div>
                    </div>
                    <div class="modal-stat">
                        <div class="label">Columns</div>
                        <div class="value">${data.columns.length}</div>
                    </div>
                    <div class="modal-stat">
                        <div class="label">Foreign Keys</div>
                        <div class="value">${data.foreign_keys.length}</div>
                    </div>
                    <div class="modal-stat">
                        <div class="label">Has Relations</div>
                        <div class="value">${data.has_relations ? '✅ Yes' : '❌ No'}</div>
                    </div>
                </div>

                <div class="modal-section">
                    <h4><i class="fas fa-columns"></i> Columns</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Column</th>
                                <th>Type</th>
                                <th>Data Type</th>
                                <th>PK</th>
                                <th>FK</th>
                                <th>Nullable</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.columns.map(col => `
                                <tr>
                                    <td><strong>${col.name}</strong></td>
                                    <td>${col.type}</td>
                                    <td>${col.data_type}</td>
                                    <td>${col.is_primary ? '✅' : '❌'}</td>
                                    <td>${col.is_foreign ? '✅' : '❌'}</td>
                                    <td>${col.is_nullable ? 'Yes' : 'No'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;

            if (data.foreign_keys.length > 0) {
                html += `
                    <div class="modal-section">
                        <h4><i class="fas fa-link"></i> Relationships</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>Column</th>
                                    <th>Referenced Table</th>
                                    <th>Referenced Column</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.foreign_keys.map(fk => `
                                    <tr>
                                        <td>${fk.COLUMN_NAME}</td>
                                        <td>${fk.REFERENCED_TABLE_NAME}</td>
                                        <td>${fk.REFERENCED_COLUMN_NAME}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }

            if (data.sample_data.length > 0) {
                const keys = Object.keys(data.sample_data[0]);
                html += `
                    <div class="modal-section">
                        <h4><i class="fas fa-eye"></i> Sample Data (First 5 rows)</h4>
                        <table>
                            <thead>
                                <tr>
                                    ${keys.map(key => `<th>${key}</th>`).join('')}
                                </tr>
                            </thead>
                            <tbody>
                                ${data.sample_data.map(row => `
                                    <tr>
                                        ${keys.map(key => `<td>${row[key] ?? '-'}</td>`).join('')}
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }

            modalContent.innerHTML = html;

        } catch (error) {
            modalLoading.classList.remove('active');
            modalContent.style.display = 'block';
            modalContent.innerHTML = `<p style="color:var(--danger);">Error loading table details.</p>`;
        }
    }

    document.querySelectorAll('.table-card').forEach(card => {
        card.addEventListener('click', () => {
            const tableName = card.dataset.table;
            showTableDetails(tableName);
        });
    });

    document.getElementById('modalClose').addEventListener('click', () => {
        modalOverlay.classList.remove('active');
    });

    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.classList.remove('active');
        }
    });

    let diagramVisible = false;

    async function drawDiagram() {
        try {
            const response = await fetch('/diagram-data');
            const data = await response.json();

            const canvas = document.getElementById('diagramCanvas');
            const ctx = canvas.getContext('2d');

            const count = data.nodes.length;
            canvas.width = Math.max(1200, count * 180);
            canvas.height = Math.max(400, Math.ceil(count / 5) * 120);

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            const cols = Math.ceil(Math.sqrt(count));
            const boxW = 160, boxH = 70;
            const padding = 50;
            const startX = 40, startY = 40;
            const positions = [];

            data.nodes.forEach((node, i) => {
                const col = i % cols;
                const row = Math.floor(i / cols);
                positions.push({
                    x: startX + col * (boxW + padding),
                    y: startY + row * (boxH + padding),
                    name: node.name,
                    rows: node.rows
                });
            });

            data.edges.forEach(edge => {
                const from = positions[edge.from];
                const to = positions[edge.to];
                if (from && to) {
                    ctx.beginPath();
                    ctx.moveTo(from.x + boxW, from.y + boxH/2);
                    ctx.lineTo(to.x, to.y + boxH/2);
                    ctx.strokeStyle = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#10b981';
                    ctx.lineWidth = 2;
                    ctx.setLineDash([5, 5]);
                    ctx.stroke();
                    ctx.setLineDash([]);
                }
            });

            positions.forEach(pos => {
                ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--bg-card').trim() || 'rgba(30,50,80,0.3)';
                ctx.shadowBlur = 10;
                ctx.shadowColor = 'rgba(0,0,0,0.2)';
                ctx.beginPath();
                ctx.roundRect(pos.x, pos.y, boxW, boxH, 8);
                ctx.fill();
                ctx.shadowBlur = 0;

                ctx.strokeStyle = getComputedStyle(document.documentElement).getPropertyValue('--border-color').trim() || 'rgba(255,255,255,0.08)';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.roundRect(pos.x, pos.y, boxW, boxH, 8);
                ctx.stroke();

                ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#10b981';
                ctx.beginPath();
                ctx.roundRect(pos.x, pos.y, boxW, 28, 8);
                ctx.fill();

                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 11px Inter, sans-serif';
                const displayName = pos.name.length > 18 ? pos.name.substring(0, 15) + '...' : pos.name;
                ctx.fillText(displayName, pos.x + 8, pos.y + 18);

                ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--text-secondary').trim() || 'rgba(180,195,230,0.7)';
                ctx.font = '10px Inter, sans-serif';
                ctx.fillText(pos.rows + ' records', pos.x + 8, pos.y + 48);
            });
        } catch (error) {
            console.error('Diagram error:', error);
        }
    }

    CanvasRenderingContext2D.prototype.roundRect = function(x, y, w, h, r) {
        if (w < 2 * r) r = w / 2;
        if (h < 2 * r) r = h / 2;
        this.moveTo(x + r, y);
        this.lineTo(x + w - r, y);
        this.quadraticCurveTo(x + w, y, x + w, y + r);
        this.lineTo(x + w, y + h - r);
        this.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
        this.lineTo(x + r, y + h);
        this.quadraticCurveTo(x, y + h, x, y + h - r);
        this.lineTo(x, y + r);
        this.quadraticCurveTo(x, y, x + r, y);
        return this;
    };

    document.getElementById('diagramToggle').addEventListener('click', async function() {
        const container = document.getElementById('diagramContainer');
        if (!diagramVisible) {
            container.classList.add('active');
            await drawDiagram();
            diagramVisible = true;
            this.innerHTML = '<i class="fas fa-times"></i> Hide Diagram';
        } else {
            container.classList.remove('active');
            diagramVisible = false;
            this.innerHTML = '<i class="fas fa-project-diagram"></i> Diagram';
        }
    });

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.style.borderColor = type === 'success' ? 'var(--accent)' : 'var(--danger)';
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    window.addEventListener('resize', () => {
        if (diagramVisible) drawDiagram();
    });
</script>
</body>
</html>