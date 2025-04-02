<?php

include 'components/connect.php';

if(isset($_POST['submit'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $c_pass = password_verify($_POST['c_pass'], $pass);
   $c_pass = filter_var($c_pass, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = create_unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_files/'.$rename;

   if(!empty($image)){
      if($image_size > 2000000){
         $warning_msg[] = 'Image size is too large!';
      }else{
         move_uploaded_file($image_tmp_name, $image_folder);
      }
   }else{
      $rename = '';
   }

   $verify_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $verify_email->execute([$email]);

   if($verify_email->rowCount() > 0){
      $warning_msg[] = 'Email already taken!';
   }else{
      if($c_pass == 1){
         $insert_user = $conn->prepare("INSERT INTO `users`(id, name, email, password, image) VALUES(?,?,?,?,?)");
         $insert_user->execute([$id, $name, $email, $pass, $rename]);
         $success_msg[] = 'Registered successfully!';
      }else{
         $warning_msg[] = 'Confirm password not matched!';
      }
   }
   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register - DishRated</title>
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/header.php'; ?>

<section class="auth-form">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Create Account</h3>
      
      <div class="input-group">
         <label for="name">Full Name</label>
         <input type="text" name="name" id="name" required maxlength="50" 
            placeholder="Enter your name" class="box">
      </div>
      
      <div class="input-group">
         <label for="email">Email Address</label>
         <input type="email" name="email" id="email" required maxlength="50" 
            placeholder="Enter your email" class="box">
      </div>
      
      <div class="input-group">
         <label for="password">Password</label>
         <input type="password" name="pass" id="password" required maxlength="50" 
            placeholder="Create a password" class="box">
      </div>
      
      <div class="input-group">
         <label for="cpassword">Confirm Password</label>
         <input type="password" name="c_pass" id="cpassword" required maxlength="50" 
            placeholder="Confirm your password" class="box">
      </div>
      
      <div class="input-group">
         <label for="image">Profile Picture (optional)</label>
         <input type="file" name="image" id="image" class="box" accept="image/*">
      </div>

      <input type="submit" value="Register" name="submit" class="btn">
      
      <div class="account-link">
         <span>Already have an account?</span>
         <a href="login.php">Sign In</a>
      </div>
   </form>
</section>

<?php include 'components/alers.php'; ?>
</body>
</html>