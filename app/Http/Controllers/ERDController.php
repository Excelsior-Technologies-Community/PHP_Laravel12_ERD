<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ERDController extends Controller
{
    public function index()
    {
        $tables = $this->getDatabaseTables();
        $relationships = $this->getTableRelationships();
        $stats = $this->getDatabaseStats();
        
        return view('erd', compact('tables', 'relationships', 'stats'));
    }

    private function getDatabaseTables()
    {
        $databaseName = DB::connection()->getDatabaseName();
        
        $tables = DB::select("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_TYPE = 'BASE TABLE'
        ");
        
        $tableData = [];
        
        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            
            if ($this->shouldSkipTable($tableName)) {
                continue;
            }
            
            $columns = Schema::getColumnListing($tableName);
            $columnDetails = [];
            
            foreach ($columns as $column) {
                $columnInfo = DB::select("
                    SELECT 
                        COLUMN_NAME,
                        DATA_TYPE,
                        COLUMN_TYPE,
                        IS_NULLABLE,
                        COLUMN_DEFAULT,
                        COLUMN_KEY
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = ? 
                    AND COLUMN_NAME = ?
                ", [$tableName, $column]);
                
                $isPrimary = !empty($columnInfo) && $columnInfo[0]->COLUMN_KEY === 'PRI';
                $isForeignKey = $this->isForeignKey($tableName, $column);
                
                $columnDetails[] = [
                    'name' => $column,
                    'type' => !empty($columnInfo) ? $columnInfo[0]->COLUMN_TYPE : 'unknown',
                    'data_type' => !empty($columnInfo) ? $columnInfo[0]->DATA_TYPE : 'unknown',
                    'is_primary' => $isPrimary,
                    'is_foreign' => $isForeignKey,
                    'is_nullable' => !empty($columnInfo) && $columnInfo[0]->IS_NULLABLE === 'YES',
                    'default' => !empty($columnInfo) ? $columnInfo[0]->COLUMN_DEFAULT : null
                ];
            }
            
            try {
                $rowCount = DB::table($tableName)->count();
            } catch (\Exception $e) {
                $rowCount = 0;
            }
            
            $tableData[] = [
                'name' => $tableName,
                'columns' => $columns,
                'column_details' => $columnDetails,
                'primary_keys' => $this->getPrimaryKeys($tableName),
                'foreign_keys' => $this->getForeignKeys($tableName),
                'row_count' => $rowCount
            ];
        }
        
        return $tableData;
    }

    private function isForeignKey($tableName, $columnName)
    {
        $foreignKeys = DB::select("
            SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$tableName, $columnName]);
        
        return !empty($foreignKeys);
    }

    private function getPrimaryKeys($tableName)
    {
        try {
            $primaryKeys = DB::select("
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND COLUMN_KEY = 'PRI'
            ", [$tableName]);
            
            return array_column($primaryKeys, 'COLUMN_NAME');
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getForeignKeys($tableName)
    {
        try {
            $foreignKeys = DB::select("
                SELECT 
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$tableName]);
            
            return $foreignKeys;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getTableRelationships()
    {
        try {
            $relationships = DB::select("
                SELECT 
                    TABLE_NAME,
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME,
                    CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            return $relationships;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getDatabaseStats()
    {
        try {
            $totalTables = DB::select("
                SELECT COUNT(*) as count 
                FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_TYPE = 'BASE TABLE'
            ")[0]->count;
            
            $totalColumns = DB::select("
                SELECT COUNT(*) as count 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE()
            ")[0]->count;
            
            $totalRelationships = count($this->getTableRelationships());
            
            $totalRows = 0;
            $tables = $this->getDatabaseTables();
            foreach ($tables as $table) {
                $totalRows += $table['row_count'];
            }
            
            return [
                'total_tables' => $totalTables,
                'total_columns' => $totalColumns,
                'total_relationships' => $totalRelationships,
                'total_rows' => $totalRows,
            ];
        } catch (\Exception $e) {
            return [
                'total_tables' => 0,
                'total_columns' => 0,
                'total_relationships' => 0,
                'total_rows' => 0,
            ];
        }
    }

    private function shouldSkipTable($tableName)
    {
        $skipTables = [
            'cache', 'cache_locks', 'failed_jobs', 'jobs', 
            'job_batches', 'migrations', 'password_reset_tokens', 
            'sessions', 'personal_access_tokens'
        ];
        
        return in_array($tableName, $skipTables);
    }

    public function exportPDF()
    {
        $tables = $this->getDatabaseTables();
        $relationships = $this->getTableRelationships();
        $stats = $this->getDatabaseStats();
        
        $pdf = Pdf::loadView('pdf.erd-pdf', compact('tables', 'relationships', 'stats'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('ERD-Diagram-' . date('Y-m-d') . '.pdf');
    }

    public function getTableDetails($tableName)
    {
        try {
            $columns = Schema::getColumnListing($tableName);
            $foreignKeys = $this->getForeignKeys($tableName);
            $primaryKeys = $this->getPrimaryKeys($tableName);
            $rowCount = DB::table($tableName)->count();
            
            $columnDetails = [];
            foreach ($columns as $column) {
                $columnInfo = DB::select("
                    SELECT DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_KEY
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = ? 
                    AND COLUMN_NAME = ?
                ", [$tableName, $column]);
                
                $columnDetails[] = [
                    'name' => $column,
                    'type' => !empty($columnInfo) ? $columnInfo[0]->COLUMN_TYPE : 'unknown',
                    'data_type' => !empty($columnInfo) ? $columnInfo[0]->DATA_TYPE : 'unknown',
                    'is_primary' => !empty($columnInfo) && $columnInfo[0]->COLUMN_KEY === 'PRI',
                    'is_foreign' => $this->isForeignKey($tableName, $column),
                    'is_nullable' => !empty($columnInfo) && $columnInfo[0]->IS_NULLABLE === 'YES',
                    'default' => !empty($columnInfo) ? $columnInfo[0]->COLUMN_DEFAULT : null
                ];
            }
            
            $sampleData = DB::table($tableName)->limit(5)->get();
            
            return response()->json([
                'success' => true,
                'name' => $tableName,
                'columns' => $columnDetails,
                'foreign_keys' => $foreignKeys,
                'primary_keys' => $primaryKeys,
                'row_count' => $rowCount,
                'sample_data' => $sampleData,
                'has_relations' => count($foreignKeys) > 0 || $this->getTablesReferencing($tableName) > 0
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Table not found'], 404);
        }
    }

    private function getTablesReferencing($tableName)
    {
        try {
            $references = DB::select("
                SELECT COUNT(*) as count
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE REFERENCED_TABLE_NAME = ?
                AND TABLE_SCHEMA = DATABASE()
            ", [$tableName]);
            
            return $references[0]->count ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getDiagramData()
    {
        $tables = $this->getDatabaseTables();
        $relationships = $this->getTableRelationships();
        
        $nodes = [];
        $edges = [];
        
        foreach ($tables as $index => $table) {
            $nodes[] = [
                'id' => $index,
                'name' => $table['name'],
                'columns' => count($table['columns']),
                'rows' => $table['row_count']
            ];
        }
        
        foreach ($relationships as $rel) {
            $fromIndex = array_search($rel->TABLE_NAME, array_column($tables, 'name'));
            $toIndex = array_search($rel->REFERENCED_TABLE_NAME, array_column($tables, 'name'));
            
            if ($fromIndex !== false && $toIndex !== false) {
                $edges[] = [
                    'from' => $fromIndex,
                    'to' => $toIndex,
                    'from_column' => $rel->COLUMN_NAME,
                    'to_column' => $rel->REFERENCED_COLUMN_NAME
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'nodes' => $nodes,
            'edges' => $edges,
            'total_tables' => count($tables),
            'total_relationships' => count($relationships)
        ]);
    }

    public function getTableStats()
    {
        $stats = $this->getDatabaseStats();
        return response()->json($stats);
    }
}