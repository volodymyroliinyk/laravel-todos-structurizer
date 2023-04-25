<?php

namespace VolodymyrOliinyk\TodosStructurizer\Commands;

use Illuminate\Console\Command;

/**
 * @pattern (JetBrains PhpStorm) \bTODO\:((.|\n)*)\:ENDTODO\b*
 * @ussage TODO:[category|priority:<high|medium|low>]:<free multiline text>:ENDTODO
 */
final class TodosStructurizedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'todos-structurized:collect ' .
    '{--category= : task category name}  ';

    const PATTERN_1 = '/(TODO\:([\S\s]*?)\:ENDTODO)+/sm';
    const PATTERN_2 = '/(TODO\:\[(.*?)\]\:([\S\s]*?)\:ENDTODO)+/sm';

    const TODO_COLUMN_WIDTH = 80;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finding todos and structurizing by category or any tag.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line('');
        $this->line($this->description);
        $this->line('');

        $category = $this->option('category');

        $basePathDirectory = base_path();

        $filesCollected = $this->collectingFiles($this->getDirectories());

        /**
         * Collecting structured todos.
         */
        $hashesOfStructuredTodos = [];
        $todosWithStructure = [];
        $outputTableData = [];
        $todosCategories = [];

        foreach ($filesCollected as $file) {
            $fileContent = file_get_contents($file);

            preg_match_all(self::PATTERN_2, $fileContent, $matches, PREG_OFFSET_CAPTURE);

            if (!empty($matches[0])) {
                $this->loopStructuredTodos(
                    $matches,
                    $fileContent,
                    $basePathDirectory,
                    $file,
                    $todosCategories,
                    $hashesOfStructuredTodos,
                    $todosWithStructure,
                    $outputTableData
                );
            }
            unset($matches);
        }

        // Categories table.
        if (!empty($todosCategories)) {
            asort($todosCategories);

            foreach ($todosCategories as $todoCategory) {
                $outputTableData3[] = [$todoCategory];
            }

            $this->table([
                'Category',
            ], $outputTableData3);

            $this->line(sprintf('Total todos categories: %s', count($outputTableData3)));
        }

        // Filtering all structured todos by category.
        if (!empty($category)) {
            $outputTableData = array_filter($outputTableData, function ($var) use ($category) {
                return ($var[0] == $category);
            });
        }

        /* TODO:[todos-structurized]:
        Filter output by priority.
        :ENDTODO */

        // Sorting by multiple columns.
        $sort = [];
        foreach ($outputTableData as $k => $v) {
            $sort['category'][$k] = $v[0];
            $sort['priority'][$k] = $v[1];
            $sort['todo'][$k] = $v[3];
        }

        array_multisort(
            $sort['category'],
            SORT_ASC,
            $sort['priority'],
            SORT_ASC,
            $sort['todo'],
            SORT_ASC,
            $outputTableData);

        // Collected todos table.
        $this->table([
            'Category',
            'Metadata string',
            'Todo',
            'Line number',
            'File path',
        ], $outputTableData);
        $this->line(sprintf('Total structured todos: %s', count($outputTableData)));

        //-------------------------------------------------------------------------------------------------------------

        // Collecting unstructured todos.
        $todosWithoutStructure = [];
        $outputTableData2 = [];

        foreach ($filesCollected as $file) {
            $fileContent = file_get_contents($file);

            preg_match_all(self::PATTERN_1, $fileContent, $matches, PREG_OFFSET_CAPTURE);

            if (!empty($matches[0])) {
                $this->loopUnstructuredTodos($matches, $fileContent, $file, $hashesOfStructuredTodos,
                    $basePathDirectory, $todosWithoutStructure, $outputTableData2);
            }

        }

        // Final output.
        $this->table([
            'Todo',
            'Line number',
            'File path',
        ], $outputTableData2);

        $this->line(sprintf('Total unstructured todos: %s', count($outputTableData2)));

        return Command::SUCCESS;
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
        $todoCollectorConfig = config('todos-structurized');
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
        $todoCollectorConfig = config('todos-structurized');
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
     * @param $results
     * @return array|mixed
     */
    private function getDirFiles(string $dir, array &$results = [])
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } elseif ($value != "." && $value != "..") {
                if (!in_array($path, $this->getDirectoriesIgnored())) {
                    $this->getDirFiles($path, $results);
                }
            }
        }

        return $results;
    }

    /**
     * @param array $matches
     * @param string $fileContent
     * @param string $basePathDirectory
     * @param string $file
     * @param array $todosCategories
     * @param array $hashesOfStructuredTodos
     * @param array $todosWithStructure
     * @param array $outputTableData
     * @return void
     */
    private function loopStructuredTodos(
        array  $matches,
        string $fileContent,
        string $basePathDirectory,
        string $file,
        array  &$todosCategories = [],
        array  &$hashesOfStructuredTodos = [],
        array  &$todosWithStructure = [],
        array  &$outputTableData = []
    )
    {
        $matchesCount = count($matches[0]);
        for ($mc = 0; $mc < $matchesCount; $mc++) {
            $fileLineNumber = substr_count(mb_substr($fileContent, 0, $matches[0][$mc][1]), PHP_EOL) + 1;
            $todoMetadataString = $matches[2][$mc][0];
            $todoContentString = trim($matches[3][$mc][0]);
            $todoContentString = str_replace(['//', '     ', '    ', '   ', '  '], ' ', $todoContentString);
            $metadataPriority = '? PRIORITY ?';

            if ($todoMetadataString == '') {
                $todoMetadataString = '? METADATA ?';
            }

            if (mb_strlen($todoContentString) > 100) {
                $todoContentString = wordwrap($todoContentString, self::TODO_COLUMN_WIDTH, "\n");
            }

            $todoMetadataStringParts = explode('|', $todoMetadataString);

            if (strpos($todoMetadataString, '|') !== false) {
                $metadataCategory = $todoMetadataStringParts[0];
            } else {
                $metadataCategory = $todoMetadataStringParts[0];
            }

            if ($metadataCategory == '') {
                $metadataCategory = '? CATEGORY ?';
            }

            if (!in_array($metadataCategory, $todosCategories)) {
                $todosCategories[] = $metadataCategory;
            }

            if (!empty($todoMetadataStringParts[1])) {
                $metadataPriorityParts = explode(':', $todoMetadataStringParts[1]);

                if (!empty($metadataPriorityParts[1])) {
                    $metadataPriority = $metadataPriorityParts[1];
                }
            }

            $hash = hash('sha256', sprintf('%s%s', $file, $fileLineNumber));
            $hashesOfStructuredTodos[] = $hash;

            $todosWithStructure[] = [
                'hash' => $hash,
                'file_path' => str_replace($basePathDirectory, '', $file),
                'line_number' => $fileLineNumber,
                'metadata_string' => $todoMetadataString,
                'metadata_string_parts' => $todoMetadataStringParts,
                'metadata' => [
                    'category' => $metadataCategory,
                    'priority' => $metadataPriority,
                ],
                'todo_string' => $todoContentString,
            ];

            $outputTableData[] = [
                $metadataCategory,
                $metadataPriority,
                $todoMetadataString,
                $todoContentString,
                $fileLineNumber,
                str_replace($basePathDirectory, '', $file)
            ];
        }
    }

    /**
     * @param array $matches
     * @param string $fileContent
     * @param string $file
     * @param array $hashesOfStructuredTodos
     * @param string $basePathDirectory
     * @param array $todosWithoutStructure
     * @param array $outputTableData2
     * @return void
     */
    private function loopUnstructuredTodos(
        array  $matches,
        string $fileContent,
        string $file,
        array  $hashesOfStructuredTodos,
        string $basePathDirectory,
        array  &$todosWithoutStructure = [],
        array  &$outputTableData2 = []
    )
    {

        $matchesCount = count($matches[0]);
        for ($mc = 0; $mc < $matchesCount; $mc++) {
            $fileLineNumber = substr_count(mb_substr($fileContent, 0, $matches[0][$mc][1]), PHP_EOL) + 1;
            $hash = hash('sha256', sprintf('%s%s', $file, $fileLineNumber));

            if (in_array($hash, $hashesOfStructuredTodos)) {
                continue;
            }

            $todoContentString = trim($matches[2][$mc][0]);
            $todoContentString = str_replace(['//', '     ', '    ', '   ', '  '], ' ', $todoContentString);

            if (mb_strlen($todoContentString) > 100) {
                $todoContentString = wordwrap($todoContentString, self::TODO_COLUMN_WIDTH, "\n");
            }

            $todosWithoutStructure[] = [
                'hash' => $hash,
                'file_path' => str_replace($basePathDirectory, '', $file),
                'line_number' => $fileLineNumber,
                'metadata_string' => null,
                'metadata_string_parts' => null,
                'metadata' => [
                    'category' => null,
                ],
                'todo_string' => $todoContentString,
            ];

            $outputTableData2[] = [
                $todoContentString,
                $fileLineNumber,
                str_replace($basePathDirectory, '', $file)
            ];
        }
    }

}
