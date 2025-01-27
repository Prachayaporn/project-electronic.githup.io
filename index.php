<?php 
include('server.php');

$query = mysqli_query($conn, "SELECT * FROM equipment");
$rows = mysqli_num_rows($query);
$errors = array(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- ใช้ไอคอนจาก Bootstrap -->
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Arial', sans-serif;
        }
        .homecontent {
            margin-top: 70px; /* Make space for navbar */
            text-align: center;
        }
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            max-width: 350px;
            margin: 15px auto; /* Center the card */
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
        }
        .card-img-top {
            height: 250px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
        }
        .card-body {
            padding: 20px;
            text-align: center;
        }
        .card-title {
            font-size: 1.6rem;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .card-text {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        .alert {
            margin-bottom: 30px;
            font-size: 1.2rem;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        a {
            text-decoration: none;
            color: inherit;
        }
        .row {
            display: flex;
            justify-content: center;
        }
        
        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        table td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
   
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include("nev.php"); ?>

    <div class="homecontent container">
        <!-- Success Message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <h3>
                    <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                    ?>
                </h3>
            </div>
        <?php endif ?>

        <!-- Cards Section -->
        <div class="row g-4">
            <!-- Borrow Equipment Card -->
            <div class="col-12 col-md-6 col-lg-4">
                <a href="borrow.php">
                    <div class="card">
                        <img src="img/1.jpg" class="card-img-top" alt="Borrow Equipment">
                        <div class="card-body">
                            <h5 class="card-title">ยืมอุปกรณ์</h5>
                            <p class="card-text">คลิกที่นี่เพื่อเลือกอุปกรณ์ที่คุณต้องการยืม.</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Return Equipment Card -->
            <div class="col-12 col-md-6 col-lg-4">
                <a href="return.php">
                    <div class="card">
                        <img src="img/2.jpg" class="card-img-top" alt="Return Equipment">
                        <div class="card-body">
                            <h5 class="card-title">คืนอุปกรณ์</h5>
                            <p class="card-text">คลิกที่นี่เพื่อตรวจสอบและคืนอุปกรณ์.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <table>
        <thead>
            <tr>
                <th>รูปภาพ</th>
                <th>ชื่ออุปกรณ์</th>
                <th>เลขอุปกรณ์</th>
                <th>จำนวน</th>

            </tr>
        </thead>
        <tbody>
            <?php if ($rows > 0): ?>
                <?php while ($equipment = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td>
                        <?php if (!empty($equipment['imeg_em'])): ?>
                                <img src="em_photo/<?php echo htmlspecialchars($equipment['imeg_em']); ?>" width="50" alt="รูปอุปกรณ์">
                            <?php else: ?>
                                <img src="img/no image.png" width="50" alt="ไม่มีรูปภาพ">
                            <?php endif; ?>
                        </td>
                        <td><?php echo $equipment['name_em']; ?></td>
                        <td><?php echo $equipment['num_em']; ?></td>
                        <td><?php echo $equipment['available']; ?></td>
                       
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-danger">ไม่มีอุปกรณ์</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
