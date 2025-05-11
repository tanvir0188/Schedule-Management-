function submitRequest() {
  const today = new Date().toISOString().split('T')[0];

  const data = {
    company_name: $("#companyName").val(),
    start_date: today,
    end_date: "", // Optional - blank at creation
    stage: $("#stage").val(),
    note: $("#notes").val(),
    ticket_status: "pending",
    service_person: "not assigned",
  };

  $.ajax({
    url: "http://localhost/Schedule-Management-/backend/create_service_request.php",
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify(data),
    success: function (response) {
      if (response.status === "success") {
        alert("Service request created successfully.");
        $("form")[0].reset();
        fetchServiceRequests(); // Optionally refresh table
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function () {
      alert("Failed to submit request. Please try again.");
    }
  });
}
function getStatusBadge(status) {
  const lower = (status || "").toLowerCase();

  switch (lower) {
    case "pending":
      return '<span class="badge bg-warning text-dark">Pending</span>';
    case "solved":
      return '<span class="badge bg-success">Solved</span>';
    case "on process":
      return '<span class="badge bg-info text-dark">On Process</span>';
    default:
      return `<span class="badge bg-secondary">${status}</span>`;
  }
}



// Fetching service requests to populate the table
function fetchServiceRequests() {
  $.ajax({
    url: "http://localhost/Schedule-Management-/backend/get_service_requests.php",
    method: "GET",
    success: function (response) {
      if (response.status === "success") {
        const tbody = $("#clientOverviewTableBody");
        tbody.empty();

        const today = new Date();

        const statusPriority = {
          "pending": 1,
          "on process": 2,
          "solved": 3
        };

        // Calculate durations and prepare sortable data
        const requestsWithDuration = response.requests.map(req => {
          const start = new Date(req.start_date);
          const end = req.end_date ? new Date(req.end_date) : today;
          const diffTime = Math.abs(end - start);
          const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

          return {
            ...req,
            current_duration_days: diffDays,
            status_rank: statusPriority[(req.ticket_status || "").toLowerCase()] || 99 // default rank if unrecognized
          };
        });

        // Sort by status rank and duration
        requestsWithDuration.sort((a, b) => {
          if (a.status_rank !== b.status_rank) {
            return a.status_rank - b.status_rank; // lower rank (pending) first
          }
          return b.current_duration_days - a.current_duration_days; // higher duration first
        });

        // Render sorted rows
        requestsWithDuration.forEach((req, index) => {
          const current_duration = `${req.current_duration_days} day(s)`;

          const row = `
            <tr data-id="${req.id}">
              <td>${index + 1}</td>
              <td>${req.company_name}</td>
              <td>${req.start_date}</td>
              <td>${req.stage}</td>
              <td>${req.note || ""}</td>
              <td>${getStatusBadge(req.ticket_status)}</td>
              <td>${req.service_person || ""}</td>
              <td>${current_duration}</td>
              <td>${req.end_date || ""}</td>
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

  // Editable fields: company_name, stage, note
  const editableFields = ['company_name', 'stage', 'note'];

  // Fields corresponding to each <td> in order
  const allFields = ['company_name', 'start_date', 'stage', 'note', 'ticket_status', 'service_person', 'current_duration', 'end_date'];

  allFields.forEach((field, i) => {
    if (editableFields.includes(field)) {
      if (field === 'stage') {
        // Create a select dropdown for 'stage'
        const stageOptions = [
          'Inspect on',
          'Service',
          'Parts Delivery & Servicing',
          'Parts Delivery',
          'Bill Submit & Forklift Collection'
        ];
        const stageSelect = stageOptions.map(option => 
          `<option value="${option}" ${option === originalData[i] ? 'selected' : ''}>${option}</option>`
        ).join('');
        cells[i + 1].innerHTML = `<select class="form-select" data-field="${field}">${stageSelect}</select>`;
      } else {
        // Use text input for other editable fields
        cells[i + 1].innerHTML = `<input type="text" class="form-control" value="${originalData[i]}" data-field="${field}">`;
      }
    }
  });

  // Replace action buttons
  cells[9].innerHTML = `
    <button class="btn btn-sm btn-success me-2" onclick="saveRequest(this, ${id})">Save</button>
    <button class="btn btn-sm btn-secondary" onclick="cancelEdit(this, ${id}, '${encodeURIComponent(JSON.stringify(originalData))}')">Cancel</button>
  `;
}


function saveRequest(button) {
  const row = button.closest('tr');
  const cells = row.querySelectorAll('td');
  const updatedData = {};

  // Get the ID from the row (if available) or from the URL
  const id = row.getAttribute('data-id') || new URLSearchParams(window.location.search).get('id');

  // Only extract allowed fields
  ['company_name', 'stage', 'note'].forEach((field, i) => {
    const cellIndex = ['company_name', 'start_date', 'stage', 'note', 'ticket_status', 'service_person', 'current_duration', 'end_date'].indexOf(field);
    const input = cells[cellIndex + 1].querySelector('input, select'); // Allow for <select> elements like stage
    if (input) {
      updatedData[field] = input.value;
    }
  });

  // Add the ID to the data being sent
  updatedData.id = id;

  // Send the updated data via AJAX using PATCH method
  $.ajax({
    url: `http://localhost/Schedule-Management-/backend/edit_service_request.php?id=${id}`,
    method: "PATCH", // Correct HTTP method for partial updates
    contentType: "application/json",
    data: JSON.stringify(updatedData),
    xhrFields: {
      withCredentials: true
    },
    success: function (response) {
      if (response.status === "success") {
        alert('Service request updated successfully!');
        fetchServiceRequests(); // Reload the table
      } else {
        alert('Error: ' + response.message);
      }
    },
    error: function () {
      alert('Failed to update service request.');
    }
  });
}



function cancelEdit(button, id, encodedData) {
  const row = button.closest('tr');
  const cells = row.querySelectorAll('td');
  const data = JSON.parse(decodeURIComponent(encodedData));

  const fields = ['company_name', 'start_date', 'stage', 'note', 'ticket_status', 'service_person', 'current_duration', 'end_date'];
  for (let i = 0; i < fields.length; i++) {
    // For status field, insert badge instead of plain text
    if (fields[i] === 'ticket_status') {
      cells[i + 1].innerHTML = getStatusBadge(data[i]);
    } else {
      cells[i + 1].innerText = data[i];
    }
  }

  // Restore Edit and Delete buttons
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
