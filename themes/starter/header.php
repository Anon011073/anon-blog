<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['site_name']; ?></title>
    <!-- Basic starter styles -->
    <link rel="stylesheet" href="themes/starter/style.css">
    <style>
        /* Injected theme options */
        :root {
            --accent-color: <?php echo $config['theme_options']['accent_color'] ?? '#e91e63'; ?>;
        }
    </style>
</head>
<body>
    <header>
        <h1><a href="index.php"><?php echo htmlspecialchars($config['site_name']); ?></a></h1>
        <nav>
            <!-- Navigation logic can be added here -->
        </nav>
    </header>
    <main class="container">
