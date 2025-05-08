function submitRequest() {
  const data = {
    company_name: $("#companyName").val(),
    start_date: $("#startDate").val(),
    end_date: $("#endDate").val(),
    stage: $("#stage").val(),
    note: $("#notes").val(),
    ticket_status: $("#ticketStatus").val(),
    service_person: $("#servicePerson").val(),
    current_duration: $("#currentDuration").val()
  };

  $.ajax({
    url: "http://localhost/Schedule-Management-/backend/create_service_request.php", // Replace with your actual API path
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify(data),
    xhrFields: {
      withCredentials: true
    },
    success: function(response) {
      if (response.status === "success") {
        alert("Service request created successfully.");
        $("form")[0].reset(); // Reset form
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function() {
      alert("Failed to submit request. Please try again.");
    }
  });
}

// Fetching service requests to populate the table
function fetchServiceRequests() {
  $.ajax({
    url: "http://localhost/Schedule-Management-/backend/get_service_requests.php", // Replace with your actual endpoint
    method: "GET",
    xhrFields: {
      withCredentials: true
    },
    success: function (response) {
      if (response.status === "success") {
        const tbody = $("#clientOverviewTableBody");
        tbody.empty(); // Clear previous data

        response.requests.forEach((req, index) => {
          const row = `
            <tr data-id="${req.id}">
              <td>${index + 1}</td>
              <td>${req.company_name}</td>
              <td>${req.start_date}</td>
              <td>${req.stage}</td>
              <td>${req.note || ""}</td>
              <td>${req.ticket_status || ""}</td>
              <td>${req.service_person || ""}</td>
              <td>${req.current_duration || ""}</td>
              <td>${req.end_date}</td>
              <td>
                <button class="btn btn-sm btn-primary" onclick="editRequest(this, ${req.id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteRequest(${req.id})">Delete</button>
              </td>
            </tr>
          `;
          tbody.append(row);
        });
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function () {
      alert("Failed to fetch service requests.");
    }
  });
}

// Edit request: Transform row into input fields
function editRequest(button, id) {
  const row = button.closest('tr');
  const cells = row.querySelectorAll('td');
  const originalData = Array.from(cells).slice(1, 9).map(cell => cell.innerText);

  // Replace cells with input fields
  const fields = ['company_name', 'start_date', 'stage', 'note', 'ticket_status', 'service_person', 'current_duration', 'end_date'];
  for (let i = 0; i < fields.length; i++) {
    const value = originalData[i];
    if (fields[i] === "start_date" || fields[i] === "end_date") {
      // Use <input type="date"> for date fields
      cells[i + 1].innerHTML = `<input type="date" class="form-control" value="${value}" data-field="${fields[i]}">`;
    } else if (fields[i] === "current_duration") {
      // Use <input type="number"> for duration
      cells[i + 1].innerHTML = `<input type="number" class="form-control" value="${value}" data-field="${fields[i]}">`;
    } else {
      // Default to text input for other fields
      cells[i + 1].innerHTML = `<input type="text" class="form-control" value="${value}" data-field="${fields[i]}">`;
    }
  }

  // Replace Edit with Save & Cancel
  cells[9].innerHTML = `
    <button class="btn btn-sm btn-success me-2" onclick="saveRequest(this, ${id})">Save</button>
    <button class="btn btn-sm btn-secondary" onclick="cancelEdit(this, ${id}, ${JSON.stringify(originalData).replace(/"/g, '&quot;')})">Cancel</button>
  `;
}

// Save request: Send updated data to the backend
function saveRequest(button, id) {
  const row = button.closest('tr');
  const cells = row.querySelectorAll('td');
  const updatedData = {};

  // Collect data from input fields
  cells.forEach((cell, index) => {
    if (index >= 1 && index <= 8) {
      const input = cell.querySelector('input, select, textarea');
      if (input) {
        updatedData[input.getAttribute('data-field')] = input.value;
      }
    }
  });

  // Send the updated data via AJAX
  $.ajax({
    url: `http://localhost/Schedule-Management-/backend/edit_service_request.php?id=${id}`,
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify(updatedData),
    xhrFields: {
      withCredentials: true
    },
    success: function (response) {
      if (response.status === "success") {
        alert('Service request updated successfully!');
        fetchServiceRequests(); // Reload the requests
      } else {
        alert('Error: ' + response.message);
      }
    },
    error: function () {
      alert('Failed to update service request.');
    }
  });
}

// Cancel edit: Revert back to original data
function cancelEdit(button, id, originalData) {
  const row = button.closest('tr');
  const cells = row.querySelectorAll('td');

  // Revert to original values
  originalData = JSON.parse(originalData);
  cells.forEach((cell, index) => {
    if (index >= 1 && index <= 8) {
      cell.innerHTML = originalData[index - 1];
    }
  });

  // Replace Save & Cancel with Edit button
  cells[9].innerHTML = `
    <button class="btn btn-sm btn-primary" onclick="editRequest(this, ${id})">Edit</button>
    <button class="btn btn-sm btn-danger" onclick="deleteRequest(${id})">Delete</button>
  `;
}

// Delete request (optional)
function deleteRequest(id) {
  if (confirm("Are you sure you want to delete this service request?")) {
    $.ajax({
      url: `http://localhost/Schedule-Management-/backend/delete.php?id=${id}`,
      method: "DELETE",

      success: function (response) {
        if (response.status === "success") {
          alert('Service request deleted successfully!');
          fetchServiceRequests(); // Reload the requests
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('Failed to delete service request.');
      }
    });
  }
}

// Initialize on page load
$(document).ready(function () {
  fetchServiceRequests(); // Populate the table on load
});


// Call this function on page load
$(document).ready(function () {
  fetchServiceRequests();
});
