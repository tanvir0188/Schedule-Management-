function logout() {
  $.ajax({
    url: "http://localhost/Schedule-Management-/backend/logout.php", // Replace with actual logout API path
    method: "POST",
    xhrFields: {
      withCredentials: true
    },
    success: function(response) {
      if (response.status === "success") {
        alert("Logged out successfully.");
        // Hide management section and show login again
        $("#managementSection").addClass("hidden");
        $("#managementLoginSection").removeClass("hidden");
      } else {
        alert("Logout failed: " + response.message);
      }
    },
    error: function() {
      alert("Error during logout. Please try again.");
    }
  });
}
