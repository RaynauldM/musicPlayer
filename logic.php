<?php
function connectToDatabase($host, $username, $database)
{
    $conn = new mysqli($host, $username, '', $database);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function createTables($conn)
{
    // Start a transaction
    $conn->begin_transaction();

    try {
        // Check if 'artists' table exists
        $result = $conn->query("SHOW TABLES LIKE 'artists'");
        if ($result->num_rows == 0) {
            // 'artists' table does not exist, create it
            $sql = "CREATE TABLE artists (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL
            )";

            if ($conn->query($sql) === TRUE) {
                echo "Table 'artists' created successfully<br>";
            } else {
                throw new Exception("Error creating table 'artists': " . $conn->error);
            }
        } else {
            echo "Table 'artists' already exists<br>";
        }

        // Check if 'records' table exists
        $result = $conn->query("SHOW TABLES LIKE 'records'");
        if ($result->num_rows == 0) {
            // 'records' table does not exist, create it
            $sql = "CREATE TABLE records (
                id INT AUTO_INCREMENT PRIMARY KEY,
                artist_id INT,
                name VARCHAR(255) NOT NULL,
                FOREIGN KEY (artist_id) REFERENCES artists(id)
            )";

            if ($conn->query($sql) === TRUE) {
                echo "Table 'records' created successfully<br>";
            } else {
                throw new Exception("Error creating table 'records': " . $conn->error);
            }
        } else {
            echo "Table 'records' already exists<br>";
        }

        // Check if 'songs' table exists
        $result = $conn->query("SHOW TABLES LIKE 'songs'");
        if ($result->num_rows == 0) {
            // 'songs' table does not exist, create it
            $sql = "CREATE TABLE songs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                record_id INT,
                artist_id INT,
                name VARCHAR(255) NOT NULL,
                FOREIGN KEY (record_id) REFERENCES records(id),
                FOREIGN KEY (artist_id) REFERENCES artists(id)
            )";

            if ($conn->query($sql) === TRUE) {
                echo "Table 'songs' created successfully<br>";
            } else {
                throw new Exception("Error creating table 'songs': " . $conn->error);
            }
        } else {
            echo "Table 'songs' already exists<br>";
        }

        // Get all subdirectories (artists) in the 'lib' folder
        $libPath = 'lib';
        $artists = array_filter(glob($libPath . '/*', GLOB_ONLYDIR), 'is_dir');

        foreach ($artists as $artist) {
            // Your existing code for 'artists' and 'records' tables
        }

        // Commit the transaction
        $conn->commit();
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();
        echo "Transaction failed: " . $e->getMessage() . "<br>";
    }
}



function scanLibFolder($libPath, $conn)
{
    // Get all subdirectories (artists) in the 'lib' folder
    $artists = array_filter(glob($libPath . '/*', GLOB_ONLYDIR), 'is_dir');

    foreach ($artists as $artist) {
        // Insert or update artist in the 'artists' table
        $artistName = basename($artist);
        $artistExists = $conn->query("SELECT id FROM artists WHERE name='$artistName'")->num_rows > 0;

        if (!$artistExists) {
            $sql = "INSERT INTO artists (name) VALUES ('$artistName')";
            if ($conn->query($sql) === TRUE) {
                echo "Artist '$artistName' added successfully<br>";
            } else {
                echo "Error adding artist '$artistName': " . $conn->error . "<br>";
            }
        } else {
            echo "Artist '$artistName' already exists<br>";
        }

        // Get the artist_id for the current artist
        $artistIdResult = $conn->query("SELECT id FROM artists WHERE name='$artistName'");
        $artistId = ($artistIdResult->num_rows > 0) ? $artistIdResult->fetch_assoc()['id'] : null;

        if (!$artistId) {
            echo "Error retrieving artist_id for '$artistName'<br>";
            continue;
        }

        // Get all files (records) in the artist's directory
        $records = array_filter(glob($artist . '/*', GLOB_ONLYDIR), 'is_dir');

        foreach ($records as $record) {
            // Insert or update record in the 'records' table
            $recordName = basename($record);
            $recordExists = $conn->query("SELECT id FROM records WHERE artist_id='$artistId' AND name='$recordName'")->num_rows > 0;

            if (!$recordExists) {
                $sql = "INSERT INTO records (artist_id, name) VALUES ('$artistId', '$recordName')";
                if ($conn->query($sql) === TRUE) {
                    echo "Record '$recordName' added successfully<br>";
                } else {
                    echo "Error adding record '$recordName': " . $conn->error . "<br>";
                }
            } else {
                echo "Record '$recordName' already exists<br>";
            }

            // Get the record_id for the current record
            $recordIdResult = $conn->query("SELECT id FROM records WHERE artist_id='$artistId' AND name='$recordName'");
            $recordId = ($recordIdResult->num_rows > 0) ? $recordIdResult->fetch_assoc()['id'] : null;

            if (!$recordId) {
                echo "Error retrieving record_id for '$recordName'<br>";
                continue;
            }

            // Get all files (songs) in the record's directory
            $songs = array_filter(glob($record . '/*.*'), 'is_file');

            foreach ($songs as $song) {
                // Insert or update song in the 'songs' table
                $songName = basename($song);
                $songExists = $conn->query("SELECT id FROM songs WHERE record_id='$recordId' AND artist_id='$artistId' AND name='$songName'")->num_rows > 0;
            
                if (!$songExists) {
                    $sql = "INSERT INTO songs (record_id, artist_id, name) VALUES ('$recordId', '$artistId', '$songName')";
                    if ($conn->query($sql) === TRUE) {
                        echo "Song '$songName' added successfully<br>";
                    } else {
                        echo "Error adding song '$songName': " . $conn->error . "<br>";
                    }
                } else {
                    echo "Song '$songName' already exists<br>";
                }
            }
        }
    }

    // Clean up: Remove artists, records, and songs that no longer exist in the directory
    removeDeletedArtists($conn, $artists);
    removeDeletedRecords($conn, $artists);
    removeDeletedSongs($conn, $artists);
}

function removeDeletedSongs($conn, $currentArtists)
{
    $currentArtistsNames = array_map('basename', $currentArtists);
    $sql = "DELETE FROM songs WHERE artist_id NOT IN (SELECT id FROM artists WHERE name IN ('" . implode("','", $currentArtistsNames) . "'))";
    $conn->query($sql);
}



function removeDeletedArtists($conn, $currentArtists)
{
    $currentArtistsNames = array_map('basename', $currentArtists);
    $sql = "DELETE FROM artists WHERE name NOT IN ('" . implode("','", $currentArtistsNames) . "')";
    $conn->query($sql);
}

function removeDeletedRecords($conn, $currentArtists)
{
    $currentArtistsNames = array_map('basename', $currentArtists);
    $sql = "DELETE FROM records WHERE artist_id NOT IN (SELECT id FROM artists WHERE name IN ('" . implode("','", $currentArtistsNames) . "'))";
    $conn->query($sql);
}
?>