<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Music Player</title>
        <meta name="description" content="Music player for my CD files">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div id="screenContainer" class="container">
            <header>
                <h1>Raynauld's Music Player</h1>
            </header>
            <div id="albumSelector" class='selector container'>
                <?php
                include 'logic.php';
                
                // MySQL connection details
                $host = 'localhost';
                $username = 'root';
                
                $database = 'musiclib';

                $conn = connectToDatabase($host, $username, $database);
                createTables($conn);
                scanLibFolder('lib', $conn);

                // Close the database connection
                $conn->close();
                ?>
            </div>
            <div id="songSelector" class='selector container'>default songselector</div>
            <main>
                <audio controls></audio>
            </main>
            <footer>default footer</footer>
        </div>

        <script src="index.js" async defer></script>
    </body>
</html>
