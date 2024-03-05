<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qlbanhang";

//create connection
$conn = new mysqli($servername, $username, $password, $dbname);
//check connection
if ($conn->connect_error) {
    die("Connection failed : " .$conn->connect_error);
}
// xu ly khi form duoc gui di
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // lay gia tri tu form
    $oldPassword = $_POST['pass_old'];
    $newpassword = $_POST['pass_new'];
    $confirmpassword = $_POST['pass_new2'];

    // kiem tra mat khau cu 
    session_start();
    $id = $_SESSION['id'];
    $sql = "SELECT password from customers where id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storepassword = $row['password'];

        if (md5($oldPassword) != $storepassword) {
            echo "Mat khau cu khong trung khop.";
            header('Refresh: 3;url = sua_mk.php');
        }else {
            // kiem tra mat khau moi va xac nhan mat khau moi
            if($newpassword !== $confirmpassword) {
                echo "Mat khau moi va xac nhan mat khau khong khop.";
                header('Refresh: 3;url = sua_mk.php');
            }elseif ($newpassword === $oldPassword) {
                echo "Mat khau moi phai khac mat khau cu.";
                header('Refresh: 3;url = sua_mk.php');
            }else {
                //tien hanh bam mat khau moi
                $hashedpassword = md5($newpassword);

                // luu mat khau vao csdl
                $updatesql = "UPDATE customers set password = '$hashedpassword' where id = '$id'";
                if ($conn->query($updatesql) === TRUE) {
                    echo "Đổi mật khẩu thành công.";
                    echo '<a href="homepage.php">Trang chủ</a>';
                } else {
                    echo "Lỗi khi cập nhật mật khẩu: " . $conn->error;
                }
            }
        }
    } else {
        echo "Khong tim thay nguoi dung.";
    }

}
