<?php
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];

        $username = $_POST['username'];
        $sex = $_POST['sex'];
        $tel = $_POST['tel'];
        $options = [
            'cost' => 10
          ];
    
        $password  = password_hash($_POST['password'], PASSWORD_BCRYPT,$options);
        
        include_once '../conn.php'; 
        $select_user = "SELECT * FROM customer_account WHERE Username = '$username'";
        $run_qry = mysqli_query($conn, $select_user);
        echo mysqli_num_rows($run_qry);
        if (mysqli_num_rows($run_qry) == 0) {
            $stmt_1 = mysqli_query($conn, "INSERT INTO customer(CusFName , CusLName, Sex ,Tel ,role)
            VALUES('$fname', '$lname' ,'$sex','$tel','member');");
            $last_inserted_id = mysqli_insert_id($conn);

            $stmt_2 = mysqli_query($conn, "INSERT INTO customer_account (UserName , PassWord , CusID)
            VALUES('$username' , '$password' , '$last_inserted_id' );");

            if (!$stmt_1 && !$stmt_2) {
                echo "Error";
            } else {
                echo 'Insert data = is Successful.';
            }
            // header("Location: login.php");
            mysqli_close($conn);
            exit(); 

        }
        else {
            echo "User Have Already!";
            header("Location: login.php");
            mysqli_close($conn);
            exit(); 
        }
    }
?>

