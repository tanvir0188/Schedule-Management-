<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Schedule Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">



</head>

<body>
    <div class="container mt-4" id="clientSection">
        <h2 class="mb-4">Submit New Service Request</h2>
        <a href="index.html">Home</a>
        <form onsubmit="event.preventDefault(); submitRequest();">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="companyName" class="form-label">Company Name:</label>
                    <input type="text" class="form-control" id="companyName" name="companyName" required>
                </div>

                <div class="col-md-6">
                    <label for="startDate" class="form-label">Start Date:</label>
                    <input type="date" class="form-control" id="startDate" name="startDate" required>
                </div>

                <div class="col-md-6">
                    <label for="endDate" class="form-label">End Date:</label>
                    <input type="date" class="form-control" id="endDate" name="endDate" required>
                </div>

                <div class="col-md-6">
                    <label for="stage" class="form-label">Stage:</label>
                    <select class="form-select" id="stage" name="stage" required>
                        <option value="">Select Stage</option>
                        <option value="Inspect on">Inspect on</option>
                        <option value="Service">Service</option>
                        <option value="Parts Delivery & Servicing">Parts Delivery & Servicing</option>
                        <option value="Parts Delivery">Parts Delivery</option>
                        <option value="Bill Submit & Forklift Collection">Bill Submit & Forklift Collection</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="ticketStatus" class="form-label">Ticket Status:</label>
                    <input type="text" class="form-control" id="ticketStatus" name="ticketStatus">
                </div>

                <div class="col-md-6">
                    <label for="servicePerson" class="form-label">Service Person:</label>
                    <input type="text" class="form-control" id="servicePerson" name="servicePerson">
                </div>

                <div class="col-md-6">
                    <label for="currentDuration" class="form-label">Current Duration (in days):</label>
                    <input type="number" class="form-control" id="currentDuration" name="currentDuration">
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">Problem Notes:</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </div>
        </form>
    </div>

    <div class="container mt-5">
        <h2>Service Requests Overview</h2>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Sl No</th>
                        <th>Company Name</th>
                        <th>Start Date</th>
                        <th>Stage</th>
                        <th>Tx Notes</th>
                        <th>Ticket Status</th>
                        <th>Service Person</th>
                        <th>Current Duration</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="clientOverviewTableBody">
                    <!-- Dynamically filled by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="api-calls/login.js"></script>
    <script src="api-calls/serviceRequest.js"></script>
    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>