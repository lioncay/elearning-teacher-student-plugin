<?php
global $wpdb;

if (isset($_POST['submit'])) {
    $chapterID = $_POST['chapter_id'];
    $counter = 0;
    $multiplechoicestring = "";
    $multiplechoicestring .= $_POST['question'] . "&";
    $multiplechoicestring .= $_POST['explanation'] . "&";
    while (true) {
        $answerpossibility = "answer_" . $counter;
        $answerpossibilitycheck = "trueorfalse_" . $counter;
        if (isset($_POST[$answerpossibility])) {
            $multiplechoicestring .= $_POST[$answerpossibility] . "#";
            $multiplechoicestring .= $_POST[$answerpossibilitycheck] . "|";
        } else {
            break;
        }
        $counter++;
    }
    echo $multiplechoicestring;

    $query = $wpdb->prepare(
        'SELECT MAX(entry_order) AS `entry_order` FROM `chapterentries` WHERE chapter_id = %d ORDER BY entry_order',
        $chapter_id=$chapterID
    );
    $wpdb->query( $query );
    if ($wpdb->num_rows) {
        $highestOrderId=$wpdb->last_result[0]->entry_order;
        for ($i = $_POST['entry_order'] + 1; $i <= $highestOrderId; $i++) {
            $query = $wpdb->prepare(
                'UPDATE `chapterentries` SET `entry_order`=%d WHERE entry_order=%d AND chapter_id = %d',
                $newentry_order = $i + 1,
                $oldentry_order = $i,
                $chapter_id = $chapterID
            );
            $wpdb->query($query);
        }
        $query = $wpdb->prepare(
            'INSERT INTO `chapterentries` (`title`, `chapter_id`, `entry_type`, `input`, `entry_order`) VALUES (%s,%d,%s,%s,%d)',
            $one = $_POST['etitle'],
            $two = $chapterID,
            $three = 'MC',
            $four = $multiplechoicestring,
            $five = $_POST['entry_order'] + 1
        );
        $wpdb->query($query);
    } else {
        if ($_POST['entry_order'] == 0) {
            $query = $wpdb->prepare(
                'INSERT INTO `chapterentries` (`title`, `chapter_id`, `entry_type`, `input`, `entry_order`) VALUES (%s,%d,%s,%s,%d)',
                $one = $_POST['etitle'],
                $two = $chapterID,
                $three = 'MC',
                $four = $multiplechoicestring,
                $five = 1
            );
            $wpdb->query($query);
        } else {
            echo "Ups da hat etwas bei deiner Eingabe nicht funktioniert. Bitte wende dich an den Verantwortlichen Administrator.";
        }
    }
    echo '<script>window.location.replace("' . get_home_url() . '/chapter?chapter_id=' . $chapterID . '")</script>';
}
if (isset($_GET['chapter_id'])) {
    $query = $wpdb->prepare(
        'SELECT title,entry_order FROM `chapterentries` WHERE `chapter_id` = %s ORDER BY entry_order',
        $chapter_id=$_GET['chapter_id']
    );
    $wpdb->query( $query );
    $items=$wpdb->last_result;
    $itemsleng=$wpdb->num_rows;
    ?>
    <form id="multiplechoice" action="" method="post">
        <input type="hidden" name="chapter_id" value="<?php echo $_GET['chapter_id'] ?>">
        <input type="text" placeholder="Bezeichnung" name="etitle" required/>
        <select id="entry_order" name="entry_order" required>
            <option value="" disabled selected>Wo soll dieser Eintrag eingeordnet werden</option>
            <option value="0">am Anfang</option>
            <?php
            if($itemsleng){
                foreach ($items as $item) {
                    echo '<option value="'. $item->entry_order .'">nach '. $item->title .'</option>';
                }
            }
            ?>
        </select>
        <input type="text" placeholder="Frage" name="question" required>
        <input type="text" placeholder="Erklärung" name="explanation" required>
        <input type="button" id="addAn" value="Weitere Antwort hinzufügen">
        <input type="text" placeholder="Antwortmöglichkeit 1" name="answer_0" required>
        <select id="trueorfalse_0" name="trueorfalse_0" required>
            <option value="" disabled selected>Wahr oder Falsch</option>
            <option value="1">Wahr</option>
            <option value="0">Falsch</option>
        </select>
        <button type="submit" name="submit">Hinzufügen</button>
    </form>
    <script>
        let count = 1;

        function createAnswerField() {
            let input = document.createElement('input');
            input.type = 'text';
            input.name = 'answer_' + count;
            input.required = true;
            let out = count + 1;
            input.placeholder = 'Antwortmöglichkeit ' + out;
            count++;
            return input;
        }

        function createSelect() {
            let select = document.createElement('select');
            let right = document.createElement('option');
            right.value = 1;
            right.text = "Wahr";
            let wrong = document.createElement('option');
            wrong.value = 0;
            wrong.text = "Falsch";
            let placeh = document.createElement('option');
            placeh.value = "";
            placeh.text = "Wahr oder Falsch";
            placeh.disabled = true;
            placeh.selected = true;
            select.required = true;
            let out = count - 1;
            select.id = "trueorfalse_" + out;
            select.name = "trueorfalse_" + out;
            select.appendChild(placeh);
            select.appendChild(right);
            select.appendChild(wrong);
            return select;
        }

        function createButton() {
            var input = document.createElement('button');
            input.type = 'submit';
            input.name = 'submit';
            input.innerHTML = 'hinzufügen';
            return input;
        }

        var form = document.getElementById('multiplechoice');
        document.getElementById('addAn').addEventListener('click', function (e) {
            form.removeChild(form.elements['submit']);
            form.appendChild(createAnswerField());
            form.appendChild(createSelect());
            form.appendChild(createButton());
        });
    </script>
    <?php
} else {
    echo "Bitte zuerst ein Kapitel anclicken und von dort einen Eintrag hinzufügen!";
}
?>