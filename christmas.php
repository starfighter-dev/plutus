<?php
   require('env.php');
   $conn_str = sprintf("mysql:host=%s;dbname=%s", DB_HOST, DB_NAME);
   $dbh = new PDO($conn_str, DB_USERNAME, DB_PASSWORD);
   $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $dbh->exec("set names utf8"); // TODO bad?



   $transactions = [
      "VALUES ('person1','web','2022-12-25','Christmas Pressie from Grandma Becca',20.00)",
      "VALUES ('person2','web','2022-12-25','Christmas Pressie from Grandma Becca',20.00)",
      "VALUES ('person2','web','2022-12-25','Christmas Pressie from Aunty Angie',20.00)",
   ];

   foreach ( $transactions as $t ) {
      $sql = "insert into transactions (account,type,date,descr,amount) ".$t;
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
   }

?>
