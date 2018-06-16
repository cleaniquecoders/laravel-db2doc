<?php

namespace CleaniqueCoders\LaravelDB2DOC\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LaravelDb2DocCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db2doc {--database=default} {--format=md}';

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
        $database_connection = $this->option('database');
        $format              = $this->option('format');

        $connection  = DB::connection($database_connection)->getDoctrineConnection();
        $schema      = $connection->getSchemaManager();
        $tables      = $schema->listTableNames();
        $collections = [];
        foreach ($tables as $table) {
            $columns = $schema->listTableColumns($table);
            $this->info('Table: ' . $table);
            foreach ($columns as $column) {
                $details['column']     = $column->getName();
                $details['type']       = $column->getType()->getName();
                $details['length']     = $column->getLength() && 255 !== $column->getLength() ? $column->getLength() : null;
                $details['default']    = (true == $column->getDefault() ? 'Yes' : 'No');
                $details['nullable']   = (true == ! $column->getNotNull() ? 'Yes' : 'No');
                $details['comment']    = $column->getComment();
                $collections[$table][] = $details;
            }
        }

        if ('json' == $format) {
            $output   = json_encode($collections);
            $filename = config('app.name') . ' Database Schema.json';
        } elseif ('md' == $format) {
            $schema          = $this->render_markdown_content($collections);
            $stub            = $this->getStub();
            $database_config = config('database.' . $database_connection);
            $output          = str_replace([
                'APP_NAME',
                'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE',
                'SCHEMA_CONTENT',
            ], [
                config('app.name'),
                $database_connection, $database_config['host'], $database_config['port'], $database_config['database'],
                $schema,
            ], $stub);
            $filename = config('app.name') . ' Database Schema.md';
        }

        if (! file_exists(storage_path('app/db2doc'))) {
            mkdir(storage_path('app/db2doc'));
        }

        file_put_contents(storage_path('app/db2doc/' . $filename), $output);
    }

    public function getStub()
    {
        return __DIR__ . 'stubs/header.stub';
    }

    public function render_markdown_content($collections)
    {
        $output = [];
        foreach ($collections as $table => $properties) {
            $output[] = '### ' . $table . PHP_EOL . PHP_EOL;
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

        return join('', $output);
    }
}
