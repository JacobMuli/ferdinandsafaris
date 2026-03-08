<?php

$file = 'database/data/tours.csv';
$handle = fopen($file, 'r');

if (!$handle) {
    die("Cannot open file\n");
}

// Read header
$headers = fgetcsv($handle);
$headerCount = count($headers);

echo "Header column count: $headerCount\n";
echo "Headers: " . implode(', ', $headers) . "\n\n";

$rowNum = 1;
$mismatchRows = [];
$validRows = 0;

while (($row = fgetcsv($handle)) !== false) {
    $rowNum++;
    $colCount = count($row);
    
    if ($colCount !== $headerCount) {
        $mismatchRows[] = [
            'row' => $rowNum,
            'expected' => $headerCount,
            'actual' => $colCount,
            'name' => $row[1] ?? 'N/A'
        ];
    } else {
        $validRows++;
    }
}

fclose($handle);

echo "Total rows (excluding header): " . ($rowNum - 1) . "\n";
echo "Valid rows: $validRows\n";
echo "Mismatched rows: " . count($mismatchRows) . "\n\n";

if (!empty($mismatchRows)) {
    echo "Rows with column count mismatch:\n";
    echo str_repeat('-', 80) . "\n";
    printf("%-6s %-50s %-10s %-10s\n", "Row", "Tour Name", "Expected", "Actual");
    echo str_repeat('-', 80) . "\n";
    
    foreach ($mismatchRows as $info) {
        printf("%-6d %-50s %-10d %-10d\n", 
            $info['row'], 
            substr($info['name'], 0, 47), 
            $info['expected'], 
            $info['actual']
        );
    }
}
