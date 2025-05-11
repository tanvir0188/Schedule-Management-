function fetchUsers() {
  $.ajax({
    url: "http://localhost/Schedule-Management-/backend/get_users.php",
    method: "GET",
    success: function (response) {
      if (response.status === "success") {
        const users = response.users;
        console.log(users);
        const tbody = $("#userTableBody");

        
        // Render sorted rows
        users.forEach((user, index) => {
          

          const row = `
            <tr data-id="${user.id}">
              <td>${user.username}</td>
              <td>********</td>              
              <td>
                <button class="btn btn-sm btn-primary" onclick="editRequest(this, ${user.id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteRequest(${user.id})">Delete</button>
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

  const username = cells[0].innerText;

  // Save original data for cancel
  const originalData = {
    username: username
  };

  // Replace username and password with inputs
  cells[0].innerHTML = `<input type="text" class="form-control" value="${username}" data-field="username">`;
  cells[1].innerHTML = `<input type="password" class="form-control" placeholder="New password (leave blank to keep existing)" data-field="password">`;

  // Replace action buttons
  cells[2].innerHTML = `
    <button class="btn btn-sm btn-success me-2" onclick="saveRequest(this, ${id})">Save</button>
    <button class="btn btn-sm btn-secondary" onclick='cancelEdit(this, ${id}, "${encodeURIComponent(JSON.stringify(originalData))}")'>Cancel</button>
  `;
}

function saveRequest(button, id) {
  const row = button.closest('tr');
  const username = row.querySelector('input[data-field="username"]').value.trim();
  const password = row.querySelector('input[data-field="password"]').value.trim();

  if (!username) {
    alert("Username is required.");
    return;
  }

  $.ajax({
    url: "http://localhost/Schedule-Management-/backend/edit_users.php",
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify({ id, username, password }),
    success: function (response) {
      if (response.status === "success") {
        alert("User updated successfully.");
        $("#userTableBody").empty();
        fetchUsers();
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function () {
      alert("Failed to update user.");
    }
  });
}

function cancelEdit(button, id, encodedData) {
  const row = button.closest('tr');
  const cells = row.querySelectorAll('td');
  const data = JSON.parse(decodeURIComponent(encodedData));

  cells[0].innerText = data.username;
  cells[1].innerText = "********"; // Hide hashed password or show placeholder

  cells[2].innerHTML = `
    <button class="btn btn-sm btn-primary" onclick="editRequest(this, ${id})">Edit</button>
    <button class="btn btn-sm btn-danger" onclick="deleteRequest(${id})">Delete</button>
  `;
}



$(document).ready(function () {
  fetchUsers(); // Populate the table on load
});
