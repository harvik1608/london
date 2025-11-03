<?php
// Sample JSON data
$json = '[{"id":"1","duration":"30","retail_price":"20","special_price":"","caption":"Short"},{"id":"2","duration":"30","retail_price":"25","special_price":"","caption":"Medium"},{"id":"3","duration":"30","retail_price":"28","special_price":"","caption":"Long"},{"id":"4","duration":"30","retail_price":"33","special_price":"","caption":"Extra Long"}]';

// Decode JSON string into an associative array
$data = json_decode($json, true);

// Extract unique captions for table headers
$captions = array_column($data, 'caption');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Table</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>

<table>
    <thead>
        <tr>
            <th>Duration (min)</th>
            <?php foreach ($captions as $caption): ?>
                <th><?php echo htmlspecialchars($caption); ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo $data[0]['duration']; ?> min</td>
            <?php foreach ($data as $item): ?>
                <td>$<?php echo $item['retail_price']; ?></td>
            <?php endforeach; ?>
        </tr>
    </tbody>
</table>

</body>
</html>
