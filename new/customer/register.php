<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Register</title>
</head>
<body>


        <!-- Background image -->
    <div class="container" >
        <div class="p-5">
                <?php 
                include_once('./component/backBtn.php');
                echo btn::btn("login.php");
                ?>
        </div>
        <!-- Background image -->

        <div class="card" >
            <div class="card-body ">
                <div class="row justify-content-center align-items-center">
                    <div class="col-md-6">
                    <h2 class="fw-bold mb-5 text-center text-head" >Sign up</h2>
                    <form method="post" action="registerProcess.php">
                        <!-- 2 column grid layout with text inputs for the first and last names -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="form-outline">         
                                    <label for="fname">First name</label>
                                    <input type="text" id="fname" name="fname"  class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="form-outline">                  
                                    <label for="lname">Last name</label>
                                    <input type="text" id="lname" name="lname"  class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="tel" class="form-label">Telephone</label>
                            <input type="tel" class="form-control" id="tel" name="tel" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sex" value="M" required> Male
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="sex" value="F"> Female
                        </div>
                        <button type="submit" class="btn btn-primary" >Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('password').addEventListener('input', function() {
            var password = this.value;
            var passwordError = document.getElementById('password-error');
            var specialChars = /[!@#$%^&*()\-_=+{};:,<.>]/;
            var numericChars = /[0-9]/;
            var alphabeticChars = /[a-z]/;
            var alphabeticCharsUp = /[A-Z]/;
            var buttonSubmit = document.getElementById('button-submit')
            if (!(specialChars.test(password))) {
                passwordError.textContent = "รหัสผ่านต้องประกอบด้วยอักษรพิเศษอย่างน้อย 1 ตัวอักษร";
            } else if (!(alphabeticCharsUp.test(password))) {
                passwordError.textContent = "รหัสผ่านต้องประกอบด้วยตัวอักษรตัวใหญ่อย่างน้อย 1 ตัวอักษร";
            } else if (!(alphabeticChars.test(password))) {
                passwordError.textContent = "รหัสผ่านต้องประกอบด้วยตัวอักษรตัวเล็กอย่างน้อย 1 ตัวอักษร";
            } else if (!(numericChars.test(password))) {
                passwordError.textContent = "รหัสผ่านต้องประกอบด้วยตัวเลขอย่างน้อย 1 ตัว";
            } else if(password.length < 8){
                passwordError.textContent = "รหัสผ่านต้องมีความยาวมากกว่า 8 ตัวอักษร";
            } else if (password.length > 24) {
                passwordError.textContent = "รหัสผ่านต้องมีความยาวไม่เกิน 24 ตัวอักษร";
            } else {
                passwordError.textContent = "";
                buttonSubmit.click();
            }
        });
    </script>
</body>
</html>
