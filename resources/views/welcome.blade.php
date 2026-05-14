<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel ERD - Database Visualizer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        /* Header */
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        h1 {
            color: #4a5568;
            font-size: 24px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #4299e1;
            color: white;
        }

        .btn-success {
            background: #48bb78;
            color: white;
        }

        .btn-secondary {
            background: #edf2f7;
            color: #4a5568;
        }

        .btn:hover {
            opacity: 0.8;
        }

        /* Stats Cards */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #4299e1;
        }

        .stat-label {
            color: #718096;
            margin-top: 5px;
        }

        /* Search */
        .search-box {
            margin-bottom: 20px;
        }

        .search-box input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Filter Buttons */
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 16px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter-btn.active {
            background: #4299e1;
            color: white;
            border-color: #4299e1;
        }

        /* Tables Grid */
        .tables {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .table-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .table-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .table-header {
            background: #4299e1;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
        }

        .table-name {
            font-weight: bold;
            font-size: 18px;
        }

        .table-badge {
            background: rgba(255,255,255,0.2);
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 12px;
        }

        .table-body {
            padding: 15px;
        }

        .columns {
            list-style: none;
        }

        .columns li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            font-family: monospace;
            font-size: 14px;
        }

        .columns li:last-child {
            border-bottom: none;
        }

        .pk {
            color: #ecc94b;
            font-weight: bold;
        }

        .fk {
            color: #48bb78;
            font-weight: bold;
        }

        .relations {
            margin-top: 12px;
            padding: 10px;
            background: #f7fafc;
            border-radius: 5px;
            font-size: 12px;
            color: #718096;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 10px;
            max-width: 90%;
            width: 700px;
            max-height: 80%;
            overflow: auto;
            padding: 25px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .close {
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }

        .close:hover {
            color: #333;
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 50px;
            display: none;
        }

        .loading.active {
            display: block;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4299e1;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Diagram */
        .diagram {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            display: none;
            overflow-x: auto;
        }

        .diagram.active {
            display: block;
        }

        canvas {
            background: #fafafa;
            border-radius: 5px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 20px;
            color: #718096;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .header {
                flex-direction: column;
                text-align: center;
            }
            .tables {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Laravel ERD - Database Schema Visualizer</h1>
    <div>
        <button class="btn btn-secondary" onclick="toggleDiagram()"> View Diagram</button>
        <a href="/export-pdf" class="btn btn-success"> Export PDF</a>
    </div>
</div>



<!-- Diagram -->
<div class="diagram" id="diagram">
    <h3 style="margin-bottom: 15px;">Relationship Diagram</h3>
    <canvas id="diagramCanvas" width="1000" height="500"></canvas>
</div>

<!-- Search -->
<div class="search-box">
    <input type="text" id="search" placeholder=" Search table or column...">
</div>

<!-- Filters -->
<div class="filters">
    <button class="filter-btn active" data-filter="all">All Tables</button>
    <button class="filter-btn" data-filter="relations">With Relations</button>
    <button class="filter-btn" data-filter="no-relations">Without Relations</button>
</div>

<!-- Loading -->
<div class="loading" id="loading">
    <div class="spinner"></div>
    <p style="margin-top: 10px;">Loading tables...</p>
</div>

<!-- Tables -->
<div class="tables" id="tables">
    @if(isset($tables) && count($tables) > 0)
        @foreach($tables as $table)
            <div class="table-card" data-table="{{ $table['name'] }}" data-relations="{{ count($table['foreign_keys']) }}">
                <div class="table-header">
                    <span class="table-name"> {{ $table['name'] }}</span>
                    <span class="table-badge">{{ $table['row_count'] }} rows</span>
                </div>
                <div class="table-body">
                    <ul class="columns">
                        @foreach($table['columns'] as $column)
                            <li>
                                @php
                                    $isPrimary = in_array($column, $table['primary_keys'] ?? []);
                                    $isForeign = false;
                                    foreach($table['foreign_keys'] ?? [] as $fk) {
                                        if($fk->COLUMN_NAME == $column) $isForeign = true;
                                    }
                                @endphp
                                @if($isPrimary)
                                    <span class="pk"> PK</span>
                                @elseif($isForeign)
                                    <span class="fk" FK</span>
                                @else
                                    <span></span>
                                @endif
                                {{ $column }}
                            </li>
                        @endforeach
                    </ul>
                    @if(count($table['foreign_keys'] ?? []) > 0)
                        <div class="relations">
                            🔗 @foreach($table['foreign_keys'] as $fk)
                                {{ $fk->COLUMN_NAME }} → {{ $fk->REFERENCED_TABLE_NAME }}
                                @if(!$loop->last), @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="stat-card" style="grid-column: 1/-1; text-align: center;">
            <p>No tables found. Run <code>php artisan migrate</code> first.</p>
        </div>
    @endif
</div>

<div class="footer">
     Laravel ERD - Automatically detects database schema and relationships
</div>

<!-- Modal -->
<div class="modal" id="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Table Details</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div id="modalBody">
            <div class="loading" style="display: block;">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // Calculate totals
    function calculateTotals() {
        let totalCols = 0;
        let totalRows = 0;
        
        document.querySelectorAll('.table-card').forEach(card => {
            const cols = card.querySelectorAll('.columns li').length;
            const badgeText = card.querySelector('.table-badge')?.innerText || '0 rows';
            const rows = parseInt(badgeText) || 0;
            totalCols += cols;
            totalRows += rows;
        });
        
        document.getElementById('totalColumns').innerText = totalCols;
        document.getElementById('totalRows').innerText = totalRows.toLocaleString();
    }
    
    // Search functionality
    document.getElementById('search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.table-card').forEach(card => {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
    
    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            document.querySelectorAll('.table-card').forEach(card => {
                const relations = parseInt(card.dataset.relations) || 0;
                if (filter === 'all') {
                    card.style.display = '';
                } else if (filter === 'relations') {
                    card.style.display = relations > 0 ? '' : 'none';
                } else if (filter === 'no-relations') {
                    card.style.display = relations === 0 ? '' : 'none';
                }
            });
        });
    });
    
    // Show table details on click
    const modal = document.getElementById('modal');
    
    async function showTableDetails(tableName) {
        modal.classList.add('active');
        const modalBody = document.getElementById('modalBody');
        modalBody.innerHTML = '<div class="loading" style="display: block;"><div class="spinner"></div><p>Loading...</p></div>';
        
        try {
            const response = await fetch(`/table-details/${tableName}`);
            const data = await response.json();
            
            modalBody.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <p><strong> Total Records:</strong> ${data.row_count.toLocaleString()}</p>
                    <p><strong> Total Columns:</strong> ${data.columns.length}</p>
                    <p><strong> Foreign Keys:</strong> ${data.foreign_keys.length}</p>
                </div>
                
                <h3 style="margin: 15px 0 10px;">Columns:</h3>
                <ul style="margin-bottom: 20px; padding-left: 20px;">
                    ${data.columns.map(col => `<li><code>${col}</code></li>`).join('')}
                </ul>
                
                ${data.foreign_keys.length > 0 ? `
                    <h3 style="margin: 15px 0 10px;">Relationships:</h3>
                    <ul style="margin-bottom: 20px; padding-left: 20px;">
                        ${data.foreign_keys.map(fk => `<li>${fk.COLUMN_NAME} → ${fk.REFERENCED_TABLE_NAME}.${fk.REFERENCED_COLUMN_NAME}</li>`).join('')}
                    </ul>
                ` : ''}
                
                <h3 style="margin: 15px 0 10px;">Sample Data (first 5 rows):</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background: #e2e8f0;">
                                ${data.sample_data.length > 0 ? Object.keys(data.sample_data[0]).map(key => `<th style="padding: 8px; border: 1px solid #ddd;">${key}</th>`).join('') : '<th>No data</th>'}
                            </tr>
                        </thead>
                        <tbody>
                            ${data.sample_data.map(row => `
                                <tr>
                                    ${Object.values(row).map(val => `<td style="padding: 8px; border: 1px solid #ddd;">${val || '-'}</td>`).join('')}
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } catch (error) {
            modalBody.innerHTML = '<p style="color: red;">Error loading table details.</p>';
        }
    }
    
    document.querySelectorAll('.table-card').forEach(card => {
        card.addEventListener('click', () => {
            const tableName = card.dataset.table;
            showTableDetails(tableName);
        });
    });
    
    function closeModal() {
        modal.classList.remove('active');
    }
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
    
    // Diagram functionality
    let diagramVisible = false;
    
    async function drawDiagram() {
        try {
            const response = await fetch('/diagram-data');
            const data = await response.json();
            
            const canvas = document.getElementById('diagramCanvas');
            const ctx = canvas.getContext('2d');
            
            const count = data.nodes.length;
            canvas.width = Math.max(1000, count * 180);
            
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Draw background
            ctx.fillStyle = '#fafafa';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Calculate positions
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
            
            // Draw lines
            data.edges.forEach(edge => {
                const from = positions[edge.from];
                const to = positions[edge.to];
                if (from && to) {
                    ctx.beginPath();
                    ctx.moveTo(from.x + boxW, from.y + boxH/2);
                    ctx.lineTo(to.x, to.y + boxH/2);
                    ctx.strokeStyle = '#4299e1';
                    ctx.lineWidth = 2;
                    ctx.stroke();
                }
            });
            
            // Draw boxes
            positions.forEach(pos => {
                // Box
                ctx.fillStyle = 'white';
                ctx.shadowBlur = 3;
                ctx.fillRect(pos.x, pos.y, boxW, boxH);
                ctx.strokeStyle = '#4299e1';
                ctx.strokeRect(pos.x, pos.y, boxW, boxH);
                
                // Header
                ctx.fillStyle = '#4299e1';
                ctx.fillRect(pos.x, pos.y, boxW, 28);
                
                // Text
                ctx.fillStyle = 'white';
                ctx.font = 'bold 11px Arial';
                ctx.fillText(pos.name.length > 18 ? pos.name.substring(0, 15) + '...' : pos.name, pos.x + 8, pos.y + 18);
                
                ctx.fillStyle = '#666';
                ctx.font = '10px Arial';
                ctx.fillText(pos.rows + ' records', pos.x + 8, pos.y + 48);
                
                ctx.shadowBlur = 0;
            });
        } catch (error) {
            console.error('Diagram error:', error);
        }
    }
    
    async function toggleDiagram() {
        const diagram = document.getElementById('diagram');
        if (!diagramVisible) {
            diagram.classList.add('active');
            await drawDiagram();
            diagramVisible = true;
        } else {
            diagram.classList.remove('active');
            diagramVisible = false;
        }
    }
    
    // Initialize
    calculateTotals();
</script>
</body>
</html> 