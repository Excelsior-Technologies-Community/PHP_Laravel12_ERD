<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laravel ERD Diagram - {{ date('Y-m-d') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 30px;
            background: white;
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
            color: #4f46e5;
            font-size: 28px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 30px;
            color: #666;
            font-size: 12px;
        }

        .stats-section {
            margin-bottom: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .stats-grid {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
        }

        .relationship-section {
            margin-bottom: 30px;
            padding: 15px;
            background: #f0fdf4;
            border-radius: 10px;
        }

        .relationship-section h3 {
            color: #10b981;
            margin-bottom: 15px;
        }

        .relationship-list {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .relationship-item {
            background: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 11px;
            border: 1px solid #ddd;
        }

        .tables-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .table-box {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .table-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 12px 15px;
            color: white;
        }

        .table-title {
            font-size: 16px;
            font-weight: bold;
        }

        .table-stats {
            font-size: 10px;
            opacity: 0.8;
            margin-top: 5px;
        }

        .table-content {
            padding: 12px 15px;
        }

        .columns-list {
            list-style: none;
        }

        .columns-list li {
            padding: 6px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 11px;
            font-family: 'Courier New', monospace;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .column-badge {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .column-badge.primary {
            background: #f59e0b;
        }

        .column-badge.foreign {
            background: #10b981;
        }

        .column-badge.normal {
            background: #6b7280;
        }

        .badge-text {
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 10px;
            background: #f0f0f0;
        }

        .relationship-info {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #ddd;
            font-size: 10px;
            color: #666;
        }

        footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #999;
        }

        @media print {
            body {
                padding: 20px;
            }
            .table-box {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <h1>📊 Laravel Database ERD Diagram</h1>
    <div class="subtitle">Generated on {{ date('F j, Y, g:i a') }}</div>

    <div class="stats-section">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">{{ count($tables) }}</div>
                <div class="stat-label">Total Tables</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">
                    @php
                        $totalColumns = 0;
                        foreach($tables as $table) {
                            $totalColumns += count($table['columns']);
                        }
                        echo $totalColumns;
                    @endphp
                </div>
                <div class="stat-label">Total Columns</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($relationships ?? []) }}</div>
                <div class="stat-label">Relationships</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">
                    @php
                        $totalRows = 0;
                        foreach($tables as $table) {
                            $totalRows += $table['row_count'] ?? 0;
                        }
                        echo number_format($totalRows);
                    @endphp
                </div>
                <div class="stat-label">Total Records</div>
            </div>
        </div>
    </div>

    @if(isset($relationships) && count($relationships) > 0)
    <div class="relationship-section">
        <h3>🔗 Database Relationships</h3>
        <div class="relationship-list">
            @foreach($relationships as $rel)
                <div class="relationship-item">
                    {{ $rel->TABLE_NAME }}.{{ $rel->COLUMN_NAME }} → 
                    {{ $rel->REFERENCED_TABLE_NAME }}.{{ $rel->REFERENCED_COLUMN_NAME }}
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="tables-grid">
        @foreach($tables as $table)
            <div class="table-box">
                <div class="table-header">
                    <div class="table-title">📋 {{ $table['name'] }}</div>
                    <div class="table-stats">{{ number_format($table['row_count'] ?? 0) }} records • {{ count($table['columns']) }} columns</div>
                </div>
                <div class="table-content">
                    <ul class="columns-list">
                        @foreach($table['columns'] as $index => $column)
                            @php
                                $isPrimary = in_array($column, $table['primary_keys'] ?? []);
                                $isForeign = false;
                                if(isset($table['foreign_keys'])) {
                                    foreach($table['foreign_keys'] as $fk) {
                                        if($fk->COLUMN_NAME == $column) {
                                            $isForeign = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <li>
                                <span class="column-badge {{ $isPrimary ? 'primary' : ($isForeign ? 'foreign' : 'normal') }}"></span>
                                <code style="flex: 1;">{{ $column }}</code>
                                @if($isPrimary)
                                    <span class="badge-text" style="background: #fef3c7; color: #d97706;">PK</span>
                                @endif
                                @if($isForeign)
                                    <span class="badge-text" style="background: #d1fae5; color: #059669;">FK</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    @if(isset($table['foreign_keys']) && count($table['foreign_keys']) > 0)
                        <div class="relationship-info">
                            🔗 References: 
                            @foreach($table['foreign_keys'] as $fk)
                                {{ $fk->COLUMN_NAME }} → {{ $fk->REFERENCED_TABLE_NAME }}.{{ $fk->REFERENCED_COLUMN_NAME }}
                                @if(!$loop->last), @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <footer>
        Generated by Laravel 12 ERD System • Smart Database Relationship Visualizer
    </footer>
</body>
</html>