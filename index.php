<?php

    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    require("csvdb.php");

    $csv = __DIR__ . "/uploads/list.csv";

    //print_r(PDO::getAvailableDrivers());

    //phpinfo();

    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // require_once("functions.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Guests</title>
    <link rel="stylesheet" href="lib/tabulator/dist/css/tabulator.min.css">
    <link rel="stylesheet" href="src/style.css">
</head>
<body>

<style>
    textarea{
        min-height: 10em;
        max-height: 20em;
        width: 100%;
        resize: vertical;
        padding: 5px;
    }
    #table{
        max-height: 50vh;
    }
</style>

<div class="wrapper">

    <h1>CSV Queries</h1><hr><br>

    <div class="form-container">
        <form action="" method="POST">
            <label for="query">Query:</label><br>
            <textarea name="query" id="query" placeholder="Type new query here..."><?= isset($_POST["query"]) ? $_POST["query"] : '' ?></textarea>
            <button type="submit">Filter</button>
            <button type="button" id="copyText">Copy Query</button>
            <button><a href="upload.php">Upload</a></button>
            <button id="download-csv">Download Table as CSV</button>
        </form>
    </div><hr>

    <div>

        <section class="data">
            <?php
                $default = "SELECT *
                                FROM data";


                if (!empty($_POST['query'])) { 
                    $query = $_POST['query'];
                }else{
                    $query = $default;
                }

                $data = csv_query($csv, $query);
                echo "<div id='table'></div>";
            ?>

        </section>

    </div>

</div>

    <script src="lib/tabulator/dist/js/tabulator.min.js"></script>

    <script>
        var tableData = <?php echo json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        var table = new Tabulator("#table", {
            autoColumns: "full",
            data: tableData,
            autoColumnsDefinitions: function(columns){

                columns.unshift({
                    title: "#",
                    formatter: "rownum",
                    width: 60,
                    headerSort: false
                });

                return columns;
            }
        });

        document.getElementById("download-csv").addEventListener("click", function(){
            table.download("csv", "table-data.csv");
        });

        document.getElementById('copyText').addEventListener('click', async () => {
            const text = document.getElementById('query').value;

            try {
                await navigator.clipboard.writeText(text);
                console.log('Copied to clipboard!');
                alert("Text copied!");
            } catch (err) {
                console.error('Failed to copy:', err);
                alert("Text could not be copied.");
            }
        });


        
    </script>

</body>
</html>