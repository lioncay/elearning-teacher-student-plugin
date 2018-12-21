<?php
?>

<form>
    <button type="button">Create new Course</button>
</form>

<ul>
    <?php
    $items = wp_get_nav_menu_items("main",array());
    foreach ($items as $item) {
        ?><li><?php$item->title?></li><?php;
    }
    ?>
</ul>
