<?php
    function displaySongs($conn, $artistId = null, $recordId = null)
    {
        // Prioritize record ID if both artist and record IDs are provided
        $condition = ($recordId !== null) ? "WHERE record_id = '$recordId'" : "";
        $condition = ($artistId !== null && $recordId === null) ? "WHERE artist_id = '$artistId'" : $condition;
    
        $result = $conn->query("SELECT * FROM songs $condition");
        while ($row = $result->fetch_assoc()) {
            echo "<button class='song' data-song-id='" . $row['id'] . "'>" . $row['name'] . "</button>";
        }
    }