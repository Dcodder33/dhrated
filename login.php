<?php

include 'components/connect.php';

if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = $_POST['pass'];
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $verify_email = $conn->prepare("SELECT * FROM `users` WHERE email = ? LIMIT 1");
   $verify_email->execute([$email]);
   

   if($verify_email->rowCount() > 0){
      $fetch = $verify_email->fetch(PDO::FETCH_ASSOC);
      $verfiy_pass = password_verify($pass, $fetch['password']);
      if($verfiy_pass == 1){
         setcookie('user_id', $fetch['id'], time() + 60*60*24*30, '/');
         header('location:all_posts.php');
      }else{
         $warning_msg[] = 'Incorrect password!';
      }
   }else{
      $warning_msg[] = 'Incorrect email!';
   }
   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login - DishRated</title>
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/header.php'; ?>

<section class="auth-form">
   <form action="" method="post">
      <h3>Welcome Back!</h3>
      
      <div class="input-group">
         <label for="email">Email Address</label>
         <input type="email" name="email" id="email" required maxlength="50" 
            placeholder="Enter your email" class="box">
      </div>
      
      <div class="input-group">
         <label for="password">Password</label>
         <input type="password" name="pass" id="password" required maxlength="50" 
            placeholder="Enter your password" class="box">
      </div>

      <input type="submit" value="Login" name="submit" class="btn">
      
      <div class="account-link">
         <span>Don't have an account?</span>
         <a href="register.php">Sign Up</a>
      </div>
   </form>
</section>

<?php include 'components/alers.php'; ?>
</body>
</html>