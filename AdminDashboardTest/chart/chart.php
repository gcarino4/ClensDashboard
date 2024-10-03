<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "colens";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to sum amount by type and date in payments table
$sql = "SELECT type, DATE(date) as date, SUM(amount) as total_amount FROM payments GROUP BY type, DATE(date)";

// Query to sum amount_paid by type and invoice_date in receivable table
$sql2 = "SELECT type, DATE(invoice_date) as invoice_date, SUM(amount_paid) as total_amount_paid FROM receivable GROUP BY type, DATE(invoice_date)";

$result = $conn->query($sql);
$result2 = $conn->query($sql2);

$data = [];
$labels = [];
$date = [];

$data2 = [];
$labels2 = [];
$date2 = [];

// Bar chart (Payments)
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $date[] = $row['date'];
        $labels[] = $row['type']; // Labels for x-axis
        $data[] = $row['total_amount']; // Data for each label
    }
} else {
    echo "0 results";
}

// Doughnut chart (Receivable)
if ($result2->num_rows > 0) {
    while ($row2 = $result2->fetch_assoc()) {
        $date2[] = $row2['invoice_date'];
        $labels2[] = $row2['type']; // Labels for doughnut chart
        $data2[] = $row2['total_amount_paid']; // Data for each label
    }
} else {
    echo "0 results";
}

// Merge and deduplicate dates
$all_dates = array_unique(array_merge($date, $date2));
sort($all_dates); // Sort dates in ascending order

// Prepare the data for JavaScript
$labels_json = json_encode($labels);
$data_json = json_encode($data);
$date_json = json_encode($date);

$labels_json2 = json_encode($labels2);
$data_json2 = json_encode($data2);
$date_json2 = json_encode($date2);

$all_dates_json = json_encode($all_dates);

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart Example</title>
    <style>
        .chart {
            width: 100%;
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        canvas {
            width: 100%;
            height: 300px;
        }

        .chart-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 50px;
            width: 90%;
            max-width: 1200px;
        }
    </style>
</head>

