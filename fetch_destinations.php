<?php
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    // Connect to the database
    $conn = new mysqli('localhost', 'root', 'Adnan@66202', 'syncgo');
    $stmt = $conn->prepare("SELECT DISTINCT destination FROM destinations WHERE destination LIKE ?");
    $likeQuery = "%$query%";
    $stmt->bind_param('s', $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['destination'];
    }
    echo json_encode($suggestions);
}
?>
