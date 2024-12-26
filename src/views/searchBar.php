<?php
include '../database/db.php';

$mysqli = getDbConnection();

if (isset($_GET['q'])) {
    $searchQuery = '%' . $_GET['q'] . '%';
    $sql = "SELECT id_item, name FROM item WHERE name LIKE ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    echo json_encode($items);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="w-full max-w-md">
    <div class="relative">
        <input
                type="text"
                id="searchInput"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Zoek..."
        />
        <button
                onclick="handleSearch()"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 focus:outline-none"
        >
            Zoek
        </button>
    </div>
    <div id="results" class="mt-4 text-gray-700"></div>
</div>

<script>
    async function handleSearch() {
        const query = document.getElementById('searchInput').value;
        const resultsDiv = document.getElementById('results');

        if (query.trim() === '') {
            resultsDiv.innerHTML = `<p class="text-red-500">Vul een zoekterm in.</p>`;
            return;
        }

        try {
            const response = await fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            if (data.length > 0) {
                resultsDiv.innerHTML = data
                    .map(item => {
                        return `<p><a href="?id_item=${encodeURIComponent(item.id_item)}" class="text-blue-500 hover:underline">${item.name}</a></p>`;
                    })
                    .join('');
            } else {
                resultsDiv.innerHTML = `<p class="text-gray-500">Geen zoekresultaten gevonden, probeer het opnieuw.</p>`;
            }
        } catch (error) {
            console.error('Error fetching search results:', error);
            resultsDiv.innerHTML = `<p class="text-red-500">Error while searching.</p>`;
        }
    }

    document.getElementById('searchInput').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            handleSearch();
        }
    });
</script>
</body>
</html>
