<?php

namespace VolodymyrOliinyk\TodosStructurizer\Commands;

use Illuminate\Console\Command;
use VolodymyrOliinyk\TodosStructurizer\Exceptions\TodosStructurizerException;

/**
 * @pattern (JetBrains PhpStorm) \bTODO\:((.|\n)*)\:ENDTODO\b*
 * @ussage TODO:[category|priority:<high|medium|low>]:<free multiline text>:ENDTODO
 */
final class TodosStructurizerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'todos-structurizer:collect ' .
    '{--category= : task category name} ' .
    '{--priority= : task priority name: high|medium|low} ';

    // Pattern without category, priority.
    const PATTERN_1 = '/(TODO\:([\S\s]*?)\:ENDTODO)+/sm';

    // Pattern with category, priority.
    const PATTERN_2 = '/(TODO\:\[(.*?)\]\:([\S\s]*?)\:ENDTODO)+/sm';

    // Width of table column with main td string.
    const TODO_COLUMN_WIDTH = 80;

    const PRIORITIES = [
        'high',
        'medium',
        'low',
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finding todos and structurizing by category or any tag.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->line('');
        $this->line($this->description);
        $this->line('');

        $category = $this->option('category');
        $priority = $this->option('priority');

        if (!empty($priority)) {
            if (!in_array($priority, self::PRIORITIES)) {
                throw new TodosStructurizerException('Error. Priority isn\'t allowed.', 500);
            }
        }

        $basePathDirectory = base_path();
        var_dump($basePathDirectory);

        $filesCollected = $this->collectingFiles($this->getDirectories());
        var_dump($filesCollected);
        // Collecting structured todos.

        $hashesOfStructuredTodos = [];
        $outputTableData = [];
        $todosCategories = [];

        foreach ($filesCollected as $file) {
            $fileContent = file_get_contents($file);

            if (empty($fileContent)) {
                continue;
            }

            preg_match_all(self::PATTERN_2, $fileContent, $matches, PREG_OFFSET_CAPTURE);

            if (!empty($matches[0])) {
                $this->loopStructuredTodos(
                    $matches,
                    $fileContent,
                    $basePathDirectory,
                    $file,
                    $todosCategories,
                    $hashesOfStructuredTodos,
                    $outputTableData
                );
            }

            unset($matches);
        }

        // Categories table.
        if (!empty($todosCategories)) {
            asort($todosCategories);

            $outputTableData3 = [];

            foreach ($todosCategories as $todoCategory) {
                $outputTableData3[] = [$todoCategory];
            }

            $this->table([
                'Category',
            ], $outputTableData3);

            $this->line('');
            $this->line(sprintf('Total todos categories: %s', count($outputTableData3)));
            $this->line('');
        }

        if (empty($outputTableData)) {
            $this->line('No structurized todos.');
        }

        // Filtering all structured todos by category.
        if (!empty($category)) {
            $outputTableData = array_filter($outputTableData, function ($var) use ($category) {
                return ($var[0] == $category);
            });
        }

        // Filtering all structured todos by priority.
        if (!empty($priority)) {
            $outputTableData = array_filter($outputTableData, function ($var) use ($priority) {
                return ($var[1] == $priority);
            });
        }

        // Sorting by multiple columns.
        $sort = [];
        foreach ($outputTableData as $k => $v) {
            $sort['category'][$k] = $v['category'] ?? null;
            $sort['priority'][$k] = $v['priority'] ?? null;
            $sort['todo_content'][$k] = $v['todo_content'];
        }

        if (!empty($sort)) {
            array_multisort(
                $sort['category'],
                SORT_ASC,
                $sort['priority'],
                SORT_ASC,
                $sort['todo_content'],
                SORT_ASC,
                $outputTableData
            );
        }

        $i = 1;
        foreach ($outputTableData as $k => $v) {
            $outputTableData[$k] = [
                'N' => $i,
                'category' => $v['category'],
                'property' => $v['property'],
                'todo_content' => $v['todo_content'],
                'file_line_number' => $v['file_line_number'],
                'file_path' => $v['file_path'],
            ];
            $i++;
        }

        // Collected todos table.
        $this->table([
            'N' => 'N',
            'category' => 'Category',
            'property' => 'Priority',
            'todo_content' => 'Todo',
            'file_line_number' => 'Line number',
            'file_path' => 'File path',
        ], $outputTableData);

        $this->line('');
        $this->line(sprintf('Total structured todos: %s', count($outputTableData)));
        $this->line('');

        //-------------------------------------------------------------------------------------------------------------

        // Collecting unstructured todos.

        $outputTableData2 = [];

        foreach ($filesCollected as $file) {
            $fileContent = file_get_contents($file);

            if (empty($fileContent)) {
                continue;
            }

            preg_match_all(self::PATTERN_1, $fileContent, $matches, PREG_OFFSET_CAPTURE);

            if (!empty($matches[0])) {
                $this->loopUnstructuredTodos(
                    $matches,
                    $fileContent,
                    $file,
                    $hashesOfStructuredTodos,
                    $basePathDirectory,
                    $outputTableData2
                );
            }
        }

        $i = 1;
        foreach ($outputTableData2 as $k => $v) {
            $outputTableData2[$k] = [
                'N' => $i,
                'todo_content' => $v['todo_content'],
                'file_line_number' => $v['file_line_number'],
                'file_path' => $v['file_path'],
            ];
            $i++;
        }

        // Final output.
        $this->table([
            'N',
            'Todo',
            'Line number',
            'File path',
        ], $outputTableData2);

        $this->line('');
        $this->line(sprintf('Total unstructured todos: %s', count($outputTableData2)));
        $this->line('');

        return self::SUCCESS;
    }

    /**
     * Collecting all files from directories.
     *
     * @param array $directories
     * @return array
     */
    private function collectingFiles(array $directories): array
    {
        $filesCollected = [];

        foreach ($directories as $directory) {
            $files = $this->getDirFiles($directory);

            foreach ($files as $file) {
                $dir = dirname($file);

                if (in_array($dir, $this->getDirectoriesIgnored())) {
                    continue;
                }

                $filesCollected[] = $file;
            }
        }

        $filesCollected = array_unique($filesCollected);

        return $filesCollected;
    }

    /**
     * Where need search todos.
     *
     * @return array
     */
    private function getDirectories(): array
    {
        $todoCollectorConfig = config('todos-structurizer');
        $allDirectories = [];

        foreach ($todoCollectorConfig['directories'] as $directory) {
            $allDirectories[] = base_path($directory);
        }

        return $allDirectories;
    }

    /**
     * Ignored directories.
     *
     * @return array
     */
    private function getDirectoriesIgnored(): array
    {
        $todoCollectorConfig = config('todos-structurizer');
        $allDirectories = [];

        foreach ($todoCollectorConfig['directories-ignored'] as $directory) {
            $allDirectories[] = base_path($directory);
        }

        return $allDirectories;
    }

    /**
     * Getting files from directories, recursively.
     *
     * @param string $dir
     * @param array $results
     * @return array
     */
    private function getDirFiles(string $dir, array &$results = []): array
    {
        $files = scandir($dir);

        if (!empty($files)) {
            foreach ($files as $key => $value) {
                $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

                if (empty($path)) {
                    continue;
                }

                if (!is_dir($path)) {
                    $results[] = $path;
                } elseif ($value != "." && $value != "..") {
                    if (!in_array($path, $this->getDirectoriesIgnored())) {
                        $this->getDirFiles($path, $results);
                    }
                }
            }
        }
        return $results;
    }

    /* TODO:[todos-structurizer]:
       - Is it possible make this method shorter and less arguments.
     :ENDTODO */

    /**
     * @param array $matches
     * @param string $fileContent
     * @param string $basePathDirectory
     * @param string $file
     * @param array $todosCategories
     * @param array $hashesOfStructuredTodos
     * @param array $outputTableData
     * @return void
     */
    private function loopStructuredTodos(
        array $matches,
        string $fileContent,
        string $basePathDirectory,
        string $file,
        array &$todosCategories = [],
        array &$hashesOfStructuredTodos = [],
        array &$outputTableData = []
    ): void
    {
        $matchesCount = count($matches[0]);

        for ($mc = 0; $mc < $matchesCount; $mc++) {
            $fileLineNumber = substr_count(mb_substr($fileContent, 0, $matches[0][$mc][1]), PHP_EOL) + 1;
            $metadata = $matches[2][$mc][0];

            $todoContent = $this->todoContentStringPrepareForTable($matches[3][$mc][0]);

            $metadataPriority = '-';

            if ($metadata == '') {
                $metadata = '-';
            }

            $metadataParts = explode('|', $metadata);
            $metadataCategory = $metadataParts[0];

            if ($metadataCategory == '') {
                $metadataCategory = '-';
            }

            if (!in_array($metadataCategory, $todosCategories)) {
                $todosCategories[] = $metadataCategory;
            }

            if (!empty($metadataParts[1])) {
                $metadataPriorityParts = explode(':', $metadataParts[1]);

                if (!empty($metadataPriorityParts[1])) {
                    $metadataPriority = $metadataPriorityParts[1];
                }
            }

            $hash = hash('sha256', sprintf('%s%s', $file, $fileLineNumber));
            $hashesOfStructuredTodos[] = $hash;

            $outputTableData[] = [
                'category' => $metadataCategory,
                'property' => $metadataPriority,
                'todo_content' => $todoContent,
                'file_line_number' => $fileLineNumber,
                'file_path' => str_replace($basePathDirectory, '', $file),
            ];
        }
    }

    /* TODO:[todos-structurizer]:
       - Is it possible make this method shorter and less arguments.
     :ENDTODO */

    /**
     * @param array $matches
     * @param string $fileContent
     * @param string $file
     * @param array $hashesOfStructuredTodos
     * @param string $basePathDirectory
     * @param array $outputTableData2
     * @return void
     */
    private function loopUnstructuredTodos(
        array $matches,
        string $fileContent,
        string $file,
        array $hashesOfStructuredTodos,
        string $basePathDirectory,
        array &$outputTableData2 = []
    ): void
    {
        $matchesCount = count($matches[0]);

        for ($mc = 0; $mc < $matchesCount; $mc++) {
            $fileLineNumber = substr_count(mb_substr($fileContent, 0, $matches[0][$mc][1]), PHP_EOL) + 1;
            $hash = hash('sha256', sprintf('%s%s', $file, $fileLineNumber));

            if (in_array($hash, $hashesOfStructuredTodos)) {
                continue;
            }

            $todoContent = $this->todoContentStringPrepareForTable($matches[2][$mc][0]);

            $outputTableData2[] = [
                'todo_content' => $todoContent,
                'file_line_number' => $fileLineNumber,
                'file_path' => str_replace($basePathDirectory, '', $file),
            ];
        }
    }

    /**
     * @param string $todoContent
     * @return string
     */
    private function todoContentStringPrepareForTable(string $todoContent): string
    {
        $todoContent = trim($todoContent);
        $todoContent = str_replace(['//', '     ', '    ', '   ', '  '], ' ', $todoContent);

        if (mb_strlen($todoContent) > 100) {
            $todoContent = wordwrap($todoContent, self::TODO_COLUMN_WIDTH, "\n");
        }

        return $todoContent;
    }
}
