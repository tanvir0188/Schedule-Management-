<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">



</head>

<body>
  <div class="container mt-4" id="clientSection">
    <h2 class="mb-4">User List</h2>
    <a href="index.html">Home</a>
    <a href="management.php">Management</a>
  </div>

  <div class="container mt-5">
    <h2>Service Requests Overview</h2>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>User name</th>
            <th>Passowrd</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="userTableBody">
          <!-- Dynamically filled by JS -->
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="api-calls/users.js"></script>
  <!-- Bootstrap JS Bundle (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>