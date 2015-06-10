<?php

session_start();

function extractAnimals($aParams)
{
    $conn = mysqli_connect("localhost", "root", "", "test2");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $conn->set_charset("utf8");

    if ($_POST[data] == 'suggest') {
        $sQuery = "SELECT a.id, name, type, pic_url, f.is_fav
              FROM animals a
              JOIN species s ON a.id_species = s.id
              JOIN favorites f ON a.id = f.id
              ORDER BY RAND() LIMIT 4;";
        $_SESSION['lamp'] = 0;
    } else {
        $sQuery = "SELECT a.id, name, type, pic_url, f.is_fav FROM animals a
              JOIN favorites f ON a.id = f.id
              JOIN species s ON a.id_species = s.id
              WHERE is_fav = 1;";
        $_SESSION['lamp'] = 1;
    }

    $result = $conn->query($sQuery) or die("error" . mysqli_error($conn));

    $aResult = array();
    while($row = mysqli_fetch_assoc($result)) $aResult[] = $row;
    return $aResult;
}

function clickAnimal($aParams)
{
    $conn = mysqli_connect("localhost", "root", "", "test2");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $conn->set_charset("utf8");

    foreach ($_POST['checkbox'] as $nValue) {
        $nValue = mysqli_real_escape_string($conn, $nValue);

        if ($nValue > 0) $sQuery = "UPDATE favorites SET is_fav = 1 WHERE id = $nValue";
        else $sQuery = "UPDATE favorites SET is_fav = 0 WHERE id = $nValue*(-1)";

        if (mysqli_query($conn, $sQuery)) {
            #echo "record updated successfully";
        } else {
            echo "error: " . $sQuery . "<br>" . mysqli_error($conn);
        }
    }
    if ($_SESSION['lamp']) {
        $sQuery = "SELECT a.id, name, type, pic_url, f.is_fav FROM animals a
              JOIN favorites f ON a.id = f.id
              JOIN species s ON a.id_species = s.id
              WHERE is_fav = 1;";

        $result = $conn->query($sQuery) or die("error" . mysqli_error($conn));

        $aResult = array();
        while ($row = mysqli_fetch_assoc($result)) $aResult[] = $row;
        if($aResult == false) return true;
        return $aResult;
    }
    return false;
}

    header('Content-Type: application/json');
    echo json_encode(call_user_func($_GET['action'], $_POST));