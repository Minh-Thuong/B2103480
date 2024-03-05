<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a file was uploaded successfully
    if (isset($_FILES["csv_file"]) && $_FILES["csv_file"]["error"] == UPLOAD_ERR_OK) {
        $csv_file = $_FILES["csv_file"]["tmp_name"];
        $file_info = pathinfo($_FILES["csv_file"]["name"]);

        // Check if the uploaded file is a CSV file
        if (strtolower($file_info["extension"]) == "csv") {
            // Read the CSV file into an array
            $csv = array();
            $lines = file($csv_file, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $key => $value) {
                $csv[$key] = str_getcsv($value);
            }

            // Connect to the database
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "qlbanhang";

            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Insert data from CSV into the database
            foreach ($csv as $row) {
                // Escape special characters in each value to prevent SQL injection
                $escaped_values = array_map(function($value) use ($conn) {
                    return mysqli_real_escape_string($conn, $value);
                }, $row);
                
                // Assuming column1 is the first column in the CSV, column2 is the second column, and so on
                list($column1, $column2, $column3, $column4, $column5, $column6, $column7) = $escaped_values;
            
                $sql = "INSERT INTO customers (id, fullname, email, Birthday, reg_date, password, img_profile) 
                        VALUES ('$column1', '$column2', '$column3','$column4','$column5','$column6','$column7')";
                if ($conn->query($sql) === TRUE) {
                    echo "Inserted data: $column1, $column2, $column3, $column4, $column5, $column6, $column7<br>";
                } else {
                    echo "Error inserting data: " . $conn->error . "<br>";
                }
            }

            $conn->close();
        } else {
            echo "Only CSV files are allowed.";
        }
    } else {
        echo "Error uploading file.";
    }
}
?>
