document.getElementById("registerform").addEventListener("submit", function(e) {
    let password = document.getElementById("Citizen_Password").value;
    let confirmPassword = document.getElementById("Confirm_Password").value;

    if (password !== confirmPassword) {
        e.preventDefault();
        alert("Passwords do not match!");
    }
});