<?php
require 'vendor/autoload.php'; $vehicleDetails = [];

$mongoDBUri = "mongodb://localhost:27017";
$client = new MongoDB\Client($mongoDBUri);
$database = $client->selectDatabase('registerations');
$collection = $database->selectCollection('vehicledetails');

$vehicles = $collection->find()->toArray();
$currentDate = new DateTime(); $currentDate->setTime(0, 0); foreach ($vehicles as &$vehicle) {
        if (isset($vehicle->offence->due_date) || isset($vehicle->offence->due_date_to_pay_fine)) {
                $dueDateStr = isset($vehicle->offence->due_date) ? $vehicle->offence->due_date : $vehicle->offence->due_date_to_pay_fine;

                $dueDate = new DateTime($dueDateStr);
        
                $interval = $currentDate->diff($dueDate);
        $daysOverdue = $interval->days;         
                $finePerDay = 10;
        
                $vehicle->offence->total_fine += ($daysOverdue > 0) ? ($daysOverdue * $finePerDay) : 0;
    } else {
                $vehicle->offence->total_fine += 0;     }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Animation</title>
    <style>
        body {
    background-color: #80c2dc; /* Sky blue background */
    margin: 0; /* Remove default margin */
    overflow: hidden; /* Hide scrollbar */
    height: 100vh; /* Full height of the viewport */
    display: flex; /* Use flexbox to align items */
    flex-direction: column; /* Arrange items in a column */
    justify-content: flex-end; /* Align items to the bottom */
    align-items: center; /* Center items horizontally */
    position: relative; /* Set relative positioning for absolute children */
}

.line {
    width: 7px; /* Width of the vertical line */
    background-color: black; /* Color of the vertical line */
    position: absolute; /* Position it absolutely */
    top: calc(100vh - 630px); /* Position it above the second image container */
    left: calc(50% - 200px); /* Move the line slightly left from the center */
    height: 330px; /* Height of the vertical line (adjustable) */
    z-index: 10; /* Ensure it appears above other elements */
}

.lineh {
    width: 1930px; /* Width of the horizontal line */
    background-color: #243c61; /* Color of the horizontal line */
    position: absolute; /* Position it absolutely */
    top: calc(100vh - 300px); /* Position it at a specific height */
    height: 15px; /* Height of the horizontal line */
    z-index: 10; /* Ensure it appears above other elements */
}

.line-image {
    position: absolute; /* Position it absolutely */
    top: calc(100vh - 630px - 25px); /* Adjust position above the line */
    left: calc(50% - 212px); /* Align it with the line */
    width: 70px; /* Width of the small image */
    height: auto; /* Maintain aspect ratio */
    z-index: 20; /* Ensure it appears above the line */
}

.image-container {
    width: 100%; /* Full width of the viewport */
    height: 300px; /* Set the height of the image container */
    background-image: url('b.jpg'); /* Set your image here */
    background-repeat: repeat-x; /* Repeat the image horizontally */
    background-size: 550px auto; /* Set the size of the image */
    position: relative; /* Set position for child elements */
    margin-top: 30px; /* Space above the first image container */
    z-index: 1; /* Set z-index lower than the line */
}

h1 {
    color: #122054; 
    font-family: verdana; /* Set the heading color */
    margin: 20px 0; /* Space above and below the heading */
    text-align: center;
    margin-left: -1000px; /* Center the heading text */
    z-index: 40; /* Ensure it appears above other elements */
}

.image-container2 {
    width: 100%; /* Full width of the viewport */
    height: 300px; /* Set the height of the image container */
    background-image: url('downblue.png'); /* Set your image here */
    background-repeat: repeat-x; /* Repeat the image horizontally */
    background-size: 550px auto; /* Set the size of the image */
    position: relative; /* Set position for child elements */
    margin-top: 0; /* No space above the second image container */
    z-index: 1; /* Set z-index lower than the line */
}

/* Adjusted positioning for the vehicle images */
.vehicle {
    position: absolute; /* Position the vehicle image absolutely */
    top: calc(100vh - 370px); /* Position above the horizontal line (lineh) */
    height: 100px; /* Set height for vehicle images */
    animation: move 10s linear forwards; /* Animation for movement */
    z-index: 20; /* Ensure vehicles are above the image container */
}

@keyframes move {
    from {
        right: -150px; /* Start from left off the screen */
 /* Maintain vertical position during animation */
    }
    to {
        right: 100%; /* Move to the right off the screen */
       /* Maintain vertical position */
    }
}

#vehicleDetails {
    position: absolute; /* Positioning it absolutely */
    bottom: 80px; /* Adjust this value to move it down */
    left: calc(50% - 150px); /* Center it horizontally */
    background: linear-gradient(135deg, #2596be, #6daec8, #8fcfe9, #2c7c94); /* Gradient background */
    padding: 20px; /* Padding for the details box */
    border-radius: 8px; /* Rounded corners */
    color: black; /* Text color for better contrast */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5); /* Shadow effect */
    z-index: 30; /* Ensure it's above other elements */
    display: none; /* Initially hidden */
}

#vehicleDetails h2,
#vehicleDetails p {
    margin: 0; /* Remove default margins for better alignment */
}

#offenceDetails {
    margin-top: 10px; /* Space between details and offences button */
    display: none; /* Initially hidden */
    background: #80c2dc; /* White background for better contrast */
    padding: 5px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
}

/* New styles for the offences section */
.offences {
    display: none; /* Initially hidden */
    margin-top: 10px; /* Space above the offences section */
    color: red; /* Color for offence details */
}

