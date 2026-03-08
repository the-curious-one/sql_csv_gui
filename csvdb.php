<?php /**
 * Run an SQL query against a CSV file and return results as associative array
 *
 * @param string $csvPath Path to CSV file
 * @param string $sql SQL query (use table name "data")
 * @return array
 */
function csv_query($csvPath, $sql) {

    // Create in-memory SQLite database
    $db = new PDO('sqlite::memory:');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Open CSV
    $file = fopen($csvPath, 'r');

    // Get headers
    $headers = fgetcsv($file);

    // Create table dynamically
    $columns = array_map(function($col){
        // sanitize column names for SQLite
        $col = trim($col);
        $col = preg_replace('/[^a-zA-Z0-9_]/', '_', $col);
        return "`$col` TEXT";
    }, $headers);

    $db->exec("CREATE TABLE data (" . implode(",", $columns) . ")");

    // Prepare insert
    $placeholders = implode(",", array_fill(0, count($headers), "?"));
    $stmt = $db->prepare("INSERT INTO data VALUES ($placeholders)");

    // Insert rows
    while (($row = fgetcsv($file)) !== false) {
        $stmt->execute($row);
    }

    fclose($file);

    // Run user's query
    $result = $db->query($sql);

    // Return associative array
    return $result->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Render array as HTML table
 *
 * @param array $data
 * @return string
 */
function render_table($data) {

    if (empty($data)) {
        return "<p>No results</p>";
    }

    $html = "<table id='table' border='1'>";

    // headers
    $html .= "<tr>";
    foreach (array_keys($data[0]) as $col) {
        $html .= "<th>" . htmlspecialchars($col) . "</th>";
    }
    $html .= "</tr>";

    // rows
    foreach ($data as $row) {
        $html .= "<tr>";
        foreach ($row as $cell) {
            $html .= "<td>" . htmlspecialchars($cell) . "</td>";
        }
        $html .= "</tr>";
    }

    $html .= "</table>";

    return $html;
}

?>