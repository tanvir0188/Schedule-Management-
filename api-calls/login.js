function login() {
  const username = $("#username").val().trim();
  const password = $("#password").val().trim();

  if (!username || !password) {
    $("#errorMessage").text("Please enter both username and password.");
    return;
  }

  $.ajax({
    url: "http://localhost/Schedule-Management-/backend/login.php", // Replace with your real path
    method: "POST",
    data: JSON.stringify({ username, password }),
    contentType: "application/json",
    xhrFields: {
      withCredentials: true
    },
    success: function(response) {
      if (response.status === "success") {
        $("#errorMessage").text("");
        $("#managementLoginSection").addClass("hidden");
        $("#managementSection").removeClass("hidden");
      } else {
        $("#errorMessage").text(response.message || "Login failed.");
      }
    },
    error: function(xhr, status, error) {
      console.error("Login Error:", error);
      $("#errorMessage").text("An error occurred. Please try again.");
    }
  });
}
function checkLoginStatus() {
  $.ajax({
    url: "http://localhost/Schedule-Management-/backend/check_session.php", // Adjust this path
    method: "GET",
    xhrFields: {
      withCredentials: true
    },
    success: function(response) {
      if (response.status === "success" && response.user.username === "admin") {
        $("#managementLoginSection").addClass("hidden");
        $("#managementSection").removeClass("hidden");
      } else {
        $("#managementLoginSection").removeClass("hidden");
        $("#managementSection").addClass("hidden");
      }
    },
    error: function() {
      $("#managementLoginSection").removeClass("hidden");
      $("#managementSection").addClass("hidden");
    }
  });
}

// Call on page load
$(document).ready(function() {
  checkLoginStatus();
});