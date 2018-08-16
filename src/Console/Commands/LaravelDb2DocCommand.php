<?php

namespace CleaniqueCoders\LaravelDB2DOC\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LaravelDb2DocCommand extends Command
{
    public $database_connection;
    public $format;
    public $connection;
    public $schema;
    public $tables;
    public $collections = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:2doc {--database=} {--format=md}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate database schema to markdown (by default)';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*
         * Initialize
         */
        $this->init();

        /*
         * Generate Table & Column Data Structure
         */
        $this->generateDataStructure();

        /*
         * Generate Document
         */
        $this->generateDocument();
    }

    private function init()
    {
        if (! file_exists(storage_path('app/db2doc'))) {
            mkdir(storage_path('app/db2doc'));
        }

        $this->database_connection = $this->option('database') ?? config('database.default');
        $this->format              = $this->option('format');
        $this->connection          = DB::connection($this->database_connection)->getDoctrineConnection();
        $this->schema              = $this->connection->getSchemaManager();
        $this->tables              = $this->schema->listTableNames();
    }

    private function generateDataStructure()
    {
        $tables = $this->tables;
        $schema = $this->schema;

        $this->collections = [];
        foreach ($tables as $table) {
            $columns = $schema->listTableColumns($table);
            $foreignKeys = collect($schema->listTableForeignKeys($table))->keyBy(function ($foreignColumn) {
                return $foreignColumn->getLocalColumns()[0];
            });
            $this->info('Table: ' . $table);
            foreach ($columns as $column) {
                $columnName = $column->getName();
                
                if (isset($foreignKeys[$columnName])) {
                    $foreignColumn = $foreignKeys[$columnName];
                    $foreignTable = $foreignColumn->getForeignTableName();
                    $columnType = 'fk -> ' . $foreignTable;
                } else {
                    $columnType = $column->getType()->getName();
                }
                
                $details['column']           = $columnName;
                $details['type']             = $columnType;
                $details['length']           = $column->getLength() && 255 !== $column->getLength() ? $column->getLength() : null;
                $details['default']          = (true == $column->getDefault() ? 'Yes' : 'No');
                $details['nullable']         = (true === ! $column->getNotNull() ? 'Yes' : 'No');
                $details['comment']          = $column->getComment();
                $this->collections[$table][] = $details;
            }
        }
    }

    private function generateDocument()
    {
        switch ($this->format) {
            case 'json':
                $rendered = $this->render_json_content();
                break;

            default:
                $rendered = $this->render_markdown_content();
                break;
        }
        $filename = $rendered['filename'];
        $output   = $rendered['output'];
        $path     = storage_path('app/db2doc/' . $filename);
        if (file_exists($path)) {
            unlink($path);
        }
        file_put_contents($path, $output);
    }

    private function getStub()
    {
        return file_get_contents(__DIR__ . '/stubs/header.stub');
    }

    private function render_json_content()
    {
        $collections = $this->collections;

        return [
            'output'   => json_encode($collections),
            'filename' => config('app.name') . ' Database Schema.json',
        ];
    }

    private function render_markdown_content()
    {
        $collections = $this->collections;
        $output      = [];
        foreach ($collections as $table => $properties) {
            $output[] = '### ' . Str::title($table) . PHP_EOL . PHP_EOL;
            $output[] = '| Column | Type | Length | Default | Nullable | Comment |' . PHP_EOL;
            $output[] = '|--------|------|--------|---------|----------|---------|' . PHP_EOL;
            foreach ($properties as $key => $value) {
                $fields = [];
                foreach ($value as $k => $v) {
                    $fields[] = "{$v}";
                }
                $output[] = '| ' . join(' | ', $fields) . ' |' . PHP_EOL;
            }
            $output[] = PHP_EOL;
        }

        $schema          = join('', $output);
        $stub            = $this->getStub();
        $database_config = config('database.' . $this->database_connection);
        $output          = str_replace([
                'APP_NAME',
                'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE',
                'SCHEMA_CONTENT',
            ], [
                config('app.name'),
                $this->database_connection, $database_config['host'], $database_config['port'], $database_config['database'],
                $schema,
            ], $stub);

        $filename = config('app.name') . ' Database Schema.md';

        return [
            'output'   => $output,
            'filename' => $filename,
        ];
    }
}
