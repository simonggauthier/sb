<!DOCTYPE html>
<html>

<head>
</head>

<body>
    <h1>SB Api</h1>

    <?php
    include '../../api/php/api/definition.php';

    $definition = new ApiDefinition('../../api/definition.json');

    foreach ($definition->routes as $route) {
    ?>
        <div class="route">
            <h2><?php echo $route->method . ' ' . $route->action . ' (' . $route->controllerName . ')' ?></h2>

            <form method="<?php echo $route->method ?>" action="/sbapi/<?php echo $route->action ?>">
                <?php foreach ($route->parameters as $param) {
                    $type = $param->hint === null ? 'text' : 'textarea';
                ?>

                    <input type="<?php echo $type ?>" name="<?php echo $param->name ?>" placeholder="<?php echo $param->name ?>" value="<?php echo $param->name === 'delete' ? 'delete' : '' ?>" />

                <?php } ?>

                <button type="submit">Submit</button>
            </form>
        </div>
    <?php
    }
    ?>
</body>

</html>
