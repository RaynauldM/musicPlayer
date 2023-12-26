<?php
    include 'connection.php';
    include 'displaySongs.php';
    

function displayArtists($conn)
{
    $result = $conn->query("SELECT * FROM artists");
    while ($row = $result->fetch_assoc()) {
        echo "<button class='artist' data-artist-id='" . $row['id'] . "'>" . $row['name'] . "</button>";
    }
}
function displayRecords($conn, $artistId)
{
    $result = $conn->query("SELECT * FROM records WHERE artist_id = '$artistId'");
    while ($row = $result->fetch_assoc()) {
        echo "<button class='record' data-record-id='" . $row['id'] . "'>" . $row['name'] . "</button>";
    }
}

