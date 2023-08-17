<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">
    <style>
        button {
            transform: scale(0.8);
        }

        .img-container {
            position: relative;
            display: inline-block;
            margin: 5px;
        }

        .img-container img {
            max-width: 200px;
        }

        .img-buttons {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.8);
            padding: 5px;
        }
    </style>
</head>

<body>
<h1>Let's Upload!</h1>

<?php
$path = 'uploads/';
if (isset($_GET['edit'])) {
    ?>
    <section>
        <h2>Replace Image: <?= $_GET['edit'] ?></h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="old_file" value="<?= $_GET['edit'] ?>">
            <label for="image">Choose a new image</label>
            <input type="file" id="image" name="image" required>
            <button type="submit" name="replace_image">Replace</button>
        </form>
    </section>
    <?php
} else {
    ?>
    <section>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="image">Choose an image</label>
            <input type="file" id="image" name="image" required>
            <button type="submit" name="upload">Upload</button>
        </form>
    </section>
    <?php
}
?>

<section id="display">
    <article>
        <h2>Uploaded Images</h2>
        <?php
        $files = glob($path . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        foreach ($files as $file) {
            echo '<div class="img-container">
                <img src="' . $file . '" alt="">
                <div class="img-buttons">
                    <a href="?delete=' . basename($file) . '">Delete</a> |
                    <a href="?edit=' . basename($file) . '">Edit</a>
                </div>
            </div>';
        }
        ?>
    </article>
</section>

<?php
if (isset($_GET['delete'])) {
    $fileToDelete = $path . $_GET['delete'];
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $maxSize = 4000000;
    
    if ($file['error'] > 0) {
        echo "<p>Erreur lors de l'upload</p>";
    } else {
        $mimeType = mime_content_type($file['tmp_name']);
        $mimeType = explode('/', $mimeType);
        $ext = end($mimeType);

        if (!in_array($ext, $allowedExtensions)) {
            echo '<p>Extension non autorisée</p>';
        } elseif ($file['size'] > $maxSize) {
            echo '<p>Fichier trop volumineux</p>';
        } else {
            if (isset($_POST['replace_image'])) {
                $oldFile = $path . $_POST['old_file'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $newFile = uniqid() . '.' . $ext;
            $status = move_uploaded_file($file['tmp_name'], $path . $newFile);

            if ($status) {
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "<p>Problème lors de l'upload</p>";
            }
        }
    }
}
?>
</body>
</html>
