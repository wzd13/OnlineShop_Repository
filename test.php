<?php
// Send the 401 Unauthorized HTTP header
http_response_code(401);
header('WWW-Authenticate: Basic realm="Restricted Area"');

// Optional: You can stop the script here if you want
// exit;

// Below is the custom HTML for the 401 page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 Unauthorized</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background: white;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 72px;
            margin: 0;
            color: #e74c3c;
        }
        p {
            font-size: 24px;
            margin: 10px 0 0 0;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: white;
            background: #3498db;
            padding: 10px 20px;
            border-radius: 5px;
        }
        a:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>401</h1>
        <p>Unauthorized Access</p>
        <a href="index.php">Go Back</a>
    </div>
</body>
</html>
