<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ERDController extends Controller
{
    /**
     * Display the ERD dashboard with actual database structure
     */
    public function index()
    {
        // Get all tables from the database
        $tables = $this->getDatabaseTables();
        
        // Get all relationships between tables
        $relationships = $this->getTableRelationships();
        
        return view('welcome', compact('tables', 'relationships'));
    }
    
    /**
     * Fetch all tables with their columns from the database
     */
    private function getDatabaseTables()
    {
        $databaseName = DB::connection()->getDatabaseName();
        
        // Get all table names - FIXED: Using INFORMATION_SCHEMA for consistency
        $tables = DB::select("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_TYPE = 'BASE TABLE'
        ");
        
        $tableData = [];
        
        foreach ($tables as $table) {
            // FIXED: Use TABLE_NAME column from INFORMATION_SCHEMA
            $tableName = $table->TABLE_NAME;
            
            // Skip Laravel system tables if needed
            if ($this->shouldSkipTable($tableName)) {
                continue;
            }
            
            // Get column information
            $columns = Schema::getColumnListing($tableName);
            
            // Get column types and keys using INFORMATION_SCHEMA
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
                    'is_primary' => $isPrimary,
                    'is_foreign' => $isForeignKey,
                    'is_nullable' => !empty($columnInfo) && $columnInfo[0]->IS_NULLABLE === 'YES',
                    'default' => !empty($columnInfo) ? $columnInfo[0]->COLUMN_DEFAULT : null
                ];
            }
            
            // Get table statistics
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
    
    /**
     * Check if column is a foreign key
     */
    private function isForeignKey($tableName, $columnName)
    {
        $foreignKeys = DB::select("
            SELECT 
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$tableName, $columnName]);
        
        return !empty($foreignKeys);
    }
    
    /**
     * Get primary keys of a table
     */
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
    
    /**
     * Get foreign keys of a table with their references
     */
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
    
    /**
     * Get all table relationships across the database
     */
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
    
    /**
     * Determine if a table should be skipped
     */
    private function shouldSkipTable($tableName)
    {
        // Skip Laravel system tables if you want
        $skipTables = [
            'cache',
            'cache_locks',
            'failed_jobs',
            'jobs',
            'job_batches',
            'migrations',
            'password_reset_tokens',
            'sessions'
        ];
        
        return in_array($tableName, $skipTables);
    }
    
    /**
     * Export ERD to PDF with enhanced styling
     */
    public function exportPDF()
    {
        $tables = $this->getDatabaseTables();
        $relationships = $this->getTableRelationships();
        
        $pdf = Pdf::loadView('pdf.erd-pdf', compact('tables', 'relationships'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('ERD-Diagram-' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Get detailed table information for AJAX requests
     */
    public function getTableDetails($tableName)
    {
        try {
            $columns = Schema::getColumnListing($tableName);
            $foreignKeys = $this->getForeignKeys($tableName);
            $rowCount = DB::table($tableName)->count();
            
            // Get sample data
            $sampleData = DB::table($tableName)->limit(5)->get();
            
            return response()->json([
                'name' => $tableName,
                'columns' => $columns,
                'foreign_keys' => $foreignKeys,
                'row_count' => $rowCount,
                'sample_data' => $sampleData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Table not found'], 404);
        }
    }
    
    /**
     * Get relationship diagram data for visual representation
     */
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
            'nodes' => $nodes,
            'edges' => $edges,
            'total_tables' => count($tables),
            'total_relationships' => count($relationships)
        ]);
    }
}