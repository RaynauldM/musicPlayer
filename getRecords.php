
<?php
include 'logic.php';

$artistId = isset($_GET['artistId']) ? $_GET['artistId'] : null;
$recordId = isset($_GET['recordId']) ? $_GET['recordId'] : null;

$conn = connectToDatabase('localhost', 'root', 'musiclib');
displayRecords($conn, $artistId);
$conn->close();
?>
