function addNewManager() {
  const newUsername = $("#newUsername").val().trim();
  const newPassword = $("#newPassword").val().trim();

  if (!newUsername || !newPassword) {
    $("#addManagerMessage").css("color", "red").text("Please enter both username and password.");
    return;
  }

  $.ajax({
    url: "http://localhost/Schedule-Management-/backend/sign-up.php", // Replace with your real path
    method: "POST",
    data: JSON.stringify({ username: newUsername, password: newPassword }),
    contentType: "application/json",
    xhrFields: {
      withCredentials: true
    },
    success: function(response) {
      if (response.status === "success") {
        $("#addManagerMessage").css("color", "green").text(response.message);
        $("#newUsername").val("");
        $("#newPassword").val("");
      } else {
        $("#addManagerMessage").css("color", "red").text(response.message || "Signup failed.");
      }
    },
    error: function(xhr, status, error) {
      console.error("Signup Error:", error);
      $("#addManagerMessage").css("color", "red").text("An error occurred. Please try again.");
    }
  });
}