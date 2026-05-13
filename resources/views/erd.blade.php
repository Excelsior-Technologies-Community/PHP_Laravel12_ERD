<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Laravel ERD</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:Arial, sans-serif;
            background:#f4f7fb;
            color:#222;
            transition:0.3s;
        }

        body.dark{
            background:#121212;
            color:white;
        }

        .navbar{
            padding:20px;
            background:linear-gradient(90deg,#4f46e5,#7c3aed);
            display:flex;
            justify-content:space-between;
            align-items:center;
            color:white;
        }

        .navbar h1{
            font-size:28px;
        }

        .nav-buttons{
            display:flex;
            gap:10px;
        }

        button,a{
            border:none;
            padding:12px 18px;
            border-radius:10px;
            cursor:pointer;
            text-decoration:none;
            font-weight:bold;
            transition:0.3s;
        }

        .theme-btn{
            background:white;
            color:#4f46e5;
        }

        .download-btn{
            background:#10b981;
            color:white;
        }

        .container{
            width:90%;
            margin:30px auto;
        }

        .search-box{
            margin-bottom:30px;
        }

        .search-box input{
            width:100%;
            padding:15px;
            border-radius:12px;
            border:1px solid #ccc;
            font-size:16px;
        }

        .tables{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
            gap:25px;
        }

        .table-box{
            background:white;
            border-radius:18px;
            padding:20px;
            box-shadow:0 10px 25px rgba(0,0,0,0.08);
            transition:0.3s;
        }

        body.dark .table-box{
            background:#1f1f1f;
        }

        .table-box:hover{
            transform:translateY(-5px);
        }

        .table-title{
            font-size:22px;
            margin-bottom:15px;
            color:#4f46e5;
        }

        ul{
            list-style:none;
        }

        li{
            padding:10px;
            border-bottom:1px solid #eee;
        }

        body.dark li{
            border-bottom:1px solid #333;
        }

        .relationship{
            margin-top:15px;
            font-size:14px;
            color:#888;
        }

        footer{
            text-align:center;
            padding:30px;
            margin-top:40px;
            color:#777;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <h1>Advanced Laravel 12 ERD</h1>

        <div class="nav-buttons">
            <button class="theme-btn" id="themeToggle">
                Toggle Theme
            </button>

            <a href="/export-pdf" class="download-btn">
                Download PDF
            </a>
        </div>
    </div>

    <div class="container">

        <div class="search-box">
            <input
                type="text"
                id="searchTable"
                placeholder="Search tables..."
            >
        </div>

        <div class="tables" id="tableContainer">

            @foreach($tables as $table)

                <div class="table-box">

                    <div class="table-title">
                        {{ $table['name'] }}
                    </div>

                    <ul>
                        @foreach($table['columns'] as $column)

                            <li>{{ $column }}</li>

                        @endforeach
                    </ul>

                    <div class="relationship">
                        Relationship Detected
                    </div>

                </div>

            @endforeach

        </div>

    </div>

    <footer>
        Smart Database Relationship Visualizer © 2026
    </footer>

    <script>

        // Dark Mode
        const toggleBtn = document.getElementById('themeToggle');

        toggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark');
        });

        // Search Tables
        const searchInput = document.getElementById('searchTable');

        searchInput.addEventListener('keyup', function() {

            let value = this.value.toLowerCase();

            let tables = document.querySelectorAll('.table-box');

            tables.forEach(table => {

                let text = table.innerText.toLowerCase();

                if(text.includes(value)) {
                    table.style.display = 'block';
                    table.style.border = '3px solid #4f46e5';
                } else {
                    table.style.display = 'none';
                }

            });

        });

    </script>

</body>
</html>