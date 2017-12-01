<!DOCTYPE html>

<html>
    <head>
        <?php if (isset($title)): ?>
            <title><?= htmlspecialchars($title) ?></title>
        <?php else: ?>
            <title>Oth</title>
        <?php endif ?>
        <base href="http://mi-casa/oth/">

        <style type="text/css">
            input {
                display: block;
                margin-bottom: 20px;
            }
        </style>

    </head>

    <body>
        <nav>
            <a href="index.php">Home</a>
        </nav>
        <div class="container">