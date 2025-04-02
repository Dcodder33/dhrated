<?php
include 'components/connect.php';

if($user_id == ''){
   header('location:login.php');
}

if(isset($_POST['publish'])){

   $id = create_unique_id();
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $restaurant = $_POST['restaurant'];
   $restaurant = filter_var($restaurant, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = create_unique_id().'.'.$image_ext;
   $image_folder = 'uploaded_files/'.$rename;

   // Validate and process image
   if(!empty($image)){
      if($image_size > 2000000){
         $warning_msg[] = 'Image size is too large!';
      }else{
         move_uploaded_file($image_tmp_name, $image_folder);
      }
   }else{
      $rename = '';
   }

   // Only insert if no image error
   if(!isset($warning_msg)){
      try {
         $insert_post = $conn->prepare("INSERT INTO `posts`(id, user_id, title, restaurant, price, image) VALUES(?,?,?,?,?,?)");
         $insert_post->execute([$id, $user_id, $title, $restaurant, $price, $rename]);
         $success_msg[] = 'Post published successfully!';
      } catch(PDOException $e) {
         $error_msg[] = 'Database error: ' . $e->getMessage();
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
   <title>Add Post - DishRated</title>
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/header.php'; ?>

<section class="account-form">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Share a Dish</h3>
      
      <p class="placeholder">Dish name <span>*</span></p>
      <input type="text" name="title" required maxlength="100" placeholder="Enter dish name" class="box">
      
      <p class="placeholder">Restaurant <span>*</span></p>
      <input type="text" name="restaurant" required maxlength="100" placeholder="Enter restaurant name" class="box">
      
      <p class="placeholder">Price <span>*</span></p>
      <input type="number" name="price" required min="0" max="9999999" maxlength="7" placeholder="Enter dish price" class="box">
      
      <p class="placeholder">Dish photo</p>
      <input type="file" name="image" class="box" accept="image/*">
      
      <input type="submit" value="Publish Post" name="publish" class="btn">
      <a href="all_posts.php" class="option-btn">Go Back</a>
   </form>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="js/script.js"></script>
<?php include 'components/alers.php'; ?>

</body>
</html>
