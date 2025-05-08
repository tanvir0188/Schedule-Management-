<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Schedule Management - Management</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .login-form,
        #managementSection,
        #addManagerSection {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            gap: 15px;
            align-items: center;
        }

        .login-form,
        #addManagerSection {
            display: flex;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        select,
        textarea,
        button {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        select {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .hidden {
            display: none;
        }

        #managementLoginSection {
            text-align: center;
            margin-bottom: 20px;
        }

        #managementLoginSection button {
            padding: 10px 15px;
            font-size: 16px;
        }

        .status-pending {
            background-color: #ffdddd;
            /* Light red */
            color: #ff0000;
            /* Red text for better visibility */
            padding: 5px;
            border-radius: 4px;
            text-align: center;
        }

        .status-on-process {
            background-color: #ffffdd;
            /* Light yellow */
            color: #ffaa00;
            /* Orange/Yellow text */
            padding: 5px;
            border-radius: 4px;
            text-align: center;
        }

        .status-solved {
            background-color: #ddffdd;
            /* Light green */
            color: #00aa00;
            /* Green text */
            padding: 5px;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="managementLoginSection">
        <h2>Management Login</h2>
        <div id="loginContainer">
            <div class="login-form">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password">
                </div>
                <button onclick="login()">Login</button>
            </div>
            <p id="errorMessage" style="color: red;"></p>
        </div>
    </div>

    <div id="managementSection" class="hidden">
        <h2>Management Dashboard</h2>
        <button onclick="logout()">Logout</button>
        <a href="client.php">Make request</a>
        <div id="addManagerSection" class="addManagerSection">
            <h3>Add New Manager</h3>
            <div class="form-group">
                <label for="newUsername">New Username:</label>
                <input type="text" id="newUsername">
            </div>
            <div class="form-group">
                <label for="newPassword">New Password:</label>
                <input type="password" id="newPassword">
            </div>
            <button onclick="addNewManager()">Add Manager</button>
            <p id="addManagerMessage"></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="api-calls/login.js"></script>
    <script src="api-calls/signup.js"></script>
    <script src="api-calls/logout.js"></script>


</body>

</html>