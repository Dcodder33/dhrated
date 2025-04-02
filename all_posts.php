<?php

include 'components/connect.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>all posts</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/header.php'; ?>
<!-- header section ends -->

<!-- view all posts section starts  -->

<section class="all-posts">

   <div class="heading">
      <h1>Trending Dishes</h1>
      <?php if($user_id != ''): ?>
         <a href="add_post.php" class="btn">Add Review</a>
      <?php else: ?>
         <a href="login.php" class="btn" onclick="return confirm('Please login to add a review!')">Add Review</a>
      <?php endif; ?>
   </div>

   <div class="box-container">
      <?php
      // Modified query to sort by average rating
      $select_posts = $conn->prepare("
         SELECT p.*, u.name as username, 
         COALESCE(AVG(r.rating), 0) as avg_rating,
         COUNT(r.id) as total_reviews
         FROM `posts` p 
         JOIN `users` u ON p.user_id = u.id
         LEFT JOIN `reviews` r ON p.id = r.post_id
         GROUP BY p.id
         ORDER BY avg_rating DESC, total_reviews DESC
      ");
      $select_posts->execute();
      if($select_posts->rowCount() > 0){
         while($fetch_post = $select_posts->fetch(PDO::FETCH_ASSOC)){
            // Add rating display
            $stars = round($fetch_post['avg_rating']);
      ?>
      <div class="box">
         <img src="uploaded_files/<?= $fetch_post['image']; ?>" alt="" class="image">
         <h3 class="title"><?= $fetch_post['title']; ?></h3>
         <div class="post-info">
            <div class="price">
               <i class="fas fa-rupee-sign"></i> <?= number_format($fetch_post['price'], 2); ?>
            </div>
            <p class="restaurant"><?= $fetch_post['restaurant']; ?></p>
            <p class="author">by <?= $fetch_post['username']; ?></p>
         </div>
         <div class="rating-stars">
            <?php for($i = 1; $i <= 5; $i++): ?>
               <i class="fas fa-star <?= ($i <= $stars) ? 'active' : ''; ?>"></i>
            <?php endfor; ?>
            <span>(<?= $fetch_post['total_reviews']; ?> reviews)</span>
         </div>
         <a href="view_post.php?get_id=<?= $fetch_post['id']; ?>" class="inline-btn">view post</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">no posts added yet!</p>';
      }
      ?>

   </div>

</section>

<!-- view all posts section ends -->















<!-- sweetalert cdn link  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/alers.php'; ?>

</body>
</html>