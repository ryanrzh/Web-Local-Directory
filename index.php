<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local-Directory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #e0e0e0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #1e1e1e;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        .list-group-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            border: 1px solid #333;
            border-radius: 5px;
            margin-bottom: 10px;
            background-color: #2a2a2a;
        }
        .list-group-item .icon {
            margin-right: 10px;
            font-size: 1.5em;
        }
        .list-group-item a {
            text-decoration: none;
            color: #90caf9;
            flex-grow: 1;
        }
        .list-group-item a:hover {
            text-decoration: underline;
        }
        .timestamp {
            font-size: 0.9em;
            color: #bdbdbd;
        }
        .sort-menu {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sort-menu select, .sort-menu button, .sort-menu input {
            padding: 5px 10px;
            border: 1px solid #333;
            border-radius: 5px;
            background-color: #2a2a2a;
            color: #e0e0e0;
            cursor: pointer;
        }
        .sort-menu select:focus, .sort-menu button:focus, .sort-menu input:focus {
            outline: none;
            border-color: #90caf9;
        }
        .project-count {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Welcome to Index</h1>
        <p>Below are the folders hosted on this server:</p>
        <div class="sort-menu">
            <div>
                <label for="sort">Sort by:</label>
                <select id="sort" onchange="sortFolders()">
                    <option value="name">Name</option>
                    <option value="created">Date Created</option>
                    <option value="modified">Last Modified</option>
                </select>
                <button onclick="reverseOrder()">Reverse Order</button>
            </div>
            <div>
                <label for="search">Search:</label>
                <input type="text" id="search" oninput="searchFolders()" placeholder="Search folders...">
            </div>
            <p class="project-count">Total Folders: <span id="projectCount"></span></p>
        </div>
        <ul class="list-group" id="folderList">
            <?php
            $dir = __DIR__;
            $folders = array_filter(glob($dir . '/*'), 'is_dir');
            $folderData = [];

            foreach ($folders as $folder) {
                $folderName = basename($folder);
                $created = date("Y-m-d", filectime($folder));
                $lastModified = date("Y-m-d H:i:s", filemtime($folder));
                $folderData[] = [
                    'name' => $folderName,
                    'created' => $created,
                    'modified' => $lastModified
                ];
            }

            usort($folderData, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            foreach ($folderData as $folder) {
                echo "<li class='list-group-item' data-name='{$folder['name']}' data-created='{$folder['created']}' data-modified='{$folder['modified']}'>
                        <span class='icon'>📁</span>
                        <a href='/{$folder['name']}'>{$folder['name']}</a>
                        <span class='timestamp'>Created: {$folder['created']} | Last modified: {$folder['modified']}</span>
                      </li>";
            }
            ?>
        </ul>
    </div>

    <script>
        document.getElementById('projectCount').textContent = document.querySelectorAll('.list-group-item').length;

        function sortFolders() {
            const sortOption = document.getElementById('sort').value;
            const folderList = document.getElementById('folderList');
            const folders = Array.from(folderList.getElementsByClassName('list-group-item'));

            folders.sort((a, b) => {
                const aValue = a.getAttribute(`data-${sortOption}`).toLowerCase();
                const bValue = b.getAttribute(`data-${sortOption}`).toLowerCase();
                if (aValue < bValue) return -1;
                if (aValue > bValue) return 1;
                return 0;
            });

            folderList.innerHTML = '';
            folders.forEach(folder => folderList.appendChild(folder));
        }

        function reverseOrder() {
            const folderList = document.getElementById('folderList');
            const folders = Array.from(folderList.getElementsByClassName('list-group-item'));
            folderList.innerHTML = '';
            folders.reverse().forEach(folder => folderList.appendChild(folder));
        }

        function searchFolders() {
            const searchQuery = document.getElementById('search').value.toLowerCase();
            const folders = document.querySelectorAll('.list-group-item');

            folders.forEach(folder => {
                const folderName = folder.getAttribute('data-name').toLowerCase();
                if (folderName.includes(searchQuery)) {
                    folder.style.display = '';
                } else {
                    folder.style.display = 'none';
                }
            });

            const visibleFolders = Array.from(folders).filter(folder => folder.style.display !== 'none');
            document.getElementById('projectCount').textContent = visibleFolders.length;
        }
    </script>
</body>
</html>
