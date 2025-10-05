function updateClock() {
    let now = new Date();
    let day = now.getDate();
    let year = now.getFullYear();

    let month = now.toLocaleString('en-US', { month: 'short' }); // Use short month name
let weekday = now.toLocaleString('en-US', { weekday: 'short' }); // Use short weekday name

    let hours = now.getHours();
    let minutes = now.getMinutes();
    let seconds = now.getSeconds();
    let ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12;
    hours = hours ? hours : 12;
    hours = String(hours).padStart(2, '0');
    minutes = String(minutes).padStart(2, '0');
    seconds = String(seconds).padStart(2, '0');
    let formatted = `${day} ${month.toUpperCase()} ${year} | ${weekday.toUpperCase()} | ${hours}:${minutes}:${seconds} ${ampm}`;
    document.getElementById("digitalclock").innerText = formatted;
}
updateClock(); // Initial call
setInterval(updateClock, 1000);