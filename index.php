<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Music Player</title>
        <meta name="description" content="Music player for my CD files">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="style.css">
        <script defer src="index.js"></script>
    </head>
    <body>
        <?php include 'logic.php'; ?>
        <div id="screenContainer" class="container">
        
            <header>
                <h1>Raynauld's Music Player</h1>
            </header>
            <div id="albumSelector" class='selector container'>
                
                <?php
                    $conn = connectToDatabase('localhost', 'root', 'musiclib');
                    displayArtists($conn);
                ?>
            </div>
            <div id="songSelector" class='selector container'>
            <?php
                $conn = connectToDatabase('localhost', 'root', 'musiclib');
                $recordId = isset($_GET['recordId']) ? $_GET['recordId'] : null;
                displaySongs($conn, $recordId); // Pass $recordId when needed
            ?>
        </div>
        <main>
            <div id="audioContainer" class='container'>
                <audio id='audio' controls></audio>
            </div>
        </main>
        
        <footer><?php getLibData();?></footer>

        
    </div>

    
</body>
</html>
