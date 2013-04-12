<!DOCTYPE HTML>
<html>
    <head>
    <meta charset="utf-8">
        <title>Design and Code an integrated Facebook App</title>
        <link rel="stylesheet" type="text/css" href="static/style.css"></link>
    </head>
    <body>
        <div id="content">
            <?php echo $content; ?>
        </div>
        <?php if(isset($logoutUrl)): ?>
        <div id="logout">
            <a class="button right" href="<?php echo $logoutUrl; ?>"><span class="buttonimage left"></span>Logout</a>
        </div>
        <?php endif; ?>
    </body>
</html>