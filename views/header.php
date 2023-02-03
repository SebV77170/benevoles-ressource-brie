<!Doctype html>
<html>
<head>
    <title><?php $titre ?></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/calendar.css">
  <title><?= isset($title) ? h($title) : 'Mon calendrier'; ?></title>
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-3">
    <a href="index.php" class="navbar-brand">Mon calendrier</a>
</nav>
