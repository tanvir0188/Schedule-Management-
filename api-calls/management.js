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


function editRequest(button, id) {
  const row = button.closest('tr');
  const cells = row.querySelectorAll('td');
  const originalData = Array.from(cells).slice(1, 9).map(cell => cell.innerText);

  // Replace cells with input fields
  const fields = ['service_person', 'ticket_status'];
  for (let i = 0; i < fields.length; i++) {
    const value = originalData[i + 5];  // Service person is 6th field and ticket_status is 7th
    if (fields[i] === "ticket_status") {
      // Use <select> for ticket status
      cells[i + 4].innerHTML = `
        <select class="form-select" data-field="${fields[i]}">
          <option value="pending" ${value === "pending" ? "selected" : ""}>Pending</option>
          <option value="solved" ${value === "solved" ? "selected" : ""}>Solved</option>
          <option value="on process" ${value === "on process" ? "selected" : ""}>On Process</option>
        </select>
      `;
    } else {
      // Use <input> for service person
      cells[i + 6].innerHTML = `<input type="text" class="form-control" value="${value}" data-field="${fields[i]}">`;
    }
  }

  // Replace Edit with Save & Cancel
  cells[9].innerHTML = `
    <button class="btn btn-sm btn-success me-2" onclick="saveRequest(this, ${id})">Save</button>
    <button class="btn btn-sm btn-secondary" onclick="cancelEdit(this, ${id}, '${encodeURIComponent(JSON.stringify(originalData))}')">Cancel</button>
  `;
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
  `;
}




function saveRequest(button, id) {
  const row = button.closest('tr');
  const cells = row.querySelectorAll('td');
  const updatedData = { id: id };

  // Collect data from input fields (service person and ticket status)
  ['ticket_status','service_person'].forEach((field, i) => {
    const input = cells[i + 5].querySelector('input, select');
    if (input) {
      updatedData[field] = input.value;
    }
  });

  // Send the updated data via AJAX using PATCH method
  $.ajax({
    url: `http://localhost/Schedule-Management-/backend/management_edit.php?id=${id}`, // Correct URL
    method: "PATCH",
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



$(document).ready(function () {
  fetchServiceRequests(); // Populate the table on load
});

