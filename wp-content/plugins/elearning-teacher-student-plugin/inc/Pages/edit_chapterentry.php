<?php
global $wpdb;
if(isset($_POST['submit'])){

    $query = $wpdb->prepare(
        'SELECT id,entry_order FROM `chapterentries` WHERE `chapter_id` = %s ORDER BY entry_order',
        $chapter_id=$_POST['chapter_id']
    );
    $wpdb->query( $query );
    $items=$wpdb->last_result;
    $itemsleng=$wpdb->num_rows;

    $entriesToUpdate = array();
    $old_order = $_POST['old_order'];
    $new_order = $_POST['entry_order']+1;
    if($new_order>$old_order){
        foreach ($items as $item) {
            if($item->entry_order>$old_order && $item->entry_order<=$new_order){
                array_push($entriesToUpdate,$item->id);
            }
        }
        $new_order--;
    }elseif ($new_order<$old_order){
        foreach ($items as $item) {
            if($item->entry_order<$old_order && $item->entry_order>=$new_order){
                array_push($entriesToUpdate,$item->id);
            }
        }
    }
    foreach ($entriesToUpdate as $item){
        if($new_order>$old_order){
            $query = $wpdb->prepare(
                'UPDATE `chapterentries` SET `entry_order`=`entry_order`-1 WHERE `id` = %d',
                $id = $item
            );
            $wpdb->query($query);
        }elseif ($new_order<$old_order) {
            $query = $wpdb->prepare(
                'UPDATE `chapterentries` SET `entry_order`=`entry_order`+1 WHERE `id` = %d',
                $id = $item
            );
            $wpdb->query($query);
        }
    }
    if ($old_order!=$new_order){
        $query = $wpdb->prepare(
            'UPDATE `chapterentries` SET `entry_order`=%d WHERE `id` = %d',
            $one = $new_order,
            $id = $_POST['chapterentry_id']
        );
        $wpdb->query($query);
    }
    if($_POST['type']=="IN"){
        $query = $wpdb->prepare(
            'UPDATE `chapterentries` SET `title`=%s, `input`=%s WHERE `id` = %d',
            $one=$_POST['etitle'],
            $two=$_POST['input'],
            $chapterId=$_POST['chapterentry_id']
        );
        $wpdb->query( $query );
    }elseif ($_POST['type']=="OQ"){
        $openquestionstring = "";
        $openquestionstring .= $_POST['question'] . "&";
        $openquestionstring .= $_POST['answer_1'] . "&";
        $openquestionstring .= $_POST['answer_2'] . "&";
        $openquestionstring .= $_POST['answer_3'];
        $query = $wpdb->prepare(
            'UPDATE `chapterentries` SET `title`=%s, `input`=%s WHERE `id` = %d',
            $one=$_POST['etitle'],
            $two=$openquestionstring,
            $chapterId=$_POST['chapterentry_id']
        );
        $wpdb->query( $query );
    }
    elseif ($_POST['type']=="MC"){
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
        $query = $wpdb->prepare(
            'UPDATE `chapterentries` SET `title`=%s, `input`=%s WHERE `id` = %d',
            $one=$_POST['etitle'],
            $two=$multiplechoicestring,
            $chapterId=$_POST['chapterentry_id']
        );
        $wpdb->query( $query );
    }
    echo '<script>window.location.replace("' . get_home_url() . '/chapter?chapter_id=' . $_POST['chapter_id'] . '")</script>';
}else if (isset($_GET['id'])) {
    $query = $wpdb->prepare(
        'SELECT * FROM `chapterentries` WHERE `id` = %d',
        $id=$_GET['id']
    );
    $wpdb->query( $query );
    $entry = $wpdb->last_result[0];

    $query = $wpdb->prepare(
        'SELECT title,entry_order FROM `chapterentries` WHERE `chapter_id` = %s ORDER BY entry_order',
        $chapter_id=$entry->chapter_id
    );
    $wpdb->query( $query );
    $items=$wpdb->last_result;
    $itemsleng=$wpdb->num_rows;
    if ($_GET['type'] == "IN") {
        ?>
        <form action="" method="post">
            <input type="text" placeholder="Bezeichnung" name="etitle" value="<?php echo $entry->title; ?>" required/>
            <select id="entry_order" name="entry_order" required>
                <option value="" disabled>Wo soll dieser Eintrag eingeordnet werden</option>
                <?php
                if ($entry->entry_order-1==0){
                    echo '<option value="0" selected>am Anfang</option>';
                }else{
                    echo '<option value="0">am Anfang</option>';
                }
                if ($itemsleng) {
                    foreach ($items as $item) {
                        if ($item->entry_order==$entry->entry_order-1){
                            echo '<option value="' . $item->entry_order . '" selected>nach ' . $item->title . '</option>';
                        }else if($item->entry_order!=$entry->entry_order){
                            echo '<option value="' . $item->entry_order . '">nach ' . $item->title . '</option>';
                        }
                    }
                }
                ?>
            </select>
            <?php
            $content = $entry->input;
            $editor_id = 'input';
            wp_editor($content, $editor_id);
            ?>
            <input type="hidden" name="chapter_id" value="<?php echo $entry->chapter_id ?>">
            <input type="hidden" name="chapterentry_id" value="<?php echo $_GET['id'] ?>">
            <input type="hidden" name="old_order" value="<?php echo $entry->entry_order ?>">
            <input type="hidden" name="type" value="IN">
            <button type="submit" name="submit">Bearbeiten</button>
        </form>
        <?php
    } elseif ($_GET['type'] == "OQ") {
        $oqelements = explode("&", $entry->input);
        ?>
        <form action="" method="post">
            <input type="text" placeholder="Bezeichnung" name="etitle" value="<?php echo $entry->title; ?>" required/>
            <select id="entry_order" name="entry_order" required>
                <option value="" disabled>Wo soll dieser Eintrag eingeordnet werden</option>
                <?php
                if ($entry->entry_order-1==0){
                    echo '<option value="0" selected>am Anfang</option>';
                }else{
                    echo '<option value="0">am Anfang</option>';
                }
                if ($itemsleng) {
                    foreach ($items as $item) {
                        if ($item->entry_order==$entry->entry_order-1){
                            echo '<option value="' . $item->entry_order . '" selected>nach ' . $item->title . '</option>';
                        }else if($item->entry_order!=$entry->entry_order){
                            echo '<option value="' . $item->entry_order . '">nach ' . $item->title . '</option>';
                        }
                    }
                }
                ?>
            </select>
            <input type="text" placeholder="Frage" name="question" value="<?php echo $oqelements[0]; ?>" required/>
            <input type="text" placeholder="Antwortmöglichkeit 1 (mögliche Antwort oder nur Keywords die vorkommen sollten.)" name="answer_1" value="<?php echo $oqelements[1]; ?>" required/>
            <input type="text" placeholder="Antwortmöglichkeit 2 (optional)" name="answer_2" value="<?php echo $oqelements[2]; ?>"/>
            <input type="text" placeholder="Antwortmöglichkeit 3 (optional)" name="answer_3" value="<?php echo $oqelements[3]; ?>"/>
            <input type="hidden" name="chapter_id" value="<?php echo $entry->chapter_id ?>">
            <input type="hidden" name="chapterentry_id" value="<?php echo $_GET['id'] ?>">
            <input type="hidden" name="old_order" value="<?php echo $entry->entry_order ?>">
            <input type="hidden" name="type" value="OQ">
            <button type="submit" name="submit">Bearbeiten</button>
        </form>
        <?php
    } elseif ($_GET['type'] == "MC") {
        $mcelements = explode("&", $entry->input);
        $answers = explode("|", $mcelements[2]);
        array_pop($answers);
        ?>
        <form id="multiplechoice" action="" method="post">
            <input type="hidden" name="chapter_id" value="<?php echo $entry->chapter_id ?>">
            <input type="hidden" name="chapterentry_id" value="<?php echo $_GET['id'] ?>">
            <input type="hidden" name="old_order" value="<?php echo $entry->entry_order ?>">
            <input type="hidden" name="type" value="MC">
            <input type="text" placeholder="Bezeichnung" name="etitle" value="<?php echo $entry->title; ?>" required/>
            <select id="entry_order" name="entry_order" required>
                <option value="" disabled>Wo soll dieser Eintrag eingeordnet werden</option>
                <?php
                if ($entry->entry_order-1==0){
                    echo '<option value="0" selected>am Anfang</option>';
                }else{
                    echo '<option value="0">am Anfang</option>';
                }
                if ($itemsleng) {
                    foreach ($items as $item) {
                        if ($item->entry_order==$entry->entry_order-1){
                            echo '<option value="' . $item->entry_order . '" selected>nach ' . $item->title . '</option>';
                        }else if($item->entry_order!=$entry->entry_order){
                            echo '<option value="' . $item->entry_order . '">nach ' . $item->title . '</option>';
                        }
                    }
                }
                ?>
            </select>
            <input type="text" placeholder="Frage" name="question" value="<?php echo $mcelements[0]; ?>" required>
            <input type="text" placeholder="Erklärung" name="explanation" value="<?php echo $mcelements[1]; ?>" required>
            <input type="button" id="addAn" value="Weitere Antwort hinzufügen">
            <script>count = 0;</script>
            <?php
            $count = 0;
            foreach ($answers as $item){
                $item = explode("#", $item);
                ?>
                <input type="text" placeholder="Antwortmöglichkeit <?php echo $count+1; ?>" name="answer_<?php echo $count; ?>" value="<?php echo $item[0]; ?>" required>
                <select id="trueorfalse_<?php echo $count; ?>" name="trueorfalse_<?php echo $count; ?>" required>
                    <option value="" disabled>Wahr oder Falsch</option>
                    <?php if($item[1]){ ?>
                        <option value="1" selected>Wahr</option>
                        <option value="0">Falsch</option>
                    <?php }else{ ?>
                        <option value="1">Wahr</option>
                        <option value="0" selected>Falsch</option>
                    <?php
                    };
                    $count++;
                    ?>
                </select>
                <?php
                echo "<script>count++;</script>";
            }
            ?>
            <button type="submit" name="submit">Bearbeiten</button>
        </form>
        <script>
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
    }
} else {
    echo "<p>Hoppala! Da hat etwas nicht funktioniert. Bitte versuche es noch einmal..." .
        "Suche dir ein Kapitel aus und klicke noch einmal auf den Bearbeiten-Stift neben einem Eintrag deiner Wahl</p>";
}

?>