<body>
    <div class="chart-container">
        <div class="chart">
            <canvas id="linechart"></canvas>
        </div>
        <div class="chart">
            <canvas id="barchart"></canvas>
        </div>
        <div class="chart">
            <canvas id="doughnut"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // PHP data passed to JavaScript
            const date = <?php echo $all_dates_json; ?>;


            const labels = <?php echo $labels_json; ?>;
            const data = <?php echo $data_json; ?>;


            const labels2 = <?php echo $labels_json2; ?>;
            const data2 = <?php echo $data_json2; ?>;

            const ctxLine = document.getElementById('linechart').getContext('2d');
            const ctx = document.getElementById('barchart').getContext('2d');
            const ctx2 = document.getElementById('doughnut').getContext('2d');

            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: date, // Assumes labels from both datasets are similar
                    datasets: [
                        {
                            label: 'Expenses',
                            data: data,
                            fill: false,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            tension: 0.1
                        },
                        {
                            label: 'Income',
                            data: data2,
                            fill: false,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Total Amount'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Comparison of Income and Expenses'
                        }
                    }
                }
            });

            // Bar chart for Payments
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels, // X-axis labels
                    datasets: [{
                        label: 'Payments', // Descriptive label for the dataset
                        data: data, // Data corresponding to each label
                        borderWidth: 1,
                        backgroundColor: [
                            'rgba(255, 99, 71, 0.8)',
                            'rgba(135, 206, 235, 0.7)',
                            'rgba(255, 165, 0, 0.9)',
                            'rgba(60, 179, 113, 0.6)',
                            'rgba(123, 104, 238, 0.75)',
                            'rgba(255, 20, 147, 0.8)',
                            'rgba(240, 128, 128, 0.85)',
                            'rgba(70, 130, 180, 0.9)',
                            'rgba(199, 21, 133, 0.65)',
                            'rgba(30, 144, 255, 0.6)',
                            'rgba(32, 178, 170, 0.8)',
                            'rgba(218, 112, 214, 0.7)',
                            'rgba(255, 140, 0, 0.95)',
                            'rgba(152, 251, 152, 0.5)',
                            'rgba(176, 196, 222, 0.85)',
                            'rgba(255, 105, 180, 0.75)',
                            'rgba(0, 206, 209, 0.9)',
                            'rgba(154, 205, 50, 0.7)',
                            'rgba(255, 69, 0, 0.6)',
                            'rgba(173, 216, 230, 0.8)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 71, 0.8)',
                            'rgba(135, 206, 235, 0.7)',
                            'rgba(255, 165, 0, 0.9)',
                            'rgba(60, 179, 113, 0.6)',
                            'rgba(123, 104, 238, 0.75)',
                            'rgba(255, 20, 147, 0.8)',
                            'rgba(240, 128, 128, 0.85)',
                            'rgba(70, 130, 180, 0.9)',
                            'rgba(199, 21, 133, 0.65)',
                            'rgba(30, 144, 255, 0.6)',
                            'rgba(32, 178, 170, 0.8)',
                            'rgba(218, 112, 214, 0.7)',
                            'rgba(255, 140, 0, 0.95)',
                            'rgba(152, 251, 152, 0.5)',
                            'rgba(176, 196, 222, 0.85)',
                            'rgba(255, 105, 180, 0.75)',
                            'rgba(0, 206, 209, 0.9)',
                            'rgba(154, 205, 50, 0.7)',
                            'rgba(255, 69, 0, 0.6)',
                            'rgba(173, 216, 230, 0.8)'
                        ],
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Expenses'
                        }
                    }
                }
            });

            // Doughnut chart for Receivable
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: labels2,
                    datasets: [{
                        label: 'Amount Paid',
                        data: data2,
                        backgroundColor: [
                            'rgba(255, 99, 71, 0.8)',
                            'rgba(135, 206, 235, 0.7)',
                            'rgba(255, 165, 0, 0.9)',
                            'rgba(60, 179, 113, 0.6)',
                            'rgba(123, 104, 238, 0.75)',
                            'rgba(255, 20, 147, 0.8)',
                            'rgba(240, 128, 128, 0.85)',
                            'rgba(70, 130, 180, 0.9)',
                            'rgba(199, 21, 133, 0.65)',
                            'rgba(30, 144, 255, 0.6)',
                            'rgba(32, 178, 170, 0.8)',
                            'rgba(218, 112, 214, 0.7)',
                            'rgba(255, 140, 0, 0.95)',
                            'rgba(152, 251, 152, 0.5)',
                            'rgba(176, 196, 222, 0.85)',
                            'rgba(255, 105, 180, 0.75)',
                            'rgba(0, 206, 209, 0.9)',
                            'rgba(154, 205, 50, 0.7)',
                            'rgba(255, 69, 0, 0.6)',
                            'rgba(173, 216, 230, 0.8)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 71, 0.8)',
                            'rgba(135, 206, 235, 0.7)',
                            'rgba(255, 165, 0, 0.9)',
                            'rgba(60, 179, 113, 0.6)',
                            'rgba(123, 104, 238, 0.75)',
                            'rgba(255, 20, 147, 0.8)',
                            'rgba(240, 128, 128, 0.85)',
                            'rgba(70, 130, 180, 0.9)',
                            'rgba(199, 21, 133, 0.65)',
                            'rgba(30, 144, 255, 0.6)',
                            'rgba(32, 178, 170, 0.8)',
                            'rgba(218, 112, 214, 0.7)',
                            'rgba(255, 140, 0, 0.95)',
                            'rgba(152, 251, 152, 0.5)',
                            'rgba(176, 196, 222, 0.85)',
                            'rgba(255, 105, 180, 0.75)',
                            'rgba(0, 206, 209, 0.9)',
                            'rgba(154, 205, 50, 0.7)',
                            'rgba(255, 69, 0, 0.6)',
                            'rgba(173, 216, 230, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Income'
                        }
                    }
                }
            });

        });
    </script>
</body>


</html>