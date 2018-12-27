<?php
if(isset($_POST['submit'])){
    echo '<script>window.location.replace("' . get_home_url() . '/' . $_POST['unit_name'] . '")</script>';
}
if(isset($_GET['chapter_name'])){
    ?>
    <form action="" method="post">
        <input type="hidden" name="unit_name" value="<?php echo $_GET['chapter_name'] ?>">
        <button type="submit" name="submit">Hinzufügen</button>
    </form>
    <?php
} else{
    echo "Bitte zuerst ein Kapitel anclicken und von dort einen Eintrag hinzufügen!";
}
?>