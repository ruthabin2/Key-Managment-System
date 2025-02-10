<?php
session_start();

// Check if user is logged in and has the required role
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="stylesheet" href="register.css">
<link rel="stylesheet" href="css/my.css">
<title>ATM Key Registration</title>
</head>
<body>
<?php include('sidebar.php'); ?>
<?php include('navbar.php'); ?>
<?php include('DbConn.php'); ?>

<div class="table-title" style="background-color: white;color: black;">
  <div class="row">
    <div class="col-sm-6 p-0 d-flex justify-content-lg-start justify-content-center">
    </div>
  </div>

  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>ATM Name</th>
        <th>IP Address</th>
        <th>Host Name</th>
        <th>Key 1</th>
        <th>Key 2</th>
        <th>Key 3</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
        require_once 'config.php';
        $conn = mysqli_connect("localhost", "root", "", "atm");
        $query = "SELECT * FROM atm_key";
        $result = mysqli_query($conn, $query) or die('error');

        while ($row = mysqli_fetch_assoc($result)) {
          openssl_private_decrypt($row['ipaddress'], $decrypted2, $private_key);
          openssl_private_decrypt($row['hostname'], $decrypted3, $private_key);
          openssl_private_decrypt($row['key1'], $decrypted4, $private_key);
          openssl_private_decrypt($row['key2'], $decrypted5, $private_key);
          openssl_private_decrypt($row['key3'], $decrypted6, $private_key);
          
          echo '<tr>';
          echo '<td>' . $row['atmname'] . '</td>';
          echo '<td>' . $decrypted2 . '</td>';
          echo '<td>' . $decrypted3 . '</td>';
          echo '<td>' . $decrypted4 . '</td>';
          echo '<td>' . $decrypted5 . '</td>';
          echo '<td>' . $decrypted6 . '</td>';
          echo '<td>';
          echo '<a href="#" class="edit-link" data-atmname="' . $row['atmname'] . '"> <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>';
          echo '<a href="#" class="delete" data-atmname="' . $row['atmname'] . '"> <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>';
          echo '</td>';
          echo '</tr>';
        }
      ?>
    </tbody>
  </table>

  <div id="edit-card" class="edit-card" style="display:none;">
    <span class="close">×</span>
    <h2>ATM Information</h2>
    <form id="edit-form" method="post">
      <input type="hidden" id="atm-name-original" name="atm-name-original">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <label for="atm-name">ATM Name:</label>
      <input type="text" id="atm-name" name="atm-name" required><br>
      <label for="ipaddress">IP Address:</label>
      <input type="text" id="ipaddress" name="ipaddress" required><br>
      <label for="hostname">Host Name:</label>
      <input type="text" id="hostname" name="hostname" required><br>
      <label for="key1">Key 1:</label>
      <input type="text" id="key1" name="key1" required><br>
      <label for="key2">Key 2:</label>
      <input type="text" id="key2" name="key2" required><br>
      <label for="key3">Key 3:</label>
      <input type="text" id="key3" name="key3" required><br>
      <button type="submit">Save Changes</button>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var editLinks = document.querySelectorAll('.edit-link');
      editLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
          event.preventDefault();
          document.getElementById('edit-card').style.display = 'block';
          var row = link.closest('tr');
          var atmName = row.querySelector('td:nth-child(1)').innerText;
          var ipaddress = row.querySelector('td:nth-child(2)').innerText;
          var hostname = row.querySelector('td:nth-child(3)').innerText;
          var key1 = row.querySelector('td:nth-child(4)').innerText;
          var key2 = row.querySelector('td:nth-child(5)').innerText;
          var key3 = row.querySelector('td:nth-child(6)').innerText;

          document.getElementById('atm-name-original').value = atmName;
          document.getElementById('atm-name').value = atmName;
          document.getElementById('ipaddress').value = ipaddress;
          document.getElementById('hostname').value = hostname;
          document.getElementById('key1').value = key1;
          document.getElementById('key2').value = key2;
          document.getElementById('key3').value = key3;
        });
      });

      var editForm = document.getElementById('edit-form');
      var editCard = document.getElementById('edit-card');
      var originalFormData = new FormData(editForm);

      editForm.addEventListener('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(editForm);
        var formDataChanged = false;
        for (var pair of formData.entries()) {
          var fieldName = pair[0];
          var fieldValue = pair[1];
          var originalValue = originalFormData.get(fieldName);
          if (fieldValue !== originalValue) {
            formDataChanged = true;
            break;
          }
        }

        if (!formDataChanged) {
          alert('No changes made.');
          return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'save_changes.php');
        xhr.onload = function() {
          if (xhr.status === 200) {
            alert(xhr.responseText);
            var successMessage = document.createElement('div');
            successMessage.classList.add('alert', 'alert-success');
            successMessage.textContent = 'Changes saved successfully!';
            editCard.insertBefore(successMessage, editCard.firstChild);
            setTimeout(function() {
              editCard.removeChild(successMessage);
            }, 3000);
          } else {
            
            console.error('Error:', xhr.statusText);
          }
        };
        xhr.onerror = function() {
          console.error('Network Error');
        };
        xhr.send(formData);
        
      });
      document.querySelector('.close').addEventListener('click', function() {
        editCard.style.display = 'none';
      });
      
      var deleteLinks = document.querySelectorAll('.delete');
      deleteLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
          event.preventDefault();

          var row = link.closest('tr');
          var atmName = row.querySelector('td:nth-child(1)').innerText;

          var confirmDelete = confirm('Are you sure you want to delete this row?');
          if (confirmDelete) {
          
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_ajax.php');
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
              if (xhr.status === 200) {
                console.log(xhr.responseText);
                if (xhr.responseText.includes('Record deleted successfully')) {
                  var rowToDelete = link.closest('tr');
                  rowToDelete.parentNode.removeChild(rowToDelete);
                } else {
                  alert(xhr.responseText);
                }
              } else {
                console.error('Error:', xhr.statusText);
              }
            };
            xhr.onerror = function() {
              console.error('Network Error');
            };
            xhr.send('atmname=' + encodeURIComponent(atmName));
          }
        });
      });
    });
  </script>
</body>
</html>
