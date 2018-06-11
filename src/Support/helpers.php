<?php

if (! function_exists('render_markdown')) {
    function render_markdown($collections)
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
