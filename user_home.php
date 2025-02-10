

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>ATM Key</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/my.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include('DbConn.php'); ?>
<?php include('sidebarU.php'); ?>
<?php include('navbar.php'); ?>

<div class="content">
    <form method="POST" class="sear">
        <input type="text" id="searchTerm" placeholder="Search By ATM Name">
        <button type="button" onclick="searchATM()"><i class="fa fa-search"></i></button>
        <br>
    </form>

    <div class="atm-names">
        <?php
        require_once 'config.php';
        $conn = mysqli_connect("localhost", "root", "", "atm");

        // Function to retrieve all ATM names
        function getAllATMNames($conn)
        {
            $query = "SELECT DISTINCT atmname FROM atm_key";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
              //  echo "<a href='#' class='atm-link' data-atm-name='" . $row['atmname'] . "'>" . $row['atmname'] . "</a><br>";
                
      echo "<a href='#' class='atm-link' data-atm-name='" . $row['atmname'] . "'>";
      echo "<div class='card'>";
      echo "<div class='data-container'>";
      echo $row['atmname'] . "<br>";
      echo "</div>";
      echo "</div>";
      echo "</a>";
            }
        }

        getAllATMNames($conn);
        ?>
    </div>

    <!-- Display card for ATM information -->
    <div id="atm-info" class="card" style="display:none;"></div>
</div>

<div id="edit-card" class="edit-card">
    <span class="close">×</span>
    <h2>ATM Information</h2>
    <form id="edit-form" class="edit-form">
        <!-- Input fields for editing -->
        <label for="atmname">ATM Name:</label>
        <input type="text" id="atmname" name="atmname" readonly><br>
        <label for="ipaddress">IP Address:</label>
        <input type="text" id="ipaddress" name="ipaddress" readonly><br>
        <label for="hostname">Host Name:</label>
        <input type="text" id="hostname" name="hostname" readonly><br>
        <label for="key1">Key 1:</label>
        <input type="text" id="key1" name="key1" readonly><br>
        <label for="key2">Key 2:</label>
        <input type="text" id="key2" name="key2" readonly><br>
        <label for="key3">Key 3:</label>
        <input type="text" id="key3" name="key3" readonly><br>
    </form>
</div>

<script>
    function searchATM() {
        var searchTerm = document.getElementById("searchTerm").value.toLowerCase();
        var atmNames = document.querySelectorAll('.atm-link');
        atmNames.forEach(function (atm) {
            var atmName = atm.textContent.toLowerCase();
            if (atmName.includes(searchTerm)) {
                atm.style.display = 'block';
            } else {
                atm.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Get edit links
        var editLinks = document.querySelectorAll('.atm-link');

        // Add click event listener to each edit link
        editLinks.forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                // Show edit card/modal
                document.getElementById('edit-card').style.display = 'block';

                const atm = this.textContent;

                // Make an AJAX request to fetch ATM details
                fetch('get_atm_info.php?atm=' + encodeURIComponent(atm))
                    .then(response => response.json())
                    .then(data => {
                        // Populate form fields with retrieved data
                        document.getElementById('atmname').value = data.atmname;
                        document.getElementById('ipaddress').value = data.ipaddress;
                        document.getElementById('hostname').value = data.hostname;
                        document.getElementById('key1').value = data.key1;
                        document.getElementById('key2').value = data.key2;
                        document.getElementById('key3').value = data.key3;
                    })
                    .catch(error => console.error('Error fetching ATM details:', error));
            });
        });
    });

    // Get the close button element
    const closeButton = document.querySelector('.close');

    // Add a click event listener to the close button
    closeButton.addEventListener('click', function () {
        // Hide or remove the card (you can choose either approach)
        // Example: Hide the card by setting its display to 'none'
        const card = this.closest('.edit-card');
        card.style.display = 'none';
    });
</script>

<?php include('footerr.php'); ?>
</body>
</html>