#clock {
    position: absolute; /* Positioning it absolutely */
    top: 20px; /* Move it down from the top */
    left: calc(50% - 50px); /* Center it horizontally */
    font-size: 24px; /* Font size for the clock */
    color: #122054; /* Clock color */
    font-family: verdana; /* Font family for the clock */
}

button {
    margin-top: 10px; /* Space above the button */
    padding: 5px 10px; /* Padding for the button */
    border: none; /* Remove border */
    border-radius: 5px; /* Rounded corners */
    background-color: #008ACA; /* Button color */
    color: white; /* Button text color */
    cursor: pointer; /* Pointer cursor */
}

button:hover {
    background-color: #4087a5; /* Darker shade on hover */
}

#date {
    position: absolute; /* Position it absolutely */
    top: 55px; /* Adjust this value to move it down */
    left: calc(50% - 52px); /* Center it horizontally */
    font-size: 20px; /* Font size for the date */
    color: #122054; /* Date text color */
    font-family: verdana; /* Font family for the date */
    background-color: #b0daea; /* Semi-transparent white background */
    padding: 7px; /* Padding for the date box */
    border-radius: 5px; /* Rounded corners */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3); /* Shadow effect */
    z-index: 30; /* Ensure it's above other elements */
    display: block; /* Ensure it displays as a block element */
}

#msg {
    color: #122054;
}

    </style>
</head>
<body>
<h1>SMART FINE SYSTEM</h1>
<div id="date"></div>
<div id="clock"></div>
<img src="camers.png" alt="Line Tip Image" class="line-image"> 
    <div class="image-container"></div> 
    <div class="image-container2"></div> 
    <div class="line"></div> 
    <div class="lineh"></div> 

    <div id="vehicleDetails">
        <h2>Vehicle Details:</h2>
        <p id="registrationNumber"></p>
        <p id="ownerName"></p>
        <p id="totalFine"></p>
        <p id="msg"></p> 
        <button id="offencesButton" onclick="showOffenceDetails()">Offences</button> 
    <div id="offenceDetails"></div> 
    </div> 
</div>
<script>
        const vehicles = <?php echo json_encode($vehicles); ?>;

        function updateClock() {
        const now = new Date();
        
                const dateOptions = { year: 'numeric', month: '2-digit', day: '2-digit' };
        const dateString = now.toLocaleDateString('en-GB', dateOptions);                 document.getElementById('date').textContent = `Date: ${dateString}`;
        
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        document.getElementById('clock').textContent = `Time: ${hours}:${minutes}:${seconds}`;
    }

        setInterval(updateClock, 1000);
    updateClock();          function insertVehicleDetails(vehicle) {
        fetch('vd.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(vehicle)         })
        .then(response => response.json())
        .then(data => {
            console.log('Insert success:', data);
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }

    let currentIndex = 0;     function animateVehicles() {
                function animateSingleVehicle() {
            const vehicle = vehicles[currentIndex];                         const vehicleImg = document.createElement('img');
            let pathh = "photos/";
            let vehicleModel = vehicle.vehicle_model;             let vehicleImagePath = pathh + `${vehicleModel.replace(/\s+/g, '_')}.png`;

                        vehicleImg.src = vehicleImagePath;
            vehicleImg.className = 'vehicle';
            vehicleImg.style.animationDelay = '0s';            
                        document.body.appendChild(vehicleImg);

                        setTimeout(() => {
                const currentTime = document.getElementById('clock').textContent.split(' ')[1];
                                insertVehicleDetails({
                    registration_number: vehicle.registration_number,
                    owner_name: vehicle.ownerdetails.name,
                    vehicle_type: vehicle.vehicle_type,
                    phone_number: vehicle.ownerdetails.phone_number,
                    total_fine: vehicle.offence.total_fine,
                    time_passed: currentTime
                });
                setTimeout(() => {
                   if (vehicle.offence.total_fine > 0 && vehicle.offence.total_fine <3000) {
                       vehicleImg.style.border = '3px solid orange';                    } 
                   else if(vehicle.offence.total_fine >=3000){
                    vehicleImg.style.border = '3px solid red';                    }
                   else {
                       vehicleImg.style.border = '3px solid green';                    }
               }, 0000);
                document.getElementById('registrationNumber').textContent = `Registration Number: ${vehicle.registration_number}`;
                document.getElementById('ownerName').textContent = `Owner: ${vehicle.ownerdetails.name}`;
                document.getElementById('totalFine').textContent = `Total Fine: ₹${vehicle.offence.total_fine}`;
                document.getElementById('msg').textContent = `Message sent to: ${vehicle.ownerdetails.phone_number}`;
                document.getElementById('vehicleDetails').style.display = 'block';             }, 3000);

                        currentIndex = (currentIndex + 1) % vehicles.length;

                        setTimeout(animateSingleVehicle, 10000);
        }

        animateSingleVehicle();     }

        animateVehicles();

        document.getElementById('offencesButton').addEventListener('click', function() {
    const offenceDetails = document.getElementById('offenceDetails');
    const currentVehicle = vehicles[(currentIndex - 1 + vehicles.length) % vehicles.length];     const offencesList = currentVehicle.offence.offences;

        let offencesDisplay;
    if (offencesList.length > 0) {
        offencesDisplay = offencesList.map(offence => 
            `${offence.type_of_offence} - ₹${offence.fine} (Issued on: ${offence.offence_issued_date} at ${offence.time})`
        ).join('<br>');
    } else {
        offencesDisplay = "No offences";
    }

        offenceDetails.innerHTML = offencesDisplay;     offenceDetails.style.display = offenceDetails.style.display === 'none' ? 'block' : 'none'; });

</script>



</body>
</html>
